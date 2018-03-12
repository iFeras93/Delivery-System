<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\ProductsRequest;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ProductsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //Page Title
        $title = "Products List";

        //Get 10 Product Per Page
        $products = Product::paginate(10);
        return view('admin.products.index', compact(['title', 'products']));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        $title = "Create New Product";
        return view('admin.products.create', compact(['title']));
    }

    /**
     * @param ProductsRequest $request
     */
    public function store(ProductsRequest $request)
    {
        //
        if ($request->isMethod('post')) {
            $product = new Product();
            $product->title = $request->input('title');
            $product->description = $request->input('description');
            $product->price = $request->input('price');
            $product->user_id = Auth::user()->id;
            $product->save();
            return redirect(route('products.index'));
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
        //Get Product By ID
        $product = Product::find($id);
        $title = "Update Product " . $product->title;
        return view('admin.products.edit', compact(['title', 'product']));

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
     * @param ProductsRequest $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(ProductsRequest $request, $id)
    {
        //Get Product By ID
        $product = Product::find($id);
        if ($request->isMethod('post')) {
            $product->title = $request->input('title');
            $product->description = $request->input('description');
            $product->price = $request->input('price');
            $product->save();
            return redirect(route('products.index'));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //Get Product By ID
        $prodcut = Product::find($id);

        //Check If Product Exists
        if (!$prodcut)
            return redirect()->back()->with(['message' => 'No Product Founded']);

        $prodcut->delete();
        return redirect()->back();
    }
}
