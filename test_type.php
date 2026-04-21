<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$profile = App\Models\ProfileTemplate::first();
echo "ID: " . $profile->profile_template_id . "\n";
echo "Type of ID: " . gettype($profile->profile_template_id) . "\n";
