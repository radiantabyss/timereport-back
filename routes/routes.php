<?php
use RA\Route;
use RA\RouteCrud;

Route::get('/', 'Index\IndexAction');

Route::get('/efactura/connect/{uuid}/{team_uuid}', 'Efactura\ConnectAction');
Route::get('/efactura/callback', 'Efactura\CallbackAction');

RouteCrud::run('ConversionRate');
