<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;




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
}