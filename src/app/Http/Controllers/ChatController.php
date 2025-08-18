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

        //取引評価を表示するか判定するためのフラグ
        // ログインユーザーが購入者、または購入者が評価済みの出品者はtrue
        $showEvaluationLink = $trade['purchaser_user_id'] == $userId
            ? true
            : Evaluation::where('trade_id',$tradeId)
                ->where('user_id',$userId)
                ->exists();

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

        return view('chat', compact('tradeId','showEvaluationLink','otherTrades' ,'item', 'loginUser','otherUser','chats'));
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

        $isSameTradeChat = Chat::where('trade_id', $tradeId)
                ->whereNotIn('id', array($chatId))
                ->exists();

        DB::transaction(function () use ($targetChat,$isSameTradeChat,$tradeId) {
            //取引チャットが削除対象のメッセージのみの場合、対象の取引チャットを削除する（データの整合性を保つため）
            if(!$isSameTradeChat){
                Trade::find($tradeId)->delete();
            }

            //対象メッセージの削除
            $targetChat->delete();

            //img_urlがある場合は、ストレージから対象の画像を削除する
            if(!is_null($targetChat->img_url)){
                Storage::disk('public')->delete('img/'.$targetChat->img_url);
            }
        });

        if(!$isSameTradeChat){
            // 取引チャットを削除した場合は、マイページに遷移する
            return redirect()->route('mypage.index');
        }

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
                // セッションに保存されているチャットが１件だった場合
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
