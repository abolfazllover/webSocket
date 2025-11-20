<!DOCTYPE html>
<html lang="fa" style="height: 100%">
<head>
    <meta charset="UTF-8">
    <title>Register page</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @include('meta')
</head>
<body style="height: 100%">

<div class="ui container" style="height: 100%">
    <div class="ui grid two column middle aligned center aligned" style="height: 100%">
        <div class="column">
            <div class="ui padded secondary segment">
                <h2>ثبت نام | ایجاد حساب</h2>
                <form method="post" action="{{route('register_post')}}" class="ui form">
                    @csrf
                    <div class="field">
                        <label>نام </label>
                        <input name="name">
                    </div>
                    <div class="field">
                        <label>email </label>
                        <input name="email">
                    </div>
                    <div class="field">
                        <label>نام کاربری</label>
                        <input name="username">
                    </div>
                    <div class="field">
                        <label>رمز عبور</label>
                        <input name="password">
                    </div>
                    <div class="field">
                        <button class="ui fluid primary button">ایجاد</button>
                    </div>
                    <p>حساب کاربری دارید؟
                        <a href="{{route('login')}}">ورود</a>
                    </p>
                </form>
            </div>
        </div>
    </div>
</div>

@include('config')
</body>
</html>
