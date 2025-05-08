<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddressRequest;
use App\Http\Requests\ProfileRequest;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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

    public function saveTempImg(ProfileRequest $request){
        $tempImg = $request->file('image');

        // 画像名を一意にする
        $imgName = Str::uuid().$tempImg->getClientOriginalName();
        $tempImg->storeAs('public/img/temp', $imgName);

        return redirect()->route('profile.index')->with('imgName',$imgName);
    }

    public function store(AddressRequest $request){
        $imgName = session()->get('imgName') ?? '';

        // プロフィール情報登録用の配列を作成する
        $profile = [
            'user_id' => Auth::id(),
            'img_url' => $imgName ? 'profile_img/'.$imgName : null,
            'post' => $request->post,
            'address' => $request->address,
            'building' => $request->building,
        ];

        Profile::create($profile);

        if($imgName){
            Storage::move('public/img/temp/' . $imgName, 'public/img/profile_img/' . $imgName);
        }

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

            if($imgName = session('imgName')){
                Storage::move('public/img/temp/' . $imgName, 'public/img/profile_img/' . $imgName);
                $profile->profile->img_url = 'profile_img/' . $imgName;
            }

            $profile->profile->save(); // プロフィール情報を保存
        }

        return redirect()->route('profile.index');
    }
}
