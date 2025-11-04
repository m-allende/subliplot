<?php

namespace App\Http\Controllers;

use App\Models\Promotion;
use App\Models\Price;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class PromotionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            if(is_array($request->search) && $request->search["value"] != null){
                $values = Promotion::with(["lastPrice", "products", "services"])
                                ->where('name', "like", '%' . $request->search["value"] . '%')
                                ->orWhere('code', "like", '%' . $request->search["value"] . '%')
                                ->get();
            }else if($request->search != null && !is_array($request->search)){
                $values = Promotion::with(["lastPrice", "products", "services"])
                                ->where('name', "like", '%' . $request->search . '%')
                                ->orWhere('code', "like", '%' . $request->search . '%')
                                ->get();
            }else{
                if($request->length && !$request->start){
                    $values = Promotion::with(["lastPrice", "products", "services"])->limit($request->length)->get();
                }else{
                    $values = Promotion::with(["lastPrice", "products", "services"])->get();
                }
            }

            return datatables()->of($values)->toJson();
        }

        return view('promotion.index');
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
        $validator = $this->validator($request,0);
        $error = $validator->errors();
        if ($error->first()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->messages(),
            ]);
        } else {
            DB::beginTransaction();
            try {

                $promotion = Promotion::create($request->all());
                $data = json_decode($request["products"]);
                foreach ($data as $key => $value) {
                    $promotion->products()->attach($value[0], ['quantity' => $value[1]]);
                }

                $data = json_decode($request["services"]);
                foreach ($data as $key => $value) {
                    $promotion->services()->attach($value[0]);
                }

                $price = new Price();
                $price->price = 0;
                $promotion->prices()->save($price);

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
    }

    /**
     * Display the specified resource.
     */
    public function show(Promotion $promotion)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Promotion $promotion)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Promotion $promotion)
    {
        $validator = $this->validator($request,$promotion->id);
        $error = $validator->errors();
        if ($error->first()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->messages(),
            ]);
        } else {
            DB::beginTransaction();
            try {

                $promotion->update($request->all());

                $promotion->products()->detach();
                $data = json_decode($request["products"]);
                foreach ($data as $key => $value) {
                    $promotion->products()->attach($value[0], ['quantity' => $value[1]]);
                }

                $promotion->services()->detach();
                $data = json_decode($request["services"]);
                foreach ($data as $key => $value) {
                    $promotion->services()->attach($value[0]);
                }

                if(!$promotion->prices()->exists()){
                    $price = new Price();
                    $price->price = 0;
                    $promotion->prices()->save($price);
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
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Promotion $promotion)
    {
        $deleted = $promotion->delete();
        if ($deleted) {
            return response()->json([
                'status' => 200,
                'message' => "Eliminado Correctamente",
            ]);
        }
    }

    public function validator(Request $request, $id)
    {
        $rules = [
            'name' => ['required',Rule::unique('promotions')->ignore($id),],
            'code' => ['required',Rule::unique('promotions')->ignore($id)],
        ];


        $messages =  [
            'name.required' => 'Debe ingresar Nombre',
            'code.required' => 'Debe ingresar Código',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        return $validator;
    }
}
