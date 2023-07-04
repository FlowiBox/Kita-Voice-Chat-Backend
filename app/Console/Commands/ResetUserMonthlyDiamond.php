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
        DB::statement("
            UPDATE users
            SET monthly_diamond_received = 0
            WHERE agency_id != 0
        ");

        $month = now()->month;
        $year = now()->year;
        DB::statement("
            UPDATE user_sallaries
            SET sallary = 0, cut_amount = 0
            WHERE month = :month and year = :year
        ", ['month' => $month, 'year' => $year]);

        //$this->info('update-room-user-now:cron Command Run Successfully !');
    }
}
