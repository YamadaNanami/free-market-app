@extends('layouts.app')

@section('title','会員登録画面')

@section('css')
<link rel="stylesheet" href="{{ asset('../css/register.css') }}">
@endsection

@section('content')
<h2 class="page-title">会員登録</h2>
<form action="/register" method="post" class="register-form">
    <label for="name" class="form-label">ユーザ名</label>
    <input type="text" name="name" class="form-input" id="name">
    <label for="email" class="form-label">メールアドレス</label>
    <input type="email" name="email" class="form-input" id="email">
    <label for="password" class="form-label">パスワード</label>
    <input type="password" name="password" class="form-input" id="password">
    <label for="conf-password" class="form-label">確認用パスワード</label>
    <input type="password" name="conf-password" class="form-input" id="conf-password">
    <button type="submit" class="register-btn">登録する</button>
</form>
<div class="login-link-wrap">
    <a href="/login" class="login-link">ログインはこちら</a>
</div>

@endsection
