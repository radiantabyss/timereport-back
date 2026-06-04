<?php
use RA\Route;
use RA\RouteCrud;

Route::get('/company/member/search', 'Company\Member\SearchAction');
Route::get('/client/search', 'Client\SearchAction');
Route::get('/project/search', 'Project\SearchAction');

Route::get('/timereport', 'Timereport\ListAction');
Route::get('/invoice', 'Invoice\ListAction');
Route::get('/invoice/single/{id}', 'Invoice\SingleAction');
Route::get('/invoice/pdf/{id}', 'Invoice\PdfAction');
