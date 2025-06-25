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

try {
    // Check if request is POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Only POST requests allowed');
    }

    // Check if files were uploaded
    if (!isset($_FILES['images']) || empty($_FILES['images']['name'][0])) {
        throw new Exception('No files uploaded');
    }

    // Get optimization options
    $options = [
        'quality' => isset($_POST['quality']) ? (int)$_POST['quality'] : 80,
        'max_width' => isset($_POST['max_width']) && !empty($_POST['max_width']) ? (int)$_POST['max_width'] : null,
        'max_height' => isset($_POST['max_height']) && !empty($_POST['max_height']) ? (int)$_POST['max_height'] : null,
        'create_webp' => isset($_POST['create_webp']) && $_POST['create_webp'] === 'true',
        'create_thumbnail' => isset($_POST['create_thumbnail']) && $_POST['create_thumbnail'] === 'true',
        'format' => isset($_POST['format']) ? $_POST['format'] : null
    ];

    // Validate quality range
    $options['quality'] = max(20, min(100, $options['quality']));

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
            'data' => [
                'original' => $result['original'],
                'optimized' => $result['optimized'],
                'savings' => $result['best_savings'],
                'download_links' => []
            ]
        ];

        // Generate download links
        foreach ($result['optimized'] as $optimized) {
            $response['data']['download_links'][] = [
                'format' => $optimized['format'],
                'filename' => $optimized['filename'],
                'size' => $optimized['size_human'],
                'savings' => $optimized['savings'],
                'url' => 'download.php?file=' . urlencode($optimized['filename'])
            ];
        }

    } else {
        // Batch processing
        $batchResult = $optimizer->optimizeBatch($files, $options);
        
        $response = [
            'success' => true,
            'type' => 'batch',
            'data' => [
                'summary' => $batchResult['summary'],
                'results' => [],
                'download_links' => []
            ]
        ];

        // Process batch results
        foreach ($batchResult['results'] as $result) {
            if ($result['success']) {
                $fileData = [
                    'original' => $result['original'],
                    'optimized' => $result['optimized'],
                    'savings' => $result['best_savings'],
                    'download_links' => []
                ];

                // Generate download links for each optimized version
                foreach ($result['optimized'] as $optimized) {
                    $fileData['download_links'][] = [
                        'format' => $optimized['format'],
                        'filename' => $optimized['filename'],
                        'size' => $optimized['size_human'],
                        'savings' => $optimized['savings'],
                        'url' => 'download.php?file=' . urlencode($optimized['filename'])
                    ];

                    // Add to global download links
                    $response['data']['download_links'][] = [
                        'format' => $optimized['format'],
                        'filename' => $optimized['filename'],
                        'size' => $optimized['size_human'],
                        'savings' => $optimized['savings'],
                        'url' => 'download.php?file=' . urlencode($optimized['filename'])
                    ];
                }

                $response['data']['results'][] = $fileData;
            } else {
                // Add failed file to results
                $response['data']['results'][] = [
                    'success' => false,
                    'original' => ['name' => $files[array_search($result, $batchResult['results'])]['name']],
                    'errors' => $result['errors']
                ];
            }
        }

        // Add batch download link
        if (count($response['data']['download_links']) > 1) {
            $response['data']['batch_download'] = 'download.php?batch=true&files=' . 
                urlencode(json_encode(array_column($response['data']['download_links'], 'filename')));
        }
    }

    // Add processing statistics
    $response['stats'] = [
        'processing_time' => round(microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'], 2),
        'memory_used' => memory_get_peak_usage(true),
        'files_processed' => count($files)
    ];

    // Clean up old files (async)
    $optimizer->cleanupOldFiles();

    echo json_encode($response, JSON_PRETTY_PRINT);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'debug' => [
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString()
        ]
    ], JSON_PRETTY_PRINT);

} catch (Error $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Fatal error: ' . $e->getMessage(),
        'debug' => [
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ]
    ], JSON_PRETTY_PRINT);
}

// Helper function to log processing info
function logProcessing($message) {
    $logFile = __DIR__ . '/logs/processing.log';
    $logDir = dirname($logFile);
    
    if (!is_dir($logDir)) {
        mkdir($logDir, 0755, true);
    }
    
    $timestamp = date('Y-m-d H:i:s');
    $logMessage = "[$timestamp] $message" . PHP_EOL;
    file_put_contents($logFile, $logMessage, FILE_APPEND | LOCK_EX);
}

// Log successful processing
if (isset($response) && $response['success']) {
    $filesCount = $response['stats']['files_processed'];
    $processingTime = $response['stats']['processing_time'];
    logProcessing("Successfully processed $filesCount files in {$processingTime}s");
}
?>