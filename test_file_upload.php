<?php

// Test File Upload API
$baseUrl = 'http://task-octpber-.test/api';

echo "=== Testing File Upload API ===\n\n";

// 1. Login to get token
echo "1. Testing Login...\n";
$loginData = [
    'email'    => 'ahmed@company.com',
    'password' => 'password123',
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $baseUrl . '/auth/login');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($loginData));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json',
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$loginResult = json_decode($response, true);

if ($httpCode === 200 && isset($loginResult['data']['token'])) {
    $token = $loginResult['data']['token'];
    echo "✅ Login successful! Token: " . substr($token, 0, 20) . "...\n\n";
} else {
    echo "❌ Login failed!\n";
    print_r($loginResult);
    exit;
}

// 2. Create a test file
echo "2. Creating test file...\n";
$testFile    = 'test_upload.txt';
$testContent = "This is a test file for upload testing.\nCreated at: " . date('Y-m-d H:i:s');
file_put_contents($testFile, $testContent);
echo "✅ Test file created: $testFile\n\n";

// 3. Test file upload to task
echo "3. Testing file upload to task...\n";

// First, get a task ID
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $baseUrl . '/tasks');
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $token,
    'Accept: application/json',
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$tasksResult = json_decode($response, true);

if ($httpCode === 200 && isset($tasksResult['data']['data'][0]['id'])) {
    $taskId = $tasksResult['data']['data'][0]['id'];
    echo "✅ Found task ID: $taskId\n";
} else {
    echo "❌ Failed to get task ID!\n";
    print_r($tasksResult);
    exit;
}

// Now upload file to this task
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $baseUrl . "/tasks/$taskId/attachments");
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, [
    'file' => new CURLFile($testFile, 'text/plain', 'test_upload.txt'),
]);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $token,
    'Accept: application/json',
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$uploadResult = json_decode($response, true);

echo "Upload Response (HTTP $httpCode):\n";
print_r($uploadResult);

if ($httpCode === 201) {
    echo "✅ File upload successful!\n";

    // Check if file was actually created
    if (isset($uploadResult['data']['file_path'])) {
        $filePath = $uploadResult['data']['file_path'];
        if (file_exists($filePath)) {
            echo "✅ File exists on disk: $filePath\n";
            echo "File size: " . filesize($filePath) . " bytes\n";
        } else {
            echo "❌ File not found on disk: $filePath\n";
        }
    }
} else {
    echo "❌ File upload failed!\n";
}

// 4. Clean up test file
echo "\n4. Cleaning up...\n";
if (file_exists($testFile)) {
    unlink($testFile);
    echo "✅ Test file removed\n";
}

echo "\n=== Test Complete ===\n";
