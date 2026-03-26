<?php

class QuickChart {
  public $protocol;
  public $host;
  public $port;
  public $path;

  public $config;
  public $width;
  public $height;
  public $devicePixelRatio;
  public $format;
  public $backgroundColor;
  public $apiKey;
  public $version;
  public $authUsername;
  public $authPassword;
  public $bearerToken;

  const USER_AGENT = 'quickchart-php (1.0.0)';

  function __construct($options = array()) {
    $this->protocol = isset($options['protocol']) ? $options['protocol'] : 'https';
    $this->host = isset($options['host']) ? $options['host'] : 'quickchart.io';
    $this->port = isset($options['port']) ? $options['port'] : 443;
    $this->path = isset($options['path']) ? $options['path'] : false;
    $this->width = isset($options['width']) ? $options['width'] : 500;
    $this->height = isset($options['height']) ? $options['height'] : 300;
    $this->devicePixelRatio = isset($options['devicePixelRatio']) ? $options['devicePixelRatio'] : 1.0;
    $this->format = isset($options['format']) ? $options['format'] : 'png';
    $this->backgroundColor = isset($options['backgroundColor']) ? $options['backgroundColor'] : 'transparent';
    $this->apiKey = isset($options['apiKey']) ? $options['apiKey'] : null;
    $this->version = isset($options['version']) ? $options['version'] : null;
    $this->authUsername = isset($options['authUsername']) ? $options['authUsername'] : null;
    $this->authPassword = isset($options['authPassword']) ? $options['authPassword'] : null;
    $this->bearerToken = isset($options['bearerToken']) ? $options['bearerToken'] : null;
  }

  function setConfig($chartjsConfig) {
    $this->config = $chartjsConfig;
  }

  function setWidth($width) {
    $this->width = $width;
  }

  function setHeight($height) {
    $this->height = $height;
  }

  function setDevicePixelRatio($devicePixelRatio) {
    $this->devicePixelRatio = $devicePixelRatio;
  }

  function setFormat($format) {
    $this->format = $format;
  }

  function setBackgroundColor($backgroundColor) {
    $this->backgroundColor = $backgroundColor;
  }

  function setApiKey($apiKey) {
    $this->apiKey = $apiKey;
  }

  function setVersion($version) {
    $this->version = $version;
  }

  function setBasicAuth($username, $password) {
    $this->authUsername = $username;
    $this->authPassword = $password;
    // Clear bearer token when using basic auth
    $this->bearerToken = null;
  }

  function setBearerToken($token) {
    $this->bearerToken = $token;
    // Clear basic auth when using bearer token
    $this->authUsername = null;
    $this->authPassword = null;
  }

  function clearAuth() {
    $this->authUsername = null;
    $this->authPassword = null;
    $this->bearerToken = null;
  }

  function getConfigStr() {
    if (is_array($this->config)) {
      return json_encode($this->config);
    }
    return $this->config;
  }

  protected function buildAuthHeaders($additionalHeaders = array()) {
    $headers = $additionalHeaders;
    
    if ($this->bearerToken) {
      $headers[] = 'Authorization: Bearer ' . $this->bearerToken;
    } elseif ($this->authUsername && $this->authPassword) {
      $credentials = base64_encode($this->authUsername . ':' . $this->authPassword);
      $headers[] = 'Authorization: Basic ' . $credentials;
    }
    
    return $headers;
  }

  function getUrl() {
    $url = sprintf(
      '%s/chart?ref=qc-php&c=%s&w=%d&h=%d&devicePixelRatio=%s&format=%s&bkg=%s',
      $this->getRootEndpoint(),
      urlencode($this->getConfigStr()),
      $this->width,
      $this->height,
      number_format($this->devicePixelRatio, 1),
      urlencode($this->format),
      urlencode($this->backgroundColor)
    );

    if ($this->apiKey) {
      $url .= '&key=' . urlencode($this->apiKey);
    }

    if ($this->version) {
      $url .= '&v=' . urlencode($this->version);
    }

    return $url;
  }

  function getShortUrl() {
    if ($this->host != 'quickchart.io') {
      throw new Exception('Short URLs must use quickchart.io host');
    }
    $ch = curl_init($this->getRootEndpoint() . '/chart/create');
    $postData = array(
      'backgroundColor' => $this->backgroundColor,
      'width' => $this->width,
      'height' => $this->height,
      'devicePixelRatio' => number_format($this->devicePixelRatio, 1),
      'format' => $this->format,
      'chart' => $this->getConfigStr(),
    );
    if ($this->apiKey) {
      $postData['key'] = $this->apiKey;
    }
    if ($this->version) {
      $postData['version'] = $this->version;
    }
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
    $headers = $this->buildAuthHeaders(array(
      'Content-Type: application/json',
      'User-Agent: ' . QuickChart::USER_AGENT,
    ));
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($ch);
    $httpStatusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if ($result === false) {
      $error = curl_error($ch);
      curl_close($ch);
      throw new Exception("Curl error: $error");
    }

    curl_close($ch);

    if ($httpStatusCode < 200 || $httpStatusCode >= 300) {
      throw new Exception("QuickChart API returned error with status code $httpStatusCode");
    }

    // Note: do not dereference json_decode directly for 5.3 compatibility.
    $ret = json_decode($result, true);
    if (!isset($ret['url'])) {
      throw new Exception('QuickChart API response did not include a URL');
    }
    return $ret['url'];
  }

  function toBinary() {
    $ch = curl_init($this->getRootEndpoint() . '/chart');
    $postData = array(
      'backgroundColor' => $this->backgroundColor,
      'devicePixelRatio' => $this->devicePixelRatio,
      'width' => $this->width,
      'height' => $this->height,
      'format' => $this->format,
      'chart' => $this->getConfigStr(),
    );
    if ($this->apiKey) {
      $postData['key'] = $this->apiKey;
    }
    if ($this->version) {
      $postData['version'] = $this->version;
    }

    $responseHeaders = [];
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
    $headers = $this->buildAuthHeaders(array('Content-Type:application/json'));
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_HEADERFUNCTION, function($curl, $header) use (&$responseHeaders) {
      $len = strlen($header);
      $header = explode(':', $header, 2);
      if (count($header) < 2) { // ignore invalid headers
        return $len;
      }
      $responseHeaders[strtolower(trim($header[0]))][] = trim($header[1]);
      return $len;
    });
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($ch);
    $httpStatusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if ($result === false) {
      $error = curl_error($ch);
      curl_close($ch);
      throw new Exception("Curl error: $error");
    }

    curl_close($ch);

    if ($httpStatusCode >= 200 && $httpStatusCode < 300) {
      return $result;
    }

    $errorHeader = isset($responseHeaders['x-quickchart-error'][0]) ? $responseHeaders['x-quickchart-error'][0] : null;
    if ($errorHeader) {
      throw new Exception("QuickChart API returned error with status code $httpStatusCode: $errorHeader");
    }

    throw new Exception("QuickChart API returned error with status code $httpStatusCode");
  }

  function toFile($path) {
    $data = $this->toBinary();
    file_put_contents($path, $data);
  }

  protected function getRootEndpoint() {
    return $this->protocol . '://' . $this->host . ( $this->port ? ':' . $this->port : '' ) . ( $this->path ? $this->path : '' ) ;
  }
}


