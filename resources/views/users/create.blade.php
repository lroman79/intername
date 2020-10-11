<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Create new user</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">

    <style>
    .main-title{
      text-align: center;
    }
    .container {
    max-width: 500px;
    margin: 50px auto;
    text-align: left;
    font-family: sans-serif;
    }

    form {
        background: #ecf5fc;
        padding: 40px 50px 45px;
    }

    .form-control:focus {
        border-color: #000;
        box-shadow: none;
    }

    label {
        font-weight: 600;
    }

    .error {
        color: red;
        font-weight: 400;
        display: block;
        padding: 6px 0;
        font-size: 14px;
    }

    .form-control.error {
        border-color: red;
        padding: .375rem .75rem;
    }
    </style>
</head>

<body>
    <h1 class="main-title">Create new user</h1>
    <div class="container mt-5">
        <!-- Success message -->
        @if(Session::has('success'))
            <div class="alert alert-success">
                {{Session::get('success')}}
            </div>
        @endif

        <form method="post" action="{{ route('users.store') }}">
            <!-- @method('PUT') -->
            <!-- CROSS Site Request Forgery Protection -->
            @csrf

            <div class="form-group">
                <label>Name</label>
                <input type="text" class="form-control {{ $errors->has('name') ? 'error' : '' }}" name="name" id="name" value="{{ old('name') }}">
            </div>

             <!-- Error -->
                @if ($errors->has('name'))
                <div class="error">
                    {{ $errors->first('name') }}
                </div>
                @endif

            <div class="form-group">
                <label>Email</label>
                <input type="email" class="form-control {{ $errors->has('email') ? 'error' : '' }}" name="email" id="email" value="{{ old('email') }}">
            </div>

             <!-- Error -->
             @if ($errors->has('name'))
                <div class="error">
                    {{ $errors->first('email') }}
                </div>
                @endif

            <div class="form-group">
                <label>Post Title</label>
                <input type="text" class="form-control {{ $errors->has('post_title') ? 'error' : '' }}" name="post_title" id="post_title" value="{{ old('post_title') }}">
            </div>

            <!-- Error -->
            @if ($errors->has('name'))
                <div class="error">
                    {{ $errors->first('post_title') }}
                </div>
                @endif

            <div class="form-group">
                <label>Post Body</label>
                <textarea class="form-control" name="post_body" id="post_body" rows="4"></textarea>
            </div>

            <!-- Error -->
            @if ($errors->has('name'))
                <div class="error">
                    {{ $errors->first('post_body') }}
                </div>
                @endif

            <input type="submit" name="send" value="Submit" class="btn btn-dark btn-block">
        </form>
    </div>
</body>

</html>