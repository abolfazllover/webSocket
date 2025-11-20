<!DOCTYPE html>
<html lang="fa" style="height: 100%">
<head>
    <meta charset="UTF-8">
    <title>login page</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @include('meta')
</head>
<body style="height: 100%">

<div class="ui container" style="height: 100%">
    <div class="ui grid two column middle aligned center aligned" style="height: 100%">
        <div class="column">
            <div class="ui padded secondary segment">
                <h2>لاگین به حساب</h2>
                <form action="{{route('login_post')}}" method="post" class="ui form">
                    @csrf
                    <div class="field">
                        <label>ایمیل</label>
                        <input name="email">
                    </div>
                    <div class="field">
                        <label>رمز عبور</label>
                        <input name="password">
                    </div>
                    <div class="field">
                        <button class="ui fluid primary button">ورود</button>
                    </div>
                    <p>حساب کاربری ندارید؟
                    <a href="{{route('register')}}">ثبت نام</a>
                    </p>
                </form>
            </div>
        </div>
    </div>
</div>

@include('config')
</body>
</html>
