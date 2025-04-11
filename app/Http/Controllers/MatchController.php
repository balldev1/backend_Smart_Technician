<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\Technician;

class MatchController extends Controller
{
   public function match()
{
    // เรียงงานตามระดับ urgency: High -> Medium -> Low
    $tasks = Task::orderByRaw("
        CASE 
            WHEN urgency = 'High' THEN 1
            WHEN urgency = 'Medium' THEN 2
            WHEN urgency = 'Low' THEN 3
            ELSE 4
        END
    ")->get();

    $technicians = Technician::all();
    $matches = [];
    $technicianTasksCount = [];

    foreach ($tasks as $task) {
        // ค้นหาช่างที่มี skill ตรงกับ required_skill
        $availableTechnicians = $technicians->filter(function($technician) use ($task) {
            return in_array($task->required_skill, $technician->skills);
        });

        // ถ้ามีช่างที่ตรงกับ requirement
        if ($availableTechnicians->count() >= $task->required_technicians) {
            // เลือกช่างที่มีงานน้อยที่สุด
            $sortedTechnicians = $availableTechnicians->sortBy(function($technician) use ($technicianTasksCount) {
                return $technicianTasksCount[$technician->id] ?? 0; // นับงานที่มีอยู่ของช่าง
            });

            // เลือกช่างตามจำนวนที่ต้องการ
            $taskMatches = $sortedTechnicians->take($task->required_technicians);

            // อัปเดตจำนวนงานที่ช่างทำ
            foreach ($taskMatches as $technician) {
                $technicianTasksCount[$technician->id] = ($technicianTasksCount[$technician->id] ?? 0) + 1;
            }

            // ใช้ created_at เป็น start_date
            // ใช้ duration เพื่อคำนวณ end_date
            $startDate = \Carbon\Carbon::parse($task->created_at);
            $endDate = (clone $startDate)->addDays($task->duration);

            // เก็บข้อมูลที่แมตช์
            $matches[] = [
                'task' => $task,
                'technicians' => $taskMatches,
                'start_date' => $startDate,
                'end_date' => $endDate
            ];
        } else {
            // แจ้งเตือนถ้าหาช่างไม่พอ
            $matches[] = [
                'task' => $task,
                'technicians' => [],
                'message' => 'ไม่สามารถจับคู่ช่างได้เนื่องจากช่างไม่เพียงพอ'
            ];
        }
    }


$totalDuration = 0;
foreach ($matches as $match) {
    if (isset($match['start_date']) && isset($match['end_date'])) {
        $start = \Carbon\Carbon::parse($match['start_date'])->startOfDay();
        $end = \Carbon\Carbon::parse($match['end_date'])->startOfDay();

        // ถ้า start_date และ end_date เป็นวันเดียวกันให้ถือเป็น 1 วัน
        if ($start->equalTo($end)) {
            $diffDays = 1; // คำนวณเป็น 1 วัน
        } else {
            // ถ้าไม่ใช่ให้คำนวณ diffInDays
            $diffDays = $end->diffInDays($start) + 1; // เพิ่ม 1 วันเพื่อให้ครอบคลุมทั้งวันเริ่มต้นและสิ้นสุด
        }

        // รวมวันทั้งหมด
        $totalDuration -= $diffDays;
    }
}




    // สรุปผล
    $technicianWorkDays = [];
    foreach ($technicianTasksCount as $technicianId => $taskCount) {
        $technicianWorkDays[$technicianId] = $taskCount;
    }

    return response()->json([
        'matches' => $matches,
        'total_duration' => $totalDuration,
        'technician_work_days' => $technicianWorkDays,
        'message' => 'Matching logic เสร็จสิ้น'
    ]);
}

}
