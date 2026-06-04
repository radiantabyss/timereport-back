<?php
use RA\Route;
use RA\RouteCrud;

Route::post('/auth/team/invite-client', 'Auth\Team\InviteClientAction');
Route::get('/auth/team/list-clients', 'Auth\Team\ListClientsAction');
Route::get('/auth/team/delete-client/{id}', 'Auth\Team\DeleteClientAction');
Route::post('/auth/team/update-meta', 'Auth\Team\UpdateMetaAction');

//dashboard routes  -------------------------------------------------------------------
RouteCrud::run('Dashboard\Dashboard');
RouteCrud::run('Dashboard\View');
RouteCrud::run('Dashboard\Panel');

Route::post('/dashboard/dashboard/move/{id}', 'Dashboard\Dashboard\MoveAction');
Route::post('/dashboard/view/sort/{dashboard_id}', 'Dashboard\View\SortAction');
Route::get('/dashboard/panel/chart/{id}', 'Dashboard\Panel\ChartAction');
Route::get('/dashboard/panel/aggregation/{id}', 'Dashboard\Panel\AggregationAction');
