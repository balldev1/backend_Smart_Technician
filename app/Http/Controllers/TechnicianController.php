<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Technician;

class TechnicianController extends Controller
{
   // ดึงข้อมูลช่างทั้งหมด
    public function index()
    {
        $technicians = Technician::all(); // ดึงข้อมูลช่างทั้งหมดจากฐานข้อมูล
        return response()->json($technicians);
    }

// เพิ่มช่าง
  public function store(Request $request)
{
    // การตรวจสอบความถูกต้อง
    $request->validate([
        'name' => 'required|string|max:255',
        'skills' => 'required|array', // ตรวจสอบว่า skills เป็น array
        'skills.*' => 'string', // ตรวจสอบแต่ละทักษะว่าเป็น string
    ]);

    // สร้างช่างใหม่
    $technician = new Technician();
    $technician->name = $request->name;
    $technician->skills = $request->skills;  // skills เป็นอาร์เรย์
    $technician->save();

    return response()->json($technician, 201); // ส่งคืนข้อมูลช่างที่เพิ่มแล้ว
}

    // แก้ไขข้อมูลช่าง
    public function update(Request $request, $id)
    {
        // ค้นหาช่างตาม id
        $technician = Technician::findOrFail($id);

        // อัปเดตข้อมูลช่าง
        $technician->name = $request->name;
        $technician->skills = $request->skills;  // คั่นด้วย comma
        $technician->save();

        return response()->json($technician); // ส่งคืนข้อมูลช่างที่อัปเดต
    }

    // ลบช่าง
    public function destroy($id)
    {
        $technician = Technician::findOrFail($id);
        $technician->delete();

        return response()->json(null, 204); // ส่งคืนการลบสำเร็จ
    }
}

// index(): ดึงข้อมูลช่างทั้งหมดจากฐานข้อมูล
// store(): รับข้อมูลจากฟอร์มและเพิ่มช่างใหม่
// update(): แก้ไขข้อมูลของช่างตาม id ที่ระบุ
// destroy(): ลบช่างที่มี id ตามที่ระบุ