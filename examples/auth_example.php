<?php

require_once('../QuickChart.php');

// Example chart configuration
$chartConfig = [
    'type' => 'bar',
    'data' => [
        'labels' => ['January', 'February', 'March', 'April', 'May'],
        'datasets' => [[
            'label' => 'Sample Data',
            'data' => [12, 19, 3, 5, 2],
            'backgroundColor' => 'rgba(54, 162, 235, 0.2)',
            'borderColor' => 'rgba(54, 162, 235, 1)',
            'borderWidth' => 1
        ]]
    ],
    'options' => [
        'scales' => [
            'y' => [
                'beginAtZero' => true
            ]
        ]
    ]
];

echo "QuickChart Authentication Examples\n";
echo "=================================\n\n";

// Example 1: Using Basic Authentication
echo "1. Basic Authentication Example:\n";
$chart1 = new QuickChart([
    'width' => 600,
    'height' => 400,
    'format' => 'png'
]);

$chart1->setConfig($chartConfig);

// Set basic authentication - replace with your actual credentials
$chart1->setBasicAuth('your_username', 'your_password');

echo "   Basic Auth configured for username: your_username\n";
echo "   URL (with auth): " . $chart1->getUrl() . "\n";

// Clear auth and show difference
$chart1->clearAuth();
echo "   URL (no auth): " . $chart1->getUrl() . "\n\n";

// Example 2: Using Bearer Token Authentication
echo "2. Bearer Token Authentication Example:\n";
$chart2 = new QuickChart([
    'width' => 600,
    'height' => 400,
    'format' => 'png'
]);

$chart2->setConfig($chartConfig);

// Set bearer token authentication - replace with your actual token
$chart2->setBearerToken('your_bearer_token_here');

echo "   Bearer token configured\n";
echo "   URL (with bearer): " . $chart2->getUrl() . "\n\n";

// Example 3: Constructor with authentication options
echo "3. Constructor Authentication Example:\n";
$chart3 = new QuickChart([
    'width' => 600,
    'height' => 400,
    'format' => 'png',
    'authUsername' => 'api_user',
    'authPassword' => 'api_password'
]);

$chart3->setConfig($chartConfig);

echo "   Basic auth set via constructor\n";
echo "   URL: " . $chart3->getUrl() . "\n\n";

// Example 4: Switching between authentication methods
echo "4. Switching Authentication Methods:\n";
$chart4 = new QuickChart();
$chart4->setConfig($chartConfig);

// Start with basic auth
$chart4->setBasicAuth('user1', 'pass1');
echo "   With Basic Auth: Username configured\n";

// Switch to bearer token (automatically clears basic auth)
$chart4->setBearerToken('token123');
echo "   With Bearer Token: Token configured\n";

// Clear all authentication
$chart4->clearAuth();
echo "   No Authentication: All auth cleared\n\n";

// Example 5: Downloading with authentication (commented for safety)
/*
echo "5. Download Chart with Authentication:\n";
$chart5 = new QuickChart([
    'width' => 500,
    'height' => 300
]);

$chart5->setConfig($chartConfig);
$chart5->setBearerToken('your_real_token');

try {
    // This will make an authenticated request
    $chart5->toFile('authenticated_chart.png');
    echo "   Chart saved to authenticated_chart.png\n";
} catch (Exception $e) {
    echo "   Error: " . $e->getMessage() . "\n";
}
*/

echo "Authentication setup complete!\n";
echo "\nNote: Replace placeholder credentials with real values for actual use.\n";

?>