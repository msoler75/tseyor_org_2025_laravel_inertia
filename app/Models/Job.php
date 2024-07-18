<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Support\Facades\Log;
use Illuminate\Mail\SendQueuedMailable;
use Illuminate\Notifications\SendQueuedNotifications;

class Job extends Model
{
    use CrudTrait;
    protected $table = 'jobs';
    protected $fillable = ['queue', 'payload', 'attempts', 'reserved_at', 'available_at', 'created_at'];
    public $timestamps = false;


    // accesors

    // accesors

    public function getDisplayAttribute($value)
    {
        $payload = @json_decode($this->getOriginal('payload'), true);
        Log::channel("jobs")->info("Job::getDisplayAttribute", ["payload"=>$payload]);
        return $payload['displayName'] ?? 'error';
    }

    public function getDataAttribute($value)
    {
        try {
            $payload = @json_decode($this->getOriginal('payload'), true);
            Log::channel("jobs")->info("Job::getDataAttribute", ["payload"=>$payload]);
            $command = @unserialize($payload['data']['command']);
            Log::channel("jobs")->info("class:".class_basename($command));
            if(is_object($command) && $command instanceof SendQueuedNotifications && method_exists($command->notification, '__toString'))
                return $command->notification->__toString();
            if(is_object($command) && $command instanceof SendQueuedMailable && method_exists($command->mailable, '__toString'))
                    return $command->mailable->__toString();
            return "";//.$command;
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            $command = "No encontrado";
        } catch(\Exception $e) {
            $command = "Error";
        } catch(\ErrorException $e) {
            $command = "Error";
        }
        catch(\Illuminate\View\ViewException $e) {
            $command = "View Exception 1";
        }
        catch(\Spatie\LaravelIgnition\Exceptions\ViewException $e) {
            $command = "View Exception 2";
        }
        catch(\Illuminate\Contracts\Container\BindingResolutionException $exception) {
            $command = "BindingResolutionException";
        }
        return $command;
    }
}
