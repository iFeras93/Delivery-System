<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Order;
use App\Models\Product;
use App\Models\Setting;
use Auth;
use Bodunde\GoogleGeocoder\Geocoder;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class OrdersController extends Controller
{
    private $user;
    private $response;

    public function __construct()
    {
        $this->user = Auth::user();
        $this->response = [];
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        //get list of orders
        $orders = Order::where('client_id', $this->user['id'])->paginate(10);

        //this is array response you show
        $this->response = [
            'status' => true,
            'message' => 'success',
            'result' => $orders
        ];

        //here we can return data with and without response http code (200,404, ..etc)
        //example: return response()->json($response, 200);
        //but i used the easiest way for mobile developer
        return response()->json($this->response);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        if ($request->isMethod('post')) {
            //lat and lon attribute
            $client_lat = $request->client_lat;
            $client_long = $request->client_long;
            //new Instance
            $order = new Order();
            // generate code by generateOrderCode function
            $order->order_code = $this->generateOrderCode();
            $order->client_long = $client_long;
            $order->client_lat = $client_lat;

            $order->client_id = Auth::user()->id;

            $products = $request->input('products');
            // give product_price column  price of all products in order
            $order->product_price = $this->calculateProductsPrice($products);
            // give distance_price column a distance_price per km
            $order->distance_price = $this->calculateTotalPrice($client_lat, $client_long);
            // calculate total price for order
            $order->total_price = $this->calculateTotalPrice($client_lat, $client_long) + $this->calculateProductsPrice($products);
            $order->save();
            // save order and products many to many relation using sync function
            $order->products()->sync($products);

            $this->response = [
                'status' => true,
                'message' => 'success',
                'result' => []
            ];
            //here we can return data with and without response http code (200,404, ..etc)
            //example: return response()->json($response, 200);
            //but i used the easiest way for mobile developer
            return response()->json($this->response);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //get order by id
        $order = Order::find($id);

        if (!$order) {
            $this->response = [
                'status' => false,
                'message' => 'error',
                'result' => []
            ];
        }

        //this is array response you show
        $this->response = [
            'status' => true,
            'message' => 'success',
            'result' => $order
        ];

        //here we can return data with and without response http code (200,404, ..etc)
        //example: return response()->json($response, 200);
        //but i used the easiest way for mobile developer
        return response()->json($this->response);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }


    //generate number to add after ORDER_ name in order_code column
    private function generateOrderCode()
    {
        //get random 4 number from 9 to 9999
        $randNumber = rand(9, 9999);
        $code = "ORDER_" . $randNumber;
        return $code;
    }

    // calculate price for list of products
    private function calculateProductsPrice($products = [])
    {
        $productsTotalPrice = 0;
        foreach ($products as $product) {
            $prdct = Product::where('id', $product)->first();
            $productsTotalPrice += $prdct->price;
        }

        return $productsTotalPrice;
    }

    //calculate distance price per km
    private function calculateTotalPrice($client_lat, $client_lon)
    {
        //to get main lon & lat
        $settings = Setting::find(1);
        $mainCoordinates = [
            "lat" => $settings->main_lat,
            "lng" => $settings->main_long,
        ];
        $clientCoordinates = [
            'lat' => $client_lat,
            'lng' => $client_lon
        ];

        $geocoder = new Geocoder();
        $totalDistance = $geocoder->getDistanceBetween($mainCoordinates, $clientCoordinates, 'km');
        $distanceTotalPrice = $settings->price_of_km * $totalDistance;

        return $distanceTotalPrice;
    }

}
