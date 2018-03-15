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

                        <br> <br>

                        @if($order->status == 0)
                            <a href=""
                               onclick="event.preventDefault();
                                       document.getElementsByClassName('pay-order-form')[0].submit();"
                               class="btn btn-info btn-rounded btn-md btn-block my-0">
                                Pay With Paypal
                            </a>
                            <form class="pay-order-form"
                                  action="{{ route('client.payment') }}"
                                  method="POST"
                                  style="display: none;">
                                {{ csrf_field() }}
                                <input type="hidden" name="order_id" value="{{ $order->id }}">
                            </form>
                        @elseif($order->status == 1)
                            <div class="row">
                                <div class="col-md-6">
                                    <a href=""
                                       onclick="event.preventDefault();
                                       document.getElementsByClassName('delivered-order-form')[0].submit();"
                                       class="btn btn-success btn-rounded btn-md btn-block my-0">
                                        Order Delivered
                                    </a>
                                    <form class="delivered-order-form"
                                          action="{{ route('client.orders.delivered') }}"
                                          method="POST"
                                          style="display: none;">
                                        {{ csrf_field() }}
                                        <input value="{{ $order->id }}" name="order_id" type="hidden">
                                    </form>
                                </div>
                                <div class="col-md-6">
                                    <a href=""
                                       onclick="event.preventDefault();
                                               document.getElementsByClassName('remove-order-form-{{$order->id}}')[0].submit();"
                                       class="btn btn-danger btn-rounded btn-md my-0 btn-block">
                                        Cancel And Refund
                                    </a>
                                    <form class="remove-order-form-{{$order->id}}"
                                          action="{{ route('client.orders.destroy',$order->id) }}"
                                          method="POST"
                                          style="display: none;">
                                        {{ csrf_field() }}
                                    </form>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>


        </div>
    </div>
@endsection
