<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
    <body class="d-flex align-items-center min-vh-100 bg-light">
        <div class="container login-container">
            <div class="row justify-content-center w-100">
                <div class="col-lg-10">
                    <div class="card shadow-lg border-0 rounded-lg overflow-hidden">
                        <div class="card-body p-0">
                            <div class="row">
                                <div class="col-md-6 d-none d-md-flex align-items-center justify-content-center bg-light">
                                    <img src="https://via.placeholder.com/400x400.png?text=Your+Logo+Here"
                                        alt="Logo"
                                        class="img-fluid p-5">
                                </div>

                                <div class="col-md-6 p-5">
                                    <div class="text-center mb-4">
                                        <h3 class="fw-bold">Login</h3>
                                        <p class="text-muted">Welcome back! Please login to your account.</p>
                                    </div>

                                    <form action="{{ route('login') }}" method="POST"> 
                                        @csrf 
                                        @error('username')
                                            <div class="alert alert-danger text-center mb-3 py-2">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                        <div class="form-floating mb-3">
                                            <input type="text" class="form-control" id="username" name="username" placeholder="Username" required>
                                            <label for="username">Username</label>
                                        </div>

                                        <div class="form-floating mb-3">
                                            <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                                            <label for="password">Password</label>
                                        </div>

                                        @error('password')
                                            <div class="alert alert-danger text-center mb-3 py-2">
                                                {{ $message }}
                                            </div>
                                        @enderror

                                        <div class="d-grid">
                                            <button type="submit" class="btn btn-primary btn-lg">
                                                Login
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
