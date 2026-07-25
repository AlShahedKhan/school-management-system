<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class ModalFormComponentTest extends TestCase
{
    public function test_form_modal_can_be_reused_with_unique_ids_and_different_fields(): void
    {
        Route::middleware('web')->get('/_test/modal-form-component', function () {
            return Blade::render(<<<'BLADE'
                <x-modal.form
                    id="teacherModal"
                    form-id="teacherForm"
                    title="Teacher registration"
                    close-button-id="closeTeacherModal"
                >
                    <x-input.control id="teacherName" name="name" />
                </x-modal.form>

                <x-modal.form
                    id="studentModal"
                    form-id="studentForm"
                    title="Student update"
                    close-button-id="closeStudentModal"
                    action="/students/1"
                    method="PATCH"
                >
                    <x-input.control id="studentRoll" name="roll" />

                    <x-slot:footer>
                        <div id="studentFooter">Custom footer</div>
                    </x-slot:footer>
                </x-modal.form>
            BLADE);
        });

        $response = $this->get('/_test/modal-form-component');
        $response->assertOk();

        $html = $response->getContent();

        $this->assertStringContainsString('id="teacherModal"', $html);
        $this->assertStringContainsString('id="teacherForm"', $html);
        $this->assertStringContainsString('id="closeTeacherModal"', $html);
        $this->assertStringContainsString('aria-labelledby="teacherModalTitle"', $html);
        $this->assertStringContainsString('id="teacherName"', $html);

        $this->assertStringContainsString('id="studentModal"', $html);
        $this->assertStringContainsString('id="studentForm"', $html);
        $this->assertStringContainsString('aria-labelledby="studentModalTitle"', $html);
        $this->assertStringContainsString('action="/students/1"', $html);
        $this->assertStringContainsString('name="_method" value="PATCH"', $html);
        $this->assertStringContainsString('id="studentRoll"', $html);
        $this->assertStringContainsString('id="studentFooter"', $html);
        $this->assertStringNotContainsString('id="closeStudentModal"', $html);

        preg_match_all('/\sid="([^"]+)"/', $html, $idMatches);

        $this->assertSame(
            $idMatches[1],
            array_values(array_unique($idMatches[1])),
            'Rendered modal instances must not contain duplicate IDs.',
        );
        $this->assertSame(2, substr_count($html, 'name="_token"'));
    }

    public function test_teacher_modal_preserves_existing_javascript_hooks(): void
    {
        Route::middleware('web')->get('/_test/teacher-modal-component', function () {
            return Blade::render("@include('school.partials.teacher-register-modal')");
        });

        $response = $this->get('/_test/teacher-modal-component');
        $response->assertOk();

        $html = $response->getContent();
        $expectedIds = [
            'teacherModal',
            'teacherModalTitle',
            'teacherForm',
            'teacher_id',
            'teacherName',
            'teacherDesignation',
            'teacherMobile',
            'teacherMobileError',
            'teacherEmail',
            'passInput',
            'confirmPassInput',
            'teacherDob',
            'photoInput',
            'imagePreview',
            'closeTeacherModal',
        ];

        foreach ($expectedIds as $id) {
            preg_match_all('/\sid="' . preg_quote($id, '/') . '"/', $html, $matches);

            $this->assertCount(1, $matches[0], "Missing or duplicate ID: {$id}");
        }

        $this->assertStringContainsString('teacher-register-modal-title', $html);
        $this->assertStringContainsString('name="mobile"', $html);
        $this->assertStringContainsString('pattern="[0-9]+"', $html);
        $this->assertStringContainsString('enctype="multipart/form-data"', $html);
    }
}
