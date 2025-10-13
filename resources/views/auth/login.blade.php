<?php
$favicon = 'backEnd/img/favicon.png';
$copyright_text = 'Copyright © ' . date('Y') . ' All rights reserved | This Application is made with by MetroCoding';
$logo = 'uploads/settings/logo.png';
$css = 'background: url(' . url('backEnd/img/login-bg.png') . ')  no-repeat center; background-size: cover; ';

?>
<!doctype html>
<html lang="en">

<head>
    <!-- All Meta Tags -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="icon" href="{{ asset($favicon) }}" type="image/png" />
    <title>@lang('auth.login')</title>
    <meta name="_token" content="{!! csrf_token() !!}" />

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{ asset('theme/edulia/css/bootstrap.min.css') }}">

    <!-- Fontawesome CSS -->
    <link rel="stylesheet" href="{{ asset('theme/edulia/css/fontawesome.all.min.css') }}">

    <link rel="stylesheet" href="{{ asset('theme/edulia/css/style.css') }}">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.2);
            padding: 50px 40px;
            width: 100%;
            max-width: 480px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            position: relative;
            overflow: hidden;
        }

        .login-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #667eea, #764ba2, #667eea);
            background-size: 200% 100%;
            animation: shimmer 3s infinite linear;
        }

        @keyframes shimmer {
            0% { background-position: -200% 0; }
            100% { background-position: 200% 0; }
        }

        .logo-container {
            text-align: center;
            margin-bottom: 40px;
        }

        .logo-container img {
            max-width: 180px;
            height: auto;
            filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.1));
        }

        .login-title {
            text-align: center;
            margin-bottom: 35px;
        }

        .login-title h4 {
            color: #2d3748;
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 8px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .login-title p {
            color: #718096;
            font-size: 14px;
            font-weight: 400;
        }

        .input-group {
            position: relative;
            margin-bottom: 25px;
        }

        .input-icon {
            position: absolute;
            left: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: #a0aec0;
            z-index: 2;
            font-size: 18px;
            transition: all 0.3s ease;
        }

        .form-control-custom {
            width: 100%;
            padding: 16px 20px 16px 55px;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 400;
            color: #2d3748;
            background: #ffffff;
            transition: all 0.3s ease;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        }

        .form-control-custom:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.1);
            transform: translateY(-2px);
        }

        .form-control-custom:focus + .input-icon {
            color: #667eea;
            transform: translateY(-50%) scale(1.1);
        }

        .error-message {
            display: block;
            color: #e53e3e;
            font-size: 13px;
            font-weight: 500;
            margin-top: 8px;
            padding-left: 10px;
            border-left: 3px solid #e53e3e;
            background: #fed7d7;
            padding: 8px 12px;
            border-radius: 6px;
        }

        .form-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            flex-wrap: wrap;
            gap: 15px;
        }

        .remember-me {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .checkbox-custom {
            width: 18px;
            height: 18px;
            border: 2px solid #cbd5e0;
            border-radius: 4px;
            cursor: pointer;
            position: relative;
            transition: all 0.3s ease;
        }

        .checkbox-custom:checked {
            background: #667eea;
            border-color: #667eea;
        }

        .checkbox-custom:checked::after {
            content: '✓';
            position: absolute;
            color: white;
            font-size: 12px;
            font-weight: bold;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        .remember-text {
            color: #4a5568;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
        }

        .forgot-password {
            color: #667eea;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.3s ease;
            padding: 8px 16px;
            border-radius: 8px;
            background: rgba(102, 126, 234, 0.1);
        }

        .forgot-password:hover {
            color: #5a67d8;
            background: rgba(102, 126, 234, 0.2);
            text-decoration: none;
            transform: translateY(-1px);
        }

        .login-button {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
            position: relative;
            overflow: hidden;
        }

        .login-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
        }

        .login-button:active {
            transform: translateY(0);
        }

        .login-button::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }

        .login-button:hover::before {
            left: 100%;
        }

        .copyright {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
            color: #a0aec0;
            font-size: 12px;
            font-weight: 400;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .login-container {
                margin: 20px;
                padding: 40px 30px;
                max-width: 90%;
            }

            .login-title h4 {
                font-size: 24px;
            }

            .form-options {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }

            .forgot-password {
                align-self: flex-end;
            }
        }

        @media (max-width: 480px) {
            .login-container {
                padding: 30px 20px;
                margin: 15px;
            }

            .logo-container img {
                max-width: 140px;
            }

            .login-title h4 {
                font-size: 22px;
            }

            .form-control-custom {
                padding: 14px 16px 14px 50px;
                font-size: 14px;
            }

            .input-icon {
                font-size: 16px;
                left: 16px;
            }
        }

        /* Loading animation */
        .loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255,255,255,.3);
            border-radius: 50%;
            border-top-color: #fff;
            animation: spin 1s ease-in-out infinite;
            margin-right: 10px;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    </style>
</head>

<body>
    <div class="login-container">
        <!-- Logo Section -->
        <div class="logo-container">
            <img src="{{ asset($logo) }}" alt="Company Logo">
        </div>

        <!-- Title Section -->
        <div class="login-title">
            <h4>Welcome Back</h4>
            <p>Enter your credentials to access your account</p>
        </div>

        <!-- Login Form -->
        <form action="{{ route('login.post') }}" method="POST" id="loginForm">
            @csrf
            <input type="hidden" name="username" id="username-hidden">

            <!-- Email Input -->
            <div class="input-group">
                <i class="fal fa-envelope input-icon"></i>
                <input type="text" 
                       name="email" 
                       class="form-control-custom" 
                       placeholder="Enter your email address" 
                       value="{{ old('email') }}"
                       required>
            </div>
            @if ($errors->has('email'))
                <span class="error-message" role="alert">
                    <i class="fas fa-exclamation-circle"></i> {{ $errors->first('email') }}
                </span>
            @endif

            <!-- Password Input -->
            <div class="input-group">
                <i class="fal fa-lock-alt input-icon"></i>
                <input type="password" 
                       name="password" 
                       class="form-control-custom" 
                       placeholder="Enter your password" 
                       required>
            </div>
            @if ($errors->has('password'))
                <span class="error-message" role="alert">
                    <i class="fas fa-exclamation-circle"></i> {{ $errors->first('password') }}
                </span>
            @endif

            <!-- Form Options -->
            <div class="form-options">
                <div class="remember-me">
                    <input type="checkbox" 
                           class="checkbox-custom" 
                           name="remember" 
                           id="rememberMe" 
                           {{ old('remember') ? 'checked' : '' }}>
                    <label for="rememberMe" class="remember-text">Remember me</label>
                </div>
                <a href="{{ route('recoveryPassword') }}" class="forgot-password">
                    <i class="fas fa-key"></i> Forgot Password?
                </a>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="login-button" id="loginButton">
                <span id="buttonText">Sign In</span>
                <span id="buttonLoading" style="display: none;">
                    <span class="loading"></span>Signing In...
                </span>
            </button>
        </form>

        <!-- Copyright -->
        <div class="copyright">
            {{ $copyright_text }}
        </div>
    </div>

    <!-- jQuery JS -->
    <script src="{{ asset('theme/edulia/js/jquery.min.js') }}"></script>

    <!-- Toastr CSS & JS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <!-- Main Script JS -->
    <script src="{{ asset('theme/edulia/js/script.js') }}"></script>
    <script src="{{ asset('backEnd/') }}/js/login.js"></script>

    <script type="text/javascript">
        $(document).ready(function() {
            // Auto-fill username from email
            $("input[name='email']").keyup(function() {
                $("#username-hidden").val($(this).val());
            });

            // Form submission loading state
            $("#loginForm").on('submit', function() {
                $("#buttonText").hide();
                $("#buttonLoading").show();
                $("#loginButton").prop('disabled', true);
            });

            // Add focus effects
            $(".form-control-custom").on('focus', function() {
                $(this).parent().find('.input-icon').css('color', '#667eea');
            }).on('blur', function() {
                $(this).parent().find('.input-icon').css('color', '#a0aec0');
            });

            // Checkbox styling
            $(".checkbox-custom").on('change', function() {
                if ($(this).is(':checked')) {
                    $(this).css({
                        'background': '#667eea',
                        'border-color': '#667eea'
                    });
                } else {
                    $(this).css({
                        'background': 'transparent',
                        'border-color': '#cbd5e0'
                    });
                }
            });

            // Initialize checkbox state
            $(".checkbox-custom").each(function() {
                if ($(this).is(':checked')) {
                    $(this).css({
                        'background': '#667eea',
                        'border-color': '#667eea'
                    });
                }
            });
        });

        // Toastr notifications
        @if (Session::has('toast_message'))
            toastr.{{ Session::get('toast_message')['type'] }}('{{ Session::get('toast_message')['message'] }}');
        @endif
    </script>
</body>
</html>