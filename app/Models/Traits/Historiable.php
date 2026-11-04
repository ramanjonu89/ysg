<?php

namespace App\Models\Traits;

use App\Models\History;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

trait Historiable
{
  public static function bootHistoriable()
  {
    static::created(function ($entity) {
      static::storeLog($entity, static::class, 'CREATED');
    });

    static::updated(function ($entity) {
      static::storeLog($entity, static::class, 'UPDATED');
    });

    static::deleted(function ($entity) {
      static::storeLog($entity, static::class, 'DELETED');
    });
  }

  /**
   * Generate the model name
   * @param  Model  $model
   * @return string
   */
  public static function getTagName(Model $model)
  {
    return !empty($model->tagName) ? $model->tagName : Str::title(Str::snake(class_basename($model), ' '));
  }

  /**
   * Retrieve the current login user id
   * @return int|string|null
   */
  public static function activeUserId()
  {
    return Auth::guard(static::activeUserGuard())->id();
  }

  /**
   * Retrieve the current login user guard name
   * @return mixed|null
   */
  public static function activeUserGuard()
  {
    foreach (array_keys(config('auth.guards')) as $guard) {

      if (auth()->guard($guard)->check()) {
        return $guard;
      }
    }
    return 'command';
  }

  public static function storeLog($model, $modelPath, $action)
  {
    $adminUser = 1536;
    $newValues = null;
    $oldValues = null;
    if ($action === 'CREATED') {
      $newValues = $model->getAttributes();
    } elseif ($action === 'UPDATED') {
      $newValues = $model->getChanges();
    }

    unset($newValues['updated_at']);
    if (!empty($model->ignoreLoggingFields)) {
      foreach ($newValues as $key => $value) {
        if (in_array($key, $model->ignoreLoggingFields)) unset($newValues[$key]);
      }
    }

    $keyChanges = array_keys($newValues);

    if ($action !== 'CREATED') {
      $oldValues = $model->getOriginal();
      foreach ($oldValues as $key => $value) {
        if (!in_array($key, $keyChanges)) {
          unset($oldValues[$key]);
          continue;
        }
      }
    }

    foreach ($newValues as $key => $value) {
      if (isset($oldValues) && isset($oldValues[$key]) && $newValues[$key] == $oldValues[$key]) {
        unset($oldValues[$key]);
        unset($newValues[$key]);
      }
    }

    $history = new History();
    $history->historiable_id = $model->id;
    $history->historiable_type = $modelPath;
    $history->user_id = static::activeUserId() ?? $adminUser;
    $history->guard_name = static::activeUserGuard();
    $history->module_name = static::getTagName($model);
    $history->action = $action;
    $history->old_value = !empty($oldValues) ? json_encode($oldValues) : null;
    $history->new_value = !empty($newValues) ? json_encode($newValues) : null;
    $history->ip_address = request()->ip();
    $history->save();
  }
}