<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
use App\Models\User;
use Illuminate\Support\Facades\Hash;

$u = User::where('email', 'admin@admin.com')->first();
if($u){
    $u->password = Hash::make('password');
    $u->save();
    echo 'SUCCESS: Password reset to "password" for admin@admin.com';
} else {
    echo 'ERROR: User admin@admin.com not found';
}
