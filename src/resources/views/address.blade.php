@extends('layouts.app')

@section('title','送付先住所変更画面')

@section('css')
<link rel="stylesheet" href="{{ asset('css/address.css') }}">
@endsection

@section('action','/logout')
@section('btn-name','ログアウト')

@section('content')
<h2 class="page-title">住所の変更</h2>
<form action="/purchase/address/:item_id" method="post" class="update-form">
    <label for="post-code" class="form-label">郵便番号</label>
    <input type="text" name="post-code" class="form-input" id="post-code">
    <label for="add" class="form-label">住所</label>
    <input type="text" name="add" class="form-input" id="add">
    <label for="building" class="form-label">建物名</label>
    <input type="text" name="building" class="form-input" id="building">
    <button type="submit" class="update-btn">更新する</button>
</form>
@endsection
