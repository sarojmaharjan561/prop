<?php

namespace App\Http\Controllers;

use App\Models\BillType;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class BillTypeController extends Controller
{
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
        return view('bill_type');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $attributes = $request->validate([
            'type' => 'required|unique:bill_types,type',
            'description' => 'required',
        ]);

        $attributes['created_by'] = Auth::id();

        BillType::create($attributes);

        return response()->json(['success' => 'Successfully']);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\BillType  $billType
     * @return \Illuminate\Http\Response
     */
    public function show(BillType $billType)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\BillType  $billType
     * @return \Illuminate\Http\Response
     */
    public function edit(BillType $billType)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $attributes = $request->validate([
            'type' => ['required', Rule::unique('bill_types')->ignore($request->id)],
            'description' => 'required',
        ]);

        $attributes['updated_by'] = Auth::id();
        $bill_type = BillType::find($request->id);

        $bill_type->update($attributes);

        return response()->json(['success' => 'Successfully']);
    }

    /**
     * Remove the specified resource from storage.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        BillType::find($request->id)->delete();
        return response()->json(['success'=>'Successfully']);
    }

    public function getBillTypes()
    {
        $bill_types = BillType::all('created_at', 'type', 'description', 'id')->sortByDesc('id')->values()->all();

        $data = [
            'bill_types' => $bill_types,
        ];
        return $data;
    }

    public function getBillType(Request $request)
    {
        $bill_type = BillType::find($request->id);

        $data = [
            'bill_type' => $bill_type,
        ];
        return $data;
    }
}
