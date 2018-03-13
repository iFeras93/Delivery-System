@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">

            <!-- Change User Information -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header"> User Information</div>
                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success">
                                {{ session('status') }}
                            </div>
                        @endif
                        <form method="POST" action="{{ route('users.update',$user->id) }}">
                            {{ csrf_field() }}
                            <div class="form-group row">

                                <input type="hidden"
                                       name="id" value="{{ $user->id  }}">

                                <label for="name" class="col-md-4 col-form-label text-md-right">{{ __('Name') }}</label>
                                <div class="col-md-6">
                                    <input id="name" type="text"
                                           class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}"
                                           name="name" value="{{ $user->name  }}" required autofocus>

                                    @if ($errors->has('name'))
                                        <span class="invalid-feedback">
                                        <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="email"
                                       class="col-md-4 col-form-label text-md-right">{{ __('E-Mail Address') }}</label>

                                <div class="col-md-6">
                                    <input id="email" type="email"
                                           class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}"
                                           name="email" value="{{ $user->email }}" required>

                                    @if ($errors->has('email'))
                                        <span class="invalid-feedback">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="email"
                                       class="col-md-4 col-form-label text-md-right">Select Type</label>
                                <div class="col-md-6">
                                    <select name="type" class="col-md-12 col-form-label text-md-right">
                                        <option value="admin" {{ ($user->type == 'admin')?'selected="selected':'' }}>
                                            Administrator
                                        </option>
                                        <option value="client" {{ ($user->type == 'client')?'selected="selected':'' }}>
                                            Client
                                        </option>
                                    </select>
                                    @if ($errors->has('type'))
                                        <span class="invalid-feedback">
                                        <strong>{{ $errors->first('type') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group row mb-0">
                                <div class="col-md-6 offset-md-4">
                                    <button type="submit" class="btn btn-primary" name="change_info">
                                        Update Information
                                    </button>

                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Change User Password -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">Change Password</div>

                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success">
                                {{ session('status') }}
                            </div>
                        @endif

                        <form method="POST" action="{{ route('users.update',$user->id) }}">
                            {{ csrf_field() }}

                            <input type="hidden"
                                   name="id" value="{{ $user->id  }}">

                            <div class="form-group row">
                                <label for="current-password"
                                       class="col-md-4 col-form-label text-md-right">Current Password</label>

                                <div class="col-md-6">
                                    <input id="current-password" type="password"
                                           class="form-control{{ $errors->has('current_password') ? ' is-invalid' : '' }}"
                                           name="current_password" required>
                                    @if ($errors->has('current_password'))
                                        <span class="invalid-feedback">
                                        <strong>{{ $errors->first('current_password') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="new-password"
                                       class="col-md-4 col-form-label text-md-right">New Password</label>

                                <div class="col-md-6">
                                    <input id="new-password" type="password"
                                           class="form-control{{ $errors->has('new_password') ? ' is-invalid' : '' }}"
                                           name="new_password" required>
                                    @if ($errors->has('new_password'))
                                        <span class="invalid-feedback">
                                        <strong>{{ $errors->first('new_password') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="password-confirm"
                                       class="col-md-4 col-form-label text-md-right">{{ __('Confirm Password') }}</label>

                                <div class="col-md-6">
                                    <input id="password-confirm" type="password"
                                           class="form-control {{ $errors->has('password_confirmation') ? ' is-invalid' : '' }}"
                                           name="password_confirmation" required>
                                    @if ($errors->has('password_confirmation'))
                                        <span class="invalid-feedback">
                                        <strong>{{ $errors->first('password_confirmation') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group row mb-0">
                                <div class="col-md-6 offset-md-4">
                                    <button type="submit" class="btn btn-primary" name="change_password">
                                        Change Password
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection
