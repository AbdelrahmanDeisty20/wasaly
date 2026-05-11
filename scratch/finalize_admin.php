<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
use App\Models\User;
use Spatie\Permission\Models\Role;

$u = User::where('email', 'admin@admin.com')->first();
if($u){
    // Ensure the role exists
    $role = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
    
    // Assign the role
    $u->assignRole($role);
    $u->is_active = true;
    $u->save();
    
    echo "SUCCESS: User admin@admin.com is now ACTIVE and has the SUPER_ADMIN role.";
} else {
    echo "ERROR: User admin@admin.com not found";
}
