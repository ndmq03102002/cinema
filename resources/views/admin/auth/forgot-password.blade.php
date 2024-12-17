<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cinema Forgot Password</title>
    <link href="{{ asset('backend/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('backend/font-awesome/css/font-awesome.css') }}" rel="stylesheet">
    <link href="{{ asset('backend/css/animate.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body {
            background: url("{{ asset('backend/anh_nen.jpg') }}") no-repeat center center fixed;
            -webkit-background-size: cover;
            -moz-background-size: cover;
            -o-background-size: cover;
            background-size: cover;
            font-family: 'Montserrat', sans-serif;
            color: #fff;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .middle-box {
            max-width: 95%;
            width: auto;
            z-index: 100;
            margin: 0 auto;
            padding-top: 40px;
            background-color: rgba(0, 0, 0, 0.7) !important; /* Thêm !important */
            padding: 30px;
            border-radius: 10px;
        }

        .logo-name12 {
            color: #DAA520 !important; /* Thêm !important */
            font-size: 11vh;
            font-weight: 800;
            margin-bottom: 20px;
            font-family: 'Bebas Neue', cursive;
        }

        h3 {
            color: #fff !important; /* Thêm !important */
            margin-bottom: 20px;
        }

        p {
            color: #ccc !important; /* Thêm !important */
        }

        /* .form-control {
            background-color: #444;
            border: none;
            color: #fff;
        }

        .form-control:focus {
            background-color: #555;
            box-shadow: none;
        } */

        .btn-primary {
            background-color: #DAA520 !important; /* Thêm !important */
            border-color: #DAA520 !important; /* Thêm !important */
        }

        .btn-primary:hover, .btn-primary:focus {
            background-color: #B8860B !important; /* Thêm !important */
            border-color: #B8860B !important; /* Thêm !important */
        }

        .error-message, .error1 {
            color: #dc3545 !important; /* Thêm !important */
        }

        a {
            color: #DAA520 !important; /* Thêm !important */
        }

        a:hover {
            color: #B8860B !important; /* Thêm !important */
        }

        @import url('https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Montserrat:wght@400;700&display=swap');
    </style>
</head>
<body>
    <div class="middle-box text-center loginscreen animated fadeInDown">
        <div>
            <div>
                <h1 class="logo-name12">CINEMA</h1>
            </div>
            <h3>Forgot Password</h3>
            <p>Enter your email address and we will send you a link to reset your password.</p>
            <form class="m-t" action="{{ route('password.email') }}" method="POST">
                @csrf
                <div class="form-group">
                    <input type="email" id="email" name="email" class="form-control" placeholder="Email" required
                           style="background-color: #444 !important; border: none !important; color: #fff !important;">
                    @error('email')
                        <p class="error-message" style="color: #dc3545 !important;">{{ $message }}</p>
                    @enderror
                </div>
                <button type="submit" class="btn btn-primary block full-width m-b"
                        style="background-color: #DAA520 !important; border-color: #DAA520 !important;">
                    Send Password Reset Link
                </button>
                <br>
                <br>
                <p class="text-muted text-center"><small>Remember your password?</small></p>
                <a class="btn btn-sm btn-light btn-block" href="{{ route('auth.login') }}"
                   style="color: #DAA520 !important;">Login</a>
            </form>
            <p class="m-t"> <small>© 2024 Cinema</small> </p>
        </div>
    </div>
    <script src="{{ asset('backend/js/jquery-3.1.1.min.js') }}"></script>
    <script src="{{ asset('backend/js/bootstrap.min.js') }}"></script>
</body>
</html>