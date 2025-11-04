<?php

namespace App\Http\Controllers;

use App\Models\AttributeValue;
use App\Models\AttributeType;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class AttributeValueController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $search = null;
            if (is_array($request->search) && $request->search['value'] != null) $search = $request->search['value'];
            elseif ($request->search && !is_array($request->search)) $search = $request->search;

            $values = AttributeValue::with('type')
                ->when($search, function($q) use ($search){
                    $q->where('name','like',"%$search%")
                      ->orWhere('code','like',"%$search%")
                      ->orWhereHas('type', fn($t)=>$t->where('name','like',"%$search%"));
                })
                ->orderBy('attribute_type_id')->orderBy('sort_order')
                ->get();

            return datatables()->of($values)->toJson();
        }

        return view('attribute.value.index');
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
            AttributeValue::create($input);
            DB::commit();
            return response()->json(['status'=>200]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['status'=>400,'errors'=>$e->getMessage()]);
        }
    }

    public function update(Request $request, AttributeValue $attribute_value)
    {
        $v = $this->validator($request, $attribute_value->id);
        if ($v->fails()) {
            return response()->json(['status'=>400,'errors'=>$v->messages()]);
        }

        DB::beginTransaction();
        try {
            $input = $request->all();
            $input['active'] = $request->boolean('active');
            $attribute_value->update($input);
            DB::commit();
            return response()->json(['status'=>200]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['status'=>400,'errors'=>$e->getMessage()]);
        }
    }

    public function destroy(AttributeValue $attribute_value)
    {
        $attribute_value->delete();
        return response()->json(['status'=>200,'message'=>'Eliminado correctamente']);
    }

    protected function validator(Request $request, $id)
    {
        return Validator::make($request->all(), [
            'attribute_type_id' => ['required','exists:attribute_types,id'],
            'name'       => ['required','max:150'],
            'code'       => ['nullable','max:80',
                             Rule::unique('attribute_values','code')->ignore($id)],
            'width_mm'   => ['nullable','integer','min:0'],
            'height_mm'  => ['nullable','integer','min:0'],
            'weight_gsm' => ['nullable','integer','min:0'],
            'color_hex'  => ['nullable','max:12'],
            'sort_order' => ['nullable','integer'],
            'active'     => ['nullable'],
        ], [
            'attribute_type_id.required' => 'Debe seleccionar tipo',
            'name.required'              => 'Debe ingresar nombre',
        ]);
    }

    // Endpoint de apoyo para Select2 (tipos)
    public function typeOptions(Request $request)
    {
        $s = $request->get('search');
        $data = AttributeType::query()
            ->when($s, fn($q)=>$q->where('name','like',"%$s%")->orWhere('code','like',"%$s%"))
            ->orderBy('sort_order')
            ->get(['id','name as text']);
        return response()->json(['data'=>$data]);
    }
}
