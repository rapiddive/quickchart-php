<?php

require_once('../QuickChart.php');

/**
 * Test script to verify authentication functionality
 */

echo "QuickChart Authentication Test Suite\n";
echo "===================================\n\n";

// Test 1: Basic Auth Configuration
echo "Test 1: Basic Authentication\n";
echo "----------------------------\n";

$chart1 = new QuickChart();
$chart1->setBasicAuth('testuser', 'testpass');

// Check internal properties (using reflection for testing)
$reflection = new ReflectionClass($chart1);
$usernameProperty = $reflection->getProperty('authUsername');
$passwordProperty = $reflection->getProperty('authPassword');
$bearerProperty = $reflection->getProperty('bearerToken');

$usernameProperty->setAccessible(true);
$passwordProperty->setAccessible(true);
$bearerProperty->setAccessible(true);

$username = $usernameProperty->getValue($chart1);
$password = $passwordProperty->getValue($chart1);
$bearer = $bearerProperty->getValue($chart1);

echo "✓ Username set: " . ($username === 'testuser' ? 'PASS' : 'FAIL') . "\n";
echo "✓ Password set: " . ($password === 'testpass' ? 'PASS' : 'FAIL') . "\n";
echo "✓ Bearer token cleared: " . ($bearer === null ? 'PASS' : 'FAIL') . "\n\n";

// Test 2: Bearer Token Configuration
echo "Test 2: Bearer Token Authentication\n";
echo "-----------------------------------\n";

$chart2 = new QuickChart();
$chart2->setBearerToken('test_token_123');

$reflection2 = new ReflectionClass($chart2);
$usernameProperty2 = $reflection2->getProperty('authUsername');
$passwordProperty2 = $reflection2->getProperty('authPassword');
$bearerProperty2 = $reflection2->getProperty('bearerToken');

$usernameProperty2->setAccessible(true);
$passwordProperty2->setAccessible(true);
$bearerProperty2->setAccessible(true);

$username2 = $usernameProperty2->getValue($chart2);
$password2 = $passwordProperty2->getValue($chart2);
$bearer2 = $bearerProperty2->getValue($chart2);

echo "✓ Bearer token set: " . ($bearer2 === 'test_token_123' ? 'PASS' : 'FAIL') . "\n";
echo "✓ Username cleared: " . ($username2 === null ? 'PASS' : 'FAIL') . "\n";
echo "✓ Password cleared: " . ($password2 === null ? 'PASS' : 'FAIL') . "\n\n";

// Test 3: Constructor Authentication
echo "Test 3: Constructor Authentication\n";
echo "----------------------------------\n";

$chart3 = new QuickChart([
    'authUsername' => 'constructor_user',
    'authPassword' => 'constructor_pass',
    'bearerToken' => 'constructor_token'
]);

$reflection3 = new ReflectionClass($chart3);
$usernameProperty3 = $reflection3->getProperty('authUsername');
$passwordProperty3 = $reflection3->getProperty('authPassword');
$bearerProperty3 = $reflection3->getProperty('bearerToken');

$usernameProperty3->setAccessible(true);
$passwordProperty3->setAccessible(true);
$bearerProperty3->setAccessible(true);

$username3 = $usernameProperty3->getValue($chart3);
$password3 = $passwordProperty3->getValue($chart3);
$bearer3 = $bearerProperty3->getValue($chart3);

echo "✓ Constructor username: " . ($username3 === 'constructor_user' ? 'PASS' : 'FAIL') . "\n";
echo "✓ Constructor password: " . ($password3 === 'constructor_pass' ? 'PASS' : 'FAIL') . "\n";
echo "✓ Constructor bearer: " . ($bearer3 === 'constructor_token' ? 'PASS' : 'FAIL') . "\n\n";

// Test 4: Clear Authentication
echo "Test 4: Clear Authentication\n";
echo "----------------------------\n";

$chart4 = new QuickChart();
$chart4->setBasicAuth('user', 'pass');
$chart4->setBearerToken('token');
$chart4->clearAuth();

$reflection4 = new ReflectionClass($chart4);
$usernameProperty4 = $reflection4->getProperty('authUsername');
$passwordProperty4 = $reflection4->getProperty('authPassword');
$bearerProperty4 = $reflection4->getProperty('bearerToken');

$usernameProperty4->setAccessible(true);
$passwordProperty4->setAccessible(true);
$bearerProperty4->setAccessible(true);

$username4 = $usernameProperty4->getValue($chart4);
$password4 = $passwordProperty4->getValue($chart4);
$bearer4 = $bearerProperty4->getValue($chart4);

echo "✓ Username cleared: " . ($username4 === null ? 'PASS' : 'FAIL') . "\n";
echo "✓ Password cleared: " . ($password4 === null ? 'PASS' : 'FAIL') . "\n";
echo "✓ Bearer cleared: " . ($bearer4 === null ? 'PASS' : 'FAIL') . "\n\n";

// Test 5: Header Building
echo "Test 5: Authentication Header Building\n";
echo "--------------------------------------\n";

$chart5 = new QuickChart();

// Test Basic Auth headers
$chart5->setBasicAuth('user', 'pass');
$reflection5 = new ReflectionClass($chart5);
$method = $reflection5->getMethod('buildAuthHeaders');
$method->setAccessible(true);

$headers = $method->invoke($chart5, ['Content-Type: application/json']);
$authHeaderFound = false;
$expectedAuth = 'Authorization: Basic ' . base64_encode('user:pass');

foreach ($headers as $header) {
    if (strpos($header, 'Authorization: Basic') === 0) {
        $authHeaderFound = ($header === $expectedAuth);
        break;
    }
}

echo "✓ Basic auth header: " . ($authHeaderFound ? 'PASS' : 'FAIL') . "\n";

// Test Bearer Token headers
$chart5->setBearerToken('my_token');
$headers2 = $method->invoke($chart5, ['Content-Type: application/json']);
$bearerHeaderFound = false;

foreach ($headers2 as $header) {
    if ($header === 'Authorization: Bearer my_token') {
        $bearerHeaderFound = true;
        break;
    }
}

echo "✓ Bearer token header: " . ($bearerHeaderFound ? 'PASS' : 'FAIL') . "\n";

// Test no auth
$chart5->clearAuth();
$headers3 = $method->invoke($chart5, ['Content-Type: application/json']);
$noAuthHeader = true;

foreach ($headers3 as $header) {
    if (strpos($header, 'Authorization:') === 0) {
        $noAuthHeader = false;
        break;
    }
}

echo "✓ No auth headers: " . ($noAuthHeader ? 'PASS' : 'FAIL') . "\n\n";

echo "Authentication Test Suite Completed!\n";
echo "All tests check the internal authentication mechanism.\n";
echo "For actual API testing, you'll need valid credentials and endpoints.\n";

?>