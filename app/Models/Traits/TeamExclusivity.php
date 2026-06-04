<?php
namespace App\Models\Traits;

trait TeamExclusivity
{
    protected static function boot() {
        parent::boot();

        static::addGlobalScope('team_exclusivity', function ($builder) {
            if ( \Auth::check() ) {
                $user = \Auth::user();
                $builder->where(function ($query) use ($user) {
                    $table = (new static)->getTable();
                    $query->orWhere($table.'.team_id', $user->team->id);
                });
            }
        });

        static::creating(function ($model) {
            if ( \Auth::check() ) {
                $model->team_id = \Auth::user()->team->id;
                $model->created_by = \Auth::user()->id;
            }
        });

        static::updating(function ($model) {
            unset($model->team_id);
            unset($model->created_by);
        });
    }
}
