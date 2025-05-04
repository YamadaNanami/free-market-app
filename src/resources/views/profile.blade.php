@extends('layouts.app')

@section('title','プロフィール設定画面')

@section('css')
<link rel="stylesheet" href="{{ asset('css/profile.css') }}">
@endsection

@section('content')
<h2 class="page-title">プロフィール設定</h2>
@if($hasProfile)
<form action="/mypage/profile" method="post" class="edit-form" enctype="multipart/form-data">
    @method('PATCH')
@else
<form action="/mypage/profile" method="post" class="edit-form" enctype="multipart/form-data">
@endif
    @csrf
    <div class="update-img">
        <img src="@if(is_null($profile['profile']) || is_null($profile['profile']['img_url']))''@else{{ asset('storage/img/'.$profile['profile']['img_url']) }}@endif" alt="プロフィール画像" class="img-area">
        <label for="img-file" class="img-label">
            画像を選択する
            <input type="file" name="img-file" id="img-file" accept=".jpeg,.png" class="input-img">
        </label>
    </div>
    <label for="name" class="form-label">ユーザー名</label>
    <input type="text" name="name" class="form-input" id="name" value="{{ old('name',$profile['name']) }}">
    <label for="post" class="form-label">郵便番号</label>
    <input type="text" name="post" class="form-input" id="post" value="{{ old('post',$profile['profile']['post'] ?? '') }}">
    <label for="address" class="form-label">住所</label>
    <input type="text" name="address" class="form-input" id="address" value="{{ old('address',$profile['profile']['address'] ?? '') }}">
    <label for="building" class="form-label">建物名</label>
    <input type="text" name="building" class="form-input" id="building" value="{{ old('building',$profile['profile']['building'] ?? '') }}">
    <button type="submit" class="update-btn">更新する</button>
</form>
@endsection