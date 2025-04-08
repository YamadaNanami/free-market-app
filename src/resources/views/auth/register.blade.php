@extends('layouts.app')

@section('title','会員登録画面')

@section('css')
<link rel="stylesheet" href="{{ asset('../css/register.css') }}">
@endsection

@section('content')
<h2 class="page-title">会員登録</h2>
<form action="/register" method="post" class="register-form" novalidate>
    @csrf
    <label for="name" class="form-label">ユーザ名</label>
    <input type="text" name="name" class="form-input" id="name" value="{{ old('name') }}">
    @error('name')
    <p class="error-msg">{{ $message }}</p>
    @enderror
    <label for="email" class="form-label">メールアドレス</label>
    <input type="email" name="email" class="form-input" id="email" value="{{ old('email') }}">
    @error('email')
    <p class="error-msg">{{ $message }}</p>
    @enderror
    <label for="password" class="form-label">パスワード</label>
    <input type="password" name="password" class="form-input" id="password">
    @if($errors->has('password'))
        @foreach($errors->get('password') as $message)
            @if(!str_contains($message,'一致しません'))
            <p class="error-msg">{{ $message }}</p>
            @endif
        @endforeach
    @endif
    <label for="conf-password" class="form-label">確認用パスワード</label>
    <input type="password" name="password_confirmation" class="form-input" id="conf-password">
    @if($errors->has('password'))
        @foreach($errors->get('password') as $message)
            @if(str_contains($message,'一致しません'))
            <p class="error-msg last-input-msg">{{ $message }}</p>
            @endif
        @endforeach
    @endif
    <button type="submit" class="register-btn">登録する</button>
</form>
<div class="login-link-wrap">
    <a href="/login" class="login-link">ログインはこちら</a>
</div>

@endsection
