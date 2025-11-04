<?php

namespace App\Http\Controllers;

use App\Models\AttributeType;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class AttributeTypeController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $search = null;
            if (is_array($request->search) && $request->search['value'] != null) $search = $request->search['value'];
            elseif ($request->search && !is_array($request->search)) $search = $request->search;

            $values = AttributeType::query()
                ->when($search, fn($q)=>$q->where('name','like',"%$search%")
                    ->orWhere('code','like',"%$search%"))
                ->orderBy('sort_order')->get();

            return datatables()->of($values)->toJson();
        }

        return view('attribute.type.index');
    }

    public function store(Request $request)
    {
        $v = $this->validator($request, 0);
        if ($v->fails()) {
            return response()->json(['status'=>400,'errors'=>$v->messages()]);
        }

        DB::beginTransaction();
        try {
            $input = $request->all();
            $input['active'] = $request->boolean('active');
            AttributeType::create($input);
            DB::commit();
            return response()->json(['status'=>200]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['status'=>400,'errors'=>$e->getMessage()]);
        }
    }

    public function update(Request $request, AttributeType $attribute_type)
    {
        $v = $this->validator($request, $attribute_type->id);
        if ($v->fails()) {
            return response()->json(['status'=>400,'errors'=>$v->messages()]);
        }

        DB::beginTransaction();
        try {
            $input = $request->all();
            $input['active'] = $request->boolean('active');
            $attribute_type->update($input);
            DB::commit();
            return response()->json(['status'=>200]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['status'=>400,'errors'=>$e->getMessage()]);
        }
    }

    public function destroy(AttributeType $attribute_type)
    {
        $attribute_type->delete();
        return response()->json(['status'=>200,'message'=>'Eliminado correctamente']);
    }

    protected function validator(Request $request, $id)
    {
        return Validator::make($request->all(), [
            'code'       => ['required','max:50', Rule::unique('attribute_types','code')->ignore($id)],
            'name'       => ['required','max:150'],
            'description'=> ['nullable','max:255'],
            'sort_order' => ['nullable','integer'],
            'active'     => ['nullable'],
        ], [
            'code.required' => 'Debe ingresar código',
            'name.required' => 'Debe ingresar nombre',
        ]);
    }
}
