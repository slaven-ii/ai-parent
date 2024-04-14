<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ThreadsMessages extends Model
{
    use HasFactory;

    const ROLE_ASSISTANT = 'assistant';
    const ROLE_USER = "user";
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'id',
        'threads_id',
        'run_id',
        'role',
        'content'
    ];

    public function thread()
    {
        return $this->belongsTo(Threads::class);
    }
}
