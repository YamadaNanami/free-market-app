<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddressRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Profile;

class ProfileController extends Controller
{
    public function index(){
        $id = Auth::id();
        $profile = User::with('profile')->find($id);
        if(!$profile){
            return view('profile');
        };
        return view('profile', compact('profile'));
    }

    public function update(AddressRequest $request){
        // 対象のレコードを取得する
        $profile = User::with('profile')->find(Auth::id());

        // ユーザー名の更新処理
        $profile->name = $request->name;
        $profile->save(); // ユーザー名を保存

        // プロフィール情報の更新処理
        if($profile->profile){
            $profile->profile->post = $request->post;
            $profile->profile->address = $request->address;
            $profile->profile->building = $request->building;
            $profile->profile->save(); // プロフィール情報を保存
        }

        return redirect()->route('profile.index');
    }
}
