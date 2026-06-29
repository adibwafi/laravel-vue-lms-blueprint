<?php

namespace App\Observers;

use App\Models\ExamPoin;
use Illuminate\Support\Str;

class ExamPoinObserver
{
    public function creating(ExamPoin $examPoin)
    {
        $examPoin->random_seed = Str::random(64);
    }
}
