<?php

namespace App\Observers;

use App\Models\Voucher;
use Illuminate\Support\Facades\Auth;

class VoucherObserver
{
    public function creating(Voucher $voucher)
    {
        $voucher->created_by = Auth::user()->id;
        $voucher->updated_by = Auth::user()->id;
    }

    public function updating(Voucher $voucher)
    {
        $voucher->updated_by = Auth::user()->id;
    }
}
