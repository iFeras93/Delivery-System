@extends('layouts.app')
@section('extracss')

@endsection

@section('content')
    <div class="container">
        <div class="row justify-content-center">

            <!-- Change User Information -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header"> {{ $title }}</div>
                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success">
                                {{ session('status') }}
                            </div>
                        @endif


                        <label><strong>User: </strong>
                            <p>{{ $order->user->name }}</p>
                        </label>

                        <br>
                        <label><strong>Products List: </strong>

                            <p>
                                @foreach($order->products as $product)
                                    - {{ $product->title }} <br>
                                @endforeach
                            </p>
                            <strong>Products Total Price: </strong> ${{ $order->product_price }}
                        </label>

                        <br>
                        <label>
                            <strong>Distance (per km) : </strong>
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

                            <br>
                            <strong>Price for km
                                : </strong> @php $setting=\App\Models\Setting::find(1); echo '$'.$setting->price_of_km @endphp
                        </label>

                        <br>
                        <label>
                            <strong>Total Price Of Order : </strong>
                            <p> ${{ $order->total_price }}</p>
                        </label>


                        <br>
                        <label>
                            <strong>Order Status : </strong>
                            <p>
                                @if($order->status == 0)
                                    <span class="label label-default">Pending</span>
                                @elseif($order->status == 1)
                                    <span class="label label-primary">In Progress</span>
                                @elseif($order->status == 2)
                                    <span class="label label-success">Delivered</span>
                                @else
                                    <span class="label label-danger">Canceled</span>
                                @endif
                            </p>
                        </label>


                    </div>
                </div>
            </div>


        </div>
    </div>
@endsection
