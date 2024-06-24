<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Support\Facades\Log;

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
        return $payload['displayName'];
    }

    public function getDataAttribute($value)
    {
        $payload = @json_decode($this->getOriginal('payload'), true);
        try {
            $command = unserialize($payload['data']['command']);
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
