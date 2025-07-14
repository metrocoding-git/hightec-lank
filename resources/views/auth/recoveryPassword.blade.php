<?php
$favicon = 'backEnd/img/favicon.png';
$copyright_text = 'Copyright © ' . date('Y') . ' All rights reserved | This Application is made with by MetroCoding';
$logo = 'uploads/settings/logo.png';
$css = 'background: url(' . url('backEnd/img/login-bg.png') . ') no-repeat center; background-size: cover;';
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="icon" href="{{ asset($favicon) }}" type="image/png" />
    <title>@lang('auth.reset_password')</title>
    <meta name="_token" content="{!! csrf_token() !!}" />

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{ asset('theme/edulia/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('theme/edulia/css/fontawesome.all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('theme/edulia/css/style.css') }}">

    <style>
        .login {
            min-height: 100vh;
            padding: 50px 0px;
        }

        .login_wrapper {
            width: 550px;
            background: #fff;
            padding: 30px;
            margin: auto;
        }

        .text-danger.text-left {
            font-size: 14px;
        }

        .input-control-input:focus {
            outline: none ;
            box-shadow: none;
        }

        .login_wrapper_logo img {
            max-width: 180px;
        }

        @media only screen and (max-width: 767px) {
            section.login {
                place-content: center;
                align-content: center;
                justify-content: center;
            }
        }
    </style>
</head>

<body>

<section class="login" style="{{ $css }}">
    <div class="login_wrapper">
        <div class="login_wrapper_logo text-center">
            <img src="{{ asset($logo) }}" alt="Logo">
        </div>

        <div class="login_wrapper_content mt-4">
            <h4 class="text-center">@lang('auth.reset_password')</h4>

            @if(session()->has('message-success'))
                <p class="text-success text-center">{{ session()->get('message-success') }}</p>
            @endif
            @if(session()->has('message-danger'))
                <p class="text-danger text-center">{{ session()->get('message-danger') }}</p>
            @endif

            <form method="POST" action="{}">
                @csrf

                <div class="input-control mb-3">
                    <label class="input-control-icon"><i class="fal fa-envelope"></i></label>
                    <input type="email" name="email" class="input-control-input form-control"
                           placeholder="@lang('auth.enter_email_address')" value="{{ old('email') }}">
                </div>

                @if ($errors->has('email'))
                    <span class="text-danger text-left mb-3 d-block">{{ $errors->first('email') }}</span>
                @endif

                <div class="input-control">
                    <input type="submit" class="input-control-input btn btn-primary w-100" value="@lang('common.submit')">
                </div>
            </form>
        </div>
    </div>
</section>


<!-- Scripts -->
<script src="{{ asset('theme/edulia/js/jquery.min.js') }}"></script>
<script src="{{ asset('theme/edulia/js/script.js') }}"></script>
<script src="{{ asset('backEnd/js/login.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script>
    @if(Session::has('toast_message'))
    toastr.{{ Session::get('toast_message')['type'] }}('{{ Session::get('toast_message')['message'] }}');
    @endif
</script>
</body>
</html>
