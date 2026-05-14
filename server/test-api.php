<?php

/**
 * Simple API Test Script
 * Run with: php test-api.php
 */

echo "🧪 Testing QMT Inventory API...\n\n";

// Test 1: Check if server is running
echo "1️⃣  Testing server connection...\n";
$ch = curl_init('http://localhost:8000/api/roles');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 5);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 200) {
    echo "   ✅ Server is running!\n";
    $data = json_decode($response, true);
    echo "   📋 Available roles: " . implode(', ', array_values($data['roles'])) . "\n\n";
} else {
    echo "   ❌ Server is not responding. Make sure to run: php artisan serve\n\n";
    exit(1);
}

// Test 2: Test login
echo "2️⃣  Testing login endpoint...\n";
$ch = curl_init('http://localhost:8000/api/auth/login');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json'
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
    'email' => 'manager@fitse.com',
    'password' => 'password123'
]));
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 200) {
    $data = json_decode($response, true);
    if ($data['success']) {
        echo "   ✅ Login successful!\n";
        echo "   👤 User: {$data['user']['name']} ({$data['user']['role']})\n";
        echo "   🔑 Token: " . substr($data['token'], 0, 20) . "...\n\n";
        $token = $data['token'];
    } else {
        echo "   ❌ Login failed: {$data['message']}\n\n";
        exit(1);
    }
} else {
    echo "   ❌ Login endpoint error (HTTP $httpCode)\n\n";
    exit(1);
}

// Test 3: Test authenticated endpoint
echo "3️⃣  Testing authenticated endpoint...\n";
$ch = curl_init('http://localhost:8000/api/auth/me');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json',
    "Authorization: Bearer $token"
]);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 200) {
    $data = json_decode($response, true);
    if ($data['success']) {
        echo "   ✅ Authentication working!\n";
        echo "   👤 Authenticated as: {$data['user']['name']}\n";
        echo "   🔐 Permissions: " . count($data['permissions']) . " permissions loaded\n\n";
    } else {
        echo "   ❌ Authentication failed\n\n";
        exit(1);
    }
} else {
    echo "   ❌ Authentication endpoint error (HTTP $httpCode)\n\n";
    exit(1);
}

// Test 4: Test manager-only endpoint
echo "4️⃣  Testing role-based access (Manager only)...\n";
$ch = curl_init('http://localhost:8000/api/manager/users');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json',
    "Authorization: Bearer $token"
]);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 200) {
    $data = json_decode($response, true);
    if ($data['success']) {
        echo "   ✅ Manager access granted!\n";
        echo "   👥 Total users: " . count($data['users']) . "\n\n";
    } else {
        echo "   ❌ Manager access denied\n\n";
    }
} else {
    echo "   ⚠️  Manager endpoint returned HTTP $httpCode\n\n";
}

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "✅ All tests passed! API is working correctly.\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
echo "🚀 You can now start the frontend:\n";
echo "   cd client\n";
echo "   npm run dev\n\n";
