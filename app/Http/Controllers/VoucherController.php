<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Voucher;
use App\Models\VoucherBuyer;
use App\Models\Buyer;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\DB;

class VoucherController extends Controller
{
    public function index() {
        $vouchers = Voucher::all();
        return response()->json([
            'message' => 'Vouchers retrieved successfully',
            'data' => ['vouchers' => $vouchers]
        ], 200);
    }

    public function show($id) {
        $voucher = Voucher::find($id);
        if (!$voucher) {
            throw new NotFoundHttpException('Voucher not found');
        }
        return response()->json([
            'message' => 'Voucher retrieved successfully',
            'data' => ['voucher' => $voucher]
        ], 200);
    }

    public function store(Request $request) {
        $validationRules = [
            'name' => 'required|string',
            'code' => 'required|string|min:5|unique:vouchers',
            'min_order' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'subtractor' => 'required|numeric|min:0',
            'description' => 'required|string',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',    
        ];

        $this->validate($request, $validationRules);
        try {
            DB::beginTransaction();
            $voucher = new Voucher();
            $voucher->name = $request->name;
            $voucher->code = $request->code;
            $voucher->min_order = $request->min_order;
            $voucher->description = $request->description;
            $voucher->start_date = $request->start_date;
            $voucher->end_date = $request->end_date;
            $voucher->subtractor = $request->subtractor;

            $image = app('Cloudder')::upload($request->image, null, array(
                "folder" => "vouchers",
                "overwrite" => false,
                "invalidate" => false,
                "format" => "jpg",
                "quality" => "auto",
                "secure" => false,
                "resource_type" => "image"
            ));

            $voucher->image = $image->getResult();
            $voucher->save();
            DB::commit();
            return response()->json([
                'message' => 'Voucher created successfully',
                'data' => ['voucher' => $voucher]
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Voucher not created',
                'data' => [ 'error' => $e->getMessage()]
            ], 500);
        }
    }

    public function update(Request $request, $id) {
        $validationRules = [
            'name' => 'required|string',
            'code' => 'required|string|min:5|unique:vouchers,code,'.$id,
            'min_order' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'description' => 'required|string',
            'subtractor' => 'required|numeric|min:0',
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ];

        $this->validate($request, $validationRules);
        try {
            DB::beginTransaction();
            $voucher = Voucher::find($id);
            if (!$voucher) {
                throw new NotFoundHttpException('Voucher not found');
            }
            $voucher->name = $request->name;
            $voucher->code = $request->code;
            $voucher->description = $request->description;
            $voucher->min_order = $request->min_order;
            $voucher->start_date = $request->start_date;
            $voucher->end_date = $request->end_date;
            $voucher->subtractor = $request->subtractor;

            if($request->hasFile('image')) {
                if($voucher->image) {
                    app('Cloudder')::delete($voucher->image->public_id);
                }
                $image = app('Cloudder')::upload($request->image, null, array(
                    "folder" => "vouchers",
                    "overwrite" => false,
                    "invalidate" => false,
                    "format" => "jpg",
                    "quality" => "auto",
                    "secure" => false,
                    "resource_type" => "image"
                ));

                $voucher->image = $image->getResult();
            }
            
            $voucher->save();
            DB::commit();
            return response()->json([
                'message' => 'Voucher updated successfully',
                'data' => ['voucher' => $voucher]
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Voucher not updated',
                'data' => [ 'error' => $e->getMessage()]
            ], 500);
        };
    }

    public function destroy($id) {
        $voucher = Voucher::find($id);
        if (!$voucher) {
            throw new NotFoundHttpException('Voucher not found');
        }
        try {
            DB::beginTransaction();
            $voucher->delete();
            DB::commit();
            return response()->json([
                'message' => 'Voucher deleted successfully',
                'data' => ['voucher' => $voucher]
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Voucher not deleted',
                'data' => [ 'error' => $e->getMessage()]
            ], 500);
        };
    }

    public function addVoucherToBuyer(Request $request, $buyerId, $id) {
        try {
            DB::beginTransaction();
            $voucherBuyer = VoucherBuyer::where('voucher_id', $id)->where('buyer_id', $buyerId)->first();
            if (!$voucherBuyer) {
                $voucherBuyer = new VoucherBuyer();
                $voucherBuyer->voucher_id = $id;
                $voucherBuyer->buyer_id = $buyerId;
                $voucherBuyer->save();
            }
            DB::commit();
            return response()->json([
                'message' => 'Voucher added to voucher buyer successfully',
                'data' => ['voucher' => $voucherBuyer]
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Voucher not added to voucher buyer',
                'data' => [ 'error' => $e->getMessage()]
            ], 500);
        };
    }

    public function removeVoucherFromBuyer(Request $request, $buyerId, $id) {
        try {
            DB::beginTransaction();
            $voucherBuyer = VoucherBuyer::where('voucher_id', $id)->where('buyer_id', $buyerId)->first();
            if (!$voucherBuyer) {
                throw new NotFoundHttpException('Voucher not found');
            }

            if($voucherBuyer->image)
                app('Cloudder')::destroy($voucherBuyer->image->public_id);

            $voucherBuyer->delete();
            DB::commit();
            return response()->json([
                'message' => 'Voucher removed from voucher buyer successfully',
                'data' => ['voucher' => $voucherBuyer]
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Voucher not removed from voucher buyer',
                'data' => [ 'error' => $e->getMessage()]
            ], 500);
        };
    }

    public function getVouchersByBuyer($buyerId) {
        $buyer = Buyer::find($buyerId);
        if (!$buyer) {
            throw new NotFoundHttpException('Buyer not found');
        }
        $vouchers = $buyer->vouchers;
        return response()->json([
            'message' => 'Vouchers list',
            'data' => ['vouchers' => $vouchers]
        ], 200);
    }

    public function getVoucherByBuyerLogin() {
        $buyer = Auth::user()->buyer;
        if (!$buyer) {
            throw new AuthorizationException('You are not authorized to access this resource');
        }
        $vouchers = $buyer->vouchers;
        return response()->json([
            'message' => 'Vouchers list',
            'data' => ['vouchers' => $vouchers]
        ], 200);
    }
}