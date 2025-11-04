<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            if(is_array($request->search) && $request->search["value"] != null){
                $values = Purchase::with(["supplier", "products" => function($query) {
                                    $query->with(["brand"]);
                            }])->where('name', "like", '%' . $request->search["value"] . '%')->get();
            }else if($request->search != null && !is_array($request->search)){
                $values = Purchase::with(["supplier", "products" => function($query) {
                                    $query->with(["brand"]);
                            }])->where('name', "like", '%' . $request->search . '%')->get();
            }else{
                $values = Purchase::with(["supplier", "products" => function($query) {
                                    $query->with(["brand"]);
                            }])->get();
            }

            return datatables()->of($values)->toJson();
        }

        return view('purchase.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $date = explode("-",$request["date"]);
            $request["date"] = $date[2]."-".$date[1]."-".$date[0];
            $purchase = Purchase::create($request->all());
            $data = json_decode($request["detail"]);
            foreach ($data as $key => $value) {
                $purchase->products()->attach($value[0], ['quantity' => $value[1], 'price'=> $value[2], 'total'=> $value[3]]);
            }

            //DB::rollback();
            DB::commit();

            return response()->json([
                'status' => 200,
                'errors' => '',
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'status' => 400,
                'errors' => $e->getMessage()
            ]);
        }

    }

    /**
     * Display the specified resource.
     */
    public function show(Purchase $purchase)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Purchase $purchase)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Purchase $purchase)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Purchase $purchase)
    {
        $deleted = $purchase->delete();
        if ($deleted) {
            return response()->json(['success' => true, 'message' => 'Compra eliminado correctamente.']);
        }
    }
}
