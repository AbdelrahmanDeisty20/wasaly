<?php

use App\Models\User;
use App\Models\Otp;
use App\Services\API\AuthService;
use Illuminate\Support\Facades\Hash;

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// 1. Setup a test user
$user = User::firstOrCreate(['email' => 'test@example.com'], [
    'full_name' => 'Test User',
    'phone' => '123456789',
    'password' => 'password',
    'type' => 'user',
]);

$service = new AuthService();

// 2. Mock registration/resend data
$code = '123456';
$codeHash = Hash::make($code);

Otp::create([
    'email' => $user->email,
    'code' => $codeHash,
    'type' => 'register',
    'user_id' => $user->id,
    'expires_at' => now()->addMinutes(10),
]);

echo "OTP created in database.\n";

// 3. Try to verify
$data = [
    'email' => 'test@example.com',
    'code' => $code
];

echo "Verifying OTP with code: " . $code . "\n";
$result = $service->verifyOtp($data);

if ($result['status']) {
    echo "SUCCESS: OTP verified successfully!\n";
} else {
    echo "FAILURE: " . $result['message'] . "\n";
}

// 4. Test expired
echo "Testing expired OTP...\n";
Otp::where('email', $user->email)->update(['expires_at' => now()->subMinutes(1)]);
$result = $service->verifyOtp($data);
if (!$result['status'] && str_contains($result['message'], 'expired' ?? 'invalid')) {
    echo "SUCCESS: Expired OTP handled correctly.\n";
} else {
    echo "FAILURE: Expired OTP check failed.\n";
}

// Clean up
Otp::where('email', $user->email)->delete();
$user->delete();
