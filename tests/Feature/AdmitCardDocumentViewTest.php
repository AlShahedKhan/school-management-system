<?php

namespace Tests\Feature;

use App\Models\SchoolAdmitCardSetting;
use Tests\TestCase;

class AdmitCardDocumentViewTest extends TestCase
{
    public function test_document_distributes_routines_without_rendering_empty_rows(): void
    {
        foreach ([0, 1, 3, 6, 7, 12, 13, 18] as $routineCount) {
            $html = $this->renderDocument($routineCount);
            $bodyRows = substr_count($html, '<tbody>') === 1
                ? substr($html, strpos($html, '<tbody>'), strpos($html, '</tbody>') - strpos($html, '<tbody>'))
                : '';

            $this->assertSame(
                min(6, $routineCount),
                substr_count($bodyRows, '<tr>'),
                "Unexpected row count for {$routineCount} routines."
            );

            if ($routineCount > 0) {
                $this->assertStringContainsString('Subject 1', $html);
            }

            $this->assertStringContainsString('footer-instructions', $html);
            $this->assertStringNotContainsString('Issue Date', $html);

            if ($routineCount >= 7) {
                $this->assertStringContainsString('Subject 7', $html);
            }

            if ($routineCount >= 13) {
                $this->assertStringContainsString('Subject 13', $html);
            }
        }
    }

    private function renderDocument(int $routineCount): string
    {
        $card = [
            'student_name' => 'Ahsan Mahbub',
            'student_id_number' => 'OL001000002',
            'father_name' => 'Arshad',
            'admit_card_number' => '24551080',
            'class_name' => 'Class-1',
            'group_name' => 'Probeti',
            'section_name' => 'Section-1',
            'session_name' => '2026-26',
            'exam_name' => 'First Term',
        ];

        $routines = collect($routineCount > 0 ? range(1, $routineCount) : [])->map(fn (int $number) => [
            'class_name' => 'Class-1',
            'group_name' => 'Probeti',
            'section_name' => 'Section-1',
            'session_name' => '2026-26',
            'exam_name' => 'First Term',
            'exam_date' => sprintf('2026-07-%02d', $number),
            'start_time' => '10:00:00',
            'subject_name' => 'Subject '.$number,
        ])->all();

        return view('school.exam.admit_card_document', [
            'cards' => [$card],
            'school' => ['school_name' => 'Green Valley School', 'full_address' => 'Green Road'],
            'routines' => $routines,
            'language' => 'en',
            'instructions' => SchoolAdmitCardSetting::DEFAULT_EN,
            'showToolbar' => false,
        ])->render();
    }

    public function test_bangla_document_uses_bangla_labels_and_three_default_instructions(): void
    {
        $html = view('school.exam.admit_card_document', [
            'cards' => [[
                'student_name' => 'Student', 'student_id_number' => '123', 'father_name' => 'Father',
                'admit_card_number' => '456', 'class_name' => 'One', 'group_name' => null,
                'section_name' => null, 'session_name' => '2026', 'exam_name' => 'First Term',
            ]],
            'school' => ['school_name' => 'School'],
            'routines' => [],
            'language' => 'bn',
            'instructions' => SchoolAdmitCardSetting::DEFAULT_BN,
            'showToolbar' => false,
        ])->render();

        $this->assertStringContainsString('প্রবেশপত্র', $html);
        $this->assertStringContainsString('পরীক্ষার নিয়মাবলী', $html);
        $this->assertCount(3, SchoolAdmitCardSetting::DEFAULT_BN);
        $this->assertStringContainsString('১২৩', $html);
    }
}
