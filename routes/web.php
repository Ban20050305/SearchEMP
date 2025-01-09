<?php

use App\Http\Controllers\EmployeeController; //EmployeeController: นำเข้า EmployeeController เพื่อใช้ในเส้นทาง /employee
use App\Http\Controllers\ProfileController; //ProfileController: นำเข้า ProfileController เพื่อใช้ในการจัดการโปรไฟล์ของผู้ใช้
use Illuminate\Foundation\Application; //Application: ใช้สำหรับดึงข้อมูลเวอร์ชันของ Laravel
use Illuminate\Support\Facades\Route; //Route: ใช้เพื่อกำหนดเส้นทางต่างๆ ในแอปพลิเคชัน
use Inertia\Inertia; //Inertia: ใช้สำหรับเรนเดอร์หน้าเว็บด้วย Inertia.js

//เมื่อเข้าถึง URL /employee จะเรียกใช้เมธอด index ใน EmployeeController เพื่อแสดงข้อมูลพนักงาน
Route::get('/employee', [EmployeeController::class, 'index']);

//เมื่อข้าถึงหน้าแรก (/), จะเรนเดอร์หน้า Welcome โดยใช้ Inertia.js ส่งข้อมูลต่อ
Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'), //canLogin: ตรวจสอบว่าเส้นทาง login
        'canRegister' => Route::has('register'), //canRegister: ตรวจสอบว่าเส้นทาง register
        'laravelVersion' => Application::VERSION, //laravelVersion: เวอร์ชันของ Laravel ที่ใช้งาน
        'phpVersion' => PHP_VERSION, //phpVersion: เวอร์ชันของ PHP ที่ใช้งาน
    ]);
});
 
//เมื่อเข้าถึง /dashboard, จะเรนเดอร์หน้า Dashboard โดยใช้ Inertia.js
Route::get('/dashboard', function () {
    return Inertia::render('Dashboard'); //ใช้ middleware auth และ verified ซึ่งหมายความว่า:
})->middleware(['auth', 'verified'])->name('dashboard'); //auth: ผู้ใช้ต้องล็อกอินก่อน  verified: ผู้ใช้ต้องยืนยันอีเมล์ก่อน 
//ตั้งชื่อเส้นทางนี้ว่า dashboard

//ใช้ middleware auth เพื่อบังคับให้ผู้ใช้ต้องล็อกอินก่อนถึงจะเข้าถึงเส้นทางเหล่านี้ได้ /profile:
Route::middleware('auth')->group(function () {
    //GET /profile: เรียกเมธอด edit ใน ProfileController เพื่อแสดงฟอร์มแก้ไขโปรไฟล์ (ชื่อเส้นทาง profile.edit)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    //PATCH /profile: เรียกเมธอด update ใน ProfileController เพื่ออัปเดตข้อมูลโปรไฟล์ (ชื่อเส้นทาง profile.update)
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    //DELETE /profile: เรียกเมธอด destroy ใน ProfileController เพื่อทำการลบโปรไฟล์ (ชื่อเส้นทาง profile.destroy)
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

//รวมไฟล์ auth.php ซึ่งจะกำหนดเส้นทางสำหรับการล็อกอิน, ลงทะเบียน, ลืมรหัสผ่าน, และการยืนยันอีเมล์ โดยปกติแล้วไฟล์นี้จะถูกสร้างขึ้นโดย Laravel เมื่อใช้การยืนยันตัวตน (Authentication)
require __DIR__.'/auth.php';
