<?php

namespace App\Http\Controllers;

use App\Models\Commune;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

use App\Exports\CommuneExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Events\ExportDone;

class CommuneController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $values = Commune::with(["region"])
                            ->where(function ($query) {
                                if (request()->has('search') && !is_array(request()->search)) {
                                    $query->where('name', 'like', "%" . request('search') . "%");
                                }
                                if (request()->has('region_id') ) {
                                    $query->where('region_id', '=', request('region_id'));
                                }
                            })->get();

            return datatables()->of($values)->toJson();
        }

        //return view('config.commune', ["title" => "Comunas"]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
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
            Commune::create($request->all());

            return response()->json([
                'status' => 200,
                'errors' => $validator->messages(),
            ]);
        }
    }

    public function show(Commune $commune)
    {
        //
    }

    public function edit(Commune $commune)
    {
        //
    }

    public function update(Request $request, Commune $commune)
    {
        $validator = $this->validator($request, $commune->id);
        $error = $validator->errors();
        if ($error->first()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->messages(),
            ]);
        } else {
            //codigo si no tiene error
            Commune::find($commune->id)->update(request()->all());

            return response()->json([
                'status' => 200,
                'errors' => $validator->messages(),
            ]);
        }
    }

    public function destroy(Commune $commune)
    {
        $deleted = $commune->delete();
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
            'name' => ['required',Rule::unique('communes')->ignore($id),],
            'region_id' => 'required|not_in:0',
            'city_id' => 'required|not_in:0',
            'abbreviation' => 'required|max:15',
        ];


        $messages =  [
            'region_id.required' => 'Debe ingresar Región',
            'city_id.required' => 'Debe ingresar Ciudad',
            'name.required' => 'Debe ingresar Nombre',
            'abbreviation.required' => 'Debe ingresar Abreviacion',
            'abbreviation.max' => 'Abreviación: El maximo de caracteres debe ser 15',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        return $validator;
    }

    public function activate(Request $request){
        $commune= Commune::withTrashed()->find($request->id);
        $commune->restore();
    }

    public function export()
    {
        $location = "Comunas";
        $filename = $location.'_'.time() .'.xlsx';
        //creo la notificacion para cuando se recarga la pagina
        $data["json"] = '{"filename": "'.$filename.'", "location": "'.$location.'"}';
        $data["ready"] = false;
        $notification = request()->user()->notifications()->create($data);
        //coloco en fila la creacion del excel
        (new CommuneExport)->queue($filename);
        //creo una cola de la exportacion done....
        event(new ExportDone($notification->id, $filename, $location));
        return back()->withSuccess('Export started!');
    }


}
