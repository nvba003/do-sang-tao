<?php

namespace App\Http\Controllers;

use App\Models\OrderSapo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderSapoController extends Controller
{
    public function submit(Request $request)
    {
        // Kiểm tra JWT đã được xác thực bởi middleware 'auth:api'
        $user = Auth::user();

        // Lưu dữ liệu vào database
        $data = $request->all(); // Hoặc sử dụng $request->input('key') để lấy dữ liệu cụ thể
        // Thực hiện lưu dữ liệu vào table tương ứng
        $order = OrderSapo::create($data);//lưu dữ liệu Sử Dụng Phương Thức create Trong Model

         // Kiểm tra xem dữ liệu đã được lưu thành công không
        if ($order) {
            return response()->json(['message' => 'Data saved successfully', 'data' => $data]);
        } else {
            return response()->json(['message' => 'Failed to save order'], 500);
        }

    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\OrderSapo  $orderSapo
     * @return \Illuminate\Http\Response
     */
    public function show(OrderSapo $orderSapo)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\OrderSapo  $orderSapo
     * @return \Illuminate\Http\Response
     */
    public function edit(OrderSapo $orderSapo)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\OrderSapo  $orderSapo
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, OrderSapo $orderSapo)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\OrderSapo  $orderSapo
     * @return \Illuminate\Http\Response
     */
    public function destroy(OrderSapo $orderSapo)
    {
        //
    }
}
