@extends('layouts.app')
@section('extracss')
@endsection
@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
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
                                <th>Products Price</th>
                                <th>Distance (per km)</th>
                                <th>Total Price</th>
                                <th>Status</th>
                                <th>actions</th>
                            </tr>
                            </thead>
                            <!--Table head-->

                            <!--Table body-->
                            @if($orders->count() > 0)

                                <tbody>
                                @foreach($orders as $order)
                                    <tr>
                                        <th scope="row">{{ $loop->iteration }}</th>
                                        <td>${{ $order->product_price }}</td>
                                        <td>
                                            @php
                                                $geocode= new \Bodunde\GoogleGeocoder\Geocoder();
                                                $settings= \App\Models\Setting::find(1);
                                                 $mainCoordinates = [
                                                    "lat" => $settings->main_lat,
                                                    "lng" => $settings->main_long,
                                                ];
                                                $clientCoordinates = [
                                                    'lat' => $order->client_lat,
                                                    'lng' => $order->client_long
                                                ];
                                                $totalDistance= $geocode->getDistanceBetween($mainCoordinates, $clientCoordinates, 'km');
                                            @endphp
                                            {{ $totalDistance }} km
                                        </td>
                                        <td>$ {{ $order->total_price }}</td>
                                        <td>
                                            <small>
                                                @if($order->status == 0)
                                                    <span class="label label-default">Pending</span>
                                                @elseif($order->status == 1)
                                                    <span class="label label-primary">In Progress</span>
                                                @elseif($order->status == 2)
                                                    <span class="label label-success">Delivered</span>
                                                @else
                                                    <span class="label label-danger">Canceled</span>
                                                @endif
                                            </small>

                                        </td>
                                        <td>
                                            @if($order->status ==2 || $order->status == 3)
                                                nothing to do
                                            @else
                                                <a href="{{ route('client.orders.show', $order->id) }}"
                                                   class="btn btn-primary btn-rounded btn-sm my-0">
                                                    Show Order
                                                </a>
                                                <a href=""
                                                   onclick="event.preventDefault();
                                                           document.getElementsByClassName('remove-order-form-{{$order->id}}')[0].submit();"
                                                   class="btn btn-danger btn-rounded btn-sm my-0">
                                                    Cancel
                                                </a>
                                                <form class="remove-order-form-{{$order->id}}"
                                                      action="{{ route('client.orders.destroy',$order->id) }}"
                                                      method="POST"
                                                      style="display: none;">
                                                    {{ csrf_field() }}
                                                </form>
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
                                @if($orders->count() > 0)
                                    {{ $orders->links() }}
                                @endif
                            </div>
                        </div>
                        <a class="btn btn-success" href="{{ route('client.orders.create') }}">
                            Create New Order
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
