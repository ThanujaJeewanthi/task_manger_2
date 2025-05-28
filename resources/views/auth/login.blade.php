@extends('layouts.auth')

@section('title', 'Login')

@section('content')
<div class="container " style="margin-top: 100px;" >
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card mt-4">
                <div class="card-header bg-dark text-white text-center">
                    <h4 class="mb-0">Login to Task Manager</h4>
                </div>
                <div class="card-body">
                    {{-- @if ($errors->any())
                        <div class="alert alert-danger" role="alert">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif --}}

                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        <div class="form-group row mb-3">
                            <label for="username" class="col-md-4 col-form-label text-md-right">{{ __('Username') }}</label>
                            <div class="col-md-6">
                                <input id="username" type="text" class="form-control @error('username') is-invalid @enderror" name="username" value="{{ old('username') }}" required autocomplete="username" autofocus>

                                @error('username')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        {{-- <div class="form-group row mb-3">
                            <label for="email" class="col-md-4 col-form-label text-md-right">{{ __('Email Address') }}</label>
                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div> --}}

                        <div class="form-group row mb-3">
                            <label for="password" class="col-md-4 col-form-label text-md-right">{{ __('Password') }}</label>
                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <div class="col-md-6 offset-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="remember">
                                        {{ __('Remember Me') }}
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row mb-3">

                        <button type="submit" class="btn btn-primary mx-auto d-block" style="width:90px;" >
                            {{ __('Login') }}
                        </button>
                        </div>
<div class="form-group row mb-3">
    <div class="form-group  mb-0" style="margin-left: 0px;">
        <div class="col-md-8 offset-md-4 d-flex justify-content-between">


            <a href="#" class="btn btn-link">
                {{ __('Forgot Password?') }}
            </a>
        </div>
    </div>
{{-- view register button if there are no users in the users table --}}
@if (DB::table('users')->count() == 0)
    <div class="form-group  mt-3" style="margin-left: 10px;">
        <div class="col-md-8 offset-md-4">
            <a href="{{ route('register.form') }}" class="btn btn-link ps-0">
                {{ __('Need an account? Register') }}
            </a>
        </div>
    </div>
@endif
</div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
