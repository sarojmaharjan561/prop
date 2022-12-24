<?php

namespace App\Http\Controllers;

use App\Models\ItemType;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class ItemTypeController extends Controller
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
        return view('item_type');
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
            'type' => 'required|unique:item_types,type',
            'description' => 'required',
        ]);

        $attributes['created_by'] = Auth::id();

        ItemType::create($attributes);

        return response()->json(['success' => 'Successfully']);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ItemType  $itemType
     * @return \Illuminate\Http\Response
     */
    public function show(ItemType $itemType)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ItemType  $itemType
     * @return \Illuminate\Http\Response
     */
    public function edit(ItemType $itemType)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ItemType  $itemType
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $attributes = $request->validate([
            'type' => ['required', Rule::unique('item_types')->ignore($request->id)],
            'description' => 'required',
        ]);

        $attributes['updated_by'] = Auth::id();
        $item_type = ItemType::find($request->id);

        $item_type->update($attributes);

        return response()->json(['success' => 'Successfully']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ItemType  $itemType
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        ItemType::find($request->id)->delete();
        return response()->json(['success'=>'Successfully']);
    }

    public function getItemTypes()
    {
        $item_types = ItemType::all('created_at', 'type', 'description', 'id')->sortByDesc('id')->values()->all();

        $data = [
            'item_types' => $item_types,
        ];
        return $data;
    }

    public function getItemType(Request $request)
    {
        $item_type = ItemType::find($request->id);

        $data = [
            'item_type' => $item_type,
        ];
        return $data;
    }
}
