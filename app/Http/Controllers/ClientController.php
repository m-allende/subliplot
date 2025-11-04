<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Address;
use App\Models\Phone;
use App\Models\Email;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            if(is_array($request->search) && $request->search["value"] != null){
                $values = Client::with(["lastAddress", "lastEmail", "lastPhone"])->where('name', "like", '%' . $request->search["value"] . '%')->get();
            }else if($request->search != null && !is_array($request->search)){
                $values = Client::with(["lastAddress", "lastEmail", "lastPhone"])->where('name', "like", '%' . $request->search . '%')->get();
            }else{
                if($request->length){
                    $values = Client::with(["lastAddress", "lastEmail", "lastPhone"])->limit($request->length)->get();
                }else{
                    $values = Client::with(["lastAddress", "lastEmail", "lastPhone"])->get();    
                }
            }

            return datatables()->of($values)->toJson();
        }

        return view('client.index');
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

                $client = Client::create($request->all());

                $address = new Address();
                $address->address = $request["address"];
                $address->latitude = $request["addressLatitude"];
                $address->longitude = $request["addressLongitude"];
                $client->addresses()->save($address);

                $phone = new Phone();
                $phone->phone = $request["phone"];
                $client->phones()->save($phone);

                $email = new Email();
                $email->email = $request["email"];
                $client->emails()->save($email);

                DB::commit();

                return response()->json([
                    'status' => 200,
                    'errors' => $validator->messages(),
                    'client' => $client
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
    public function show(Client $client)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Client $client)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Client $client)
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
                $client = Client::find($client->id)->update(request()->all());

                $address = new Address();
                $address->address = $request["address"];
                $address->latitude = $request["addressLatitude"];
                $address->longitude = $request["addressLongitude"];
                $client->addresses()->save($address);

                $phone = new Phone();
                $phone->phone = $request["phone"];
                $client->phones()->save($phone);

                $email = new Email();
                $email->email = $request["email"];
                $client->emails()->save($email);

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
    public function destroy(Client $client)
    {
        $deleted = $client->delete();
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
            'identification' => ['required',Rule::unique('clients')->ignore($id),],
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
