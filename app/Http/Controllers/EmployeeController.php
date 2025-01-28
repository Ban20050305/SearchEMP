<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;


class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request) ////เพิ่มตัวแปร $request เพื่อรับค่าที่ส่งมาจากหน้าเว็บ
    {   
        $query = $request->input('search'); //ตัวแปร query จะเก็บค่าที่ได้จากการค้นหาผ่านตัวแปร search
        

        $employees = DB::table('employees') 
        ->where('first_name', 'like', '%' . $query . '%') //ค้นหาข้อมูลจากตาราง employees โดยค้นหาจากชื่อ นามสกุล และรหัสพนักงาน
        ->orWhere('last_name', 'like', '%' . $query . '%')
        ->orWhere('emp_no', '=',  $query )
        ->paginate(20)
        ->appends(['search' => $query]); //นำค่าที่ได้จากการค้นไปแนบไว้กับ URL เพื่อที่ตอนผู้ใช้กดไปหน้าอื่น ข้อมูลจากค่าที่ค้นจะยังคงอยู่

        //Log::info($employees);

        return Inertia::render('Employee/Index', [ //ส่งข้อมูลไปแสดงที่หน้าเว็บ ผ่านตัวแปร employeesและ query
            'employees' => $employees,   
            'query' => $query,
        ]);

        //return response($employees); //ส่งข้อมูลกลับไปเป็น JSON

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // ดึงรายชื่อแผนกจากฐานข้อมูล เพื่อไปแสดงให้เลือกรายการในแบบฟอร์ม
        // $departments = DB::table('departments')->select('dept_no', 'dept_name')->get();
        $departments = DB::table('departments')->get();

        // ส่งข้อมูลไปยังหน้า Inertia
        return inertia('Employee/Create', ['departments' => $departments,]);

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // แสดงข้อมูลทั้งหมดที่รับเข้ามา
        log::info($request->all());

        // ตรวจสอบข้อมูลที่ส่งมาจากฟอร์ม
        $validator = Validator::make($request->all(), [ 
           'first_name' => 'required|string|max:14',
           'last_name' => 'required|string|max:16',
           'birth_date' => 'required|date',
           'gender' => 'required|string|max:1',
           'hire_date' => 'required|date',
           'dept_no' => 'required|string|max:4',
           'photo' => 'nullable',
         ]);

    if ($validator->fails()) {
        // ถ้ามีข้อผิดพลาดในการตรวจสอบข้อมูล ให้กลับไปที่หน้าสร้างพนักงาน
        return redirect()->route('employee.create')
            ->withInput()
            ->withErrors($validator) // ใช้สำหรับ Validation Errors
            ->with('error', 'Failed to create employee. Please try again.');
    }

    // ดึงเฉพาะข้อมูลที่ผ่านการตรวจสอบแล้ว
    $validated = $request->only([
        'first_name',
        'last_name',
        'birth_date',
        'gender',
        'hire_date',
        'dept_no',
    ]);

    try {
        DB::transaction(function () use ($validated, $request) {
            // 2. หาค่า emp_no ล่าสุด
            $latestEmpNo = DB::table('employees')->max('emp_no') ?? 0;
            $newEmpno = $latestEmpNo + 1;

            // 3. เพิ่มข้อมูลลงในตาราง employees
            $employeeData = [
                'emp_no' => $newEmpno,
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'birth_date' => $validated['birth_date'],
                'gender' => $validated['gender'],
                'hire_date' => $validated['hire_date'],
            ];

            // จัดการการอัปโหลดรูปภาพ
            if ($request->hasFile('photo')) {
                $path = $request->file('photo')->store('photos', 'public');
                $employeeData['photo'] = $path;
            }

            DB::table('employees')->insert($employeeData);

            // 4. เพิ่มข้อมูลลงในตาราง dept_emp
            DB::table('dept_emp')->insert([
                'emp_no' => $newEmpno,
                'dept_no' => $validated['dept_no'],
                'from_date' => $validated['hire_date'],
                'to_date' => '9999-01-01',
            ]);
        });

        // ถ้าทุกอย่างสำเร็จ ให้กลับไปที่หน้าแสดงรายการพนักงาน
        session()->flash('success', 'Employee created successfully!');
        return redirect()->route('employee.index');

    } catch (\Illuminate\Database\QueryException $e) {
        // ข้อผิดพลาดที่เกี่ยวข้องกับฐานข้อมูล
        Log::error('Database error: ' . $e->getMessage());
        return redirect()->route('employee.create')->with('error', 'Database error occurred. Please try again.');
    } catch (\Illuminate\Http\Exceptions\PostTooLargeException $e) {
        // ข้อผิดพลาดเมื่อไฟล์มีขนาดเกินขีดจำกัด
        Log::error('File upload error: ' . $e->getMessage());
        return redirect()->route('employee.create')->with('error', 'File size exceeded. Please upload a smaller file.');
    } catch (\Exception $e) {
        // ข้อผิดพลาดทั่วไป
        Log::error('Unexpected error: ' . $e->getMessage());
        return redirect()->route('employee.create')->with('error', 'Unexpected error occurred. Please try again.');
    }
}


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}