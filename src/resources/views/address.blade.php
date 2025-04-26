@extends('layouts.app')

@section('title','送付先住所変更画面')

@section('css')
<link rel="stylesheet" href="{{ asset('css/address.css') }}">
@endsection

@section('content')
<h2 class="page-title">住所の変更</h2>
<form action="{{ route('address.store',['item_id' => $item_id]) }}" method="post" class="update-form">
    @csrf
    <label for="post" class="form-label">郵便番号</label>
    <input type="text" name="post" class="form-input" id="post" value="">
    <label for="address" class="form-label">住所</label>
    <input type="text" name="address" class="form-input" id="address">
    <label for="building" class="form-label">建物名</label>
    <input type="text" name="building" class="form-input" id="building">
    <button type="submit" class="update-btn">更新する</button>
</form>
@endsection
