<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;

class History extends Model
{
  public $timestamps = false;

  protected $with = ['user'];

  public static function boot()
  {
    parent::boot();

    static::creating(function ($model) {
      $model->created_at = $model->freshTimestamp();
    });
  }
  
  protected $fillable = [
    'historiable_id',
    'historiable_type',
    'user_id',
    'guard_name',
    'module_name',
    'action',
    'old_value',
    'new_value',
    'ip_address'
  ];

  public function historiable()
  {
    return $this->morphTo();
  }

  public function user()
  {
    return $this->belongsTo(User::class)->withTrashed();
  }

  public function isUpdated(): bool
  {
    return $this->action === 'UPDATED';
  }

  public function updatedFieldsArray()
  {
    return array_keys((array)json_decode($this->new_value));
  }

  public function updatedFields()
  {
    return implode(', ', $this->updatedFieldsArray());
  }

  public function oldValueObject()
  {
    return json_decode($this->old_value);
  }

  public function newValueObject()
  {
    return json_decode($this->new_value);
  }

  public function getNameAttribute()
  {
    return $this->historiable->name;
  }
}
