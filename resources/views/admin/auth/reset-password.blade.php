<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cinema Reset Password</title>
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
            background-color: rgba(0, 0, 0, 0.7);
            padding: 30px;
            border-radius: 10px;
        }

        .logo-name12 {
            color: #DAA520;
            font-size: 11vh;
            font-weight: 800;
            margin-bottom: 20px;
            font-family: 'Bebas Neue', cursive;
        }

        h3 {
            color: #fff;
            margin-bottom: 20px;
        }

        p {
            color: #ccc;
        }

        .form-control {
            background-color: #444;
            border: none;
            color: #fff;
        }

        .form-control:focus {
            background-color: #555;
            box-shadow: none;
        }

        .btn-primary {
            background-color: #DAA520;
            border-color: #DAA520;
        }

        .btn-primary:hover, .btn-primary:focus {
            background-color: #B8860B;
            border-color: #B8860B;
        }

        .error-message, .error1 {
            color: #dc3545;
        }

        a {
            color: #DAA520;
        }

        a:hover {
            color: #B8860B;
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
            <h3>Reset Password</h3>
            <p>Enter your new password below.</p>
            <form class="m-t" action="{{ route('password.update', $token) }}" method="POST">
                @csrf
                <div class="form-group">
                    <input type="text" name="email" class="form-control" value="{{ $email }}" autofocus readonly>
                </div>
                <div class="form-group">
                    <input type="password" name="password" class="form-control" placeholder="New Password" required>
                    @error('password')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                </div>
                <div class="form-group">
                    <input type="password" name="password_confirmation" class="form-control" placeholder="Confirm New Password" required>
                </div>
                <button type="submit" class="btn btn-primary block full-width m-b">Reset Password</button>
            </form>
            <p class="m-t"> <small>© 2024 Cinema</small> </p>
        </div>
    </div>
    <script src="{{ asset('backend/js/jquery-3.1.1.min.js') }}"></script>
    <script src="{{ asset('backend/js/bootstrap.min.js') }}"></script>
</body>
</html>