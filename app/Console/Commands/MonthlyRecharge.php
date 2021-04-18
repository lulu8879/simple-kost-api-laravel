<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class MonthlyRecharge extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'monthly:recharge';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scheduled command to recharge user credit on every start of the month
    ';

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
        $users = User::where('role', 1)->update(['credit' => 20]);
        $users = User::where('role', 2)->update(['credit' => 40]);

        $this->info('Recharge credit success.');
    }
}
