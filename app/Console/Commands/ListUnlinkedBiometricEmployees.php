<?php

namespace App\Console\Commands;

use App\Models\Transaction;
use App\Models\User;
use App\Services\BioTimeService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ListUnlinkedBiometricEmployees extends Command
{
    protected $signature = 'dtr:unlinked-biometric
        {--start= : Start date, YYYY-MM-DD}
        {--end= : End date, YYYY-MM-DD}
        {--limit=50 : Maximum number of rows to show}
        {--fetch-names : Fetch employee names from BioTime for the unlinked codes}';

    protected $description = 'List biometric punch employee codes that do not have matching HRIS employee accounts.';

    public function handle(BioTimeService $bioTimeService): int
    {
        $start = $this->parseDateOption('start') ?? now()->startOfMonth()->toDateString();
        $end = $this->parseDateOption('end') ?? now()->toDateString();
        $limit = max(1, (int) $this->option('limit'));

        if (Carbon::parse($start)->gt(Carbon::parse($end))) {
            $this->error('The start date must be before or equal to the end date.');
            return self::FAILURE;
        }

        $knownEmployeeCodes = User::where('user_role', 'emp')
            ->pluck('emp_code')
            ->flatMap(fn ($code) => $this->empCodeVariants((string) $code))
            ->unique()
            ->values();

        $punchGroups = Transaction::query()
            ->whereBetween('punch_time', [
                Carbon::parse($start)->startOfDay()->toDateTimeString(),
                Carbon::parse($end)->endOfDay()->toDateTimeString(),
            ])
            ->selectRaw('emp_code, count(*) as punch_count, min(punch_time) as first_punch, max(punch_time) as last_punch')
            ->groupBy('emp_code')
            ->orderByDesc('punch_count')
            ->get();

        $unlinked = $punchGroups
            ->reject(fn ($row) => $knownEmployeeCodes->contains((string) $row->emp_code))
            ->take($limit)
            ->values();

        if ($unlinked->isEmpty()) {
            $this->info("No unlinked biometric employee codes found from {$start} to {$end}.");
            return self::SUCCESS;
        }

        $rows = $unlinked->map(function ($row) use ($bioTimeService) {
            return [
                'emp_code' => $row->emp_code,
                'biotime_name' => $this->option('fetch-names') ? $this->biotimeName($bioTimeService, (string) $row->emp_code) : '-',
                'punches' => $row->punch_count,
                'first_punch' => $row->first_punch,
                'last_punch' => $row->last_punch,
            ];
        });

        $this->warn("Found {$punchGroups->count()} biometric code(s), {$unlinked->count()} unlinked in the displayed limit.");
        $this->table(
            ['Emp Code', 'BioTime Name', 'Punches', 'First Punch', 'Last Punch'],
            $rows->map(fn ($row) => [
                $row['emp_code'],
                $row['biotime_name'],
                $row['punches'],
                $row['first_punch'],
                $row['last_punch'],
            ])->all()
        );
        $this->line('Create or correct the HRIS employee account emp_code, then run dtr:backfill for that emp_code/date range.');

        return self::SUCCESS;
    }

    protected function parseDateOption(string $name): ?string
    {
        $value = $this->option($name);

        if (!$value) {
            return null;
        }

        try {
            return Carbon::parse($value)->toDateString();
        } catch (\Throwable $e) {
            $this->error("Invalid {$name} date: {$value}");
            exit(self::FAILURE);
        }
    }

    protected function empCodeVariants(string $empCode): array
    {
        $trimmed = trim($empCode);
        $variants = [$empCode, $trimmed];
        $zeroFixed = preg_replace('/[oO]/', '0', $trimmed);

        if ($zeroFixed && preg_match('/^\d+$/', $zeroFixed)) {
            $unPadded = ltrim($zeroFixed, '0') ?: '0';
            $variants[] = $zeroFixed;
            $variants[] = $unPadded;
            $variants[] = str_pad($unPadded, 3, '0', STR_PAD_LEFT);
        }

        return array_values(array_unique(array_filter($variants, fn ($code) => $code !== '')));
    }

    protected function biotimeName(BioTimeService $bioTimeService, string $empCode): string
    {
        $response = $bioTimeService->getEmployees([
            'emp_code' => $empCode,
            'page_size' => 1,
        ]);

        $employee = $response['data'][0] ?? null;

        if (!$employee) {
            return '-';
        }

        $name = trim(implode(' ', array_filter([
            $employee['first_name'] ?? null,
            $employee['last_name'] ?? null,
        ])));

        return $name !== '' ? $name : '-';
    }
}
