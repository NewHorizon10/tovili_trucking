<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\frontend\TruckCompanyController;

class SendNotificationUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sendNotification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $track = new TruckCompanyController();
        $track->willSendScheduledMessages();
        return Command::SUCCESS;
    }
}
