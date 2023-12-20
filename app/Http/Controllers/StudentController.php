<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;
use DataTables;

class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Student::latest()->get();
            return Datatables::of($data)
                ->addColumn('status', function ($row) {
                    return $row->status;
                })
                ->addColumn('action', function ($value) {
                    return view('siswa.action',compact('value'));
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('siswa.index');
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
        $validatedData = $request->validate([
            'nama_siswa' => 'required',
            'class' => 'required|in:9,10,11,12',
        ]);

        Student::create([
            'name' => $validatedData['nama_siswa'],
            'class' => $request->class,
            'status' => 1, 
        ]);

        return response()->json(['success'=>'Siswa berhasil disimpan.']);
    }

    /**
     * Display the specified resource.
     */
    public function show(Student $student)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Student $student)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Student $student)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        Student::find($id)->delete();
        return response()->json(['success'=>'Siswa berhasil dihapus.']);
    }

    public function updateStatusSiswa(Request $request,$id)
    {
        $student = Student::findOrFail($id);
        // dd($request->all());
        $request->validate([
            'status' => 'required|boolean',
        ]);

        $student->update([
            'status' => $request->status,
        ]);

        return response()->json(['success' => 'Status siswa berhasil diperbarui.']);
    }
}
