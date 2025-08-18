@extends('layouts.app')

@section('title')
    @if($isPurchaser)
        '取引チャット画面（購入者）'
    @else
        '取引チャット画面（出品者）'
    @endif
@endsection

@section('css')
<link rel="stylesheet" href="{{ asset('css/chat.css') }}">
@endsection

@section('content')
<div class="flex-area">
    <div class="submenu-wrap">
        <p class="list-title">その他の取引</p>
        <ul class="trade-list">
            @foreach($otherTrades as $otherTrade)
                <li class="trade-form-wrap">
                    <a href="{{ route('chat.index',['trade_id' => $otherTrade['id']]) }}" class="chat-link">{{ $otherTrade['item']['item_name'] }}</a>
                </li>
            @endforeach
        </ul>
    </div>
    <div class="chat-area">
        <div class="chat-header">
            <img src="@empty($otherUser['profile']['img_url']) {{ asset('storage/img/noImage.png') }} @else {{ asset('storage/img/'.$otherUser['profile']['img_url']) }} @endempty" alt="プロフィール画像" class="customer-img">
            <h2 class="page-title">「{{ $otherUser['name'] }}」さんとの取引画面</h2>
            <!-- ログインユーザーが購入者の場合か、ログインユーザーが出品者の場合は購入者が既に取引評価済みの場合に取引評価リンクを表示する -->
            @if($showEvaluationLink)
                <a href="#{{ $tradeId }}" class="modal-link">取引を完了する</a>
            @endif
            <!-- モーダル -->
            <div class="modal" id="{{ $tradeId }}">
                <div class="modal-content">
                    <p class="modal-title">取引が完了しました。</p>
                    <form action="{{ route('evaluation.send',['trade_id' => $tradeId]) }}" method="post" class="evaluation-form">
                        @csrf
                        <input type="hidden" name="isPurchaser" value="{{ $isPurchaser }}">
                        <p class="text">今回の取引相手はどうでしたか？</p>
                        <div class="stars">
                            @for($i = 5; $i >= 1; $i--)
                                <input type="radio" name="evaluations" id="star{{ $i }}" value="{{ $i }}" class="input-radio">
                                <label for="star{{ $i }}">
                                    <img src="{{ asset('storage/img/star.svg') }}" alt="ユーザー評価" class="star">
                                </label>
                            @endfor
                        </div>
                        <button type="submit" class="evaluation-submit">送信する</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="trade-item-info">
            <div class="item-img-wrap">
                <img src="{{ asset('storage/img/'.$item['img_url']) }}" alt="商品画像" class="item-img">
            </div>
            <div class="item-info">
                <p class="name">{{ $item['item_name'] }}</p>
                <p class="price">¥{{ number_format($item['price']) }} (税込)</p>
            </div>
        </div>
        <div class="message-area">
            @foreach($chats as $chat)
                @if($chat['user_id'] == Auth::id())
                <!-- ログインユーザーが送信したメッセージの場合 -->
                <div class="user-message  login-user-message">
                        <div class="user-info-wrap">
                            <img src="@empty($loginUser['profile']['img_url']) {{ asset('storage/img/noImage.png') }} @else {{ asset('storage/img/'.$loginUser['profile']['img_url']) }} @endempty" alt="プロフィール画像" class="user-img">
                            <p class="user-name">{{ $loginUser['name'] }}</p>
                        </div>
                        <form action="{{ route('chat.edit',['chat_id' => $chat['id']]) }}" method="post" class="edit-form">
                            @method('PATCH')
                            @csrf
                            <textarea name="message" class="message" rows="1">{{ $chat['message'] }}</textarea>
                            <!-- 以下の画像を表示する箇所は不要だったら削除する -->
                            @if(!is_null($chat['img_url']))
                                <img src="{{ asset('storage/img/'.$chat['img_url']) }}" alt="送信された画像">
                            @endif
                            <button type="submit" class="submit-btn edit-btn">編集</button>
                        </form>
                        <form action="{{ route('chat.delete',['chat_id' => $chat['id']]) }}" method="post">
                            @method('DELETE')
                            @csrf
                            <button type="submit" class="submit-btn">削除</button>
                        </form>
                    </div>
                @else
                <!-- ログインユーザー以外のユーザーが送信したメッセージの場合 -->
                    <div class="user-message">
                        <div class="user-info-wrap">
                            <img src="@empty($otherUser['profile']['img_url']) {{ asset('storage/img/noImage.png') }} @else {{ asset('storage/img/'.$otherUser['profile']['img_url']) }} @endempty" alt="プロフィール画像" class="user-img">
                            <p class="user-name">{{ $otherUser['name'] }}</p>
                        </div>
                        <div class="message">{{ $chat['message'] }}</div>
                        <!-- 以下の画像を表示する箇所は不要だったら削除する -->
                        @if(!is_null($chat['img_url']))
                            <img src="{{ asset('storage/img/'.$chat['img_url']) }}" alt="送信された画像">
                        @endif
                    </div>
                @endif
            @endforeach
            <div class="message-form-wrap">
                <form action="{{ route('chat.send',['trade_id'=>$tradeId]) }}" method="post" class="message-form" enctype="multipart/form-data">
                    @csrf
                    @livewire('input-to-session',['tradeId' => $tradeId])
                    <label for="img-file" class="img-label">画像を追加</label>
                    <input type="file" name="img_url" id="img-file" accept=".jpeg,.png" class="input-img">
                    <button type="submit" class="message-submit">
                        <img src="{{ asset('storage/img/cmtSubmit.svg') }}" alt="コメント送信ボタンの画像" class="submit-img">
                    </button>
                </form>
                @error('message')
                    <p class="error-msg">{{ $message }}</p>
                @enderror
                @error('img_url')
                    <p class="error-msg">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>
</div>
@endsection