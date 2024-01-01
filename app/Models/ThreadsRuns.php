<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ThreadsRuns extends Model
{
    use HasFactory;

    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_QUEUED = 'queued';
    const STATUS_REQUIRES_ACTION = 'requires_action';
    const STATUS_CANCELING = 'cancelling';
    const STATUS_CANCELED = 'cancelled';
    const STATUS_FAILED = 'failed';
    const STATUS_COMPLETED = 'completed';
    const STATUS_ECPIRED = 'expired';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'assistant_id',
        'status',
        'expires_at',
        'model',
        'instructions'
    ];
}
