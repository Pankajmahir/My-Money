<?php

namespace App\Console\Commands;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class CallApiCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'call:api';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Call an API using Cron';

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
        // \Log::info("Cron is working fine!");
        // Make the API request using Laravel's HTTP client
        $response = Http::get('https://makemypayment.co.in/admin/server.php/api/reminder_cron');

        // Handle the API response
        if ($response->successful()) {
            // API request was successful, process the response
            $data = $response->json();
           \Log::info("Response is fine!");
            // ... process the data as needed
        } else {
            // API request failed, handle the error
            $error = $response->json();
            // ... handle the error as needed
        }
    }
}
