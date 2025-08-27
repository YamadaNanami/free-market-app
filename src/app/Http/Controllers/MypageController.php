<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\Evaluation;
use App\Models\Trade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Item;

class MypageController extends Controller
{
    public function index(Request $request){
        $tab = $request->tab;
        $userId = Auth::id();

        // ユーザ情報を取得
        $user = User::with('profile')->find($userId);

        // ユーザーが取引中の情報を取得する
        $trades = Trade::with('chats')
            ->where('seller_user_id', $userId)
            ->orWhere('purchaser_user_id', $userId);

        // 未読メッセージの取得
        $unreadMsg = $this->getUnreadMsg($trades,$userId);
        $totalUnread = $unreadMsg['totalUnread'];
        $totalNotice = $unreadMsg['totalNotice'];

        // 評価を取得する
        $getAvgEvaluation = $this->getAvgEvaluation($userId);

        switch($tab){
            //出品した商品リンク押下時
            case 'sell':
                // 出品した商品を取得
                $items = Item::where('user_id', $userId)->get();
                break;

            //購入した商品リンク押下時
            case 'buy':
                // ユーザーが購入した商品の情報を取得する
                $items = User::find($userId)->items;
                break;

            //取引中の商品リンク押下時
            case 'trade':
                $items = $this->getTradeItems($userId);

                break;

            //初期表示
            default:
            // 出品した商品を取得
            $items = Item::where('user_id', $userId)->get();

            return view('mypage', compact('user', 'getAvgEvaluation','totalNotice','items'));
        }

        return view('mypage', compact('tab','user', 'getAvgEvaluation','totalNotice', 'items'));
    }

    private function getUnreadMsg($trades,$userId){
        // 未読メッセージの取得
        $totalUnread = $trades->withCount(['chats' => function ($query) use ($userId) {
                $query->whereNotIn('user_id',[$userId])
                    ->where('unread_flag', 0);
            }])
            ->get();

        // 未読チャットの合計を取得する
        $counts = [];
        foreach($totalUnread as $message){
            $counts[] = $message->chats_count;
        }

        return [
            'totalNotice' => array_sum($counts),
            'totalUnread' => $totalUnread
        ];
    }

    private function getAvgEvaluation($userId){
        $evaluationSql = Evaluation::where('user_id', $userId);

        if($evaluationSql->exists()){
            // ログインユーザーへの評価がある場合
            $evaluations = Evaluation::where('user_id', $userId)->get('evaluation');

            // 評価の値を配列に入れる
            $counts = [];
            foreach($evaluations as $evaluation){
                $counts[] = $evaluation->evaluation;
            }

            return round(collect($counts)->avg());
        }else{
            // ログインユーザーへの評価がない場合は、nullを返す
            return null;
        }
    }

    public function getTradeItems($userId){
        $trades = Trade::with('item')
            ->withCount([
                // 未読チャット数をカウントする(デフォルトだとchats_countになるところをunread_countに変更)
                'chats as unread_count' => function ($query) use ($userId) {
                    $query->where('user_id', '!=', $userId)
                        ->where('unread_flag', 0);
                }
            ])
            ->addSelect([
                // 各取引チャットの未読チャットが送信された日時を取得する（未読チャットがない場合はNULL）
                // SELECT MAX(created_at) FROM chats WHERE 'trade_id' = 'trades.id' AND 'user_id' != $userId AND 'unread_flag' = 0;
                'latest_unread_at' => Chat::select(DB::raw('MAX(created_at)'))
                    ->whereColumn('trade_id', 'trades.id')
                    ->where('user_id', '!=', $userId)
                    ->where('unread_flag', 0),

                // すべてのチャットの最新日時
                // SELECT MAX(created_at) FROM chats WHERE 'trade_id' = 'trades.id';
                'latest_chat_at' => Chat::select(DB::raw('MAX(created_at)'))
                    ->whereColumn('trade_id','trades.id')
            ])
            ->where(function ($query) use ($userId) {
                $query->where('seller_user_id', $userId)
                    ->orWhere('purchaser_user_id', $userId);
            })
            // latest_unread_at IS NULLの結果で昇順にソートする
            // 未読あり：latest_unread_at IS NULL = 0 → 上に来る
            // 未読なし：latest_unread_at IS NULL = 1→ 下に回る
            ->orderByRaw('latest_unread_at IS NULL')
            // 未読チャットありの取引チャットの場合はlatest_unread_at、未読チャットなしの取引チャットの場合はlatest_chat_atの降順でソートする
            ->orderByDesc(DB::raw('COALESCE(latest_unread_at, latest_chat_at)'))
            ->get();

        $items = $trades->map(function ($trade) {
            $item = $trade->item;
            $item['tradeId'] = $trade->id;
            $item['notice'] = $trade->unread_count;
            return $item;
        });

        return $items;
    }
}
