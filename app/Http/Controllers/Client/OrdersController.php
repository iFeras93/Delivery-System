<?php

namespace App\Http\Controllers\Client;

use App\Http\Requests\ClientStoreOrderRequest;
use App\Models\Order;
use App\Models\Product;
use App\Models\Setting;
use Bodunde\GoogleGeocoder\Geocoder;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class OrdersController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        //
        $title = "My Orders List";
        $orders = Order::where('client_id', Auth::user()->id)->paginate(10);
        return view('client.orders.index', compact(['title', 'orders']));
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
        //get products list
        $products = Product::get();


        return view('client.orders.create', compact(['title', 'users', 'products']));
    }

    /**
     * @param ClientStoreOrderRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(ClientStoreOrderRequest $request)
    {
        if ($request->isMethod('post')) {

            $client_lat = $request->input('client_lat');
            $client_long = $request->input('client_long');

            //new Instance
            $order = new Order();
            $order->order_code = $this->generateOrderCode();
            $order->client_long = $client_long;
            $order->client_lat = $client_lat;

            $order->client_id = Auth::user()->id;

            $products = $request->input('products');

            $order->product_price = $this->calculateProductsPrice($products);
            $order->distance_price = $this->calculateTotalPrice($client_lat, $client_long);
            $order->total_price = $this->calculateTotalPrice($client_lat, $client_long) + $this->calculateProductsPrice($products);

            $order->save();

            $order->products()->sync($products);
            return redirect(route('client.orders.index'))->with(['status' => 'Add New Order Successfully']);
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
        $order = Order::find($id);
        $title = "Show Order Details: #" . $order->order_code;
        session()->put('order_id', $order->id);
        return view('client.orders.show', compact(['title', 'order']));
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

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //Get Order By ID
        $order = Order::find($id);

        //Check If Order Exists
        if (!$order)
            return redirect()->back()->with(['message' => 'No Orders Founded']);

        // canceled Order
        $order->status = 3;
        $order->save();

        // Return To Orders List
        return redirect()->back()->with(['status' => 'remove orders successfully']);
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
