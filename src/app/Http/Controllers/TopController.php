<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use App\Models\Item;
use App\Models\User;

class TopController extends Controller
{
    public function index(Request $request){
        $currentTab = $request->query('page', 'recommend');

        // 商品検索
        $keyword = session()->get('keyword');
        $query = Item::query();
        $query = Item::itemsSearch($keyword);

        if(Auth::check()){
            // ログイン済の場合
            $user_id = Auth::id();

            if($currentTab == 'mylist'){
                // いいねした商品を取得
                $user = User::find($user_id);
                $likeItems = $user->like;

                // itemsの配列を作成
                $items = [];

                foreach($query->get() as $searchItem){
                    // 検索条件で取得したアイテムからいいねした商品だけを取得
                    foreach($likeItems as $likeItem){
                        if($searchItem['id'] == $likeItem['id']){
                            array_push($items, $likeItem);
                        }
                    }
                }
            }else{
                // 出品した商品以外を取得
                $items = $query->whereNotIn('user_id', [$user_id])->get();
            }

        }else{
            // 未ログインの場合
            $items = $query->get();
        }

        // 売り切れの商品を取得
        $purchaseLists = Purchase::get();

        foreach($items as $item){
            foreach($purchaseLists as $purchase){
                if($item['id'] == $purchase->item_id){
                    // 売り切れの商品にフラグを設定
                    $item['soldOutItemExists'] = true;
                }
            }
        }

        return view('top', compact('items','currentTab'));
    }

    public function storeTempKeyword(Request $request){
        // 検索条件をセッションに保持する
        $keyword = $request->keyword;
        session()->put('keyword', $keyword);

        return redirect()->back();
    }
}
