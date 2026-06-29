<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CreateSuperadmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:superadmin';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create Superadmin';

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
        Role::findOrCreate('user');
        $user = User::whereEmail('superadmin@email.com')->first();
        if (!empty($user)) {
            $this->info("Superadmin already exist!");
        }
        try {
            DB::beginTransaction();
            $user = User::create([
                'fullname' => 'Superadmin',
                'email' => 'superadmin@email.com',
                'password' => Hash::make('Password123123'),
                'status' => 'active',
                'email_verified_at' => Carbon::now(),
            ]);
            $sa = Role::create(['name' => 'Super-Admin']);
            $user->assignRole($sa);
            DB::commit();
            $this->info("Success create superadmin!");
        } catch (\Throwable $th) {
            Log::error($th);
        }
    }
}
