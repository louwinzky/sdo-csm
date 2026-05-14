<?php

namespace Tests\Feature;

use App\Filament\Pages\DuplicateResponses;
use App\Livewire\Survey;
use App\Models\Office;
use App\Models\Service;
use App\Models\SurveyResponse;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class IpCaptureTest extends TestCase
{
    use RefreshDatabase;

    private Office $office;

    private Service $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->office = Office::create([
            'name' => 'ICT Unit',
            'code' => 'ICTU',
            'is_active' => true,
        ]);

        $this->service = Service::create([
            'office_id' => $this->office->id,
            'name' => 'Technical Support',
            'is_active' => true,
        ]);
    }

    private function submitFullSurvey(): void
    {
        Livewire::test(Survey::class)
            ->set('officeId', $this->office->id)
            ->set('serviceId', $this->service->id)
            ->set('age', 30)
            ->set('gender', 'Male')
            ->set('customerType', 'Citizen')
            ->call('nextStep')
            ->set('cc1', 4)
            ->call('nextStep')
            ->set('sqd0', 5)->set('sqd1', 5)->set('sqd2', 5)
            ->set('sqd3', 5)->set('sqd4', 5)->set('sqd5', 5)
            ->set('sqd6', 5)->set('sqd7', 5)->set('sqd8', 5)
            ->call('nextStep')
            ->call('submit');
    }

    public function test_ip_address_is_captured_on_submission(): void
    {
        $this->submitFullSurvey();

        $response = SurveyResponse::first();
        $this->assertNotNull($response->ip_address);
    }

    public function test_ip_address_remains_null_when_unavailable(): void
    {
        SurveyResponse::create([
            'office_id' => $this->office->id,
            'service_id' => $this->service->id,
            'age' => 25,
            'gender' => 'Female',
            'customer_type' => 'Business',
            'sqd0' => 5,
            'is_complete' => true,
        ]);

        $response = SurveyResponse::first();
        $this->assertNull($response->ip_address);
    }

    public function test_duplicate_detection_flags_same_office_and_ip(): void
    {
        $this->submitFullSurvey();
        $this->submitFullSurvey();

        $responses = SurveyResponse::orderBy('id')->get();
        $this->assertCount(2, $responses);
        $this->assertTrue($responses->last()->is_flagged);
        $this->assertEquals($responses->first()->id, $responses->last()->duplicate_of_id);
    }

    public function test_duplicate_not_flagged_for_different_office(): void
    {
        $office2 = Office::create(['name' => 'HRD', 'code' => 'HRD', 'is_active' => true]);

        $this->submitFullSurvey();

        Livewire::test(Survey::class)
            ->set('officeId', $office2->id)
            ->set('serviceId', $this->service->id)
            ->set('age', 30)
            ->set('gender', 'Male')
            ->set('customerType', 'Citizen')
            ->call('nextStep')
            ->set('cc1', 4)
            ->call('nextStep')
            ->set('sqd0', 5)->set('sqd1', 5)->set('sqd2', 5)
            ->set('sqd3', 5)->set('sqd4', 5)->set('sqd5', 5)
            ->set('sqd6', 5)->set('sqd7', 5)->set('sqd8', 5)
            ->call('nextStep')
            ->call('submit');

        $responses = SurveyResponse::orderBy('id')->get();
        $this->assertCount(2, $responses);
        $this->assertFalse($responses->last()->is_flagged);
        $this->assertNull($responses->last()->duplicate_of_id);
    }

    public function test_super_admin_can_view_duplicate_responses_page(): void
    {
        $admin = User::create([
            'name' => 'Super Admin',
            'email' => 'super@admin.com',
            'password' => bcrypt('password'),
            'super_admin' => true,
        ]);

        $this->actingAs($admin);

        $this->assertTrue(DuplicateResponses::canAccess());
        $this->assertTrue(DuplicateResponses::shouldRegisterNavigation());
    }

    public function test_regular_admin_cannot_view_duplicate_responses_page(): void
    {
        $admin = User::create([
            'name' => 'Regular Admin',
            'email' => 'admin@admin.com',
            'password' => bcrypt('password'),
            'super_admin' => false,
        ]);

        $this->actingAs($admin);

        $this->assertFalse(DuplicateResponses::canAccess());
        $this->assertFalse(DuplicateResponses::shouldRegisterNavigation());
    }

    public function test_ip_address_not_in_export_columns(): void
    {
        $exportColumns = \App\Filament\Exports\SurveyResponseExporter::getColumns();
        $columnNames = collect($exportColumns)->map(fn ($col) => $col->getName())->toArray();
        $this->assertNotContains('ip_address', $columnNames);
    }
}
