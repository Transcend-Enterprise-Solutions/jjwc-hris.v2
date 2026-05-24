<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\BioTimeService;
use App\Models\Transaction;
use Carbon\Carbon;

class FetchBioTimeTransactions extends Command
{
    protected $signature = 'fetch:biotime-transactions';
    protected $description = 'Fetch transactions from BioTime API and save to database';
    protected $bioTimeService;

    public function __construct(BioTimeService $bioTimeService)
    {
        parent::__construct();
        $this->bioTimeService = $bioTimeService;
    }

    public function handle()
    {
        $currentDate = Carbon::now();

        // Set start_time and end_time for the current day
        $startTime = $currentDate->startOfDay()->format('Y-m-d H:i:s');
        $endTime = $currentDate->endOfDay()->format('Y-m-d H:i:s');

        $params = [
            'page' => 1,
            'page_size' => 100,
            'start_time' => $startTime,
            'end_time' => $endTime,
        ];

        do {
            try {
                $response = $this->bioTimeService->getTransactions($params);

                // Process each transaction
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

                // Increment the page for the next iteration
                $params['page']++;
            } catch (\Exception $e) {
                $this->error('Error: ' . $e->getMessage());
                return;
            }

        } while ($response['next'] !== null);

        $this->info('Transactions fetched and saved successfully.');
        echo('Transactions fetched and saved successfully');
    }
}