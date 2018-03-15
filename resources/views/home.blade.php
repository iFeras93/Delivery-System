@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Home</div>

                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success">
                                {{ session('status') }}
                            </div>
                        @endif

                        <a href="{{ route('client.orders.index') }}">My Orders List</a>
                        <br>
                        <a href="#">My Transactions List</a> <small><span>(coming soon) ...</span></small>



                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
