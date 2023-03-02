<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\BillDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BillController extends Controller
{
    public function index()
    {
        return view('bill.index');
    }
    
    public function create()
    {
        return view('bill.create');
    }

    public function store(Request $request)
    {
        // dd(json_decode(stripslashes($request['items'])));
        // dd($request->all());
        $attributes = $request->validate([
            'type_id' => 'required',
            'date' => 'required',
            'paid_date' => 'required',
            'shop_name' => 'required',
            'sub_total' => 'required',
            'discount' => 'required',
            'total_amount' => 'required',
            'description' => '',
            'items' => 'required|array|min:1',
            
        ]);

        $attributes['created_by'] = Auth::id();
        
        $bill = Bill::create($attributes);
        $items = $request['items'];
        
        foreach ($items as $key => $item) {
            // dd($item);
            $data = [
                'bill_id' => $bill->id,
                'item' => $item['id'],
                'rate' => $item['rate'],
                'quantity' => $item['quantity'],
                'amount' => $item['amount'],
                'created_by' => Auth::id()
            ];
            BillDetail::create($data);
        }        
        return response()->json(['success' => 'Successfully']);
    }
}
