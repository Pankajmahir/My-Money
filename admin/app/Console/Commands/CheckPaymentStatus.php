<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class CheckPaymentStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'call api for check phone pe payment status';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $response = Http::get('https://makemypayment.co.in/admin/server.php/api/check-payment-status');

        // Handle the API response
        if ($response->successful()) {
            // API request was successful, process the response
            $data = $response->json();
            // ... process the data as needed
        } else {
            // API request failed, handle the error
            $error = $response->json();
            // ... handle the error as needed
        }
    }
}
