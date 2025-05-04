<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddressRequest;
use App\Models\User;
use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProfileController extends Controller
{
    public function index(){
        $id = Auth::id();
        $profile = User::with('profile')->find($id);

        if(is_null($profile['profile'])){
            // 新規登録時はhasProfileフラグをfalseにする
            $hasProfile = false;
        }else{
            // 更新時はhasProfileフラグをtrueにする
            $hasProfile = true;
        };

        return view('profile', compact('profile','hasProfile'));
    }

    public function store(AddressRequest $request){
        // dd('store',$request);
        // プロフィール情報登録用の配列を作成する
        $profile = [
            'user_id' => Auth::id(),
            // 'img_url' => $request->$img_url,
            'post' => $request->post,
            'address' => $request->address,
            'building' => $request->building,
        ];

        Profile::create($profile);

        return redirect()->route('top.index');
    }

    public function update(AddressRequest $request){
        // 対象のレコードを取得する
        $profile = User::with('profile')->find(Auth::id());

        // ユーザー名の更新処理
        $profile->name = $request->name;
        $profile->save(); // ユーザー名を保存

        if($profile->profile){
            $profile->profile->post = $request->post;
            $profile->profile->address = $request->address;
            $profile->profile->building = $request->building;
            $profile->profile->save(); // プロフィール情報を保存
        }

        return redirect()->route('profile.index');
    }
}
