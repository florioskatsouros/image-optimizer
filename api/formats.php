<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

// FIXED: Correct path to autoload
require_once __DIR__ . '/../vendor/autoload.php';

use ImageOptimizer\ImageOptimizer;

try {
    // Initialize optimizer to get format information
    $optimizer = new ImageOptimizer();
    
    // Get supported formats and details
    $supportedFormats = $optimizer->getSupportedFormats();
    $formatDetails = $optimizer->getSupportedFormatsDetailed();
    $systemInfo = $optimizer->getSystemInfo();
    
    // Response data
    $response = [
        'success' => true,
        'formats' => $supportedFormats,
        'details' => $formatDetails,
        'total_supported' => count($supportedFormats),
        'system_info' => [
            'php_version' => $systemInfo['php_version'],
            'has_imagick' => !empty($systemInfo['imagick_info']) && !isset($systemInfo['imagick_info']['error']),
            'has_gd' => !empty($systemInfo['gd_info']),
            'advanced_formats_count' => count($systemInfo['advanced_formats_available']),
            'memory_limit' => $systemInfo['memory_limit'],
            'max_file_size' => $optimizer->getMaxFileSizeHuman()
        ],
        'capabilities' => [
            'can_optimize' => array_filter($formatDetails, function($detail) {
                return $detail['can_optimize'];
            }),
            'can_convert_to' => array_filter($formatDetails, function($detail) {
                return $detail['can_convert_to'];
            }),
            'categories' => groupFormatsByCategory($formatDetails)
        ]
    ];
    
    echo json_encode($response, JSON_PRETTY_PRINT);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ]);
} catch (Error $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Fatal error: ' . $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ]);
}

/**
 * Group formats by category - FIXED function
 */
function groupFormatsByCategory($formatDetails) {
    $categories = [];
    
    foreach ($formatDetails as $format => $details) {
        $category = $details['category'];
        if (!isset($categories[$category])) {
            $categories[$category] = [];
        }
        $categories[$category][] = $format;
    }
    
    return $categories;
}
?>