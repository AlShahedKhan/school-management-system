<?php

namespace App\Services;

class SeatPlanGenerator
{
    public function buildAssignments(array $students, int $startNumber, bool $isMultiClass = false): array
    {
        $assignments = [];
        $seatNumber = $startNumber;

        if (!$isMultiClass) {
            foreach ($students as $student) {
                $assignments[] = [
                    'student_id_number' => $student['student_id_number'],
                    'seat_number' => $seatNumber++,
                    'class_name' => $student['class_name'] ?? null,
                ];
            }

            return $assignments;
        }

        $groups = [];
        foreach ($students as $student) {
            $classKey = $student['class_name'] ?? $student['class'] ?? 'unknown';
            $groups[$classKey][] = $student;
        }

        $classOrder = array_keys($groups);
        $index = 0;
        $remaining = count($students);

        while ($remaining > 0) {
            $className = $classOrder[$index % count($classOrder)];
            $group = $groups[$className];

            if (!empty($group)) {
                $student = array_shift($groups[$className]);
                $assignments[] = [
                    'student_id_number' => $student['student_id_number'],
                    'seat_number' => $seatNumber++,
                    'class_name' => $className,
                ];
                $remaining--;
            }

            $index++;
        }

        return $assignments;
    }
}
