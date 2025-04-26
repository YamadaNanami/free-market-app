<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\AddressRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;
use App\Models\User;

class PurchaseController extends Controller
{
    public function index($item_id){
        $user_id = Auth::id();
        $itemInfo = Item::find($item_id);

        // 表示に必要な商品情報の取得
        $item = $itemInfo->only([
            'id',
            'item_name',
            'price',
            'img_url'
        ]);

        // 送付先変更が登録されていたらレコードを取得する
        $address = $itemInfo->address()->where('user_id', $user_id)->first();

        if($address){
            // 送付先変更が行われた場合
            $user = [
                'post' => $address->pivot->post,
                'address' => $address->pivot->address,
                'building' => $address->pivot->building
            ];
        }else{
            // 初期表示
            $user = User::with('profile')->find($user_id)->profile;
        }

        return view('purchase',compact('item','user'));
    }

    public function selectPayment(Request $request){
        // （これから対応する）画面遷移、異なる商品の購入画面が表示された際にここで追加したsessionを削除するようにする
        session()->put('payment', $request->payment);
        return redirect()->back();
    }

    public function edit($item_id){
        return view('address', compact('item_id'));
    }

    public function store($item_id,AddressRequest $request){
        $address = [
            'post' => $request->post,
            'address' => $request->address,
            'building' => $request->building
        ];

        $user = User::find(Auth::id());
        // user_idとitem_idの組み合わせで重複があった場合は上書きする
        $user->address()->syncWithoutDetaching([$item_id => $address]);

        return redirect('/purchase/:'.$item_id);

    }
}
