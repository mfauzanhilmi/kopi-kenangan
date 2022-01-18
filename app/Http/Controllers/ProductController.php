<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::all();
        return response()->json([
            'message' => 'Products retrieved successfully',
            'data' => ['products' => $products]
        ], 200);
    }

    public function show($id)
    {
        $product = Product::find($id);
        if (!$product) {
            throw new NotFoundHttpException('Product not found');
        }
        return response()->json([
            'message' => 'Product retrieved successfully',
            'data' => ['product' => $product]
        ], 200);
    }

    public function store(Request $request)
    {
        $validationRules = [
            'name' => 'required|string',
            'price' => 'required|numeric',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ];

        $this->validate($request, $validationRules);
        try {
            DB::beginTransaction();

            $image = app('Cloudder')::upload($request->image, null, array(
                "folder" => "products",
                "overwrite" => false,
                "invalidate" => false,
                "format" => "jpg",
                "quality" => "auto",
                "secure" => false,
                "resource_type" => "image"
            ));

            $product = new Product();
            $product->name = $request->name;
            $product->price = $request->price;
            $product->image = $image->getResult();
            $product->save();

            DB::commit();
            return response()->json([
                'message' => 'Product created successfully',
                'data' => ['product' => $product]
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Product not created',
                'data' => ['error' => $e->getMessage()]
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $validationRules = [
            'name' => 'required|string',
            'price' => 'required|numeric',
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ];

        $this->validate($request, $validationRules);

        $product = Product::find($id);
        if (!$product) {
            throw new NotFoundHttpException('Product not found');
        }

        try {
            DB::beginTransaction();

            if ($request->image) {
                app('Cloudder')::delete($product->image->public_id);
            }

            $image = app('Cloudder')::upload($request->image, null, array(
                "folder" => "products",
                "overwrite" => false,
                "invalidate" => false,
                "format" => "jpg",
                "quality" => "auto",
                "secure" => false,
                "resource_type" => "image"
            ));

            $product->name = $request->name;
            $product->price = $request->price;
            $product->image = $image->getResult();
            $product->save();

            DB::commit();
            return response()->json([
                'message' => 'Product updated successfully',
                'data' => ['product' => $product]
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Product not updated',
                'data' => ['error' => $e->getMessage()]
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $product = Product::find($id);
            if (!$product) {
                throw new NotFoundHttpException('Product not found');
            }
            DB::beginTransaction();
            if($product->image) {
                app('Cloudder')::delete($product->image->public_id);
            }
            $product->delete();
            DB::commit();
            return response()->json([
                'message' => 'Product deleted successfully',
                'data' => ['product' => $product]
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Product not deleted',
                'data' => ['error' => $e->getMessage()]
            ], 500);
        }
    }
}
