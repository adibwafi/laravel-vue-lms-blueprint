<?php

namespace App\Providers;

use App\Models\ExamPoin;
use App\Models\Voucher;
use Illuminate\Support\Facades\DB;
use App\Observers\ExamPoinObserver;
use App\Observers\VoucherObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Voucher::observe(VoucherObserver::class);
        ExamPoin::observe(ExamPoinObserver::class);

        // DB::listen(function ($query) {
        //     $sql = $query->sql;
        //     $bindings = $query->bindings;
        //     $time = $query->time;

        //     // Build the full SQL query with bindings
        //     $fullSql = vsprintf(str_replace(['%', '?'], ['%%', '%s'], $sql), $bindings);

        //     // Output the SQL query and its bindings to the console
        //     $message = "[Query] {$fullSql} [Time: {$time}ms]";
        //     \Illuminate\Support\Facades\Log::info($message);
        // });
    }
}
