@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        {{ $title }}</div>

                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success">
                                {{ session('status') }}
                            </div>
                    @endif

                    <!--Table-->
                        <table class="table table-striped table-responsive-md btn-table">

                            <!--Table head-->
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>email</th>
                                <th>role</th>
                                <th>actions</th>
                            </tr>
                            </thead>
                            <!--Table head-->

                            <!--Table body-->
                            @if($users->count() > 0)

                                <tbody>
                                @foreach($users as $user)
                                    <tr>
                                        <th scope="row">{{ $loop->iteration }}</th>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td>
                                            @if($user->type == 'admin')
                                                Administrator
                                            @else
                                                Client
                                            @endif
                                        </td>
                                        <td>
                                            @if($user->id != 1)
                                                @if($user->type == "admin")
                                                    <a href="{{ route('users.show',$user->id) }}"
                                                       class="btn btn-primary btn-rounded btn-sm my-0">
                                                        Edit
                                                    </a>
                                                @else
                                                    <a href="{{ route('users.show',$user->id) }}"
                                                       class="btn btn-primary btn-rounded btn-sm my-0">
                                                        Edit
                                                    </a>
                                                    <a href=""
                                                       onclick="event.preventDefault();
                                                               document.getElementsByClassName('remove-user-form-{{$user->id}}')[0].submit();"
                                                       class="btn btn-danger btn-rounded btn-sm my-0">
                                                        Remove
                                                    </a>
                                                    <form class="remove-user-form-{{$user->id}}"
                                                          action="{{ route('users.destroy',$user->id) }}"
                                                          method="POST"
                                                          style="display: none;">
                                                        {{ csrf_field() }}
                                                    </form>
                                                @endif
                                            @else
                                                <small>You can't do anything for me :P</small>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                        @endif
                        <!--Table body-->

                        </table>
                        <!--Table-->

                        <div class="row">
                            <div class="col-md-12">
                                @if($users->count() > 0)
                                    {{ $users->links() }}
                                @endif
                            </div>
                        </div>

                        <a class="btn btn-info" href="{{ route('admin.dashboard') }}">
                            Back To Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
