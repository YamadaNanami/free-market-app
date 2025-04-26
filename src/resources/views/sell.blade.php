@extends('layouts.app')

@section('title','商品出品画面')

@section('css')
<link rel="stylesheet" href="{{ asset('css/sell.css') }}">
@endsection

@section('content')
<h2 class="page-title">商品の出品</h2>
<form action="/sell" method="post" class="sell-form">
    @csrf
    <section class="form-sec">
        <h4 class="sub-title">商品画像</h4>
        <img src="" alt="商品画像" class="img-area">
        <label for="img-file" class="img-label">
            画像を選択する
            <input type="file" name="img_url" id="img-file" accept=".jpeg,.png" class="input-img">
        </label>
    </section>
    <section class="form-sec">
        <h3 class="sec-title">商品の詳細</h3>
        <h4 class="sub-title">カテゴリー</h4>
        <div class="flex-area">
            @foreach($categories as $category)
            <input type="checkbox" name="categories[]" id="{{$category['id']}}" class="form-checkbox" value="{{ $category['id'] }}" multiple {{ in_array($category['id'], old('categories', [])) ? 'checked' : '' }}>
            <label for="{{$category['id']}}" class="checkbox-label">{{ $category['category'] }}</label>
            @endforeach
        </div>
        <h4 class="sub-title">商品の状態</h4>
        <select name="condition" class="form-input">
            <option value="" disabled selected>選択してください</option>
            <option value="1" {{ old('condition')== '1'?'selected':'' }}>良好</option>
            <option value="2" {{ old('condition')== '2'?'selected':'' }}>目立った傷や汚れなし</option>
            <option value="3" {{ old('condition')== '3'?'selected':'' }}>やや傷や汚れあり</option>
            <option value="4" {{ old('condition')== '4'?'selected':'' }}>状態が悪い</option>
        </select>
    </section>
    <section class="form-sec">
        <h3 class="sec-title">商品名と説明</h3>
        <h4 class="sub-title">商品名</h4>
        <input type="text" name="item_name" class="form-input" value="{{ old('item_name') }}">
        <h4 class="sub-title">ブランド名</h4>
        <input type="text" name="brand_name" class="form-input" value={{ old('brand_name') }}>
        <h4 class="sub-title">商品の説明</h4>
        <textarea name="description" class="form-input" rows="5">{{ old('description') }}</textarea>
        <h4 class="sub-title">販売価格</h4>
        <div class="price-input">
            <span class="price-icon">¥</span>
            <input type="number" name="price" class="form-input" value="{{ old('price') }}">
        </div>
    </section>
    <button type="submit" class="sell-form-btn">出品する</button>
</form>

@endsection
