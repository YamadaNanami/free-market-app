@extends('layouts.app')

@section('title','プロフィール設定画面')

@section('css')
<link rel="stylesheet" href="{{ asset('css/profile.css') }}">
@endsection

@section('content')
<h2 class="page-title">プロフィール設定</h2>
<form action="/mypage/profile" method="post" class="update-form">
    @csrf
    <div class="update-img">
        <img src="" alt="プロフィール画像" class="img-area">
        <label for="img-file" class="img-label">
            画像を選択する
            <input type="file" name="img-file" id="img-file" accept="image/*" class="input-img">
        </label>
    </div>
    <label for="name" class="form-label">ユーザー名</label>
    <input type="text" name="name" class="form-input" id="name">
    <label for="post-code" class="form-label">郵便番号</label>
    <input type="text" name="post-code" class="form-input" id="post-code">
    <label for="add" class="form-label">住所</label>
    <input type="text" name="add" class="form-input" id="add">
    <label for="building" class="form-label">建物名</label>
    <input type="text" name="building" class="form-input" id="building">
    <button type="submit" class="update-btn">更新する</button>
</form>
@endsection