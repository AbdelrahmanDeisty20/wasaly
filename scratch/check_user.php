<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
use App\Models\User;

$u = User::where('email', 'admin@admin.com')->first();
if($u){
    echo "USER_FOUND\n";
    echo "Current is_active: " . ($u->is_active ? 'TRUE' : 'FALSE') . "\n";
    echo "Current type: " . ($u->type ?? 'NULL') . "\n";
    
    // Force active and admin type if needed
    $u->is_active = true;
    // If the panel has a check on 'type', let's make sure it's correct
    // Usually admin panels check for a specific type or role
    $u->save();
    
    echo "ACTION: User admin@admin.com is now forced to be ACTIVE.\n";
} else {
    echo "USER_NOT_FOUND\n";
}
