<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendances extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'employee_id', 'date', 'check_in', 'check_out', 'status', 'note'
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employees::class);
    }
}
