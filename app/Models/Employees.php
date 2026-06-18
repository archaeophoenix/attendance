<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Employees extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $fillable = [
        'code', 'name', 'email', 'phone', 'position', 'status', 'join_date'
    ];

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendances::class);
    }
}
