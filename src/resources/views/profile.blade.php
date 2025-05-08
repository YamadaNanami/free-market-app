@extends('layouts.app')

@section('title','プロフィール設定画面')

@section('css')
<link rel="stylesheet" href="{{ asset('css/profile.css') }}">
@endsection

@section('content')
<h2 class="page-title">プロフィール設定</h2>
<form action="/mypage/profile/img" method="post" enctype="multipart/form-data" class="img-form">
    @csrf
    @if(session('imgName'))
    <img src="{{ asset('storage/img/temp/'.session('imgName')) }}" alt="プロフィール画像" class="img-area">
    @else
    <img src="@if(is_null($profile['profile']) || is_null($profile['profile']['img_url'])) {{ asset('storage/img/noImage.png') }} @else{{ asset('storage/img/'.$profile['profile']['img_url']) }}@endif" alt="プロフィール画像" class="img-area">
    @endif
    <label for="image" class="img-label">
        画像を選択する
        <input type="file" onchange="submit(this.form)" name="image" id="image" accept=".jpeg,.png" class="input-img">
    </label>
</form>
@if($hasProfile == 1)
<form action="/mypage/profile" method="post" enctype="multipart/form-data" class="edit-form">
    @method('PATCH')
@else
<form action="/mypage/profile" method="post" class="edit-form" enctype="multipart/form-data">
@endif
    @csrf
    @if(session('imgName'))
    {{ session()->flash('imgName',session('imgName'))  }}
    @endif
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