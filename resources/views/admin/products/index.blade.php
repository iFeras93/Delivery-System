@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        {{ $title }}
                    </div>

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
                                <th>Title</th>
                                <th>Description</th>
                                <th>Price</th>
                                <th>actions</th>
                            </tr>
                            </thead>
                            <!--Table head-->

                            <!--Table body-->
                            @if($products->count() > 0)

                                <tbody>
                                @foreach($products as $product)
                                    <tr>
                                        <th scope="row">{{ $loop->iteration }}</th>
                                        <td>{{ $product->title }}</td>
                                        <td>{{ $product->description }}</td>
                                        <td>
                                            {{ $product->price }}$
                                        </td>
                                        <td>
                                            <a href="{{ route('products.show',$product->id) }}"
                                               class="btn btn-primary btn-rounded btn-sm my-0">
                                                Edit
                                            </a>

                                            <a href="#"
                                               onclick="event.preventDefault();
                                                       document.getElementsByClassName('remove-product-form-{{$product->id}}')[0].submit();"
                                               class="btn btn-danger btn-rounded btn-sm my-0">
                                                Remove
                                            </a>
                                            <form class="remove-product-form-{{$product->id}}"
                                                  action="{{ route('products.destroy',$product->id) }}"
                                                  method="POST"
                                                  style="display: none;">
                                                {{ csrf_field() }}
                                            </form>

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
                                @if($products->count() > 0)
                                    {{ $products->links() }}
                                @endif
                            </div>
                        </div>


                        <a class="btn btn-success" href="{{ route('products.create') }}">
                            Create New Product
                        </a>
                        <a class="btn btn-info" href="{{ route('admin.dashboard') }}">
                            Back To Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
