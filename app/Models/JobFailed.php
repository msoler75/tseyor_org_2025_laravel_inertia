<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Support\Facades\Log;

class JobFailed extends Job
{
    protected $table = 'failed_jobs';
    protected $fillable = ['uuid', 'connection', 'queue', 'payload', 'exception', 'failed_at'];
    public $timestamps = false;
}
