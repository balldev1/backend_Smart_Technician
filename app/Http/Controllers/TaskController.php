<?php

namespace App\Http\Controllers;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
     // ดึงข้อมูลงานทั้งหมด
    public function index()
    {
        $tasks = Task::all(); // ดึงข้อมูลงานทั้งหมดจากฐานข้อมูล
        return response()->json($tasks);
    }

    // เพิ่มงาน
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'required_skill' => 'required|string',
            'urgency' => 'required|string|in:Low,Medium,High',
            'duration' => 'required|integer|min:1|max:3', // 1-3 วัน
            'required_technicians' => 'required|integer|min:1|max:3', // 1-3 ช่าง
        ]);

        $task = new Task();
        $task->title = $request->title;
        $task->required_skill = $request->required_skill;
        $task->urgency = $request->urgency;
        $task->duration = $request->duration;
        $task->required_technicians = $request->required_technicians;
        $task->save();

        return response()->json($task, 201); // ส่งคืนข้อมูลงานที่เพิ่มแล้ว
    }

    // แก้ไขข้อมูลงาน
    public function update(Request $request, $id)
    {
        // ค้นหางานตาม id
        $task = Task::findOrFail($id);

        // อัปเดตข้อมูลงาน
        $task->title = $request->title;
        $task->required_skill = $request->required_skill;
        $task->urgency = $request->urgency;
        $task->duration = $request->duration;
        $task->required_technicians = $request->required_technicians;
        $task->save();

        return response()->json($task); // ส่งคืนข้อมูลงานที่อัปเดต
    }

    // ลบงาน
    public function destroy($id)
    {
        $task = Task::findOrFail($id);
        $task->delete();

        return response()->json(null, 204); // ส่งคืนการลบสำเร็จ
    }
}
// index(): ดึงข้อมูลงานทั้งหมดจากฐานข้อมูล
// store(): รับข้อมูลจากฟอร์มและเพิ่มงานใหม่
// update(): แก้ไขข้อมูลของงานตาม id ที่ระบุ
// destroy(): ลบงานที่มี id ตามที่ระบุ