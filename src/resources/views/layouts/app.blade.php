<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/common.css') }}">
    @yield('css')
</head>

<body>
    <header class="header">
        <div class="header-wrap">
            <h1 class="page-logo">
                <a href="/" class="top-link">
                    <img src="{{ asset('storage/img/logo.svg') }}" alt="サイトのロゴ画像" class="logo-img">
                </a>
            </h1>
            <form action="/search" method="get" class="search-form">
                <input type="search" name="keyword" class="search-form-input" placeholder="なにをお探しですか？" value="{{ old('keyword',session()->get('keyword')) }}">
            </form>
            @if(Auth::check())
            <form action="/logout" method="post" class="form-wrap">
                @csrf
                <input type="submit" value="ログアウト" class="fortify-submit">
            </form>
            @else
            <form action="/login" method="post" class="form-wrap">
                @csrf
                <input type="submit" value="ログイン" class="fortify-submit">
            </form>
            @endif
            <a href="{{ route('mypage.index') }}" class="mypage-link">マイページ</a>
            <a href="/sell" class="sell-btn">出品</a>
        </div>
    </header>

    <main class="page-content">
        @yield('content')
    </main>
</body>

</html>