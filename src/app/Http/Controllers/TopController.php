<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use App\Models\Item;
use App\Models\User;

class TopController extends Controller
{
    public function index(Request $request){
        $currentTab = $request->query('page', 'default');
        // if($currentTab == null){
        //     $url = URL::current().'?'.http_build_query($request->except('parameter'));
        // }else{
        //     $url = URL::current();
        // }
        // dd($currentTab);

        // 商品検索
        $keyword = session()->get('searchKeyword');

        $query = Item::query();
        $query = Item::itemsSearch($keyword);

        if(Auth::check()){
            // ログイン済の場合
            $user_id = Auth::id();

            if($currentTab == 'mylist'){
                // いいねした商品だけを取得
                $user = User::find($user_id);
                $items = $user->like;
                // 応用なので後回し（検索状態の維持）
            }else{
                // 出品した商品以外を取得
                $items = $query->whereNotIn('user_id', [$user_id])->get();
            }

            // ユーザーが購入した商品を取得
            $purchaseLists = User::find($user_id)->items;

            foreach($items as $item){
                foreach($purchaseLists as $purchase){
                    if($item['id'] == $purchase->pivot->item_id){
                        // ユーザーが購入済の商品にフラグを設定
                        $item['soldOutItemExists'] = true;
                    }
                }
            }
        }else{
            // 未ログインの場合
            $items = $query->get();
        }

        return view('top', compact('items','currentTab'));
    }

    public function saveKeyword(Request $request){
        // 検索条件をセッションで保持（タブ切替時にも表示できるように）
        $keyword = $request->keyword;
        session()->put('searchKeyword', $keyword);

        return redirect()->route('top.index');
    }
}
