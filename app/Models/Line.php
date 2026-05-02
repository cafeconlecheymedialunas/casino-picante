<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Line extends Model
{
    protected $fillable = [
        'name',
        'icon',
        'description',
        'status',
        'whatsapp',
        'whatsapp_message',
        'telegram',
        'telegram_message',
        'whatsapp_channel',
        'facebook',
        'instagram',
    ];
}

class Setting extends Model
{
    protected $table = 'settings';

    protected $fillable = [
        'key',
        'value',
    ];
}
