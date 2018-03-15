@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Admin Dashboard</div>

                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success">
                                {{ session('status') }}
                            </div>
                        @endif

                        <a href="{{ route('users.index') }}">Users List</a>

                        <br>

                        <a href="{{ route('products.index') }}">Products List</a>

                        <br>

                        <a href="{{ route('admin.orders.index') }}">Orders List</a>


                        <br>

                        <a href="{{ route('settings.index') }}">Site Settings </a>


                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
