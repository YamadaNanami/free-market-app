@extends('layouts.app')

@section('title','ログイン画面')

@section('css')
<link rel="stylesheet" href="{{ asset('../css/login.css') }}">
@endsection

@section('content')
<h2 class="page-title">ログイン</h2>
<form action="/login" method="post" class="login-form">
    <label for="email" class="form-label">メールアドレス</label>
    <input type="email" name="email" class="form-input" id="email">
    <label for="password" class="form-label">パスワード</label>
    <input type="password" name="password" class="form-input" id="password">
    <button type="submit" class="login-btn">ログインする</button>
</form>
<div class="register-link-wrap">
    <a href="/register" class="register-link">会員登録はこちら</a>
</div>
@endsection