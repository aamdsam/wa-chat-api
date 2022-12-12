<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MessageTemplates extends Model
{
    protected $fillable = [
        'template', 'message','description'
    ];
}
