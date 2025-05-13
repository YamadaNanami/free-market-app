<?php

namespace App\Http\Controllers;

use App\Http\Requests\PurchaseRequest;
use App\Models\Item;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StripeController extends Controller
{
    public function checkout($item_id,PurchaseRequest $request){
        if(session('address')){
            // 送付先住所の変更がある場合は、セッションに保存していたaddressを削除
            session()->forget('address');
        }

        $user = User::find(Auth::id());
        $item = Item::find($item_id);

        // ユーザーが選択した支払い方法を取得
        $paymentMethod = $request->payment;

        return $user->checkoutCharge($item['price'], $item['item_name'], 1,[
            'payment_method_types' => [$paymentMethod],
            'success_url' => route('stripe.success',['item_id' => $item_id, 'address' => $request->address]),
            'cancel_url' => route('purchase.index',['item_id' => $item_id])
        ]);

    }

    public function success(Request $request){
        $user = User::find(Auth::id());
        $itemId = $request->item_id;
        $address = $request->address;

        $user->items()->attach([$itemId => [
            'post' => $address['post'],
            'address' => $address['address'],
            'building' => $address['building'],
        ] ]);

        return redirect()->route('mypage.index', ['page' => 'buy']);

    }
}
