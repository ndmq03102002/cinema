<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>INSPINIA | Update Profile</title>
    <link href="template/css/bootstrap.min.css" rel="stylesheet">
    <link href="template/font-awesome/css/font-awesome.css" rel="stylesheet">
    <link href="template/css/plugins/iCheck/custom.css" rel="stylesheet">
    <link href="template/css/animate.css" rel="stylesheet">
    <link href="template/css/style.css" rel="stylesheet">
    <link href="template/css/customize.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        .container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh; /* Chiếm toàn bộ chiều cao của viewport */
        }

        .form-container {
            width: 66%; /* Chiếm 2/3 chiều rộng màn hình */
            max-width: 800px; /* Đặt chiều rộng tối đa để đảm bảo không quá rộng trên màn hình lớn */
            margin: 0 auto; /* Căn giữa theo chiều ngang */
        }
    </style>
</head>

<body class="gray-bg">
    <div class="middle-box text-center loginscreen animated fadeInDown">
        <div>
            <div>
                @if($profile && $profile->avatar)
                    <img src="{{ asset('storage/' . $profile->avatar) }}" alt="Avatar" style="width: 100px; height: 100px; margin-top: 10px;">
                @endif
            </div>
            <h2 style="font-size: 36px; font-weight: bold; color: #333;">Your Profile</h2>

            <p>Update your profile information.</p>
            <form class="m-t" role="form" action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <div class="row">
                        <div class="col-lg-3">
                            <label for="name" class="control-label text-left">Name</label>
                        </div>
                        <div class="col-lg-9">
                            <input 
                                type="text" 
                                id="name" 
                                class="form-control" 
                                placeholder="Name" 
                                name="name" 
                                value="{{ old('name', $profile->name ?? '') }}">
                            @if($errors->has('name'))
                                <p class="error-message">* {{ $errors->first('name') }}</p>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="row">
                        <div class="col-lg-3">
                            <label for="dateofbirth" class="control-label text-left">Date of Birth</label>
                        </div>
                        <div class="col-lg-9">
                            <input 
                                type="date" 
                                id="dateofbirth" 
                                class="form-control" 
                                placeholder="Date of Birth" 
                                name="dateofbirth" 
                                value="{{ old('dateofbirth', $profile->dateofbirth ?? '') }}">
                            @if($errors->has('dateofbirth'))
                                <p class="error-message">* {{ $errors->first('dateofbirth') }}</p>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="row">
                        <div class="col-lg-3">
                            <label for="sex" class="control-label text-left">Gender</label>
                        </div>
                        <div class="col-lg-9">
                            <select name="sex" id="sex" class="form-control">
                                <option value="">Select Gender</option>
                                <option value="male" {{ old('sex', $profile->sex ?? '') == 'male' ? 'selected' : '' }}>Male</option>
                                <option value="female" {{ old('sex', $profile->sex ?? '') == 'female' ? 'selected' : '' }}>Female</option>
                                <option value="other" {{ old('sex', $profile->sex ?? '') == 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                            @if($errors->has('sex'))
                                <p class="error-message">* {{ $errors->first('sex') }}</p>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="row">
                        <div class="col-lg-3">
                            <label for="address" class="control-label text-left">Address</label>
                        </div>
                        <div class="col-lg-9">
                            <input 
                                type="text" 
                                id="address" 
                                class="form-control" 
                                placeholder="Address" 
                                name="address" 
                                value="{{ old('address', $profile->address ?? '') }}">
                            @if($errors->has('address'))
                                <p class="error-message">* {{ $errors->first('address') }}</p>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="row">
                        <div class="col-lg-3">
                            <label for="avatar" class="control-label text-left">Avatar</label>
                        </div>
                        <div class="col-lg-9">
                            <input type="file" id="avatar" class="form-control" name="avatar">
                            @if($profile && $profile->avatar)
                                <img src="{{ asset('storage/' . $profile->avatar) }}" alt="Avatar" style="width: 100px; height: 100px; margin-top: 10px;">
                            @endif
                            @if($errors->has('avatar'))
                                <p class="error-message">* {{ $errors->first('avatar') }}</p>
                            @endif
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary block full-width m-b">Save Changes</button>
                <p class="text-muted text-center"><small>Want to go back?</small></p>
                <a class="btn btn-sm btn-white btn-block" href="{{ route('blog.home') }}">Home</a>
            </form>
            <p class="m-t"> <small>Inspinia web app framework based on Bootstrap 3 &copy; 2014</small> </p>
        </div>
    </div>

    <!-- Mainly scripts -->
    <script src="js/jquery-3.1.1.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <!-- iCheck -->
    <script src="js/plugins/iCheck/icheck.min.js"></script>
    <script>
        $(document).ready(function(){
            $('.i-checks').iCheck({
                checkboxClass: 'icheckbox_square-green',
                radioClass: 'iradio_square-green',
            });
        });
    </script>
</body>

</html>
