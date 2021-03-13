<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ProductVariantPrice;
use App\Models\Variant;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function index()
    {
        $products = Product::paginate(10);
        $variants = Variant::all();

        return view('products.index', compact('products', 'variants')); 
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function create()
    {
        $variants = Variant::all();
        return view('products.create', compact('variants'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $product = Product::create([
            'title' => $request->title,
            'sku' => $request->sku,
            'description' => $request->description,
        ]);

        foreach ($request->product_variant as $variant) {
            foreach ($variant['tags'] as $tag) {
                $product_variant = ProductVariant::create([
                    'variant' => $tag,
                    'variant_id' => $variant['option'],
                    'product_id' => $product['id'],
                ]);
            }
        }

        foreach ($request->product_variant_prices as $variant_price) {
            ProductVariantPrice::create([
                'product_variant_one' => $product_variant->id,
                'price' => $variant_price['price'],
                'stock' => $variant_price['stock'],
                'product_id' => $product['id'],
            ]);
        }
        
        return response()->json('Success', 200);
    }


    /**
     * Display the specified resource.
     *
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function show($product)
    {

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product)
    {
        $variants = Variant::all();
        return view('products.edit', compact('variants', 'product'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        //
    }

    /**
     * Filter product based on quesry
     *
     * @param Request $request
     * @return JSON
     */
    public function filter(Request $request)
    {
        $variant_id = $request->variant;
        $from = $request->price_from;
        $to = $request->price_to;

        $products = Product::where('title', 'LIKE', $request->title)
                            ->orWhereHas('variants', function($q) use ($variant_id){
                                $q->where('id', $variant_id);
                            })
                            ->orWhere('created_at', date('Y-m-d H:i:s', strtotime($request->date)));
        
        if ($from && $to) {
            $products = $products->orWhereHas('variantPrices', function($q) use ($from, $to){
                $q->where('price', '>=', $from)->where('price', '<=', $to);
            });
        }

        $products = $products->get();

        return response()->json($products, 200);
    }
}
