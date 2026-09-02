<?php

namespace App\Console\Commands;

use App\Jobs\AutoSaveDtrRecords;
use App\Models\Transaction;
use App\Models\User;
use App\Services\BioTimeService;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use ReflectionMethod;

class BackfillDtrRecords extends Command
{
    protected $signature = 'dtr:backfill
        {--start= : Start date, YYYY-MM-DD}
        {--end= : End date, YYYY-MM-DD}
        {--emp-code=* : Employee code to rebuild. May be repeated.}
        {--name= : Employee name search.}
        {--fetch-biometric : Fetch biometric transactions before rebuilding DTR.}';

    protected $description = 'Fetch biometric punches and rebuild DTR records for a past date range.';

    public function handle(BioTimeService $bioTimeService): int
    {
        $start = $this->parseDateOption('start') ?? now()->toDateString();
        $end = $this->parseDateOption('end') ?? $start;

        if (Carbon::parse($start)->gt(Carbon::parse($end))) {
            $this->error('The start date must be before or equal to the end date.');
            return self::FAILURE;
        }

        $empCodes = collect($this->option('emp-code'))
            ->filter()
            ->map(fn ($code) => trim((string) $code))
            ->unique()
            ->values();

        if ($this->option('fetch-biometric')) {
            $this->fetchBiometricTransactions($bioTimeService, $start, $end, $empCodes);
        }

        $users = $this->targetUsers($empCodes, (string) $this->option('name'));

        if ($users->isEmpty()) {
            $this->warn('No matching employee accounts found.');
            return self::SUCCESS;
        }

        $this->info("Rebuilding DTR for {$users->count()} employee(s) from {$start} to {$end}.");

        $job = new AutoSaveDtrRecords();
        $processUserDtr = new ReflectionMethod($job, 'processUserDtr');
        $processUserDtr->setAccessible(true);

        $rows = 0;
        foreach ($users as $user) {
            foreach (CarbonPeriod::create($start, $end) as $date) {
                $processUserDtr->invoke($job, $user, $date->toDateString());
                $rows++;
            }
        }

        $this->info("DTR backfill completed. Processed {$rows} employee-day(s).");

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

    protected function targetUsers($empCodes, ?string $name)
    {
        return User::with('userData')
            ->where('user_role', 'emp')
            ->when($empCodes->isNotEmpty(), fn ($query) => $query->whereIn('emp_code', $empCodes))
            ->when($name, function ($query) use ($name) {
                $query->where(function ($subQuery) use ($name) {
                    $subQuery->where('name', 'like', "%{$name}%")
                        ->orWhereHas('userData', function ($userData) use ($name) {
                            $userData->where('first_name', 'like', "%{$name}%")
                                ->orWhere('middle_name', 'like', "%{$name}%")
                                ->orWhere('surname', 'like', "%{$name}%");
                        });
                });
            })
            ->orderBy('name')
            ->get();
    }

    protected function fetchBiometricTransactions(BioTimeService $bioTimeService, string $start, string $end, $empCodes): void
    {
        $this->info("Fetching biometric transactions from {$start} to {$end}.");

        $totalFetched = 0;
        foreach (CarbonPeriod::create($start, $end) as $date) {
            $page = 1;
            $dateFetched = 0;

            do {
                $params = [
                    'page' => $page,
                    'page_size' => 100,
                    'start_time' => $date->copy()->startOfDay()->format('Y-m-d H:i:s'),
                    'end_time' => $date->copy()->endOfDay()->format('Y-m-d H:i:s'),
                ];

                if ($empCodes->count() === 1) {
                    $params['emp_code'] = $empCodes->first();
                }

                $response = $bioTimeService->getTransactions($params);
                $records = $response['data'] ?? [];

                foreach ($records as $transactionData) {
                    if ($empCodes->isNotEmpty() && !$empCodes->contains((string) ($transactionData['emp_code'] ?? ''))) {
                        continue;
                    }

                    Transaction::updateOrCreate(
                        ['id' => $transactionData['id']],
                        [
                            'emp_code' => $transactionData['emp_code'],
                            'punch_time' => $transactionData['punch_time'],
                            'punch_state' => $transactionData['punch_state'],
                            'punch_state_display' => $transactionData['punch_state_display'],
                            'verify_type' => $transactionData['verify_type'],
                            'verify_type_display' => $transactionData['verify_type_display'],
                            'area_alias' => $transactionData['area_alias'],
                            'upload_time' => $transactionData['upload_time'],
                        ]
                    );

                    $dateFetched++;
                    $totalFetched++;
                }

                $page++;
            } while (!empty($response['next']));

            $this->line($date->toDateString() . ": {$dateFetched} transaction(s)");
            Log::info('DTR backfill fetched biometric transactions', [
                'date' => $date->toDateString(),
                'count' => $dateFetched,
            ]);
        }

        $this->info("Biometric fetch completed. Saved {$totalFetched} transaction(s).");
    }
}
