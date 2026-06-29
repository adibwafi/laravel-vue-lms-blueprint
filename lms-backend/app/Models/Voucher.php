<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Uuid;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\SoftDeletes;

class Voucher extends Model
{
    use HasFactory, Uuid, SoftDeletes;

    public $incrementing = false;

    protected $keyType = 'uuid';

    protected $guarded = [];


    public function scopeSearch($query, $data)
    {
        if ($data->search) {
            array_push($this->fillable, "id");

            $searchableColumns = ['token']; // Kolom-kolom yang ingin dicari

            for ($i = 0; $i < count($searchableColumns); $i++) {
                if ($i == 0) {
                    $query->where($searchableColumns[$i], 'like', '%' . $data->search . '%');
                } else {
                    $query->orWhere($searchableColumns[$i], 'like', '%' . $data->search . '%');
                }
            }
            $query->orWhereHas('redeemedBy', function ($q) use ($data) {
                $q->where('fullname', 'like', "%{$data->search}%")
                    ->orWhere('email', 'like', "%{$data->search}%");
            });
        }

        if ($data->sortBy || $data->orderBy) {
            if (empty($data->orderBy)) {
                $data->orderBy = 'asc';
            }
            $query->orderBy($data->sortBy, $data->orderBy);
        }
        // error_log("Query: " . json_encode($query->toSql()));
        return $query;
    }

    public function redeemedBy()
    {
        return $this->belongsTo(User::class, 'redeemed_by', 'id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by', 'id');
    }

    public function courseCategory()
    {
        return $this->belongsTo(CourseCategory::class, 'course_category_id', 'id');
    }
}
