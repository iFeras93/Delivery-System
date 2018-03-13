<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderRequest;
use App\Models\Order;
use App\Models\Product;
use App\Models\Setting;
use App\User;
use Bodunde\GoogleGeocoder\Geocoder;
use Illuminate\Http\Request;

class OrdersController extends Controller
{


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //title page
        $title = "Orders List";
        // get orders with users make it.
        $orders = Order::with('user')->paginate(10);
        return view('admin.orders.index', compact(['title', 'orders']));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //page title
        $title = "Create New Order";
        //get client users list
        $users = User::where('type', 'client')->get();
        //get products list
        $products = Product::get();


        return view('admin.orders.create', compact(['title', 'users', 'products', 'settings']));
    }

    /**
     * @param StoreOrderRequest $request
     */
    public function store(StoreOrderRequest $request)
    {
        //
        if ($request->isMethod('post')) {

            $client_lat = $request->input('client_lat');
            $client_long = $request->input('client_long');

            //new Instance
            $order = new Order();
            $order->order_code = $this->generateOrderCode();
            $order->client_long = $client_long;
            $order->client_lat = $client_lat;

            $order->client_id = $request->input('user');

            $products = $request->input('products');

            $order->product_price = $this->calculateProductsPrice($products);
            $order->total_price = $this->calculateTotalPrice($client_lat, $client_long) + $this->calculateProductsPrice($products);

            $order->save();

            $order->products()->sync($products);
            return redirect(route('orders.index'))->with(['status' => 'Add New Order Successfully']);
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
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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

    private function generateOrderCode()
    {
        $randNumber = rand(9, 9999);
        $code = "ORDER_" . $randNumber;
        return $code;
    }

    private function calculateProductsPrice($products = [])
    {
        $productsTotalPrice = 0;
        foreach ($products as $product) {
            $prdct = Product::where('id', $product)->first();
            $productsTotalPrice += $prdct->price;
        }

        return $productsTotalPrice;
    }

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
