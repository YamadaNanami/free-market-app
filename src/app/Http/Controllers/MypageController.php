<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Item;
use App\Models\Purchase;

class MypageController extends Controller
{
    public function index(Request $request){
        $page = $request->page;
        $id = Auth::id();

        if (!$page) {
            //初期表示
            $user = User::with('profile')->find($id);
            $items = Item::where('user_id', $id)->get();

            return view('mypage', compact('user', 'items'));
        }elseif($page == 'sell'){
            //出品した商品リンク押下時
            $user = User::find($id);
            $items = Item::where('user_id', $id)->get();

            return view('mypage', compact('page','user', 'items'));
        }elseif($page == 'buy'){
            //購入した商品リンク押下時
            $user = User::find($id);
            // (未対応)Purchaseを中間テーブルにして購入した商品を取得する
            // $items = Purchase::with('item')->where('user_id', $user);

            // $items = $user->items();
            // foreach ($items as $item){
            //     $name = $item->pivot->item_id;
            // }
            // dd($name);

            // return view('mypage', compact('page','user', 'items'));
        }
    }
}
