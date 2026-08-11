<?php

namespace App\Models;

class JobFailed extends Job
{
    protected $table = 'failed_jobs';

    protected $fillable = ['uuid', 'connection', 'queue', 'payload', 'exception', 'failed_at'];

    public $timestamps = false;
}
