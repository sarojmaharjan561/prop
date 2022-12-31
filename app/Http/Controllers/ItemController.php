<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class ItemController extends Controller
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
        return view('item');
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
            'name' => 'required|unique:items,name',
            'type_id' => 'required',
            'last_price' => '',
            'description' => '',
        ]);

        $attributes['created_by'] = Auth::id();
        // dd($attributes);

        Item::create($attributes);

        return response()->json(['success' => 'Successfully']);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Item  $item
     * @return \Illuminate\Http\Response
     */
    public function show(Item $item)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Item  $item
     * @return \Illuminate\Http\Response
     */
    public function edit(Item $item)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Item  $item
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Item $item)
    {
        $attributes = $request->validate([
            'name' => ['required', Rule::unique('items')->ignore($request->id)],
            'type_id' => 'required',
            'last_price' => '',
            'description' => '',
        ]);

        $attributes['updated_by'] = Auth::id();
        $item_type = Item::find($request->id);

        $item_type->update($attributes);

        return response()->json(['success' => 'Successfully']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Item  $item
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        Item::find($request->id)->delete();
        return response()->json(['success'=>'Successfully']);
    }

    public function getItems()
    {
        $items = Item::with(['itemType:type,id'])->select('created_at','type_id','name','last_price','description','id')->get()->sortByDesc('id')->values();
    
        $data = [
            'items' => $items,
        ];
        return $data;
    }

    public function getItem(Request $request)
    {
        $item = Item::find($request->id);

        $data = [
            'item' => $item,
        ];
        return $data;
    }
}
