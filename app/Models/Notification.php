<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = ['type', 'user_id', 'content', 'title', 'sent_at'];

    protected $casts = [
        'sent_at' => 'date'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
