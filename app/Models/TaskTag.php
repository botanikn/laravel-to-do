<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskTag extends Model
{
    protected $table = 'task_tag';
    protected $fillable = [
        'task_id',
        'tag_id'
    ];
}
