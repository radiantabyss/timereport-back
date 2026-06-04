<?php
use RA\Route;
use RA\RouteCrud;

Route::get('/client/search', 'Client\SearchAction');
Route::get('/project/search', 'Project\SearchAction');
Route::get('/user/search', 'User\SearchAction');

Route::get('/timereport', 'Timereport\ListAction');
Route::get('/invoice', 'Invoice\ListAction');
Route::get('/statement', 'Statement\ListAction');
