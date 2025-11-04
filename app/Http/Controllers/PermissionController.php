<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            if(is_array($request->search) && $request->search["value"] != null){
                $values = Permission::where('name', "like", '%' . $request->search["value"] . '%')->get();
            }else if($request->search != null && !is_array($request->search)){
                $values = Permission::where('name', "like", '%' . $request->search . '%')->get();
            }else{
                $values = Permission::all();
            }

            return datatables()->of($values)->toJson();
        }

        return view('permission.index');
    }

}
