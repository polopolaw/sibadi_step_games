<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    const STATUS_WAITING = 'waiting';
    const STATUS_PLAYING = 'active';
    const STATUS_CLOSED = 'closed';

    protected $fillable = [
        'user_id',
        'type',
        'capacity',
        'name',
        'status'
    ];

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

}
