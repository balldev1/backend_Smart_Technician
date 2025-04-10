# run php artisan serve

# เพิ่มสกิล api_post http://localhost:8000/api/technicians

-body
{
"name": "Tech01",
"skills": ["camera", "wifi"]
}

# เพิ่มงาน api_post http://localhost:8000/api/tasks

{
"title": "Install CCTV",
"required_skill": "camera",
"urgency": "High",
"duration": 3,
"required_technicians": 2
}

# แสดงที่ match กัน api_get http://localhost:8000/api/match

# เช็คข้อมูล teminal -> php artisan tinker

-   Technician::all();

---

# request

Task
id
title
required_skill → string (ทักษะที่ต้องใช้ เช่น "camera")
urgency → ระดับความเร่งด่วน (เลือกจาก "High", "Medium", "Low")
duration → จำนวนวันที่ใช้ทำงาน (ค่าระหว่าง 1 ถึง 3 วัน)
required_technicians → จำนวนช่างที่ต้องใช้สำหรับงานนี้ (ค่าระหว่าง 1 ถึง 3 คน)
เช่น งานที่มี duration 2 วัน และ require_technicians 2 คน หมายถึง ในแต่ละ 1 วันต้องการช่าง 2 คนในการทำงาน

# Technician

id
name
skills → array ของ string (เช่น ["network", "camera", "wifi"])
