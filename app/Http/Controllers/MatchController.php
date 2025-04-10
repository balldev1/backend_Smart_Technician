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

                // คำนวณวันที่ของงานที่ทำ
                $startDate = now();
                $endDate = $startDate->addDays($task->duration - 1);

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

        // คำนวณจำนวนวันที่ใช้ทั้งหมด
        $totalDuration = 0;
        foreach ($matches as $match) {
            if (isset($match['start_date'])) {
                $totalDuration = max($totalDuration, $match['end_date']->diffInDays($match['start_date']) + 1);
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
