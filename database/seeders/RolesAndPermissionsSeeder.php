<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
 //php artisan db:seed --class=RolesAndPermissionsSeeder
class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $models = ["Address", "AppNotification", "Banner", "Brand", "Category", "Center", "Coupon", "Favorite", "Governorate", "Offer", "Page", "Review", "Service", "Setting", "Specification", "SubCategory", "OrderItem", "Cart", "CartItem", "ProductImage", "UserFcmToken", "User", "Product", "Order", "Provider"];

        $actions = ["ViewAny", "View", "Create", "Update", "Delete", "Restore", "ForceDelete", "ForceDeleteAny", "RestoreAny", "Replicate", "Reorder"];

        $allPermissions = [];
        foreach ($models as $model) {
            foreach ($actions as $action) {
                $permissionName = $action . ":" . $model;
                $allPermissions[] = $permissionName;
                Permission::firstOrCreate(['name' => $permissionName]);
            }
        }

        // Create roles
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminRole->givePermissionTo(Permission::all());

        $subAdminRole = Role::firstOrCreate(['name' => 'sub_admin']);
        // Assign sub-admin permissions (e.g., everything except deletion/force deletion)
        $subAdminPermissions = Permission::where('name', 'NOT LIKE', 'Delete%')
                                        ->where('name', 'NOT LIKE', 'ForceDelete%')
                                        ->get();
        $subAdminRole->givePermissionTo($subAdminPermissions);

        // Create default users
        $admin = User::firstOrCreate(
            ['email' => 'admin@wasaly.com'],
            [
                'full_name' => 'Admin User',
                'phone' => '01000000000',
                'password' => Hash::make('password'),
                'is_active' => true,
            ]
        );
        $admin->assignRole('admin');

        $subAdmin = User::firstOrCreate(
            ['email' => 'subadmin@wasaly.com'],
            [
                'full_name' => 'Sub Admin User',
                'phone' => '01111111111',
                'password' => Hash::make('password'),
                'is_active' => true,
            ]
        );
        $subAdmin->assignRole('sub_admin');
    }
}
