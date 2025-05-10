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
        $tab = $request->tab;
        $id = Auth::id();

        // ユーザ情報を取得
        $user = User::with('profile')->find($id);

        if (!$tab) {//初期表示
            // 出品した商品を取得
            $items = Item::where('user_id', $id)->get();

            return view('mypage', compact('user', 'items'));
        }elseif($tab == 'sell'){//出品した商品リンク押下時
            // 出品した商品を取得
            $items = Item::where('user_id', $id)->get();

            return view('mypage', compact('tab','user', 'items'));
        }elseif($tab == 'buy'){//購入した商品リンク押下時
            // ユーザーが購入した商品の情報を取得する
            $items = User::find($id)->items;

            return view('mypage', compact('tab','user', 'items'));
        }
    }
}
