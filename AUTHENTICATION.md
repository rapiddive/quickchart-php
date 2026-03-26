# Authentication Guide for QuickChart PHP

This guide explains how to use the authentication bypass functionality added to the QuickChart PHP library.

## Overview

The enhanced QuickChart PHP library now supports:

- **Basic Authentication**: HTTP Basic Auth with username/password
- **Bearer Token Authentication**: Authorization header with bearer token
- **API Key Authentication**: Original QuickChart API key support (unchanged)

## Authentication Methods

### 1. Basic Authentication

Use when your target URL requires HTTP Basic Authentication.

```php
<?php
require_once('QuickChart.php');

$chart = new QuickChart();

// Method 1: Using setBasicAuth()
$chart->setBasicAuth('username', 'password');

// Method 2: Via constructor
$chart = new QuickChart([
    'authUsername' => 'username',
    'authPassword' => 'password'
]);
?>
```

### 2. Bearer Token Authentication

Use when your target URL requires a Bearer token in the Authorization header.

```php
<?php
require_once('QuickChart.php');

$chart = new QuickChart();

// Method 1: Using setBearerToken()
$chart->setBearerToken('your_bearer_token_here');

// Method 2: Via constructor
$chart = new QuickChart([
    'bearerToken' => 'your_bearer_token_here'
]);
?>
```

### 3. Clearing Authentication

Remove all authentication settings:

```php
<?php
$chart->clearAuth(); // Removes both basic auth and bearer token
?>
```

## New Methods

### `setBasicAuth($username, $password)`
- Sets HTTP Basic Authentication credentials
- Automatically clears any bearer token
- **Parameters:**
  - `$username` (string): Username for basic auth
  - `$password` (string): Password for basic auth

### `setBearerToken($token)`
- Sets Bearer token for Authorization header
- Automatically clears any basic auth credentials
- **Parameters:**
  - `$token` (string): Bearer token value

### `clearAuth()`
- Removes all authentication settings
- Clears both basic auth and bearer token

## New Constructor Options

The constructor now accepts additional authentication options:

```php
<?php
$chart = new QuickChart([
    // Existing options...
    'width' => 500,
    'height' => 300,
    
    // New authentication options
    'authUsername' => 'your_username',    // Basic auth username
    'authPassword' => 'your_password',    // Basic auth password
    'bearerToken' => 'your_token'         // Bearer token
]);
?>
```

## Complete Example

```php
<?php
require_once('QuickChart.php');

// Chart configuration
$config = [
    'type' => 'bar',
    'data' => [
        'labels' => ['A', 'B', 'C'],
        'datasets' => [[
            'data' => [1, 2, 3]
        ]]
    ]
];

// Create chart with basic authentication
$chart = new QuickChart([
    'width' => 600,
    'height' => 400
]);

$chart->setConfig($config);
$chart->setBasicAuth('api_user', 'api_secret');

try {
    // Download chart with authentication
    $chart->toFile('my_chart.png');
    echo "Chart saved successfully!";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}

// Switch to bearer token
$chart->setBearerToken('abc123token');

// Get authenticated URL
$url = $chart->getUrl();
echo "Authenticated URL: " . $url;
?>
```

## Security Notes

1. **Never hardcode credentials** in your source code
2. **Use environment variables** for sensitive credentials
3. **Basic auth vs Bearer tokens**: Bearer tokens are generally more secure and preferred for APIs
4. **HTTPS**: Always use HTTPS when transmitting credentials

## Environment Variables Example

```php
<?php
// Using environment variables for security
$chart = new QuickChart();
$chart->setConfig($config);

// Option 1: Basic Auth from environment
if (getenv('CHART_USERNAME') && getenv('CHART_PASSWORD')) {
    $chart->setBasicAuth(getenv('CHART_USERNAME'), getenv('CHART_PASSWORD'));
}

// Option 2: Bearer token from environment
if (getenv('CHART_BEARER_TOKEN')) {
    $chart->setBearerToken(getenv('CHART_BEARER_TOKEN'));
}
?>
```

## How It Works

The library automatically adds the appropriate `Authorization` header to all HTTP requests:

- **Basic Auth**: `Authorization: Basic base64(username:password)`
- **Bearer Token**: `Authorization: Bearer your_token_here`

This works for all methods that make HTTP requests:
- `getShortUrl()`
- `toBinary()`
- `toFile()`

The `getUrl()` method only generates URLs and doesn't make HTTP requests, so authentication doesn't affect it directly.