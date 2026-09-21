<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$users = \App\Models\User::select('users.*');
$dt = \Yajra\DataTables\Facades\DataTables::of($users)
    ->addIndexColumn()
    ->addColumn('role', function($row) { return 'role'; })
    ->addColumn('action-btn', function($row) { return 'btn'; })
    ->rawColumns(['role', 'action-btn'])
    ->make(true);
    
echo $dt->getContent();
