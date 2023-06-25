<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ResetUserMonthlyDiamond extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:reset-monthly-diamond';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'reset monthly diamond after 30 day';

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
     * @return mixed
     */
    public function handle()
    {
        DB::table('users')->where('agency_id', '!=', 0)->orWhere('agency_id', '!=', null)
          ->update(['monthly_diamond_received' => 0]);

        //$this->info('update-room-user-now:cron Command Run Successfully !');
    }
}
