<?php

namespace App\Http\Controllers\Customers;

use App\Models\Upload;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Notifications\Product\ProductCreated;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('customers.products.index', [
            'products' => Product::where('customer_id', auth()->user()->id)->get()
        ]);
    }

    public function create()
    {
        return view('customers.products.create');
    }


    public function store(Request $request)
    {
        $productData = $request->validate([
            'product_name' => 'required|string|max:100',
            'product_desc' => 'required|string',
            'price' => 'required|integer',
            'stock' => 'required|integer',
            'brand' => 'required|string|max:100',
            'product_image' => 'required|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $imagePath = $request->file('product_image')->store('assets/products');
        $imageUrl = Storage::url($imagePath);

        // set imageUrl to Upload
        $uplaodDetail = Upload::create([
            'image' => $imageUrl,
            'user_id' => $request->user()->id
        ]);

        $productData['upload_id'] = $uplaodDetail->id;
        $productData['customer_id'] = $request->user()->id;

        
        $product = Product::create($productData);

        $request->user()->notify(new ProductCreated($request->user(), $product));

        if (!$product) {
            $request->user()->notify(new ProductCreated($request->user(), $product, 'failed'));

            return redirect()->back()->with('error', 'Failed add product to database');
        }

        return redirect()->route('seller.product.index')->with('success', 'Success add product to database');
    }
    public function show(Product $product)
    {
        //
        return redirect()->to(config('app.frontend_url'));
    }


    public function edit(Product $product)
    {
        return view('customers.products.edit', [
            'product' => $product
        ]);
    }

    public function update(Request $request, Product $product)
    {
        $productData = $request->validate([
            'product_name' => 'string|max:100',
            'product_desc' => 'string',
            'price' => 'integer',
            'stock' => 'integer',
            'is_active' => 'string|max:100',
        ]);

        if (!$product)
            return redirect()->back()->with('error', 'Product not found');

        if (isset($request->product_image)) {
            $imagePath = $request->file('product_image')->store('assets/products');
            $imageUrl = Storage::url($imagePath);

            // set imageUrl to Upload
            $uplaodDetail = Upload::create([
                'image' => $imageUrl,
                'user_id' => $request->user()->id
            ]);
        }

        $product->product_name = $productData['product_name'];
        $product->product_desc = $productData['product_desc'];
        $product->price = $productData['price'];
        $product->stock = $productData['stock'];
        $product->brand = $productData['is_active'];

        if (isset($request->product_image)) {
            $product->upload_id = $uplaodDetail->id;
        }

        $product->save();

        return redirect()->route('seller.product.index')->with('success', 'Success update product to database');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('seller.product.index')->with('success', 'Success delete product to database');
    }
}