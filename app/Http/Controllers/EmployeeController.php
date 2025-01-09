<?php

//นำเข้าไฟล์
namespace App\Http\Controllers;

//กำหนด namespace ให้ Controller อยู่ในโฟลเดอร์ App\Http\Controllers
use Illuminate\Http\Request; //นำเข้า class และ library ต่างๆ:
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

//ประกาศคลาส EmployeeController ซึ่งมาจาก Controller
class EmployeeController extends Controller
{    
    //เมธอดนี้ใช้ดึงข้อมูลรายการพนักงานพร้อมรองรับการค้นหา และแสดงผลด้วย Inertia.js
    //พารามิเตอร์ $request ใช้เก็บข้อมูลจาก HTTP Request
    public function index(Request $request)
    { 
        //รับค่าพารามิเตอร์ search จาก URL หรือฟอร์มค้นหา
        $query = $request->input('search');
        $employees = DB::table('employees')
            ->where('first_name', 'like', '%' . $query . '%')
            ->orWhere('last_name', 'like', '%' . $query . '%')
            ->paginate(10);
        
        //ค้นหาพนักงานในฐานข้อมูล employees โดยดูที่ชื่อ (first_name) หรือ นามสกุล (last_name) ที่มีคำที่ตรงกับ $query
        //ใช้ paginate(10) เพื่อแบ่งหน้า (10 รายการต่อหน้า)
        return Inertia::render('Employee/Index', [
            'employees' => $employees,
            'query' => $query,
        ]);
    }
        //ส่งข้อมูลไปยังหน้า Inertia.js ชื่อ Employee/Index
        //employees: รายการพนักงาน
        //query: ข้อความค้นหาที่ผู้ใช้ป้อน

    /**
     * Show the form for creating a new resource.
     */
    //ใช้สำหรับแสดงฟอร์มเพิ่มข้อมูลพนักงานใหม่
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    //ใช้สำหรับบันทึกข้อมูลพนักงานใหม่ในฐานข้อมูล
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    //ใช้แสดงรายละเอียดของพนักงานตาม id
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    //ใช้สำหรับแสดงฟอร์มแก้ไขข้อมูลพนักงานตาม id (
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    //ใช้สำหรับอัปเดตข้อมูลพนักงานในฐานข้อมูลตาม id
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    //ใช้สำหรับลบข้อมูลพนักงานตาม id
    public function destroy(string $id)
    {
        //
    }
}
