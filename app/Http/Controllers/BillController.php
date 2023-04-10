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
                'item_id' => $item['id'],
                'rate' => $item['rate'],
                'quantity' => $item['quantity'],
                'amount' => $item['amount'],
                'created_by' => Auth::id()
            ];
            BillDetail::create($data);
        }        
        return response()->json(['success' => 'Successfully']);
    }

    public function getBills()
    {
        $bills = Bill::Select('created_at','shop_name','date','sub_total','discount','total_amount','id')
                    ->get()
                    ->sortByDesc('id')
                    ->values();
    
        $data = [
            'bills' => $bills,
        ];
        return $data;
    }

    public function getBill(Request $request)
    {
        $bill = Bill::with(['billType:id,type'])->find($request->id);
        $bill_detail = BillDetail::with(['items:id,name',])
                                    ->select('created_at','id','bill_id','item_id','rate','quantity','amount')
                                    ->where('bill_id', $request->id)->get();
        $bill_data['bill'] = $bill; 
        $bill_data['bill_detail'] = $bill_detail; 

        $data = [
            'bill' => $bill_data,
        ];
        return $data;
    }

    public function update(Request $request)
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

        $attributes['updated_by'] = Auth::id();

        unset($attributes['items']);

        // dd($attributes);
        
        $bill = Bill::where('id', $request['update_bill_id'])
                ->update($attributes);

        $items = $request['items'];

        BillDetail::where('bill_id', $request['update_bill_id'])->delete();
        
        foreach ($items as $key => $item) {
            // dd($item);
            $data = [
                'bill_id' => $request['update_bill_id'],
                'item_id' => $item['id'],
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
