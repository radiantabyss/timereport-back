<?php
namespace App\Models\Traits;

trait TeamExclusivityWithClient
{
    protected static function boot() {
        parent::boot();

        static::addGlobalScope('team_exclusivity', function ($builder) {
            if ( \Auth::check() ) {
                $user = \Auth::user();
                $builder->where(function ($query) use ($user) {
                    $table = (new static)->getTable();
                    $query->orWhere($table.'.team_id', $user->team->id);

                    if ( $user->type == 'client' ) {
                        $field = $table == 'client' ? 'id' : 'client_id';
                        $query->orWhere($table.'.'.$field, $user->client_id);
                    }
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

            if ( \Auth::check() && \Auth::user()->type == 'client' ) {
                unset($model->client_id);
            }
        });
    }
}
