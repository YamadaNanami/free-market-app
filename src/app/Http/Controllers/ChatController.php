<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChatRequest;
use App\Models\Chat;
use App\Models\Evaluation;
use App\Models\Trade;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ChatController extends Controller
{
    public function index($tradeId){
        $trade = Trade::find($tradeId);
        $userId = Auth::id();

        // ログインユーザーが購入者かを判定するためのフラグ
        $isPurchaser = $trade['purchaser_user_id'] == $userId ? true : false;

        //取引評価リンクを表示するか判定するためのフラグ
        $showEvaluationLink = $this->evaluateLinkState($isPurchaser,$tradeId);

        //チャット相手のuser_idを取得する
        $otherUserId = $trade['seller_user_id'] != $userId
            ? $trade['seller_user_id']
            : $trade['purchaser_user_id'];

        // チャット画面に表示していない取引中の商品を取得
        $otherTrades = Trade::where(function ($query) use ($userId) {
                $query->where('seller_user_id', $userId)
                    ->orWhere('purchaser_user_id', $userId);
            })
            ->whereNotIn('id',array($tradeId))
            ->with('item')
            ->get();

        // 対象の商品情報を取得
        $item = $trade->item()->first();

        // ログインユーザーとチャット相手のユーザーの情報を取得する
        $loginUser = User::with('profile')->find($userId);

        $otherUser = User::with('profile')->find($otherUserId);

        $sql = $trade->chats();
        $chats = $sql->orderBy('id')
            ->get();

        DB::transaction(function () use ($sql, $otherUserId) {
            //ログインユーザーがチャットを未読だった場合、チャット画面表示時にunread_flagを更新する
            $updateChats = $sql->where('user_id', $otherUserId)
                ->where('unread_flag', 0);

            $updateChats->update(['unread_flag'=> 1]);
        });

        return view('chat', compact('tradeId','isPurchaser','showEvaluationLink','otherTrades' ,'item', 'loginUser','otherUser','chats'));
    }

    private function evaluateLinkState($isPurchaser,$tradeId){
        $evaluationSql = Evaluation::where('trade_id', $tradeId);

        if($isPurchaser){
            //ログインユーザーが購入者の場合
            if($evaluationSql->first()){
                // 評価済みの場合
                return ['display' => true, 'status' => 'finished'];
            }else{
                // 未評価の場合
                return ['display' => true, 'status' => 'unfinished'];
            }
        }else{
            //ログインユーザーが出品者の場合
            $countTradeEvaluations = count($evaluationSql->get());

            if($countTradeEvaluations == 1){
                // 購入者のみ評価済みの場合
                return ['display' => true, 'status' => 'unfinished'];
            }elseif($countTradeEvaluations == 2){
                // 購入者・出品者共に評価済みの場合
                return ['display' => true, 'status' => 'finished'];
            }

            // 購入者が未評価の場合
            return ['display' => false];
        }

    }

    public function editMessage(Request $request,$chatId){
        $chat = Chat::find($chatId);

        // メッセージが更新されていたらDBを更新する
        $chat->message = $request->message;
        $chat->save();

        return redirect()->route('chat.index', ['trade_id' => $chat->trade_id]);
    }

    public function deleteMessage($chatId){
        $targetChat = Chat::find($chatId);
        $tradeId = $targetChat->trade_id;

        DB::transaction(function () use ($targetChat) {
            //対象メッセージの削除
            $targetChat->delete();

            //img_urlがある場合は、ストレージから対象の画像を削除する
            if(!is_null($targetChat->img_url)){
                Storage::disk('public')->delete('img/'.$targetChat->img_url);
            }
        });

        return redirect()->route('chat.index', ['trade_id' => $tradeId]);
    }

    public function sendMessage(ChatRequest $request,$tradeId){
        $userId = Auth::id();

        // セッションに保存されている本文を削除する
        if(!empty(session()->get('savedTexts'))){
            $savedTexts = array_values(array_filter(session()->get('savedTexts'), function ($text) use ($userId, $tradeId) {
                return !($text['userId'] == $userId && $text['tradeId'] == $tradeId);
            }));

            if(empty($savedTexts)){
                // セッションに保存されている本文がリクエストで送信されたもののみだった場合
                session()->forget('savedTexts');
            }else{
                // 複数件ある場合
                session()->put('savedTexts' , $savedTexts);
            }
        }

        DB::transaction(function () use ($request,$userId,$tradeId) {

            if($request->hasFile('img_url')){
                // アップロードされた画像ファイルがある場合
                $img = $request->file('img_url');

                $imgName = Str::uuid() . $img->getClientOriginalName();

                $img->storeAs('public/img/chat_img',$imgName);
            }

            // メッセージ（と画像ファイルのパス）をDBに登録する
            Chat::create([
                'user_id' => $userId,
                'trade_id' => $tradeId,
                'message' => $request->message,
                'img_url' => isset($imgName) ? 'chat_img/' . $imgName : null,
                'unread_flag' => 0
            ]);

        });

        return redirect()->route('chat.index', ['trade_id' => $tradeId]);
    }
}
