<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class ShopController extends Controller
{
    public function index()
    {
        $shops = Shop::all();
        return response()->json([
            'message' => 'Shops retrieved successfully',
            'data' => ['shops' => $shops]
        ], 200);
    }

    public function show($id)
    {
        $shop = Shop::find($id);
        if (!$shop) {
            throw new NotFoundHttpException('Shop not found');
        }
        return response()->json([
            'message' => 'Shop retrieved successfully',
            'data' => ['shop' => $shop]
        ], 200);
    }

    public function store(Request $request)
    {
        $validationRules = [
            'name' => 'required|string',
            'address' => 'required|string|min:10',
            'phone' => 'required|min:8'
        ];

        $this->validate($request, $validationRules);

        $shop = new Shop();
        $shop->name = $request->name;
        $shop->address = $request->address;
        $shop->phone = $request->phone;
        $shop->save();

        return response()->json([
            'message' => 'Shop created successfully',
            'data' => ['shop' => $shop]
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $validationRules = [
            'name' => 'required|string',
            'address' => 'required|string|min:10',
            'phone' => 'required|min:8'
        ];

        $this->validate($request, $validationRules);

        $shop = Shop::find($id);
        if (!$shop) {
            throw new NotFoundHttpException('Shop not found');
        }

        $shop->name = $request->name;
        $shop->address = $request->address;
        $shop->phone = $request->phone;
        $shop->save();

        return response()->json([
            'message' => 'Shop updated successfully',
            'data' => ['shop' => $shop]
        ], 200);
    }

    public function destroy($id)
    {
        $shop = Shop::find($id);
        if (!$shop) {
            throw new NotFoundHttpException('Shop not found');
        }
        $shop->delete();

        return response()->json([
            'message' => 'Shop deleted successfully',
            'data' => ['shop' => $shop]
        ], 200);
    }

    public function getProducts($id)
    {
        $shop = Shop::find($id);
        if (!$shop) {
            throw new NotFoundHttpException('Shop not found');
        }
        return response()->json([
            'message' => 'Products retrieved successfully',
            'data' => ['products' => $shop->products]
        ], 200);
    }

    public function getProduct($id, $productId)
    {
        $shop = Shop::find($id);
        if (!$shop) {
            throw new NotFoundHttpException('Shop not found');
        }
        $product = $shop->products->find($productId);
        if (!$product) {
            throw new NotFoundHttpException('Product not found');
        }
        return response()->json([
            'message' => 'Product retrieved successfully',
            'data' => ['product' => $product]
        ], 200);
    }

    public function addProduct(Request $request, $id)
    {
        $validationRules = [
            'product_id' => 'required|integer|exists:products,id'
        ];

        $this->validate($request, $validationRules);

        try {
        $shop = Shop::find($id);
        if (!$shop) {
            throw new NotFoundHttpException('Shop not found');
        }

        $product = Product::find($request->product_id);
        if (!$product) {
            throw new NotFoundHttpException('Product not found');
        }

        DB::beginTransaction();
        $shop->products()->create(['product_id' => $product->id]);
        DB::commit();
        return response()->json([
            'message' => 'Product added successfully',
            'data' => ['product' => $product]
        ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Product already exists in this shop',
                'data' => ['error' => $e->getMessage()]
            ], 400);
        }
    }

    public function removeProduct($id, $productId)
    {
        $shop = Shop::find($id);
        if (!$shop) {
            throw new NotFoundHttpException('Shop not found');
        }
        $product = $shop->products->find($productId);
        if (!$product) {
            throw new NotFoundHttpException('Product not found');
        }
        $product->delete();

        return response()->json([
            'message' => 'Product removed successfully',
            'data' => ['product' => $product]
        ], 200);
    }
}
