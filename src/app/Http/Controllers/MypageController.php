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

        if (!$page) {//初期表示
            // ユーザ情報を取得
            $user = User::with('profile')->find($id);

            // 出品した商品を取得
            $items = Item::where('user_id', $id)->get();

            return view('mypage', compact('user', 'items'));
        }elseif($page == 'sell'){//出品した商品リンク押下時
            // ユーザ情報を取得
            $user = User::find($id);

            // 出品した商品を取得
            $items = Item::where('user_id', $id)->get();

            return view('mypage', compact('page','user', 'items'));
        }elseif($page == 'buy'){//購入した商品リンク押下時
            // ユーザ情報を取得
            $user = User::find($id);

            // ユーザーが購入した商品の情報を取得する
            $items = User::find($id)->items;

            return view('mypage', compact('page','user', 'items'));
        }
    }
}
