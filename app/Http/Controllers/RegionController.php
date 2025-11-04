<?php

namespace App\Http\Controllers;

use App\Models\Region;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

use App\Exports\RegionExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Events\ExportDone;

use DataTables;

class RegionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {

            $values = Region::where(function ($query) {
                            if (request()->has('search') && !is_array(request()->search)) {
                                $query->where('name', 'like', "%" . request('search') . "%");
                            }
                        })->get();

            //return datatables()->of($values)->toJson();
            return datatables()->of($values)->toJson();
        }

        //return view('config.region', ["title" => "Regiones"]);
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
            Region::create($request->all());

            return response()->json([
                'status' => 200,
                'errors' => $validator->messages(),
            ]);
        }
    }

    public function show(Region $region)
    {
        //
    }

    public function edit(Region $region)
    {
        //
    }

    public function update(Request $request, Region $region)
    {
        $validator = $this->validator($request, $region->id);
        $error = $validator->errors();
        if ($error->first()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->messages(),
            ]);
        } else {
            //codigo si no tiene error
            Region::find($region->id)->update(request()->all());

            return response()->json([
                'status' => 200,
                'errors' => $validator->messages(),
            ]);
        }
    }

    public function destroy(Region $region)
    {
        $deleted = $region->delete();
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
            'name' => ['required',Rule::unique('regions')->ignore($id),],
            'abbreviation' => 'required|max:15',
        ];


        $messages =  [
            'name.required' => 'Debe ingresar Nombre',
            'abbreviation.required' => 'Debe ingresar Abreviacion',
            'abbreviation.max' => 'Abreviación: El maximo de caracteres debe ser 15',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        return $validator;
    }

    public function activate(Request $request){
        $region = Region::withTrashed()->find($request->id);
        $region->restore();
    }

    public function export()
    {
        $location = "Regiones";
        $filename = $location.'_'.time() .'.xlsx';
        //creo la notificacion para cuando se recarga la pagina
        $data["json"] = '{"filename": "'.$filename.'", "location": "'.$location.'"}';
        $data["ready"] = false;
        $notification = request()->user()->notifications()->create($data);
        //coloco en fila la creacion del excel
        (new RegionExport)->queue($filename);
        //creo una cola de la exportacion done....
        event(new ExportDone($notification->id, $filename, $location));
        return back()->withSuccess('Export started!');
    }


}
