<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Models\Address;
use App\Models\Phone;
use App\Models\Email;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class SupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            if(is_array($request->search) && $request->search["value"] != null){
                $values = Supplier::with(["lastAddress", "lastEmail", "lastPhone"])->where('name', "like", '%' . $request->search["value"] . '%')->get();
            }else if($request->search != null && !is_array($request->search)){
                $values = Supplier::with(["lastAddress", "lastEmail", "lastPhone"])->where('name', "like", '%' . $request->search . '%')->get();
            }else{
                $values = Supplier::with(["lastAddress", "lastEmail", "lastPhone"])->get();
            }

            return datatables()->of($values)->toJson();
        }

        return view('supplier.index');
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

                $supplier = Supplier::create($request->all());

                $address = new Address();
                $address->address = $request["address"];
                $address->latitude = $request["addressLatitude"];
                $address->longitude = $request["addressLongitude"];
                $supplier->addresses()->save($address);

                $phone = new Phone();
                $phone->phone = $request["phone"];
                $supplier->phones()->save($phone);

                $email = new Email();
                $email->email = $request["email"];
                $supplier->emails()->save($email);

                DB::commit();

                return response()->json([
                    'status' => 200,
                    'errors' => $validator->messages(),
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
    public function show(Supplier $supplier)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Supplier $supplier)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */

    public function update(Request $request, Supplier $supplier)
    {
        $validator = $this->validator($request, $client->id);
        $error = $validator->errors();
        if ($error->first()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->messages(),
            ]);
        } else {
            DB::beginTransaction();
            try {
                //codigo si no tiene error
                $supplier = Supplier::find($supplier->id)->update(request()->all());

                $address = new Address();
                $address->address = $request["address"];
                $address->latitude = $request["addressLatitude"];
                $address->longitude = $request["addressLongitude"];
                $supplier->addresses()->save($address);

                $phone = new Phone();
                $phone->phone = $request["phone"];
                $supplier->phones()->save($phone);

                $email = new Email();
                $email->email = $request["email"];
                $supplier->emails()->save($email);

                DB::commit();

                return response()->json([
                    'status' => 200,
                    'errors' => $validator->messages(),
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
    public function destroy(Supplier $supplier)
    {
        $deleted = $supplier->delete();
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
            'identification' => ['required',Rule::unique('suppliers')->ignore($id),],
            'name' => ['required'],
        ];


        $messages =  [
            'name.required' => 'Debe ingresar Nombre',
            'identification.required' => 'Debe ingresar Identificación',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        return $validator;
    }
}
