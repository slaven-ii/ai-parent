<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Threads extends Model
{
    use HasFactory;

    const STATUS_ACTIVE = "active";
    const STATUS_ARHIVED = "arhived";
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'title',
        'assistant_id'
    ];

    public function messages(): HasMany
    {
        return $this->hasMany(ThreadsMessages::class);
    }

    public function runs(): HasMany
    {
        return $this->hasMany(ThreadsRuns::class);
    }
}
