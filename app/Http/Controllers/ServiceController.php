<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\Price;
use App\Models\Photo;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            if(is_array($request->search) && $request->search["value"] != null){
                $values = Service::with(["lastPrice", "lastPhoto"])
                                ->where('name', "like", '%' . $request->search["value"] . '%')
                                ->orWhere('code', "like", '%' . $request->search["value"] . '%')
                                ->get();
            }else if($request->search != null && !is_array($request->search)){
                $values = Service::with(["lastPrice", "lastPhoto"])
                                ->where('name', "like", '%' . $request->search . '%')
                                ->orWhere('code', "like", '%' . $request->search . '%')
                                ->get();
            }else{
                if($request->length && !$request->start){
                    $values = Service::with(["lastPrice", "lastPhoto"])->limit($request->length)->get();
                }else{
                    $values = Service::with(["lastPrice", "lastPhoto"])->get();
                }
            }

            return datatables()->of($values)->toJson();
        }

        return view('service.index');
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
            $input = request()->all();
            $service = Service::create($input);

            $price = new Price();
            $price->price = 0;
            $service->prices()->save($price);

            if(isset($input["image"])){
                $image = $input["image"];
                $image = str_replace('data:image/png;base64,', '', $image);
                $image = str_replace(' ', '+', $image);
                $imageName = time() .'.'.'jpg';
                \File::put(public_path('img/upl/'). $imageName, base64_decode($image));

                $photo = new Photo();
                $photo->path = 'img/upl/'. $imageName;
                $service->photos()->save($photo);
            }

            return response()->json([
                'status' => 200,
                'errors' => $validator->messages(),
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Service $service)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Service $service)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Service $service)
    {
        $service = Service::with(["prices"])->whereId($service->id)->first();
        $validator = $this->validator($request, $service->id);
        $error = $validator->errors();
        if ($error->first()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->messages(),
            ]);
        } else {
            $input = request()->all();

            $service->update($input);

            if(!$service->prices()->exists()){
                $price = new Price();
                $price->price = 0;
                $service->prices()->save($price);
            }

            if(isset($input["image"])){
                $image = $input["image"];
                $image = str_replace('data:image/png;base64,', '', $image);
                $image = str_replace(' ', '+', $image);
                $imageName = time() .'.'.'jpg';
                \File::put(public_path('img/upl/'). $imageName, base64_decode($image));

                $photo = new Photo();
                $photo->path = 'img/upl/'. $imageName;
                $service->photos()->save($photo);
            }

            return response()->json([
                'status' => 200,
                'errors' => $validator->messages(),
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Service $service)
    {
        $deleted = $service->delete();
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
            'name' => ['required',Rule::unique('services')->ignore($id),],
            'code' => ['required',Rule::unique('services')->ignore($id)],
            'description' => ['required'],
        ];


        $messages =  [
            'name.required' => 'Debe ingresar Nombre',
            'code.required' => 'Debe ingresar Código',
            'description.required' => 'Debe ingresar Descripción',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        return $validator;
    }
}
