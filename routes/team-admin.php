<?php
use RA\Route;
use RA\RouteCrud;

Route::get('/auth/team/search-member', 'Auth\Team\SearchMemberAction');

RouteCrud::run('Client');
RouteCrud::run('Project');

RouteCrud::run('Timereport');
Route::post('/timereport/set-invoice', 'Timereport\SetInvoiceAction');

RouteCrud::run('Company\Company');
RouteCrud::run('Company\Member');
RouteCrud::run('Company\MemberSalary');
RouteCrud::run('Company\MemberAssociate');
RouteCrud::run('Company\Tax');
Route::get('/company/tax/current', 'Company\Tax\CurrentAction');

RouteCrud::run('InvoiceTemplate');
RouteCrud::run('Invoice');
Route::post('/invoice/prefill-from-timereport', 'Invoice\PrefillFromTimereportAction');
Route::get('/invoice/last-number', 'Invoice\LastNumberAction');
Route::get('/invoice/pdf/{id}', 'Invoice\PdfAction');
Route::post('/invoice/set-status/{id}', 'Invoice\SetStatusAction');
Route::post('/invoice/reverse/{id}', 'Invoice\ReverseAction');

RouteCrud::run('Statement');
Route::post('/statement/import', 'Statement\ImportAction');

RouteCrud::run('Supplier');
RouteCrud::run('ExpenseInvoice');

Route::get('/accounting/download-documents', 'Accounting\DownloadDocumentsAction');
Route::get('/conversion-rate/get-by-date', 'ConversionRate\GetByDateAction');
