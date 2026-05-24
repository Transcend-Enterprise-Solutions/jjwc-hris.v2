<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\BioTimeService;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class FetchBioTimeTransactionsMonthly extends Command
{
    protected $signature = 'fetch:biotime-transactions-monthly';
    protected $description = 'Fetch transactions from BioTime API for the current month and save to database';

    protected $bioTimeService;

    public function __construct(BioTimeService $bioTimeService)
    {
        parent::__construct();
        $this->bioTimeService = $bioTimeService;
    }

    public function handle()
    {
        $startDate = Carbon::now()->startOfMonth();
        $endDate = Carbon::now();

        $this->info("Fetching transactions day-by-day from {$startDate->format('Y-m-d')} to {$endDate->format('Y-m-d')}");

        $totalFetched = 0;

        while ($startDate->lte($endDate)) {
            $dayStart = $startDate->copy()->startOfDay()->format('Y-m-d H:i:s');
            $dayEnd = $startDate->copy()->endOfDay()->format('Y-m-d H:i:s');

            $params = [
                'page' => 1,
                'page_size' => 100,
                'start_time' => $dayStart,
                'end_time' => $dayEnd,
            ];

            $this->info("Processing date: " . $startDate->toDateString());

            do {
                try {
                    $response = $this->bioTimeService->getTransactions($params);

                    foreach ($response['data'] as $transactionData) {
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
                    }

                    $fetched = count($response['data']);
                    $totalFetched += $fetched;

                    Log::info("Fetched {$fetched} on {$startDate->toDateString()} page {$params['page']}");

                    $params['page']++;
                } catch (\Exception $e) {
                    Log::error("Error on {$startDate->toDateString()}: " . $e->getMessage());
                    $this->error("Error: " . $e->getMessage());
                    break;
                }
            } while (!empty($response['next']));

            $startDate->addDay();
        }

        $this->info("Done. Total transactions fetched: {$totalFetched}");
        Log::info("Monthly fetch complete. Total transactions: {$totalFetched}");
    }
}
