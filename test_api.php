<?php

// Test API endpoints
$baseUrl = 'http://task-octpber-.test/api';

echo "=== Testing Project Management API ===\n\n";

// 1. Test Login
echo "1. Testing Login...\n";
$loginData = [
    'email'    => 'ahmed@company.com',
    'password' => 'password123',
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $baseUrl . '/auth/login');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($loginData));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "Login Response (HTTP $httpCode):\n";
$loginResult = json_decode($response, true);
print_r($loginResult);

if ($httpCode === 200 && isset($loginResult['data']['token'])) {
    $token = $loginResult['data']['token'];
    echo "\n✅ Login successful! Token: " . substr($token, 0, 20) . "...\n\n";

    // 2. Test Profile
    echo "2. Testing Profile...\n";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $baseUrl . '/auth/profile');
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $token,
        'Content-Type: application/json',
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    echo "Profile Response (HTTP $httpCode):\n";
    $profileResult = json_decode($response, true);
    print_r($profileResult);

    // 3. Test Projects
    echo "\n3. Testing Projects...\n";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $baseUrl . '/projects');
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $token,
        'Content-Type: application/json',
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    echo "Projects Response (HTTP $httpCode):\n";
    $projectsResult = json_decode($response, true);
    print_r($projectsResult);

    // 4. Test Tasks
    echo "\n4. Testing Tasks...\n";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $baseUrl . '/tasks');
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $token,
        'Content-Type: application/json',
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    echo "Tasks Response (HTTP $httpCode):\n";
    $tasksResult = json_decode($response, true);
    print_r($tasksResult);

    // 5. Test Stats
    echo "\n5. Testing Stats...\n";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $baseUrl . '/stats');
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $token,
        'Content-Type: application/json',
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    echo "Stats Response (HTTP $httpCode):\n";
    $statsResult = json_decode($response, true);
    print_r($statsResult);

} else {
    echo "\n❌ Login failed!\n";
}

echo "\n=== Test Complete ===\n";
