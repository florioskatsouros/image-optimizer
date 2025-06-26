<?php
// Prevent direct access
if (!isset($_GET['file']) && !isset($_GET['batch'])) {
    http_response_code(400);
    die('Invalid request');
}

// Enhanced security headers
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');
header('Referrer-Policy: strict-origin-when-cross-origin');

// File paths
$optimizedDir = __DIR__ . '/optimized/';
$tempDir = __DIR__ . '/temp/';

// Create temp directory if it doesn't exist
if (!is_dir($tempDir)) {
    mkdir($tempDir, 0755, true);
}

try {
    // Handle batch download
    if (isset($_GET['batch']) && $_GET['batch'] === 'true') {
        handleBatchDownload();
    } 
    // Handle single file download
    elseif (isset($_GET['file'])) {
        handleSingleDownload();
    } 
    else {
        throw new Exception('Invalid download request');
    }

} catch (Exception $e) {
    http_response_code(404);
    echo "Download error: " . htmlspecialchars($e->getMessage());
    exit;
}

/**
 * Handle single file download with enhanced caching
 */
function handleSingleDownload() {
    global $optimizedDir;
    
    $filename = $_GET['file'];
    
    // Enhanced sanitization
    $filename = basename($filename);
    $filename = preg_replace('/[^a-zA-Z0-9._-]/', '', $filename);
    
    if (empty($filename)) {
        throw new Exception('Invalid filename');
    }
    
    // Additional security: check for valid image extensions
    $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'avif', 'bmp', 'tiff', 'tif', 'ico'];
    
    if (!in_array($extension, $allowedExtensions)) {
        throw new Exception('File type not allowed');
    }
    
    $filepath = $optimizedDir . $filename;
    
    // Check if file exists and is readable
    if (!file_exists($filepath) || !is_readable($filepath)) {
        throw new Exception('File not found');
    }
    
    // Validate file is in optimized directory (security check)
    $realPath = realpath($filepath);
    $realOptimizedDir = realpath($optimizedDir);
    
    if (strpos($realPath, $realOptimizedDir) !== 0) {
        throw new Exception('Access denied');
    }
    
    // Additional check: file age (cleanup old files)
    $fileAge = time() - filemtime($filepath);
    if ($fileAge > 86400) { // 24 hours
        throw new Exception('File has expired');
    }
    
    // Get file info
    $fileSize = filesize($filepath);
    $mimeType = getMimeType($filepath);
    $lastModified = filemtime($filepath);
    $etag = '"' . md5_file($filepath) . '"';
    
    // Check if client already has the file (304 Not Modified)
    if (isset($_SERVER['HTTP_IF_NONE_MATCH']) && $_SERVER['HTTP_IF_NONE_MATCH'] === $etag) {
        http_response_code(304);
        header('Cache-Control: public, max-age=31536000, immutable');
        exit;
    }
    
    if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) && 
        strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) >= $lastModified) {
        http_response_code(304);
        header('Cache-Control: public, max-age=31536000, immutable');
        exit;
    }
    
    // Security headers
    header('Content-Type: ' . $mimeType);
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Content-Length: ' . $fileSize);
    header('X-Content-Type-Options: nosniff');
    
    // Caching headers
    header('Cache-Control: public, max-age=3600'); // 1 hour for optimized images
    header('ETag: ' . $etag);
    header('Last-Modified: ' . gmdate('D, d M Y H:i:s', $lastModified) . ' GMT');
    
    // Performance headers
    header('X-Image-Optimized: true');
    
    // Support for range requests
    if (isset($_SERVER['HTTP_RANGE'])) {
        handleRangeRequest($filepath, $fileSize, $mimeType);
    } else {
        // Output file efficiently
        if ($fileSize > 1024 * 1024) { // > 1MB
            ob_end_clean();
            readfile($filepath);
        } else {
            readfile($filepath);
        }
    }
    
    // Log download
    logDownload($filename, $fileSize, 'single');
    exit;
}

/**
 * Handle HTTP Range requests for resume support
 */
function handleRangeRequest($filepath, $fileSize, $mimeType) {
    $range = $_SERVER['HTTP_RANGE'];
    
    // Parse range header
    if (preg_match('/bytes=(\d+)-(\d+)?/', $range, $matches)) {
        $start = intval($matches[1]);
        $end = isset($matches[2]) && $matches[2] !== '' ? intval($matches[2]) : $fileSize - 1;
        
        // Validate range
        if ($start > $end || $start >= $fileSize || $end >= $fileSize) {
            http_response_code(416); // Range Not Satisfiable
            header('Content-Range: bytes */' . $fileSize);
            exit;
        }
        
        $length = $end - $start + 1;
        
        // Set partial content headers
        http_response_code(206); // Partial Content
        header('Content-Range: bytes ' . $start . '-' . $end . '/' . $fileSize);
        header('Content-Length: ' . $length);
        header('Accept-Ranges: bytes');
        
        // Output partial content
        $file = fopen($filepath, 'rb');
        fseek($file, $start);
        echo fread($file, $length);
        fclose($file);
    }
}

/**
 * Check if JPEG is progressive
 */
function isProgressiveJpeg($filepath) {
    $extension = strtolower(pathinfo($filepath, PATHINFO_EXTENSION));
    if (!in_array($extension, ['jpg', 'jpeg'])) {
        return false;
    }
    
    $handle = fopen($filepath, 'rb');
    if (!$handle) return false;
    
    $progressive = false;
    
    // Read file header to check for progressive markers
    while (!feof($handle)) {
        $byte = fread($handle, 1);
        if (ord($byte) == 0xFF) {
            $byte = fread($handle, 1);
            if (ord($byte) == 0xC2) { // SOF2 marker indicates progressive
                $progressive = true;
                break;
            }
        }
    }
    
    fclose($handle);
    return $progressive;
}

/**
 * Handle batch download (ZIP file) with enhanced features
 */
function handleBatchDownload() {
    global $optimizedDir, $tempDir;
    
    if (!isset($_GET['files'])) {
        throw new Exception('No files specified for batch download');
    }
    
    $filesJson = $_GET['files'];
    $files = json_decode(urldecode($filesJson), true);
    
    if (!is_array($files) || empty($files)) {
        throw new Exception('Invalid files list');
    }
    
    // Validate files exist and calculate total size
    $validFiles = [];
    $totalSize = 0;
    
    foreach ($files as $filename) {
        $filename = basename($filename);
        $filename = preg_replace('/[^a-zA-Z0-9._-]/', '', $filename);
        $filepath = $optimizedDir . $filename;
        
        if (file_exists($filepath)) {
            $fileSize = filesize($filepath);
            $validFiles[] = [
                'filename' => $filename,
                'filepath' => $filepath,
                'size' => $fileSize
            ];
            $totalSize += $fileSize;
        }
    }
    
    if (empty($validFiles)) {
        throw new Exception('No valid files found');
    }
    
    // Check if total size is reasonable for ZIP
    if ($totalSize > 100 * 1024 * 1024) { // > 100MB
        throw new Exception('Batch too large. Please download files individually.');
    }
    
    // Create ZIP file with better naming
    $timestamp = date('Y-m-d_H-i-s');
    $zipFilename = 'optimized_images_' . count($validFiles) . '_files_' . $timestamp . '.zip';
    $zipPath = $tempDir . $zipFilename;
    
    $zip = new ZipArchive();
    $result = $zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE);
    
    if ($result !== TRUE) {
        throw new Exception('Could not create ZIP file: ' . $result);
    }
    
    // Add files to ZIP with folder structure
    foreach ($validFiles as $file) {
        $extension = strtolower(pathinfo($file['filename'], PATHINFO_EXTENSION));
        $folder = '';
        
        // Organize by file type in ZIP
        switch ($extension) {
            case 'webp':
                $folder = 'webp/';
                break;
            case 'avif':
                $folder = 'avif/';
                break;
            case 'jpg':
            case 'jpeg':
                if (strpos($file['filename'], '_thumb') !== false) {
                    $folder = 'thumbnails/';
                } else {
                    $folder = 'optimized/';
                }
                break;
            default:
                $folder = 'optimized/';
        }
        
        $zip->addFile($file['filepath'], $folder . $file['filename']);
    }
    
    // Add info file to ZIP
    $infoContent = "Image Optimization Results\n";
    $infoContent .= "Generated: " . date('Y-m-d H:i:s') . "\n";
    $infoContent .= "Files: " . count($validFiles) . "\n";
    $infoContent .= "Total size: " . formatBytes($totalSize) . "\n\n";
    $infoContent .= "Files included:\n";
    
    foreach ($validFiles as $file) {
        $infoContent .= "- " . $file['filename'] . " (" . formatBytes($file['size']) . ")\n";
    }
    
    $zip->addFromString('README.txt', $infoContent);
    $zip->close();
    
    // Check if ZIP was created successfully
    if (!file_exists($zipPath)) {
        throw new Exception('Failed to create ZIP file');
    }
    
    $zipSize = filesize($zipPath);
    
    // Set download headers for ZIP with caching
    header('Content-Type: application/zip');
    header('Content-Disposition: attachment; filename="' . $zipFilename . '"');
    header('Content-Length: ' . $zipSize);
    header('Cache-Control: private, max-age=3600'); // 1 hour for ZIP files
    header('Pragma: private');
    header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 3600) . ' GMT');
    
    // Performance headers
    header('X-Batch-Files: ' . count($validFiles));
    header('X-Compression-Ratio: ' . round(($zipSize / $totalSize) * 100, 1) . '%');
    
    // Output ZIP file
    readfile($zipPath);
    
    // Schedule ZIP file cleanup
    register_shutdown_function(function() use ($zipPath) {
        if (file_exists($zipPath)) {
            // Delete after 5 seconds to ensure download completes
            sleep(5);
            unlink($zipPath);
        }
    });
    
    // Log batch download
    logDownload($zipFilename, $zipSize, 'batch', count($validFiles));
    exit;
}

/**
 * Get MIME type for file with enhanced detection
 */
function getMimeType($filepath) {
    $extension = strtolower(pathinfo($filepath, PATHINFO_EXTENSION));
    
    $mimeTypes = [
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png' => 'image/png',
        'gif' => 'image/gif',
        'webp' => 'image/webp',
        'avif' => 'image/avif',
        'bmp' => 'image/bmp',
        'tiff' => 'image/tiff',
        'tif' => 'image/tiff',
        'svg' => 'image/svg+xml',
        'ico' => 'image/x-icon'
    ];
    
    // Try to detect MIME type using finfo if available
    if (function_exists('finfo_file')) {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $detectedMime = finfo_file($finfo, $filepath);
        finfo_close($finfo);
        
        if ($detectedMime && strpos($detectedMime, 'image/') === 0) {
            return $detectedMime;
        }
    }
    
    return $mimeTypes[$extension] ?? 'application/octet-stream';
}

/**
 * Enhanced download logging with more details
 */
function logDownload($filename, $fileSize, $type, $fileCount = 1) {
    $logFile = __DIR__ . '/logs/downloads.log';
    $logDir = dirname($logFile);
    
    if (!is_dir($logDir)) {
        mkdir($logDir, 0755, true);
    }
    
    $timestamp = date('Y-m-d H:i:s');
    $ip = getClientIP();
    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
    $referer = $_SERVER['HTTP_REFERER'] ?? 'direct';
    $sizeFormatted = formatBytes($fileSize);
    
    $logMessage = "[$timestamp] $type download: $filename ($sizeFormatted)";
    if ($type === 'batch') {
        $logMessage .= " - $fileCount files";
    }
    
    // Add performance metrics
    $loadTime = microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'];
    $logMessage .= " | Load: " . round($loadTime, 3) . "s";
    $logMessage .= " | IP: $ip";
    $logMessage .= " | Referer: $referer";
    $logMessage .= " | UA: " . substr($userAgent, 0, 100);
    $logMessage .= PHP_EOL;
    
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
    
    return round(pow(1024, $base - floor($base)), $precision) . ' ' . $units[floor($base)];
}

/**
 * Clean up old temp files
 */
function cleanupTempFiles() {
    global $tempDir;
    
    if (!is_dir($tempDir)) {
        return;
    }
    
    $files = glob($tempDir . '*');
    $deleted = 0;
    $cutoffTime = time() - 3600; // 1 hour
    
    foreach ($files as $file) {
        if (is_file($file) && filemtime($file) < $cutoffTime) {
            unlink($file);
            $deleted++;
        }
    }
    
    if ($deleted > 0) {
        error_log("Cleaned up $deleted temporary ZIP files");
    }
}

/**
 * Get download statistics
 */
function getDownloadStats() {
    $logFile = __DIR__ . '/logs/downloads.log';
    
    if (!file_exists($logFile)) {
        return [
            'total_downloads' => 0,
            'today_downloads' => 0,
            'popular_formats' => []
        ];
    }
    
    $lines = file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $today = date('Y-m-d');
    $todayCount = 0;
    $formats = [];
    
    foreach ($lines as $line) {
        if (strpos($line, $today) !== false) {
            $todayCount++;
        }
        
        // Extract format from filename
        if (preg_match('/download: .+\.([a-z]+)/', $line, $matches)) {
            $format = $matches[1];
            $formats[$format] = ($formats[$format] ?? 0) + 1;
        }
    }
    
    arsort($formats);
    
    return [
        'total_downloads' => count($lines),
        'today_downloads' => $todayCount,
        'popular_formats' => array_slice($formats, 0, 5, true)
    ];
}

// Clean up old temp files on each request (with 10% probability to avoid overhead)
if (rand(1, 10) === 1) {
    cleanupTempFiles();
}
?>