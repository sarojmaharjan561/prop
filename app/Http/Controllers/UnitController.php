<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreUnitRequest;
use App\Http\Requests\UpdateUnitRequest;

class UnitController extends Controller
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
        return view('unit');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreUnitRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreUnitRequest $request)
    {
        $attributes = $request->validate([
            'name' => 'required|unique:units,name',
            'description' => 'required'
        ]);

        $attributes['created_by'] = Auth::id();

        Unit::create($attributes);
                                        
        // $unit = new Unit;
        // $unit->name = $request->name;
        // $unit->description = $request->description;
        // $unit->created_by = Auth::id();
        // $unit->save();

        return response()->json(['success'=>'Successfully']);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Unit  $unit
     * @return \Illuminate\Http\Response
     */
    public function show(Unit $unit)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Unit  $unit
     * @return \Illuminate\Http\Response
     */
    public function edit(Unit $unit)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateUnitRequest  $request
     * @param  \App\Models\Unit  $unit
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateUnitRequest $request)
    {
        $attributes = $request->validate([
            'name' => ['required',Rule::unique('units')->ignore($request->id),],
            'description' => 'required'
        ]);
        
        $attributes['updated_by'] = Auth::id();
        $unit = Unit::find($request->id);

        $unit->update($attributes);                              

        return response()->json(['success'=>'Successfully']);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Unit  $unit
     * @return \Illuminate\Http\Response
     */
    public function destroy(UpdateUnitRequest $request)
    {
        Unit::find($request->id)->delete();
        return response()->json(['success'=>'Successfully']);
    }

    public function getUnits()
    {
        $units = Unit::all('created_at','name','description','id')->sortByDesc('id')->values()->all();
        
        $data = [
            'units' => $units
        ];
        return $data;
    }

    public function getUnit(StoreUnitRequest $request)
    {
        $unit = Unit::find($request->id);
        
        $data = [
            'unit' => $unit
        ];
        return $data;
    }

}
