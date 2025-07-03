<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once 'vendor/autoload.php';

use ImageOptimizer\ImageOptimizer;

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set memory and execution limits
ini_set('memory_limit', '512M');
set_time_limit(300); // 5 minutes

// Rate limiting
session_start();
$maxRequestsPerHour = 50;
$currentHour = date('YmdH');
$sessionKey = 'requests_' . $currentHour;

if (!isset($_SESSION[$sessionKey])) {
    $_SESSION[$sessionKey] = 0;
}

if ($_SESSION[$sessionKey] >= $maxRequestsPerHour) {
    http_response_code(429);
    echo json_encode([
        'success' => false, 
        'error' => 'Rate limit exceeded. Please try again later.'
    ]);
    exit;
}

$_SESSION[$sessionKey]++;

try {
    // Check if request is POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Only POST requests allowed');
    }

    // Check if files were uploaded
    if (!isset($_FILES['images']) || empty($_FILES['images']['name'][0])) {
        throw new Exception('No files uploaded');
    }

    // Get mode (optimize or convert)
    $mode = $_POST['mode'] ?? 'optimize';
    
    // FIXED: Better validation for convert mode
    if ($mode === 'convert') {
        // Check if we have valid output format
        $hasValidOutputFormat = false;
        
        if (isset($_POST['output_format']) && !empty($_POST['output_format'])) {
            $hasValidOutputFormat = true;
        }
        
        if (isset($_POST['convert_to']) && !empty($_POST['convert_to'])) {
            $convertTo = json_decode($_POST['convert_to'], true);
            if (is_array($convertTo) && !empty($convertTo)) {
                $hasValidOutputFormat = true;
            }
        }
        
        if (!$hasValidOutputFormat) {
            throw new Exception('No valid output format specified for conversion');
        }
    }
    
    // Get optimization options with validation
    $options = buildProcessingOptions($mode, $_POST);

    // Initialize optimizer
    $optimizer = new ImageOptimizer();

    // Prepare files array for processing
    $files = [];
    $fileCount = count($_FILES['images']['name']);

    for ($i = 0; $i < $fileCount; $i++) {
        if ($_FILES['images']['error'][$i] === UPLOAD_ERR_OK) {
            $files[] = [
                'name' => $_FILES['images']['name'][$i],
                'type' => $_FILES['images']['type'][$i],
                'tmp_name' => $_FILES['images']['tmp_name'][$i],
                'error' => $_FILES['images']['error'][$i],
                'size' => $_FILES['images']['size'][$i]
            ];
        }
    }

    if (empty($files)) {
        throw new Exception('No valid files to process');
    }

    // Log processing start
    logProcessing("Starting {$mode} processing for " . count($files) . " files");

    // Process single file or batch
    if (count($files) === 1) {
        $result = $optimizer->optimizeImage($files[0], $options);
        
        if (!$result['success']) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'error' => implode(', ', $result['errors'])
            ]);
            exit;
        }

        // Format response for single file
        $response = [
            'success' => true,
            'type' => 'single',
            'mode' => $mode,
            'data' => [
                'original' => $result['original'],
                'optimized' => $result['optimized'],
                'savings' => $result['best_savings'],
                'conversions_created' => $result['conversions_created'],
                'download_links' => []
            ]
        ];

        // FIXED: Better download link generation
        foreach ($result['optimized'] as $optimized) {
            $downloadLink = [
                'format' => $optimized['format'],
                'filename' => $optimized['filename'],
                'size' => $optimized['size_human'],
                'savings' => $optimized['savings'],
                'url' => 'download.php?file=' . urlencode($optimized['filename']),
                'is_conversion' => $optimized['is_conversion'] ?? false
            ];
            
            // FIXED: Handle special cases like thumbnails and ICO files
            if ($optimized['format'] === 'thumbnail') {
                $downloadLink['savings'] = 'thumbnail';
            } elseif ($optimized['format'] === 'ico') {
                $downloadLink['savings'] = 'converted';
            }
            
            $response['data']['download_links'][] = $downloadLink;
        }

    } else {
        // Batch processing
        $batchResult = $optimizer->optimizeBatch($files, $options);
        
        if (!$batchResult['success']) {
            throw new Exception('Batch processing failed: ' . implode(', ', $batchResult['errors'] ?? []));
        }
        
        $response = [
            'success' => true,
            'type' => 'batch',
            'mode' => $mode,
            'data' => [
                'summary' => $batchResult['summary'],
                'results' => [],
                'download_links' => []
            ]
        ];

        // Process batch results
        foreach ($batchResult['results'] as $index => $result) {
            if ($result['success']) {
                $fileData = [
                    'original' => $result['original'],
                    'optimized' => $result['optimized'],
                    'savings' => $result['best_savings'],
                    'conversions_created' => $result['conversions_created'] ?? count($result['optimized']),
                    'download_links' => []
                ];

                // Generate download links for each optimized version
                foreach ($result['optimized'] as $optimized) {
                    $downloadLink = [
                        'format' => $optimized['format'],
                        'filename' => $optimized['filename'],
                        'size' => $optimized['size_human'],
                        'savings' => $optimized['savings'],
                        'url' => 'download.php?file=' . urlencode($optimized['filename']),
                        'is_conversion' => $optimized['is_conversion'] ?? false
                    ];
                    
                    // FIXED: Handle special cases
                    if ($optimized['format'] === 'thumbnail') {
                        $downloadLink['savings'] = 'thumbnail';
                    } elseif (in_array($optimized['format'], ['ico', 'bmp', 'tiff'])) {
                        $downloadLink['savings'] = 'converted';
                    }
                    
                    $fileData['download_links'][] = $downloadLink;
                    $response['data']['download_links'][] = $downloadLink;
                }

                $response['data']['results'][] = $fileData;
            } else {
                // Add failed file to results
                $response['data']['results'][] = [
                    'success' => false,
                    'original' => ['name' => $files[$index]['name']],
                    'errors' => $result['errors']
                ];
            }
        }

        // Add batch download link if multiple files
        if (count($response['data']['download_links']) > 1) {
            $filenames = array_column($response['data']['download_links'], 'filename');
            $response['data']['batch_download'] = 'download.php?batch=true&files=' . urlencode(json_encode($filenames));
        }
    }

    // Add processing statistics
    $response['stats'] = [
        'processing_time' => round(microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'], 2),
        'memory_used' => memory_get_peak_usage(true),
        'memory_used_human' => formatBytes(memory_get_peak_usage(true)),
        'files_processed' => count($files),
        'mode_used' => $mode,
        'php_version' => PHP_VERSION
    ];

    // Add system info for debugging (only in development)
    if (isset($_POST['debug']) && $_POST['debug'] === 'true') {
        $response['debug'] = [
            'options_received' => $options,
            'files_info' => array_map(function($file) {
                return [
                    'name' => $file['name'],
                    'size' => $file['size'],
                    'type' => $file['type']
                ];
            }, $files),
            'system_info' => $optimizer->getSystemInfo()
        ];
    }

    // Clean up old files (run cleanup with 5% probability to avoid overhead)
    if (rand(1, 20) === 1) {
        $optimizer->cleanupOldFiles();
    }

    // Log successful processing
    $filesCount = $response['stats']['files_processed'];
    $processingTime = $response['stats']['processing_time'];
    $memoryUsed = $response['stats']['memory_used_human'];
    logProcessing("Successfully processed {$filesCount} files in {$processingTime}s using {$memoryUsed} memory");

    echo json_encode($response, JSON_PRETTY_PRINT);

} catch (Exception $e) {
    http_response_code(500);
    
    $errorResponse = [
        'success' => false,
        'error' => $e->getMessage(),
        'error_code' => $e->getCode(),
        'timestamp' => date('Y-m-d H:i:s')
    ];
    
    // Add debug info in development
    if (isset($_POST['debug']) && $_POST['debug'] === 'true') {
        $errorResponse['debug'] = [
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString(),
            'request_data' => [
                'post' => $_POST,
                'files' => isset($_FILES['images']) ? array_map(function($name, $size, $type, $error) {
                    return [
                        'name' => $name,
                        'size' => $size,
                        'type' => $type,
                        'error' => $error
                    ];
                }, $_FILES['images']['name'], $_FILES['images']['size'], $_FILES['images']['type'], $_FILES['images']['error']) : []
            ]
        ];
    }
    
    // Log error
    logProcessing("ERROR: " . $e->getMessage() . " in " . $e->getFile() . ":" . $e->getLine(), 'ERROR');
    
    echo json_encode($errorResponse, JSON_PRETTY_PRINT);

} catch (Error $e) {
    http_response_code(500);
    
    $fatalResponse = [
        'success' => false,
        'error' => 'Fatal error: ' . $e->getMessage(),
        'error_code' => $e->getCode(),
        'timestamp' => date('Y-m-d H:i:s')
    ];
    
    if (isset($_POST['debug']) && $_POST['debug'] === 'true') {
        $fatalResponse['debug'] = [
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'type' => 'Fatal Error'
        ];
    }
    
    logProcessing("FATAL ERROR: " . $e->getMessage() . " in " . $e->getFile() . ":" . $e->getLine(), 'FATAL');
    
    echo json_encode($fatalResponse, JSON_PRETTY_PRINT);
}

/**
 * FIXED: Build processing options based on mode
 */
function buildProcessingOptions($mode, $postData) {
    $options = ['mode' => $mode];
    
    if ($mode === 'convert') {
        // Handle conversion options
        $options['quality'] = isset($postData['quality']) ? max(20, min(100, (int)$postData['quality'])) : 80;
        $options['create_thumbnail'] = isset($postData['create_thumbnail']) && $postData['create_thumbnail'] === 'true';
        
        // Handle multiple format conversion
        if (isset($postData['convert_to']) && !empty($postData['convert_to'])) {
            $convertTo = json_decode($postData['convert_to'], true);
            if (is_array($convertTo) && !empty($convertTo)) {
                $options['convert_to'] = $convertTo;
            }
        }
        
        // Handle single format conversion
        if (isset($postData['output_format']) && !empty($postData['output_format'])) {
            $options['output_format'] = $postData['output_format'];
        }
        
        // Handle multiple format toggle
        if (isset($postData['convert_multiple']) && $postData['convert_multiple'] === 'true') {
            $options['convert_multiple'] = true;
        }
        
    } else {
        // Optimize mode options
        $options['quality'] = isset($postData['quality']) ? max(20, min(100, (int)$postData['quality'])) : 80;
        $options['max_width'] = isset($postData['max_width']) && !empty($postData['max_width']) ? max(100, min(8000, (int)$postData['max_width'])) : null;
        $options['max_height'] = isset($postData['max_height']) && !empty($postData['max_height']) ? max(100, min(8000, (int)$postData['max_height'])) : null;
        $options['create_webp'] = isset($postData['create_webp']) && $postData['create_webp'] === 'true';
        $options['create_avif'] = isset($postData['create_avif']) && $postData['create_avif'] === 'true';
        $options['create_thumbnail'] = isset($postData['create_thumbnail']) && $postData['create_thumbnail'] === 'true';
    }
    
    return $options;
}

/**
 * Helper function to log processing info
 */
function logProcessing($message, $level = 'INFO') {
    $logFile = __DIR__ . '/logs/processing.log';
    $logDir = dirname($logFile);
    
    if (!is_dir($logDir)) {
        mkdir($logDir, 0755, true);
    }
    
    $timestamp = date('Y-m-d H:i:s');
    $ip = getClientIP();
    $userAgent = substr($_SERVER['HTTP_USER_AGENT'] ?? 'unknown', 0, 100);
    $logMessage = "[$timestamp] [$level] $message | IP: $ip | UA: $userAgent" . PHP_EOL;
    
    file_put_contents($logFile, $logMessage, FILE_APPEND | LOCK_EX);
}

/**
 * Get real client IP address
 */
function getClientIP() {
    $headers = [
        'HTTP_CF_CONNECTING_IP',     // Cloudflare
        'HTTP_CLIENT_IP',            // Proxy
        'HTTP_X_FORWARDED_FOR',      // Load balancer/proxy
        'HTTP_X_FORWARDED',          // Proxy
        'HTTP_X_CLUSTER_CLIENT_IP',  // Cluster
        'HTTP_FORWARDED_FOR',        // Proxy
        'HTTP_FORWARDED',            // Proxy
        'REMOTE_ADDR'                // Standard
    ];
    
    foreach ($headers as $header) {
        if (!empty($_SERVER[$header])) {
            $ips = explode(',', $_SERVER[$header]);
            $ip = trim($ips[0]);
            
            // Validate IP
            if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                return $ip;
            }
        }
    }
    
    return $_SERVER['REMOTE_ADDR'] ?? 'unknown';
}

/**
 * Format bytes to human readable format
 */
function formatBytes($size, $precision = 2) {
    if ($size === 0) return '0 B';
    
    $units = ['B', 'KB', 'MB', 'GB'];
    $base = log($size, 1024);
    $unitIndex = floor($base);
    
    if ($unitIndex >= count($units)) {
        $unitIndex = count($units) - 1;
    }
    
    return round(pow(1024, $base - $unitIndex), $precision) . ' ' . $units[$unitIndex];
}

/**
 * Validate uploaded files before processing
 */
function validateUploadedFiles($files) {
    $errors = [];
    $totalSize = 0;
    $maxTotalSize = 500 * 1024 * 1024; // 500MB total
    
    foreach ($files as $file) {
        $totalSize += $file['size'];
        
        // Check individual file size
        if ($file['size'] > 100 * 1024 * 1024) {
            $errors[] = "File {$file['name']} is too large (max 100MB)";
        }
        
        // Check file extension
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'avif', 'bmp', 'tiff', 'heic', 'psd'];
        
        if (!in_array($extension, $allowedExtensions)) {
            $errors[] = "File {$file['name']} has unsupported format";
        }
    }
    
    // Check total size
    if ($totalSize > $maxTotalSize) {
        $errors[] = "Total upload size too large (max 500MB)";
    }
    
    return $errors;
}
?>