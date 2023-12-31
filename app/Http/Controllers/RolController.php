<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;
use Yajra\Datatables\Datatables;

class RolController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $columns = array(
            array('data'=>'id'),
            array('data'=>'name'),
        );
        $head = array(
            'ID',
            'Name',
        );
        return view('rol.index',[
            'columns'=>$columns,
            'head'=>$head
        ]);
    }
    public function datatables(){
        $table = Datatables::of(Role::all());
        $table->addColumn('action', function($row){
            $url = url('rol').'/'.$row['id'];
            //sreturn view('layouts.buttons_datatables',['id'=>$row['id'],'url'=>$url]);
        })->rawColumns(['action']);
        return $table->make(true);
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
        $all =  $request->all();
        $validator = Validator::make($all,[
            'name' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()->all()],422);
        }else{
            $role = Role::create(['name' => $all['name']]);
            return response()->json(['success'=>'Rol creado con exito','reload'=>1]);
        }
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {  
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $rol = Role::find($id);
        return view('rol.edit',['rol'=>$rol]);
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $all = $request->all();
        $rol = Role::find($id);
        $validator = Validator::make($all,[
            'name' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()->all()],422);
        }else if (Role::where('name', '=', $all['name'])->where('id','!=',$rol->id)->count() > 0) {
            return response()->json(['error'=>array('El rol '.$all['name'].' ya existe')],422);
        }else{
            $rol->name = $all['name'];
            $rol->save();
            return response()->json(['success'=>'Rol actualizado con exito','reload'=>1]);
        }
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $rol = Role::find($id);
        if (!empty($rol)) {
            $rol->delete();
            return response()->json(['success'=>'Registro eliminado con exito','reload'=>1]);
        }
    }
}