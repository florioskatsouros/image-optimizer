<?php

namespace ImageOptimizer;

use Intervention\Image\ImageManagerStatic as Image;
use Exception;

class ImageOptimizer
{
    private $uploadDir;
    private $optimizedDir;
    private $allowedTypes;
    private $maxFileSize;
    private $defaultQuality;

    public function __construct()
    {
        $this->uploadDir = __DIR__ . '/../uploads/';
        $this->optimizedDir = __DIR__ . '/../optimized/';
        $this->allowedTypes = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
        $this->maxFileSize = 50 * 1024 * 1024; // 50MB
        $this->defaultQuality = 80;

        // Create directories if they don't exist
        $this->createDirectories();
        
        // Configure Intervention Image
        Image::configure(['driver' => 'gd']);
    }

    /**
     * Create necessary directories
     */
    private function createDirectories()
    {
        if (!is_dir($this->uploadDir)) {
            mkdir($this->uploadDir, 0755, true);
        }
        if (!is_dir($this->optimizedDir)) {
            mkdir($this->optimizedDir, 0755, true);
        }
    }

    /**
     * Validate uploaded file
     */
    public function validateFile($file)
    {
        $errors = [];

        // Check if file was uploaded
        if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
            $errors[] = 'File upload failed';
            return $errors;
        }

        // Check file size
        if ($file['size'] > $this->maxFileSize) {
            $errors[] = 'File too large. Maximum size: 50MB';
        }

        // Check file type
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($extension, $this->allowedTypes)) {
            $errors[] = 'Invalid file type. Allowed: ' . implode(', ', $this->allowedTypes);
        }

        // Check if it's actually an image
        $imageInfo = getimagesize($file['tmp_name']);
        if (!$imageInfo) {
            $errors[] = 'File is not a valid image';
        }

        return $errors;
    }

    /**
     * Optimize single image
     */
    public function optimizeImage($file, $options = [])
    {
        try {
            // Validate file first
            $errors = $this->validateFile($file);
            if (!empty($errors)) {
                return [
                    'success' => false,
                    'errors' => $errors
                ];
            }

            // Generate unique filename
            $originalName = pathinfo($file['name'], PATHINFO_FILENAME);
            $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $uniqueId = uniqid();
            $filename = $originalName . '_' . $uniqueId;

            // Save original file
            $originalPath = $this->uploadDir . $filename . '.' . $extension;
            move_uploaded_file($file['tmp_name'], $originalPath);

            // Get original file info
            $originalSize = filesize($originalPath);
            $imageInfo = getimagesize($originalPath);
            $originalWidth = $imageInfo[0];
            $originalHeight = $imageInfo[1];

            // Load image with Intervention
            $image = Image::make($originalPath);

            // Apply optimizations
            $quality = $options['quality'] ?? $this->defaultQuality;
            $maxWidth = $options['max_width'] ?? null;
            $maxHeight = $options['max_height'] ?? null;
            $format = $options['format'] ?? $extension;

            // Resize if needed
            if ($maxWidth || $maxHeight) {
                $image->resize($maxWidth, $maxHeight, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
            }

            // Create optimized versions
            $results = [];
            
            // Original format optimized with progressive JPEG support
            $optimizedPath = $this->optimizedDir . $filename . '_optimized.' . $format;
            
            // Enable progressive JPEG for better loading experience
            if (in_array($format, ['jpg', 'jpeg'])) {
                $image->interlace(true); // Makes JPEG progressive
            }
            
            $image->encode($format, $quality)->save($optimizedPath);
            
            $optimizedSize = filesize($optimizedPath);
            $savings = round((($originalSize - $optimizedSize) / $originalSize) * 100, 1);

            $results[] = [
                'format' => $format,
                'filename' => $filename . '_optimized.' . $format,
                'path' => $optimizedPath,
                'size' => $optimizedSize,
                'size_human' => $this->formatBytes($optimizedSize),
                'savings' => $savings
            ];

            // Create WebP version if original isn't WebP
            if ($format !== 'webp' && ($options['create_webp'] ?? true)) {
                $webpPath = $this->optimizedDir . $filename . '_optimized.webp';
                
                // Clone image to avoid affecting original
                $webpImage = clone $image;
                $webpImage->encode('webp', max(60, $quality - 5))->save($webpPath);
                
                $webpSize = filesize($webpPath);
                $webpSavings = round((($originalSize - $webpSize) / $originalSize) * 100, 1);

                $results[] = [
                    'format' => 'webp',
                    'filename' => $filename . '_optimized.webp',
                    'path' => $webpPath,
                    'size' => $webpSize,
                    'size_human' => $this->formatBytes($webpSize),
                    'savings' => $webpSavings
                ];
            }

            // Create AVIF version for next-gen format (if GD supports it)
            if ($format !== 'avif' && ($options['create_avif'] ?? false) && function_exists('imageavif')) {
                try {
                    $avifPath = $this->optimizedDir . $filename . '_optimized.avif';
                    $avifImage = clone $image;
                    $avifImage->encode('avif', max(50, $quality - 10))->save($avifPath);
                    
                    $avifSize = filesize($avifPath);
                    $avifSavings = round((($originalSize - $avifSize) / $originalSize) * 100, 1);

                    $results[] = [
                        'format' => 'avif',
                        'filename' => $filename . '_optimized.avif',
                        'path' => $avifPath,
                        'size' => $avifSize,
                        'size_human' => $this->formatBytes($avifSize),
                        'savings' => $avifSavings
                    ];
                } catch (Exception $e) {
                    // AVIF encoding failed, continue without it
                    error_log('AVIF encoding failed: ' . $e->getMessage());
                }
            }

            // Create thumbnail with smart cropping
            if ($options['create_thumbnail'] ?? true) {
                $thumbPath = $this->optimizedDir . $filename . '_thumb.jpg';
                $thumbImage = Image::make($originalPath);
                
                // Smart thumbnail: fit and crop to center
                $thumbImage->fit(300, 300, function ($constraint) {
                    $constraint->upsize();
                }, 'center');
                
                // Enable progressive for thumbnail too
                $thumbImage->interlace(true)->encode('jpg', 85)->save($thumbPath);
                
                $thumbSize = filesize($thumbPath);

                $results[] = [
                    'format' => 'thumbnail',
                    'filename' => $filename . '_thumb.jpg',
                    'path' => $thumbPath,
                    'size' => $thumbSize,
                    'size_human' => $this->formatBytes($thumbSize),
                    'savings' => 'thumbnail'
                ];
            }

            // Clean up original file
            unlink($originalPath);

            // Get best savings percentage
            $savingsValues = array_filter(array_column($results, 'savings'), 'is_numeric');
            $bestSavings = !empty($savingsValues) ? max($savingsValues) : 0;

            return [
                'success' => true,
                'original' => [
                    'name' => $file['name'],
                    'size' => $originalSize,
                    'size_human' => $this->formatBytes($originalSize),
                    'width' => $originalWidth,
                    'height' => $originalHeight
                ],
                'optimized' => $results,
                'best_savings' => $bestSavings
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'errors' => ['Optimization failed: ' . $e->getMessage()]
            ];
        }
    }

    /**
     * Optimize multiple images
     */
    public function optimizeBatch($files, $options = [])
    {
        $results = [];
        $totalOriginalSize = 0;
        $totalOptimizedSize = 0;
        $successCount = 0;

        foreach ($files as $file) {
            $result = $this->optimizeImage($file, $options);
            $results[] = $result;

            if ($result['success']) {
                $successCount++;
                $totalOriginalSize += $result['original']['size'];
                $totalOptimizedSize += $result['optimized'][0]['size']; // Primary optimized version
            }
        }

        $totalSavings = $totalOriginalSize > 0 ? 
            round((($totalOriginalSize - $totalOptimizedSize) / $totalOriginalSize) * 100, 1) : 0;

        return [
            'results' => $results,
            'summary' => [
                'total_files' => count($files),
                'successful' => $successCount,
                'failed' => count($files) - $successCount,
                'total_original_size' => $this->formatBytes($totalOriginalSize),
                'total_optimized_size' => $this->formatBytes($totalOptimizedSize),
                'total_savings' => $totalSavings . '%',
                'space_saved' => $this->formatBytes($totalOriginalSize - $totalOptimizedSize)
            ]
        ];
    }

    /**
     * Get image information
     */
    public function getImageInfo($filepath)
    {
        if (!file_exists($filepath)) {
            return null;
        }

        $imageInfo = getimagesize($filepath);
        if (!$imageInfo) {
            return null;
        }

        return [
            'width' => $imageInfo[0],
            'height' => $imageInfo[1],
            'type' => image_type_to_extension($imageInfo[2], false),
            'size' => filesize($filepath),
            'size_human' => $this->formatBytes(filesize($filepath))
        ];
    }

    /**
     * Clean up old files (older than 24 hours)
     */
    public function cleanupOldFiles()
    {
        $directories = [$this->uploadDir, $this->optimizedDir];
        $deleted = 0;
        $cutoffTime = time() - 86400; // 24 hours ago

        foreach ($directories as $dir) {
            if (is_dir($dir)) {
                $files = glob($dir . '*');
                foreach ($files as $file) {
                    if (is_file($file) && filemtime($file) < $cutoffTime) {
                        unlink($file);
                        $deleted++;
                    }
                }
            }
        }

        // Log cleanup activity
        if ($deleted > 0) {
            error_log("Cleaned up $deleted old files");
        }

        return $deleted;
    }

    /**
     * Get storage usage statistics
     */
    public function getStorageStats()
    {
        $stats = [
            'uploads' => $this->getDirectorySize($this->uploadDir),
            'optimized' => $this->getDirectorySize($this->optimizedDir),
            'file_count' => count(glob($this->optimizedDir . '*'))
        ];

        $stats['total'] = $stats['uploads'] + $stats['optimized'];
        
        return [
            'uploads_size' => $this->formatBytes($stats['uploads']),
            'optimized_size' => $this->formatBytes($stats['optimized']),
            'total_size' => $this->formatBytes($stats['total']),
            'file_count' => $stats['file_count']
        ];
    }

    /**
     * Get directory size in bytes
     */
    private function getDirectorySize($directory)
    {
        $size = 0;
        if (is_dir($directory)) {
            foreach (glob($directory . '*') as $file) {
                if (is_file($file)) {
                    $size += filesize($file);
                }
            }
        }
        return $size;
    }

    /**
     * Format bytes to human readable format
     */
    private function formatBytes($size, $precision = 2)
    {
        if ($size === 0) return '0 B';
        
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $base = log($size, 1024);
        
        return round(pow(1024, $base - floor($base)), $precision) . ' ' . $units[floor($base)];
    }

    /**
     * Get supported formats
     */
    public function getSupportedFormats()
    {
        return $this->allowedTypes;
    }

    /**
     * Get max file size
     */
    public function getMaxFileSize()
    {
        return $this->maxFileSize;
    }

    /**
     * Get max file size in human readable format
     */
    public function getMaxFileSizeHuman()
    {
        return $this->formatBytes($this->maxFileSize);
    }

    /**
     * Check if system supports AVIF
     */
    public function supportsAvif()
    {
        return function_exists('imageavif');
    }

    /**
     * Get system capabilities
     */
    public function getSystemInfo()
    {
        return [
            'php_version' => PHP_VERSION,
            'gd_version' => gd_info()['GD Version'] ?? 'Unknown',
            'memory_limit' => ini_get('memory_limit'),
            'max_execution_time' => ini_get('max_execution_time'),
            'upload_max_filesize' => ini_get('upload_max_filesize'),
            'supports_webp' => function_exists('imagewebp'),
            'supports_avif' => $this->supportsAvif(),
            'intervention_image' => class_exists('Intervention\Image\ImageManagerStatic')
        ];
    }
}