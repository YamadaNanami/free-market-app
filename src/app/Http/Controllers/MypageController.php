<?php

namespace App\Http\Controllers;

use App\Models\Evaluation;
use App\Models\Trade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
                $items = [];

                foreach($trades->get() as $trade){
                    $items[] = Item::find($trade->item_id);
                }

                foreach($items as $item){
                    foreach($totalUnread as $unread){
                        if($item->id == $unread->item_id){
                            $item['tradeId'] = $unread->id;
                            $item['notice'] = $unread->chats_count;
                        }
                    }
                }

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
        // ログインユーザーへの評価を全取得する
        $evaluations = Evaluation::where('user_id', $userId)->get('evaluation');

        // 評価の値を配列に入れる
        $counts = [];
        foreach($evaluations as $evaluation){
            $counts[] = $evaluation->evaluation;
        }

        return round(collect($counts)->avg());
    }
}
