<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddressRequest;
use App\Models\Item;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PurchaseController extends Controller
{
    public function index($item_id,Request $request){
        $user_id = Auth::id();
        $itemInfo = Item::find($item_id);

        // 表示に必要な商品情報の取得
        $item = $itemInfo->only([
            'id',
            'item_name',
            'price',
            'img_url'
        ]);

        if(is_null(session('address'))){
            // 初期表示
            $user = User::with('profile')->find($user_id)->profile;
        }else{
            // 送付先変更が行われた場合
            // セッションに保存した送付先住所を取得
            $address = session('address');

            $user = [
                'post' => $address['post'],
                'address' => $address['address'],
                'building' => $address['building']
            ];

            // route('stripe.checkout')実行時にaddressをセッションから削除する
        }

        return view('purchase',compact('item','user'));
    }

    public function selectPayment(Request $request){
        $payment = $request->payment;
        return redirect()->back()->with('payment',$payment);
    }

    public function edit($item_id){
        return view('address', compact('item_id'));
    }

    public function storeTempAddress($item_id,AddressRequest $request){
        $address = [
            'post' => $request->post,
            'address' => $request->address,
            'building' => $request->building
        ];

        session()->put('address', $address);

        return redirect('/purchase/:'.$item_id);

    }

}
