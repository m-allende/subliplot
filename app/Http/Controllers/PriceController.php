<?php

namespace App\Http\Controllers;

use App\Models\Price;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use DataTables;
use Yajra\DataTables\EloquentDataTable;

class PriceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            if(is_array($request->search) && $request->search["value"] != null){
                $values = Price::with(["parent"])
                                ->where('price', "like", '%' . $request->search["value"] . '%')
                                ->orWhereHas("parent", function($query) use($request){
                                    $query->where('name', "like", '%' . $request->search["value"] . '%')
                                            ->orwhere('description', "like", '%' . $request->search["value"]. '%')
                                            ->orwhere('code', "like", '%' . $request->search["value"]. '%');
                                })
                                ->get();
            }else if($request->search != null && !is_array($request->search)){
                $values = Price::with(["parent"])
                                ->where('price', "like", '%' . $request->search . '%')
                                ->orWhereHas("parent", function($query) use($request){
                                    $query->where('name', "like", '%' . $request->search. '%')
                                            ->orwhere('description', "like", '%' . $request->search. '%')
                                            ->orwhere('code', "like", '%' . $request->search. '%');
                                })
                                ->get();
            }else{
                $values = Price::with(["parent"])->get();
            }

            //return datatables()->of($values)->toJson();

            return datatables()->of($values)
                    ->addColumn('stock', function(Price $price) {
                        return $this->stock($price);
                    })
                    ->toJson();
        }

        return view('price.index');
    }

    public function stock($price){
        $stock = 0;
        if($price->parent_type == "App\\Models\\Product"){
            $purchases = DB::table('purchase_product')
                        ->selectRaw('sum(quantity) as cantidad')
                        ->where('product_id', "=", $price->parent_id)
                        ->first();
            $stock = ($purchases->cantidad == null?0:$purchases->cantidad);

            $sales = DB::table('product_sale')
                        ->selectRaw('sum(quantity) as cantidad')
                        ->where('product_id', "=", $price->parent_id)
                        ->first();
            $remove = ($sales->cantidad == null?0:$sales->cantidad);

            $stock = $stock - $remove;
        }

        return $stock;
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
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Price $price)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Price $price)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Price $price)
    {
        if($request["price"] == null || $request["price"] == ""){
            $request["price"] = 0;
        }
        DB::beginTransaction();
        try {
            Price::find($price->id)->update($request->all());

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
     * Remove the specified resource from storage.
     */
    public function destroy(Price $price)
    {
        //
    }
}
