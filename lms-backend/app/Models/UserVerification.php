<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Uuid;

class UserVerification extends Model
{
    use HasFactory, Uuid;

    public $incrementing = false;

    protected $keyType = 'uuid';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'users_id',
        'code',
        'type',
        'expired_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'expired_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'users_id', 'id');
    }

    public function scopeSearch($query, $data)
    {
        if ($data->search) {
            array_push($this->fillable, "id");
            for ($i = 0; $i < count($this->fillable); $i++) {
                if ($i == 0) {
                    $query->where($this->fillable[$i], 'like', '%'.$data->search.'%');
                } else {
                    $query->orWhere($this->fillable[$i], 'like', '%'.$data->search.'%');
                }
            }
        }

        if ($data->sortBy || $data->orderBy) {
            if (empty($data->orderBy)) {
                $data->orderBy = 'asc';
            }
            $query->orderBy($data->sortBy, $data->orderBy);
        }
        return $query;
    }
}
