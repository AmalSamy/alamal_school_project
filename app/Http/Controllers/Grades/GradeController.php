<?php

namespace App\Http\Controllers\Grades;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Grade;
use App\Models\GradeSection;
use App\Models\Section;
use App\Models\Stage;
use Yajra\DataTables\Facades\DataTables;

class GradeController extends Controller
{
    function index()
    {
        return view('dashboard.grades.index');
    }

    function create()
    {
        $stages = Stage::all();
        return view('dashboard.grades.create', compact('stages'));
    }

    function getdata(Request $request)
    {
        $grades = Grade::query();
        return DataTables::of($grades)->addIndexColumn()->addColumn('action', function ($qur) {
            if ($qur->status == 'active') {
                return '<div data-bs-toggle="modal" data-grade-id="' . $qur->id . '"data-bs-target="#sectionModal" data-grade="' . $qur->tag . '" class="d-flex align-items-center theme-icons cursor-pointer rounded">
							<div class="font-22">	<i class="fadeIn animated bx bx-message-square-add"></i>
							</div>

						</div>';
            }
            return '-';
        })->addColumn('stage', function ($qur) {
            return $qur->stage->name;
        })->addColumn('status', function ($qur) {
            if ($qur->status == 'active') {
                return 'مفعل';
            }
            return 'غير مفعل';
        })->make(true);
    }

    function add(Request $request)
    {

        $request->validate([
            'name' => 'required',
            'tag' => 'required',
            'stage' => 'required',
            'status' => 'required'
        ], [
            'status.required' => 'حقل الحالة مطلوب',
            'name.required' => 'حقل الاسم مطلوب',
            'stage.required' => 'حقل المرحلة مطلوب',
            'tag.required' => 'حقل المرحلة مطلوب',



        ]);

        $stage_id = Stage::getIdByTag($request->stage);
        $status = Grade::getStatusByCode($request->status);
        $grade = Grade::query()->where('tag', $request->tag)->first();
        $grade->update([
            'name' => $request->name,
            'tag' => $request->tag,
            'stage_id' => $stage_id,
            'status' => $status,

        ]);

        return response()->json([
            'success' => 'تمت العملية بنجاح'
        ]);
    }




    
    function getactive()
    {
        $actives = Grade::query()->where('status', 'active')->pluck('tag');
        return response()->json([
            'tags' => $actives
        ]);
    }


    function addsection(Request $request)
    {
        // dd($request->all());
        if ($request->status == '1') {
            $status = 'active';
        } else {
            $status = 'inactive';
        }
        $section = Section::query()->where('name', $request->section)->first();
        $grade = Grade::query()->where('tag', $request->gradetag)->first();
        GradeSection::query()->updateOrCreate([
            'grade_id' => $grade->id,
            'section_id' => $section->id,
        ], [
            'status' => $status,
        ]);

        return response()->json([
            'success' => 'تمت العملية بنجاح'
        ]);
    }

    function getactivesection(Request $request)
    {
        $actives = GradeSection::query()->where('status', 'active')->where('grade_id', $request->gradeId)->get()->pluck('section.name');
        return response()->json([
            'names' => $actives
        ]);
    }

    function getactivestage()
    {
        $actives = Stage::query()->where('status', 'active')->pluck('tag');
        return response()->json([
            'tags' => $actives
        ]);
    }

    function changemaster(Request $request)
    {
        $stage = Stage::query()->where('tag', $request->tag)->first();
        $gradesActive = Grade::query()->where('stage_id', $stage)->where('status', 'active')->get();
        // dd($gradesActive);
        if ($request->status == 1) {
            $stage->update([
                'status' => 'active'
            ]);
        } else {
            $stage->update([
                'status' => 'inactive'
            ]);


            foreach ($gradesActive as $g) {
                $g->update([
                    'status' => 'inactive',
                ]);
            }
        }
        return response()->json([
            'success' => 'تمت العملية بنجاح'
        ]);
    }
}
