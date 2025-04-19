<?php
//
//namespace App\Http\Controllers;
//
//use App\Models\Products;
//use Illuminate\Http\Request;
//
//class ProductsController extends Controller
//{
//    public function index()
//    {
//        $products = Products::all();
//        return response()->json($products);
//    }
//
//    public function show($id)
//    {
//        $product = Products::find($id);
//        if (!$product) {
//            return response()->json(['message' => 'Product not found'], 404);
//        }
//        return response()->json($product);
//    }
//
//    public function store(Request $request)
//    {
//        $request->validate([
//            'product_name' => 'required|string|max:255',
//            'product_description' => 'nullable|string',
//            'product_price' => 'required|numeric|min:0',
//            'product_stock' => 'required|integer|min:0',
//            'product_category' => 'required|string|max:255',
//            'product_image1' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
//            'product_image2' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
//            'product_image3' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
//        ]);
//
//        $data = $request->all();
//
//        if ($request->hasFile('product_image1')) {
//            $data['product_image1'] = $request->file('product_image1')->store('products', 'public');
//        }
//        if ($request->hasFile('product_image2')) {
//            $data['product_image2'] = $request->file('product_image2')->store('products', 'public');
//        }
//        if ($request->hasFile('product_image3')) {
//            $data['product_image3'] = $request->file('product_image3')->store('products', 'public');
//        }
//
//        $product = Products::create($data);
//
//        return response()->json($product, 201);
//    }
//
//    public function update(Request $request, $id)
//    {
//        $product = Products::find($id);
//        if (!$product) {
//            return response()->json(['message' => 'Product not found'], 404);
//        }
//
//        $request->validate([
//            'product_name' => 'string|max:255',
//            'product_description' => 'nullable|string',
//            'product_price' => 'numeric|min:0',
//            'product_stock' => 'integer|min:0',
//            'product_category' => 'string|max:255',
//            'product_image1' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
//            'product_image2' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
//            'product_image3' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
//        ]);
//
//        $data = $request->all();
//
//        if ($request->hasFile('product_image1')) {
//            $data['product_image1'] = $request->file('product_image1')->store('products', 'public');
//        }
//        if ($request->hasFile('product_image2')) {
//            $data['product_image2'] = $request->file('product_image2')->store('products', 'public');
//        }
//        if ($request->hasFile('product_image3')) {
//            $data['product_image3'] = $request->file('product_image3')->store('products', 'public');
//        }
//
//        $product->update($data);
//
//        return response()->json($product);
//    }
//
//    public function destroy($id)
//    {
//        $product = Products::find($id);
//        if (!$product) {
//            return response()->json(['message' => 'Product not found'], 404);
//        }
//
//        $product->delete();
//
//        return response()->json(['message' => 'Product deleted']);
//    }
//}
