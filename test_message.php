<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = App\Models\User::factory()->create();
Auth::login($user);

try {
    $request = Illuminate\Http\Request::create('/messages', 'GET');
    $response = app()->make(Illuminate\Contracts\Http\Kernel::class)->handle($request);
    echo $response->getContent();
} catch (\Throwable $e) {
    echo $e->getMessage() . "\n" . $e->getFile() . ":" . $e->getLine() . "\n";
    echo $e->getTraceAsString();
}
