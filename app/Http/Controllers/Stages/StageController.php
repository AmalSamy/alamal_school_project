<?php

namespace App\Http\Controllers\Stages;

use App\Http\Controllers\Controller;
use App\Models\Grade;
use App\Models\Stage;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class StageController extends Controller
{
      function index(){
        return view('dashboard.grades.index');
    }

    function create(){
        $stages = Stage::all();
        return view('dashboard.grades.create',compact('stages'));
    }

    function getdata(Request $request){
        $grades = Grade::query();
        return DataTables::of($grades)->addIndexColumn()->make(true);
    }

    function add(Request $request){
        $request->validate([
            'name'=>'required|unique:grades,name',
            'stage'=>'required'
        ] , [
            'name.required'=>'حقل الاسم مطلوب',
            'stage.required'=>'حقل المرحلة مطلوب',
            'name.unique'=>'حقل الاسم يجب ان يكون فريد',


        ]);
        Grade::create([
            'name'=>$request->name,
            'stage_id'=>$request->stage

        ]);

        return 'تمت الاضافة';
    }
}
