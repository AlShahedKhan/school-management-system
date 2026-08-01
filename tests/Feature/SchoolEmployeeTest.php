<?php

namespace Tests\Feature;

use App\Models\Employee;
use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class SchoolEmployeeTest extends TestCase
{
    use DatabaseTransactions;

    protected User $user;
    protected School $school;

    protected function setUp(): void
    {
        parent::setUp();

        // Get an existing school user or create one safely
        $this->user = User::first() ?? User::create([
            'name' => 'Test School Admin',
            'email' => 'testschool@example.com',
            'password' => bcrypt('password'),
            'user_type' => 'school',
        ]);

        $this->school = School::where('user_id', $this->user->id)->first() ?? School::create([
            'user_id' => $this->user->id,
            'school_name' => 'Test School Academy',
            'mobile' => '01700000000',
            'division' => 'Dhaka',
            'district' => 'Dhaka',
            'upazila' => 'Dhanmondi',
            'union' => 'Dhanmondi',
            'village' => 'Dhanmondi',
            'id_number' => 'SCH-00001',
            'eiin_number' => '123456',
            'email' => 'testschool@example.com',
        ]);
    }

    /** @test */
    public function it_can_create_an_employee_profile()
    {
        $this->actingAs($this->user);

        $payload = [
            'name'              => 'Rahim Uddin',
            'designation'       => 'Accountant',
            'mobile_number'     => '01711223344',
            'salary_amount'     => 25000.00,
            'salary_start_date' => '2026-08-01',
            'pay_date'          => 'Every Day-10',
            'employee_status'   => 'active',
        ];

        $response = $this->postJson('/api/employees', $payload);

        $response->assertStatus(201)
                 ->assertJsonPath('data.name', 'Rahim Uddin')
                 ->assertJsonPath('data.designation', 'Accountant');

        $this->assertDatabaseHas('employees', [
            'school_id'     => $this->school->id,
            'name'          => 'Rahim Uddin',
            'mobile_number' => '01711223344',
            'salary_amount' => 25000.00,
            'pay_date'      => 'Every Day-10',
        ]);
    }

    /** @test */
    public function it_can_fetch_and_filter_employee_list()
    {
        $this->actingAs($this->user);

        // Create two employees
        Employee::create([
            'school_id'         => $this->school->id,
            'employee_no'       => 'TEST-EMP-' . uniqid(),
            'name'              => 'Rahim Uddin',
            'designation'       => 'Accountant',
            'mobile_number'     => '01711223344',
            'salary_amount'     => 25000.00,
            'salary_start_date' => '2026-08-01',
            'pay_date'          => 'Every Day-10',
            'employee_status'   => 'active',
        ]);

        Employee::create([
            'school_id'         => $this->school->id,
            'employee_no'       => 'EMP-00002',
            'name'              => 'Karim Mia',
            'designation'       => 'Peon',
            'mobile_number'     => '01899887766',
            'salary_amount'     => 12000.00,
            'salary_start_date' => '2026-08-01',
            'pay_date'          => 'Every Day-05',
            'employee_status'   => 'active',
        ]);

        // Test list fetch
        $responseList = $this->getJson('/api/employees');
        $responseList->assertStatus(200)
                     ->assertJsonCount(2, 'data');

        // Test search filter (search for Rahim)
        $responseFilter = $this->getJson('/api/employees?search=Rahim');
        $responseFilter->assertStatus(200)
                       ->assertJsonCount(1, 'data')
                       ->assertJsonPath('data.0.name', 'Rahim Uddin');
    }

    /** @test */
    public function it_can_update_an_employee()
    {
        $this->actingAs($this->user);

        $employee = Employee::create([
            'school_id'         => $this->school->id,
            'employee_no'       => 'TEST-EMP-' . uniqid(),
            'name'              => 'Rahim Uddin',
            'designation'       => 'Accountant',
            'mobile_number'     => '01711223344',
            'salary_amount'     => 25000.00,
            'salary_start_date' => '2026-08-01',
            'pay_date'          => 'Every Day-10',
            'employee_status'   => 'active',
        ]);

        $updatePayload = [
            'name'              => 'Rahim Uddin Updated',
            'designation'       => 'Senior Accountant',
            'mobile_number'     => '01711223344',
            'salary_amount'     => 30000.00,
            'salary_start_date' => '2026-08-01',
            'pay_date'          => 'Every Day-15',
            'employee_status'   => 'active',
        ];

        $response = $this->putJson("/api/employees/{$employee->id}", $updatePayload);

        $response->assertStatus(200)
                 ->assertJsonPath('data.name', 'Rahim Uddin Updated')
                 ->assertJsonPath('data.designation', 'Senior Accountant');

        $this->assertDatabaseHas('employees', [
            'id'            => $employee->id,
            'name'          => 'Rahim Uddin Updated',
            'designation'   => 'Senior Accountant',
            'salary_amount' => 30000.00,
            'pay_date'      => 'Every Day-15',
        ]);
    }

    /** @test */
    public function it_can_delete_an_employee()
    {
        $this->actingAs($this->user);

        $employee = Employee::create([
            'school_id'         => $this->school->id,
            'employee_no'       => 'TEST-EMP-' . uniqid(),
            'name'              => 'Rahim Uddin',
            'designation'       => 'Accountant',
            'mobile_number'     => '01711223344',
            'salary_amount'     => 25000.00,
            'salary_start_date' => '2026-08-01',
            'pay_date'          => 'Every Day-10',
            'employee_status'   => 'active',
        ]);

        $response = $this->deleteJson("/api/employees/{$employee->id}");

        $response->assertStatus(200)
                 ->assertJsonPath('message', 'Employee deleted successfully');

        $this->assertDatabaseMissing('employees', [
            'id' => $employee->id,
        ]);
    }
}
