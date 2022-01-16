<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Buyer;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class BuyerController extends Controller
{
    public function index()
    {
        $buyers = Buyer::all();
        return response()->json([
            'message' => 'Buyers retrieved successfully',
            'data' => ['buyers' => $buyers]
        ], 200);
    }

    public function show($id)
    {
        $buyer = Buyer::find($id);
        if (!$buyer) {
            throw new NotFoundHttpException('Buyer not found');
        }
        return response()->json([
            'message' => 'Buyer retrieved successfully',
            'data' => ['buyer' => $buyer]
        ], 200);
    }

    public function store(Request $request)
    {
        $validationRules = [
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed',
            'gender' => 'required|in:L,P',
            'address' => 'required|string|min:10',
            'phone' => 'required|min:8'
        ];

        $this->validate($request, $validationRules);

        try {
            DB::beginTransaction();

            
            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = app('hash')->make($request->password);
            $user->save();

            $buyer = new Buyer();
            $buyer->address = $request->address;
            $buyer->gender = $request->gender;
            $buyer->phone = $request->phone;
            $buyer->user_id = $user->id;
            $buyer->save();

            DB::commit();

            return response()->json([
                'message' => 'Buyer has been created',
                'data' => ['buyer' => $buyer->with('user')->get()]
            ]);

        } catch(Exception $e) {
            DB::rollBack();
            throw new Exception('Something went wrong');
        }
    }

    public function update(Request $request, $id)
    {
        $validationRules = [
            'name' => 'required|string',
            'gender' => 'required|in:L,P',
            'address' => 'required|string|min:10',
            'phone' => 'required|min:8'];

        $this->validate($request, $validationRules);

        try {
            $buyer = Buyer::find($id);
            if (!$buyer) {
                throw new NotFoundHttpException('Buyer not found');
            }
            DB::beginTransaction();
            $buyer->update($request->except('name'));

            $buyer->user()->update($request->only('name'));
            DB::commit();
            return response()->json([
                'message' => 'Buyer updated successfully',
                'data' => ['buyer' => $buyer->with('user')->get()]
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception('Something went wrong');
        }
    }

    public function destroy($id)
    {
        $buyer = Buyer::find($id);
        if (!$buyer) {
            throw new NotFoundHttpException('Buyer not found');
        }
        try {
            DB::beginTransaction();
            $buyer->user()->delete();
            $buyer->delete();
            DB::commit();
            return response()->json([
                'message' => 'Buyer deleted successfully'
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception('Something went wrong');
        }
    }
}
