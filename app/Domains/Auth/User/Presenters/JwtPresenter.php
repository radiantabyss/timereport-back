<?php
namespace App\Domains\Auth\User\Presenters;

class JwtPresenter
{
    public static function run($item) {
        return [
            'id' => $item->id,
            'uuid' => $item->uuid,
            'team_id' => $item->team->id,
            'name' => $item->name,
            'lang' => $item->meta['lang'] ?? config('app.default_lang'),
        ];
    }
}
