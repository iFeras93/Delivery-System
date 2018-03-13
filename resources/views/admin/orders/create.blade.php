@extends('layouts.app')
@section('extracss')
    <style>
        #map {
            width: 100%;
            height: 400px;
        }

        .controls {
            margin-top: 10px;
            border: 1px solid transparent;
            border-radius: 2px 0 0 2px;
            box-sizing: border-box;
            -moz-box-sizing: border-box;
            height: 32px;
            outline: none;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.3);
        }

        #searchInput {
            background-color: #fff;
            font-family: Roboto;
            font-size: 15px;
            font-weight: 300;
            margin-left: 12px;
            padding: 0 11px 0 13px;
            text-overflow: ellipsis;
            width: 50%;
        }

        #searchInput:focus {
            border-color: #4d90fe;
        }
    </style>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet"/>

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


                        <form method="POST" action="{{ route('orders.store') }}">
                            {{ csrf_field() }}

                            <div class="form-group row">
                                <label for="user"
                                       class="col-md-4 col-form-label text-md-right">User</label>
                                <div class="col-md-6">
                                    <select id="js-example-basic-multiple2" class="form-control" name="user"
                                            required>
                                        @if($users->count() > 0)
                                            @foreach($users as $user)
                                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                    @if ($errors->has('user'))
                                        <span class="invalid-feedback">
                                        <strong>{{ $errors->first('user') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="products"
                                       class="col-md-4 col-form-label text-md-right">Products</label>
                                <div class="col-md-6">
                                    <select id="js-example-basic-multiple" class="form-control" name="products[]"
                                            multiple="multiple"
                                            required>
                                        @if($products->count() > 0)
                                            @foreach($products as $product)
                                                <option value="{{ $product->id }}">{{ $product->title }} - ${{ $product->price }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                    @if ($errors->has('products'))
                                        <span class="invalid-feedback">
                                        <strong>{{ $errors->first('products') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>


                            <div class="row">
                                <div class="col-md-12">
                                    <input id="searchInput" class="controls" type="text" placeholder="Enter a location">
                                    <div id="map"></div>
                                </div>
                            </div>
                            <br>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="client_long"
                                               class="col-form-label text-md-right">Longitude</label>
                                        <input id="client_long" type="text"
                                               class="form-control{{ $errors->has('client_long') ? ' is-invalid' : '' }}"
                                               name="client_long"
                                               value="{{ old('client_long') }}" required readonly>
                                        @if ($errors->has('client_long'))
                                            <span class="invalid-feedback">
                                        <strong>{{ $errors->first('client_long') }}</strong>
                                    </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="client_lat"
                                               class="col-form-label text-md-right">Latitude</label>
                                        <input id="client_lat" type="text"
                                               class="form-control{{ $errors->has('client_lat') ? ' is-invalid' : '' }}"
                                               name="client_lat"
                                               value="{{ old('client_lat') }}" required readonly>
                                        @if ($errors->has('client_lat'))
                                            <span class="invalid-feedback">
                                        <strong>{{ $errors->first('client_lat') }}</strong>
                                    </span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <hr>
                            <div class="form-group row mb-0">
                                <div class="col-md-12">
                                    <button type="submit" class="btn btn-block btn-primary">
                                        Make Order
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
@section('extrajs')
    <script>
        function initMap() {
            var map = new google.maps.Map(document.getElementById('map'), {
                center: {lat: 31.316528, lng: 34.3152419},
                zoom: 12
            });

            google.maps.event.addListener(map, 'click', function (event) {
                //alert("Latitude: " + event.latLng.lat() + " " + ", longitude: " + event.latLng.lng());
                //marker = new google.maps.Marker({position: event.latLng, map: map});

                $("#client_lat").val(event.latLng.lat());
                $("#client_long").val(event.latLng.lng());

                if (marker) {
                    marker.setPosition(event.latLng);
                } else {
                    marker = new google.maps.Marker({
                        position: event.latLng,
                        map: map
                    });
                }
            });

            var input = document.getElementById('searchInput');
            map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);

            var autocomplete = new google.maps.places.Autocomplete(input);
            autocomplete.bindTo('bounds', map);

            var infowindow = new google.maps.InfoWindow();
            var marker = new google.maps.Marker({
                map: map,
                anchorPoint: new google.maps.Point(0, -29)
            });

            autocomplete.addListener('place_changed', function () {
                infowindow.close();
                marker.setVisible(false);
                var place = autocomplete.getPlace();
                if (!place.geometry) {
                    window.alert("Autocomplete's returned place contains no geometry");
                    return;
                }

                // If the place has a geometry, then present it on a map.
                if (place.geometry.viewport) {
                    map.fitBounds(place.geometry.viewport);
                } else {
                    map.setCenter(place.geometry.location);
                    map.setZoom(17);
                }
                marker.setIcon(({
                    url: place.icon,
                    size: new google.maps.Size(71, 71),
                    origin: new google.maps.Point(0, 0),
                    anchor: new google.maps.Point(17, 34),
                    scaledSize: new google.maps.Size(35, 35)
                }));
                marker.setPosition(place.geometry.location);
                marker.setVisible(true);

                var address = '';
                if (place.address_components) {
                    address = [
                        (place.address_components[0] && place.address_components[0].short_name || ''),
                        (place.address_components[1] && place.address_components[1].short_name || ''),
                        (place.address_components[2] && place.address_components[2].short_name || '')
                    ].join(' ');
                }

                infowindow.setContent('<div><strong>' + place.name + '</strong><br>' + address);
                infowindow.open(map, marker);

                //Location details
                $("#client_lat").val(place.geometry.location.lat());
                $("#client_long").val(place.geometry.location.lng());
                //document.getElementById('lat').innerHTML = ;
                //document.getElementById('lon').innerHTML =;
            });
        }
    </script>

    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAPQwgQSGCkZkWxv7PjbusEs9Yg9_lFjCk&libraries=places&callback=initMap"
            async defer></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>

    <script type="application/javascript">
        $(document).ready(function () {
            $('#js-example-basic-multiple').select2();
            $('#js-example-basic-multiple2').select2();
        });
    </script>
@endsection
