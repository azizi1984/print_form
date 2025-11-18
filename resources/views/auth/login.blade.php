@extends('layouts.app')

@section('body-class', 'login-page') @section('content')
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

                            <form action="#" method="POST"> @csrf <div class="form-floating mb-3">
                                    <input type="text"
                                           class="form-control"
                                           id="username"
                                           name="username"
                                           placeholder="Username"
                                           required>
                                    <label for="username">Username</label>
                                </div>

                                <div class="form-floating mb-3">
                                    <input type="password"
                                           class="form-control"
                                           id="password"
                                           name="password"
                                           placeholder="Password"
                                           required>
                                    <label for="password">Password</label>
                                </div>

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
@endsection
