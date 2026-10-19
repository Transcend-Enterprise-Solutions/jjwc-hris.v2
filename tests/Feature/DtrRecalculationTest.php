<?php

namespace Tests\Feature;

use App\Jobs\AutoSaveDtrRecords;
use App\Livewire\User\DtrTable;
use App\Models\BrandingConfigurations;
use App\Models\DTRSchedule;
use App\Models\EmployeesDtr;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;
use Mockery;
use Tests\TestCase;

class DtrRecalculationTest extends TestCase
{
    public function createApplication(): \Illuminate\Foundation\Application
    {
        $app = require __DIR__.'/../../bootstrap/app.php';
        $app->afterBootstrapping(\Illuminate\Foundation\Bootstrap\LoadConfiguration::class, function ($app) {
            $app['config']->set('database.default', 'sqlite');
            $app['config']->set('database.connections.sqlite.database', ':memory:');
        });
        $app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();
        return $app;
    }

    protected function setUp(): void
    {
        parent::setUp();
        Carbon::setTestNow('2026-10-20 12:00:00');
        cache()->forever('current_branding', new BrandingConfigurations());

        foreach ([new EmployeesDtr(), new Transaction(), new DTRSchedule()] as $model) {
            Schema::create($model->getTable(), function (Blueprint $table) use ($model) {
                $table->id();
                foreach ($model->getFillable() as $column) {
                    $table->string($column)->nullable();
                }
                $table->timestamps();
            });
        }
        Schema::create('holidays', function (Blueprint $table) {
            $table->id();
            $table->date('holiday_date');
            $table->string('region_id')->nullable();
        });
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    public function test_employee_recalculates_only_own_inclusive_range_and_preserves_hr_corrections(): void
    {
        $user = $this->employee();
        $job = $this->calculator();
        $this->app->instance(AutoSaveDtrRecords::class, $job);
        DTRSchedule::create([
            'emp_code' => '001', 'start_date' => '2026-10-01', 'end_date' => '2026-10-31',
            'default_start_time' => '08:00:00', 'default_end_time' => '17:00:00',
        ]);
        foreach (['2026-10-14', '2026-10-15'] as $date) {
            Transaction::create(['emp_code' => '1', 'punch_time' => "$date 08:00:00", 'punch_state' => '0']);
            Transaction::create(['emp_code' => '1', 'punch_time' => "$date 17:00:00", 'punch_state' => '1']);
        }
        $corrected = EmployeesDtr::create([
            'user_id' => 1, 'emp_code' => '001', 'date' => '2026-10-14',
            'time_out' => null, 'up_time_out' => '17:30:00', 'up_remarks' => 'HR correction',
            'updated_by' => 9, 'ot_approval_status' => 'approved', 'attachment' => 'proof.pdf',
        ]);
        EmployeesDtr::create(['user_id' => 2, 'date' => '2026-10-14', 'time_out' => '16:00:00']);
        EmployeesDtr::create(['user_id' => 1, 'date' => '2026-10-13', 'time_out' => '15:00:00']);

        Livewire::actingAs($user)->test(DtrTable::class)
            ->set('recalculateStartDate', '2026-10-14')->set('recalculateEndDate', '2026-10-15')
            ->call('recalculateDtr')->assertHasNoErrors()
            ->assertSet('startDate', '2026-10-14')->assertSet('endDate', '2026-10-15')
            ->assertDispatched('swal', fn ($name, $params) => $params[0]['icon'] === 'success');

        $this->assertSame('17:00:00', $corrected->fresh()->time_out);
        $this->assertSame('17:30:00', $corrected->fresh()->up_time_out);
        $this->assertSame('HR correction', $corrected->fresh()->up_remarks);
        $this->assertSame('approved', $corrected->fresh()->ot_approval_status);
        $this->assertSame('proof.pdf', $corrected->fresh()->attachment);
        $this->assertSame('9', $corrected->fresh()->updated_by);
        $this->assertSame('16:00:00', EmployeesDtr::where('user_id', 2)->first()->time_out);
        $this->assertSame('15:00:00', EmployeesDtr::where('date', '2026-10-13')->first()->time_out);
        $this->assertSame(2, EmployeesDtr::where('user_id', 1)->whereBetween('date', ['2026-10-14', '2026-10-15'])->count());

        $job->recalculateForUser($user, '2026-10-14', '2026-10-15');
        $this->assertSame(4, EmployeesDtr::count());
    }

    public function test_invalid_future_reversed_and_excessive_ranges_do_not_run_calculator(): void
    {
        $job = Mockery::mock(AutoSaveDtrRecords::class);
        $job->shouldNotReceive('recalculateForUser');
        $this->app->instance(AutoSaveDtrRecords::class, $job);

        foreach ([['', '2026-10-15', 'recalculateStartDate'], ['2026-02-30', '2026-10-15', 'recalculateStartDate'],
            ['2026-10-15', '2026-10-14', 'recalculateEndDate'], ['2026-10-20', '2026-10-21', 'recalculateEndDate'],
            ['2026-09-01', '2026-10-20', 'recalculateEndDate']] as [$start, $end, $field]) {
            Livewire::actingAs($this->employee())->test(DtrTable::class)
                ->set('recalculateStartDate', $start)->set('recalculateEndDate', $end)
                ->call('recalculateDtr')->assertHasErrors([$field]);
        }
        $this->assertSame(0, EmployeesDtr::count());
    }

    public function test_full_31_day_range_is_allowed(): void
    {
        $job = Mockery::mock(AutoSaveDtrRecords::class);
        $job->shouldReceive('recalculateForUser')->once()->withArgs(fn ($user, $start, $end) =>
            $user->id === 1 && $start === '2026-09-20' && $end === '2026-10-20');
        $this->app->instance(AutoSaveDtrRecords::class, $job);

        Livewire::actingAs($this->employee())->test(DtrTable::class)
            ->set('recalculateStartDate', '2026-09-20')->set('recalculateEndDate', '2026-10-20')
            ->call('recalculateDtr')->assertHasNoErrors();
    }

    public function test_non_employee_and_missing_employee_code_are_forbidden(): void
    {
        foreach ([new User(['user_role' => 'sa', 'emp_code' => 'sa-001']), new User(['user_role' => 'emp'])] as $user) {
            $user->id = 1;
            Livewire::actingAs($user)->test(DtrTable::class)->call('recalculateDtr')->assertForbidden();
        }
    }

    public function test_save_failure_rolls_back_all_dates_and_reports_failure(): void
    {
        $job = $this->calculator();
        EmployeesDtr::saving(function ($record) {
            if ($record->date === '2026-10-15') {
                throw new \RuntimeException('Simulated write failure');
            }
        });
        $this->app->instance(AutoSaveDtrRecords::class, $job);

        Livewire::actingAs($this->employee())->test(DtrTable::class)
            ->set('recalculateStartDate', '2026-10-14')->set('recalculateEndDate', '2026-10-15')
            ->call('recalculateDtr')
            ->assertDispatched('swal', fn ($name, $params) => $params[0]['icon'] === 'error');

        $this->assertSame(0, EmployeesDtr::count());
    }

    private function employee(): User
    {
        $user = new User(['name' => 'Test Employee', 'user_role' => 'emp', 'emp_code' => '001']);
        $user->id = 1;
        return $user;
    }

    private function calculator()
    {
        $job = Mockery::mock(AutoSaveDtrRecords::class)->makePartial()->shouldAllowMockingProtectedMethods();
        $job->shouldReceive('getApprovedLeaves')->andReturn(collect());
        $job->shouldReceive('getPendingApplications')->andReturn([]);
        $job->shouldReceive('getUserRegionId')->andReturn(null);
        return $job;
    }
}
