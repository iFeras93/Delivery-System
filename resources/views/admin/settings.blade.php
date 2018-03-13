@extends('layouts.app')

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


@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ $title }}</div>

                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success">
                                {{ session('status') }}
                            </div>
                        @endif


                        <form method="POST" action="{{ route('settings.update') }}">
                            {{ csrf_field() }}

                            <div class="form-group row">
                                <label for="price_of_km"
                                       class="col-md-4 col-form-label text-md-right">Distance Price (per
                                    kilometers)</label>
                                <div class="col-md-6">
                                    <input id="price_of_km" type="number"
                                           class="form-control{{ $errors->has('price_of_km') ? ' is-invalid' : '' }}"
                                           name="price_of_km" value="{{ number_format($settings->price_of_km,2) }}"
                                           required>
                                    @if ($errors->has('price_of_km'))
                                        <span class="invalid-feedback">
                                        <strong>{{ $errors->first('price_of_km') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="main_email"
                                       class="col-md-4 col-form-label text-md-right">Website Email</label>
                                <div class="col-md-6">
                                    <input id="main_email" type="email"
                                           class="form-control{{ $errors->has('main_email') ? ' is-invalid' : '' }}"
                                           name="main_email" value="{{ $settings->main_email }}"
                                           required>
                                    @if ($errors->has('main_email'))
                                        <span class="invalid-feedback">
                                        <strong>{{ $errors->first('main_email') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="PAYPAL_SANDBOX_CLIENT_ID"
                                       class="col-md-4 col-form-label text-md-right">PayPal Sandbox Client ID</label>
                                <div class="col-md-6">
                                    <input id="PAYPAL_SANDBOX_CLIENT_ID" type="text"
                                           class="form-control{{ $errors->has('PAYPAL_SANDBOX_CLIENT_ID') ? ' is-invalid' : '' }}"
                                           name="PAYPAL_SANDBOX_CLIENT_ID"
                                           value="{{ $settings->PAYPAL_SANDBOX_CLIENT_ID }}">
                                    @if ($errors->has('PAYPAL_SANDBOX_CLIENT_ID'))
                                        <span class="invalid-feedback">
                                        <strong>{{ $errors->first('PAYPAL_SANDBOX_CLIENT_ID') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="PAYPAL_SANDBOX_SECRET"
                                       class="col-md-4 col-form-label text-md-right">PayPal Sandbox Secret</label>
                                <div class="col-md-6">
                                    <input id="PAYPAL_SANDBOX_SECRET" type="text"
                                           class="form-control{{ $errors->has('PAYPAL_SANDBOX_SECRET') ? ' is-invalid' : '' }}"
                                           name="PAYPAL_SANDBOX_SECRET"
                                           value="{{ $settings->PAYPAL_SANDBOX_SECRET }}">
                                    @if ($errors->has('PAYPAL_SANDBOX_SECRET'))
                                        <span class="invalid-feedback">
                                        <strong>{{ $errors->first('PAYPAL_SANDBOX_SECRET') }}</strong>
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

                            <br> <br>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="main_long"
                                               class="col-form-label text-md-right">Longitude</label>
                                        <input id="main_long" type="text"
                                               class="form-control{{ $errors->has('main_long') ? ' is-invalid' : '' }}"
                                               name="main_long"
                                               value="{{ $settings->main_long }}" required readonly>
                                        @if ($errors->has('main_long'))
                                            <span class="invalid-feedback">
                                        <strong>{{ $errors->first('main_long') }}</strong>
                                    </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="main_lat"
                                               class="col-form-label text-md-right">Latitude</label>
                                        <input id="main_lat" type="text"
                                               class="form-control{{ $errors->has('main_lat') ? ' is-invalid' : '' }}"
                                               name="main_lat"
                                               value="{{ $settings->main_lat }}" required readonly>
                                        @if ($errors->has('main_lat'))
                                            <span class="invalid-feedback">
                                        <strong>{{ $errors->first('main_lat') }}</strong>
                                    </span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <hr>
                            <div class="form-group row mb-0">
                                <div class="col-md-6 offset-md-4">
                                    <button type="submit" class="btn btn-primary">
                                        Update Information
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
<script>
    function initMap() {
        var map = new google.maps.Map(document.getElementById('map'), {
            center: {lat: 31.316528, lng: 34.3152419},
            zoom: 12
        });

        google.maps.event.addListener(map, 'click', function (event) {
            //alert("Latitude: " + event.latLng.lat() + " " + ", longitude: " + event.latLng.lng());
            //marker = new google.maps.Marker({position: event.latLng, map: map});

            $("#main_lat").val(event.latLng.lat());
            $("#main_long").val(event.latLng.lng());

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
            $("#main_lat").val(place.geometry.location.lat());
            $("#main_long").val(place.geometry.location.lng());
            //document.getElementById('lat').innerHTML = ;
            //document.getElementById('lon').innerHTML =;
        });
    }
</script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAPQwgQSGCkZkWxv7PjbusEs9Yg9_lFjCk&libraries=places&callback=initMap"
        async defer></script>