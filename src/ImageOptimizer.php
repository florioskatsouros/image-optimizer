<?php

namespace ImageOptimizer;

use Intervention\Image\ImageManagerStatic as Image;
use Exception;

class ImageOptimizer
{
    private $uploadDir;
    private $optimizedDir;
    private $allowedTypes;
    private $advancedTypes;
    private $maxFileSize;
    private $defaultQuality;
    private $supportedFormats;

    public function __construct()
    {
        $this->uploadDir = __DIR__ . '/../uploads/';
        $this->optimizedDir = __DIR__ . '/../optimized/';
        
        // Basic formats (always supported)
        $this->allowedTypes = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
        
        // Advanced formats (check system support)
        $this->advancedTypes = [
            'tiff', 'tif', 'bmp', 'ico', 'svg', 'avif', 'heic', 'heif',
            'psd', 'eps', 'pdf', 'raw', 'cr2', 'nef', 'orf', 'arw', 'dng'
        ];
        
        $this->maxFileSize = 100 * 1024 * 1024; // Increased to 100MB for advanced formats
        $this->defaultQuality = 80;
        
        // Detect supported formats
        $this->supportedFormats = $this->detectSupportedFormats();
        
        // Create directories if they don't exist
        $this->createDirectories();
        
        // Configure Intervention Image with better driver detection
        $this->configureImageDriver();
    }

    /**
     * Create necessary directories if they don't exist
     */
    private function createDirectories()
    {
        $directories = [$this->uploadDir, $this->optimizedDir];
        
        foreach ($directories as $dir) {
            if (!is_dir($dir)) {
                if (!mkdir($dir, 0755, true)) {
                    throw new Exception("Failed to create directory: {$dir}");
                }
            }
            
            // Ensure directory is writable
            if (!is_writable($dir)) {
                throw new Exception("Directory is not writable: {$dir}");
            }
        }
        
        // Create logs directory
        $logsDir = __DIR__ . '/../logs/';
        if (!is_dir($logsDir)) {
            mkdir($logsDir, 0755, true);
        }
        
        // Create temp directory for batch downloads
        $tempDir = __DIR__ . '/../temp/';
        if (!is_dir($tempDir)) {
            mkdir($tempDir, 0755, true);
        }
    }

    /**
     * Detect what formats are actually supported on this system
     */
    private function detectSupportedFormats()
    {
        $supported = $this->allowedTypes; // Always include basic formats
        
        // Check GD/ImageMagick capabilities
        $gdInfo = function_exists('gd_info') ? gd_info() : [];
        
        // WebP support check
        if (!in_array('webp', $supported) && 
            (isset($gdInfo['WebP Support']) && $gdInfo['WebP Support']) || 
            function_exists('imagewebp')) {
            $supported[] = 'webp';
        }
        
        // AVIF support (PHP 8.1+)
        if (function_exists('imageavif') || $this->hasImageMagickFormat('AVIF')) {
            $supported[] = 'avif';
        }
        
        // HEIC support (requires ImageMagick)
        if ($this->hasImageMagickFormat('HEIC')) {
            $supported[] = 'heic';
            $supported[] = 'heif';
        }
        
        // TIFF support
        if ($this->hasImageMagickFormat('TIFF') || (isset($gdInfo['TIFF Support']) && $gdInfo['TIFF Support'])) {
            $supported[] = 'tiff';
            $supported[] = 'tif';
        }
        
        // BMP support
        if ($this->hasImageMagickFormat('BMP') || function_exists('imagebmp')) {
            $supported[] = 'bmp';
        }
        
        // ICO support - FIXED: Better detection
        if ($this->hasImageMagickFormat('ICO') || class_exists('Imagick')) {
            $supported[] = 'ico';
        }
        
        // SVG support (basic - will convert to raster)
        if ($this->hasImageMagickFormat('SVG') || class_exists('DOMDocument')) {
            $supported[] = 'svg';
        }
        
        // PSD support (ImageMagick only)
        if ($this->hasImageMagickFormat('PSD')) {
            $supported[] = 'psd';
        }
        
        // RAW formats (ImageMagick only)
        if ($this->hasImageMagickFormat('CR2')) {
            $supported = array_merge($supported, ['raw', 'cr2', 'nef', 'orf', 'arw', 'dng']);
        }
        
        // PDF support
        if ($this->hasImageMagickFormat('PDF')) {
            $supported[] = 'pdf';
        }
        
        return array_unique($supported);
    }
    
    /**
     * Check if ImageMagick supports a specific format
     */
    private function hasImageMagickFormat($format)
    {
        if (!class_exists('Imagick')) {
            return false;
        }
        
        try {
            $imagick = new \Imagick();
            $formats = $imagick->queryFormats($format);
            return !empty($formats);
        } catch (Exception $e) {
            return false;
        }
    }
    
    /**
     * Configure the best available image driver
     */
    private function configureImageDriver()
    {
        // Try ImageMagick first for better format support
        if (class_exists('Imagick')) {
            try {
                Image::configure(['driver' => 'imagick']);
                return;
            } catch (Exception $e) {
                // Fall back to GD
            }
        }
        
        // Use GD as fallback
        Image::configure(['driver' => 'gd']);
    }

    /**
     * Enhanced file validation with extended format support
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
            $errors[] = 'File too large. Maximum size: ' . $this->formatBytes($this->maxFileSize);
        }

        // Get file extension
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        
        // Check if format is supported
        if (!in_array($extension, $this->supportedFormats)) {
            $errors[] = 'Unsupported file type: ' . $extension . '. Supported: ' . implode(', ', $this->supportedFormats);
        }

        // Enhanced MIME type validation
        if (!$this->validateMimeType($file['tmp_name'], $extension)) {
            $errors[] = 'File appears to be corrupted or not a valid image';
        }

        return $errors;
    }
    
    /**
     * Advanced MIME type validation
     */
    private function validateMimeType($filepath, $extension)
    {
        // Use finfo if available
        if (function_exists('finfo_file')) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $filepath);
            finfo_close($finfo);
            
            $expectedMimes = $this->getExpectedMimeTypes($extension);
            if (in_array($mimeType, $expectedMimes)) {
                return true;
            }
        }
        
        // Fallback validation methods
        return $this->fallbackValidation($filepath, $extension);
    }
    
    /**
     * Get expected MIME types for a file extension
     */
    private function getExpectedMimeTypes($extension)
    {
        $mimeMap = [
            'jpg' => ['image/jpeg'],
            'jpeg' => ['image/jpeg'],
            'png' => ['image/png'],
            'gif' => ['image/gif'],
            'webp' => ['image/webp'],
            'avif' => ['image/avif'],
            'bmp' => ['image/bmp', 'image/x-bmp'],
            'tiff' => ['image/tiff'],
            'tif' => ['image/tiff'],
            'svg' => ['image/svg+xml'],
            'ico' => ['image/x-icon', 'image/vnd.microsoft.icon'],
            'heic' => ['image/heic', 'image/heif'],
            'heif' => ['image/heic', 'image/heif'],
            'psd' => ['image/vnd.adobe.photoshop', 'application/photoshop'],
            'pdf' => ['application/pdf'],
            'raw' => ['image/x-canon-cr2', 'image/x-canon-crw'],
            'cr2' => ['image/x-canon-cr2'],
            'nef' => ['image/x-nikon-nef'],
            'orf' => ['image/x-olympus-orf'],
            'arw' => ['image/x-sony-arw'],
            'dng' => ['image/x-adobe-dng']
        ];
        
        return $mimeMap[$extension] ?? ['application/octet-stream'];
    }
    
    /**
     * Fallback validation for formats not detected by finfo
     */
    private function fallbackValidation($filepath, $extension)
    {
        switch ($extension) {
            case 'svg':
                return $this->validateSVG($filepath);
            case 'psd':
                return $this->validatePSD($filepath);
            case 'heic':
            case 'heif':
                return $this->validateHEIC($filepath);
            default:
                // Try to load with Intervention Image
                try {
                    $image = Image::make($filepath);
                    return true;
                } catch (Exception $e) {
                    return false;
                }
        }
    }
    
    /**
     * Validate SVG files
     */
    private function validateSVG($filepath)
    {
        $content = file_get_contents($filepath);
        return strpos($content, '<svg') !== false;
    }
    
    /**
     * Validate PSD files
     */
    private function validatePSD($filepath)
    {
        $handle = fopen($filepath, 'rb');
        if (!$handle) return false;
        
        $signature = fread($handle, 4);
        fclose($handle);
        
        return $signature === '8BPS';
    }
    
    /**
     * Validate HEIC files
     */
    private function validateHEIC($filepath)
    {
        $handle = fopen($filepath, 'rb');
        if (!$handle) return false;
        
        fseek($handle, 4);
        $ftyp = fread($handle, 4);
        $brand = fread($handle, 4);
        fclose($handle);
        
        return $ftyp === 'ftyp' && (strpos($brand, 'heic') !== false || strpos($brand, 'mif1') !== false);
    }

    /**
     * Enhanced optimization with convert mode support - FIXED
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
            
            // Load image with enhanced error handling
            $image = $this->loadImageSafely($originalPath, $extension);
            if (!$image) {
                throw new Exception('Could not load image. Format may not be supported.');
            }
            
            // Get original dimensions
            $originalWidth = $image->width();
            $originalHeight = $image->height();

            // Apply optimizations based on mode
            $mode = $options['mode'] ?? 'optimize';
            $results = [];

            if ($mode === 'convert') {
                // Convert mode - handle format conversion
                $results = $this->handleConvertMode($image, $filename, $options, $originalSize);
            } else {
                // Optimize mode - existing logic
                $results = $this->handleOptimizeMode($image, $filename, $extension, $options, $originalSize);
            }

            // Create thumbnail if requested
            if ($options['create_thumbnail'] ?? false) {
                $result = $this->createThumbnail($originalPath, $filename, $originalSize);
                if ($result) {
                    $results[] = $result;
                }
            }

            // Clean up original file
            unlink($originalPath);

            // Get best savings percentage
            $savingsValues = array_filter(array_column($results, 'savings'), function($value) {
                return is_numeric($value);
            });
            $bestSavings = !empty($savingsValues) ? max($savingsValues) : 0;

            return [
                'success' => true,
                'original' => [
                    'name' => $file['name'],
                    'size' => $originalSize,
                    'size_human' => $this->formatBytes($originalSize),
                    'width' => $originalWidth,
                    'height' => $originalHeight,
                    'format' => $extension
                ],
                'optimized' => $results,
                'best_savings' => $bestSavings,
                'conversions_created' => count($results)
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'errors' => ['Processing failed: ' . $e->getMessage()]
            ];
        }
    }

    /**
     * Handle convert mode processing - FIXED
     */
    private function handleConvertMode($image, $filename, $options, $originalSize)
    {
        $results = [];
        $quality = $options['quality'] ?? 80;
        
        // Handle multiple format conversion
        if (!empty($options['convert_to'])) {
            foreach ($options['convert_to'] as $targetFormat) {
                if (in_array($targetFormat, $this->supportedFormats)) {
                    $result = $this->saveOptimizedImage($image, $filename, $targetFormat, $quality, $originalSize, true);
                    if ($result) {
                        $results[] = $result;
                    }
                }
            }
        }
        
        // Handle single format conversion
        if (!empty($options['output_format'])) {
            $targetFormat = $options['output_format'];
            if (in_array($targetFormat, $this->supportedFormats)) {
                $result = $this->saveOptimizedImage($image, $filename, $targetFormat, $quality, $originalSize, true);
                if ($result) {
                    $results[] = $result;
                }
            }
        }
        
        return $results;
    }

    /**
     * Handle optimize mode processing
     */
    private function handleOptimizeMode($image, $filename, $extension, $options, $originalSize)
    {
        $results = [];
        $quality = $options['quality'] ?? $this->defaultQuality;
        $maxWidth = $options['max_width'] ?? null;
        $maxHeight = $options['max_height'] ?? null;

        // Resize if needed
        if ($maxWidth || $maxHeight) {
            $image->resize($maxWidth, $maxHeight, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
        }

        // Create optimized version of original format
        $result = $this->saveOptimizedImage($image, $filename, $extension, $quality, $originalSize);
        if ($result) {
            $results[] = $result;
        }

        // Create WebP version if requested
        if (($options['create_webp'] ?? true) && $extension !== 'webp') {
            $result = $this->saveOptimizedImage($image, $filename, 'webp', max(60, $quality - 5), $originalSize);
            if ($result) {
                $results[] = $result;
            }
        }

        // Create AVIF version if requested and supported
        if (($options['create_avif'] ?? false) && $extension !== 'avif' && in_array('avif', $this->supportedFormats)) {
            $result = $this->saveOptimizedImage($image, $filename, 'avif', max(50, $quality - 10), $originalSize);
            if ($result) {
                $results[] = $result;
            }
        }

        return $results;
    }
    
    /**
     * Batch optimization with detailed progress tracking - FIXED
     */
    public function optimizeBatch($files, $options = [])
    {
        $startTime = microtime(true);
        $results = [];
        $summary = [
            'total_files' => count($files),
            'successful' => 0,
            'failed' => 0,
            'total_original_size' => 0,
            'total_optimized_size' => 0,
            'total_savings' => 0,
            'processing_time' => 0,
            'files_with_errors' => []
        ];
        
        foreach ($files as $index => $file) {
            try {
                $result = $this->optimizeImage($file, $options);
                $results[] = $result;
                
                if ($result['success']) {
                    $summary['successful']++;
                    $summary['total_original_size'] += $result['original']['size'];
                    
                    // Calculate total optimized size from all versions
                    $optimizedSize = array_sum(array_column($result['optimized'], 'size'));
                    $summary['total_optimized_size'] += $optimizedSize;
                } else {
                    $summary['failed']++;
                    $summary['files_with_errors'][] = [
                        'filename' => $file['name'],
                        'errors' => $result['errors']
                    ];
                }
            } catch (Exception $e) {
                $summary['failed']++;
                $summary['files_with_errors'][] = [
                    'filename' => $file['name'],
                    'errors' => ['Unexpected error: ' . $e->getMessage()]
                ];
                
                $results[] = [
                    'success' => false,
                    'original' => ['name' => $file['name']],
                    'errors' => ['Unexpected error: ' . $e->getMessage()]
                ];
            }
        }
        
        // Calculate total savings
        if ($summary['total_original_size'] > 0) {
            $summary['total_savings'] = round(
                (($summary['total_original_size'] - $summary['total_optimized_size']) / $summary['total_original_size']) * 100, 
                1
            );
        }
        
        // Add human-readable sizes
        $summary['total_original_size_human'] = $this->formatBytes($summary['total_original_size']);
        $summary['total_optimized_size_human'] = $this->formatBytes($summary['total_optimized_size']);
        $summary['processing_time'] = round(microtime(true) - $startTime, 2);
        
        return [
            'success' => true,
            'summary' => $summary,
            'results' => $results
        ];
    }
    
    /**
     * Safely load image with format-specific handling
     */
    private function loadImageSafely($filepath, $extension)
    {
        try {
            // Special handling for certain formats
            switch ($extension) {
                case 'svg':
                    return $this->loadSVG($filepath);
                case 'psd':
                    return $this->loadPSD($filepath);
                case 'heic':
                case 'heif':
                    return $this->loadHEIC($filepath);
                case 'pdf':
                    return $this->loadPDF($filepath);
                default:
                    return Image::make($filepath);
            }
        } catch (Exception $e) {
            error_log("Failed to load image {$filepath}: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Load SVG and convert to raster
     */
    private function loadSVG($filepath)
    {
        // For SVG, we need to convert to raster format
        if (class_exists('Imagick')) {
            $imagick = new \Imagick();
            $imagick->setBackgroundColor(new \ImagickPixel('transparent'));
            $imagick->readImage($filepath);
            $imagick->setImageFormat('png');
            
            return Image::make($imagick->getImageBlob());
        }
        
        throw new Exception('SVG support requires ImageMagick');
    }
    
    /**
     * Load PSD files
     */
    private function loadPSD($filepath)
    {
        if (class_exists('Imagick')) {
            $imagick = new \Imagick($filepath . '[0]'); // Get first layer
            $imagick->setImageFormat('png');
            
            return Image::make($imagick->getImageBlob());
        }
        
        throw new Exception('PSD support requires ImageMagick');
    }
    
    /**
     * Load HEIC files
     */
    private function loadHEIC($filepath)
    {
        if (class_exists('Imagick')) {
            $imagick = new \Imagick($filepath);
            $imagick->setImageFormat('jpg');
            
            return Image::make($imagick->getImageBlob());
        }
        
        throw new Exception('HEIC support requires ImageMagick');
    }
    
    /**
     * Load PDF files (first page)
     */
    private function loadPDF($filepath)
    {
        if (class_exists('Imagick')) {
            $imagick = new \Imagick($filepath . '[0]'); // First page
            $imagick->setImageFormat('jpg');
            $imagick->setImageResolution(150, 150); // Good quality for images
            
            return Image::make($imagick->getImageBlob());
        }
        
        throw new Exception('PDF support requires ImageMagick');
    }
    
    /**
     * Save optimized image with format-specific settings - FIXED
     */
    private function saveOptimizedImage($image, $filename, $format, $quality, $originalSize, $isConversion = false)
    {
        try {
            $suffix = $isConversion ? '_converted' : '_optimized';
            $optimizedPath = $this->optimizedDir . $filename . $suffix . '.' . $format;
            
            // Clone image to avoid affecting original
            $outputImage = clone $image;
            
            // Format-specific optimizations
            switch ($format) {
                case 'jpg':
                case 'jpeg':
                    $outputImage->interlace(true); // Progressive JPEG
                    $outputImage->encode('jpg', $quality);
                    break;
                    
                case 'png':
                    // PNG compression level (0-9)
                    $compressionLevel = (int)(9 - ($quality / 100) * 9);
                    $outputImage->encode('png', $compressionLevel);
                    break;
                    
                case 'webp':
                    $outputImage->encode('webp', $quality);
                    break;
                    
                case 'avif':
                    if (in_array('avif', $this->supportedFormats)) {
                        $outputImage->encode('avif', $quality);
                    } else {
                        throw new Exception('AVIF not supported');
                    }
                    break;
                    
                case 'bmp':
                    $outputImage->encode('bmp');
                    break;
                    
                case 'tiff':
                case 'tif':
                    $outputImage->encode('tiff', $quality);
                    break;
                    
                case 'ico':
                    // FIXED: Better ICO handling
                    if (class_exists('Imagick')) {
                        $this->createICOFile($outputImage, $optimizedPath);
                    } else {
                        // Fallback to PNG
                        $outputImage->encode('png');
                        $optimizedPath = str_replace('.ico', '.png', $optimizedPath);
                        $format = 'png';
                    }
                    break;
                    
                default:
                    $outputImage->encode($format, $quality);
            }
            
            // Save the image if not already saved (like ICO)
            if ($format !== 'ico' || !class_exists('Imagick')) {
                $outputImage->save($optimizedPath);
            }
            
            $optimizedSize = filesize($optimizedPath);
            $savings = $originalSize > 0 ? round((($originalSize - $optimizedSize) / $originalSize) * 100, 1) : 0;
            
            return [
                'format' => $format,
                'filename' => basename($optimizedPath),
                'path' => $optimizedPath,
                'size' => $optimizedSize,
                'size_human' => $this->formatBytes($optimizedSize),
                'savings' => max(0, $savings), // Ensure no negative savings
                'is_conversion' => $isConversion
            ];
            
        } catch (Exception $e) {
            error_log("Failed to save {$format} image: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Create ICO file using ImageMagick - FIXED
     */
    private function createICOFile($image, $outputPath)
    {
        if (!class_exists('Imagick')) {
            throw new Exception('ICO creation requires ImageMagick');
        }
        
        try {
            // Get the image data
            $imageData = $image->encode('png')->getEncoded();
            
            // Create Imagick instance from PNG data
            $imagick = new \Imagick();
            $imagick->readImageBlob($imageData);
            
            // Resize to common ICO sizes and create multi-size ICO
            $sizes = [16, 32, 48, 64, 128, 256];
            $ico = new \Imagick();
            
            foreach ($sizes as $size) {
                $resized = clone $imagick;
                $resized->resizeImage($size, $size, \Imagick::FILTER_LANCZOS, 1);
                $resized->setImageFormat('ico');
                $ico->addImage($resized);
                $resized->destroy();
            }
            
            // Write the ICO file
            $ico->writeImages($outputPath, true);
            $ico->destroy();
            $imagick->destroy();
            
        } catch (Exception $e) {
            error_log("Failed to create ICO file: " . $e->getMessage());
            throw $e;
        }
        if (!file_exists($outputPath) || filesize($outputPath) == 0) {
            throw new Exception('ICO file was not created properly');
        }
        
        return true;
    }
    
    /**
     * Enhanced thumbnail creation with smart cropping
     */
    private function createThumbnail($originalPath, $filename, $originalSize)
    {
        try {
            $thumbPath = $this->optimizedDir . $filename . '_thumb.jpg';
            $thumbImage = Image::make($originalPath);
            
            // Smart thumbnail: fit and crop to center
            $thumbImage->fit(300, 300, function ($constraint) {
                $constraint->upsize();
            }, 'center');
            
            // Enable progressive for thumbnail
            $thumbImage->interlace(true)->encode('jpg', 85)->save($thumbPath);
            
            $thumbSize = filesize($thumbPath);

            return [
                'format' => 'thumbnail',
                'filename' => $filename . '_thumb.jpg',
                'path' => $thumbPath,
                'size' => $thumbSize,
                'size_human' => $this->formatBytes($thumbSize),
                'savings' => 'thumbnail',
                'is_conversion' => false
            ];
            
        } catch (Exception $e) {
            error_log("Failed to create thumbnail: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Get detailed information about an image file
     */
    public function getImageInfo($filepath)
    {
        if (!file_exists($filepath)) {
            throw new Exception("File not found: {$filepath}");
        }
        
        $info = [
            'filepath' => $filepath,
            'filename' => basename($filepath),
            'extension' => strtolower(pathinfo($filepath, PATHINFO_EXTENSION)),
            'size' => filesize($filepath),
            'size_human' => $this->formatBytes(filesize($filepath)),
            'modified' => filemtime($filepath),
            'created' => filectime($filepath)
        ];
        
        // Try to get image dimensions and other details
        try {
            $image = Image::make($filepath);
            $info['width'] = $image->width();
            $info['height'] = $image->height();
            $info['aspect_ratio'] = round($image->width() / $image->height(), 2);
            $info['megapixels'] = round(($image->width() * $image->height()) / 1000000, 1);
            
            // Get EXIF data if available
            if (function_exists('exif_read_data') && in_array($info['extension'], ['jpg', 'jpeg'])) {
                $exif = @exif_read_data($filepath);
                if ($exif) {
                    $info['exif'] = [
                        'camera' => $exif['Model'] ?? null,
                        'datetime' => $exif['DateTime'] ?? null,
                        'iso' => $exif['ISOSpeedRatings'] ?? null,
                        'focal_length' => $exif['FocalLength'] ?? null,
                        'exposure_time' => $exif['ExposureTime'] ?? null,
                        'f_number' => $exif['FNumber'] ?? null
                    ];
                }
            }
            
        } catch (Exception $e) {
            $info['error'] = 'Could not read image data: ' . $e->getMessage();
        }
        
        return $info;
    }

    /**
     * Clean up old files from upload and optimized directories
     */
    public function cleanupOldFiles($hours = 24)
    {
        $cutoffTime = time() - ($hours * 3600);
        $deleted = [
            'uploads' => 0,
            'optimized' => 0,
            'temp' => 0
        ];
        
        $directories = [
            'uploads' => $this->uploadDir,
            'optimized' => $this->optimizedDir,
            'temp' => __DIR__ . '/../temp/'
        ];
        
        foreach ($directories as $type => $dir) {
            if (!is_dir($dir)) {
                continue;
            }
            
            $files = glob($dir . '*');
            foreach ($files as $file) {
                if (is_file($file) && filemtime($file) < $cutoffTime) {
                    if (unlink($file)) {
                        $deleted[$type]++;
                    }
                }
            }
        }
        
        // Log cleanup activity
        $total = array_sum($deleted);
        if ($total > 0) {
            error_log("Cleanup: Deleted {$total} files (uploads: {$deleted['uploads']}, optimized: {$deleted['optimized']}, temp: {$deleted['temp']})");
        }
        
        return $deleted;
    }

    /**
     * Get storage statistics
     */
    public function getStorageStats()
    {
        $stats = [
            'uploads' => [
                'count' => 0,
                'size' => 0,
                'size_human' => '0 B'
            ],
            'optimized' => [
                'count' => 0,
                'size' => 0,
                'size_human' => '0 B'
            ],
            'temp' => [
                'count' => 0,
                'size' => 0,
                'size_human' => '0 B'
            ]
        ];
        
        $directories = [
            'uploads' => $this->uploadDir,
            'optimized' => $this->optimizedDir,
            'temp' => __DIR__ . '/../temp/'
        ];
        
        foreach ($directories as $type => $dir) {
            if (is_dir($dir)) {
                $dirStats = $this->getDirectorySize($dir);
                $stats[$type] = $dirStats;
            }
        }
        
        // Calculate total
        $stats['total'] = [
            'count' => $stats['uploads']['count'] + $stats['optimized']['count'] + $stats['temp']['count'],
            'size' => $stats['uploads']['size'] + $stats['optimized']['size'] + $stats['temp']['size']
        ];
        $stats['total']['size_human'] = $this->formatBytes($stats['total']['size']);
        
        return $stats;
    }

    /**
     * Get directory size and file count
     */
    private function getDirectorySize($directory)
    {
        $size = 0;
        $count = 0;
        
        if (!is_dir($directory)) {
            return ['count' => 0, 'size' => 0, 'size_human' => '0 B'];
        }
        
        $files = glob($directory . '*');
        foreach ($files as $file) {
            if (is_file($file)) {
                $size += filesize($file);
                $count++;
            }
        }
        
        return [
            'count' => $count,
            'size' => $size,
            'size_human' => $this->formatBytes($size)
        ];
    }

    /**
     * Format bytes to human readable format
     */
    private function formatBytes($size, $precision = 2)
    {
        if ($size === 0) {
            return '0 B';
        }
        
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $base = log($size, 1024);
        $unitIndex = floor($base);
        
        if ($unitIndex >= count($units)) {
            $unitIndex = count($units) - 1;
        }
        
        return round(pow(1024, $base - $unitIndex), $precision) . ' ' . $units[$unitIndex];
    }

    /**
     * Get comprehensive system information including new format support
     */
    public function getSystemInfo()
    {
        $gdInfo = function_exists('gd_info') ? gd_info() : [];
        $imagickInfo = [];
        
        if (class_exists('Imagick')) {
            try {
                $imagick = new \Imagick();
                $imagickInfo = [
                    'version' => $imagick->getVersion()['versionString'] ?? 'Unknown',
                    'formats' => $imagick->queryFormats()
                ];
            } catch (Exception $e) {
                $imagickInfo = ['error' => $e->getMessage()];
            }
        }
        
        return [
            'php_version' => PHP_VERSION,
            'gd_info' => $gdInfo,
            'imagick_info' => $imagickInfo,
            'memory_limit' => ini_get('memory_limit'),
            'max_execution_time' => ini_get('max_execution_time'),
            'upload_max_filesize' => ini_get('upload_max_filesize'),
            'post_max_size' => ini_get('post_max_size'),
            'supported_formats' => $this->supportedFormats,
            'total_formats_supported' => count($this->supportedFormats),
            'advanced_formats_available' => array_intersect($this->advancedTypes, $this->supportedFormats),
            'intervention_image' => class_exists('Intervention\Image\ImageManagerStatic'),
            'server_info' => [
                'os' => PHP_OS,
                'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
                'document_root' => $_SERVER['DOCUMENT_ROOT'] ?? 'Unknown'
            ]
        ];
    }
    
    /**
     * Get all supported formats with details
     */
    public function getSupportedFormatsDetailed()
    {
        $formatDetails = [];
        
        foreach ($this->supportedFormats as $format) {
            $formatDetails[$format] = [
                'extension' => $format,
                'name' => $this->getFormatName($format),
                'description' => $this->getFormatDescription($format),
                'category' => $this->getFormatCategory($format),
                'can_optimize' => $this->canOptimizeFormat($format),
                'can_convert_from' => true,
                'can_convert_to' => $this->canConvertToFormat($format),
                'mime_types' => $this->getExpectedMimeTypes($format)
            ];
        }
        
        return $formatDetails;
    }
    
    /**
     * Get human-readable format name
     */
    private function getFormatName($format)
    {
        $names = [
            'jpg' => 'JPEG',
            'jpeg' => 'JPEG',
            'png' => 'PNG',
            'gif' => 'GIF',
            'webp' => 'WebP',
            'avif' => 'AVIF',
            'bmp' => 'Bitmap',
            'tiff' => 'TIFF',
            'tif' => 'TIFF',
            'svg' => 'SVG',
            'ico' => 'Icon',
            'heic' => 'HEIC',
            'heif' => 'HEIF',
            'psd' => 'Photoshop',
            'pdf' => 'PDF',
            'raw' => 'Camera RAW',
            'cr2' => 'Canon RAW',
            'nef' => 'Nikon RAW',
            'orf' => 'Olympus RAW',
            'arw' => 'Sony RAW',
            'dng' => 'Adobe DNG'
        ];
        
        return $names[$format] ?? strtoupper($format);
    }
    
    /**
     * Get format description
     */
    private function getFormatDescription($format)
    {
        $descriptions = [
            'jpg' => 'Lossy compression, good for photos',
            'jpeg' => 'Lossy compression, good for photos',
            'png' => 'Lossless compression, supports transparency',
            'gif' => 'Limited colors, supports animation',
            'webp' => 'Modern format, smaller file sizes',
            'avif' => 'Next-gen format, excellent compression',
            'bmp' => 'Uncompressed bitmap format',
            'tiff' => 'High quality, print-ready format',
            'svg' => 'Vector graphics format',
            'ico' => 'Windows icon format',
            'heic' => 'Apple\'s modern image format',
            'psd' => 'Adobe Photoshop format',
            'pdf' => 'Portable document format',
            'raw' => 'Unprocessed camera data'
        ];
        
        return $descriptions[$format] ?? 'Image format';
    }
    
    /**
     * Get format category
     */
    private function getFormatCategory($format)
    {
        $categories = [
            'web' => ['jpg', 'jpeg', 'png', 'gif', 'webp', 'avif'],
            'print' => ['tiff', 'tif', 'bmp', 'psd'],
            'vector' => ['svg'],
            'raw' => ['raw', 'cr2', 'nef', 'orf', 'arw', 'dng'],
            'document' => ['pdf'],
            'system' => ['ico']
        ];
        
        foreach ($categories as $category => $formats) {
            if (in_array($format, $formats)) {
                return $category;
            }
        }
        
        return 'other';
    }
    
    /**
     * Check if format can be optimized
     */
    private function canOptimizeFormat($format)
    {
        $optimizable = ['jpg', 'jpeg', 'png', 'webp', 'avif', 'tiff', 'tif'];
        return in_array($format, $optimizable);
    }
    
    /**
     * Check if format can be converted to
     */
    private function canConvertToFormat($format)
    {
        $convertible = ['jpg', 'jpeg', 'png', 'webp', 'avif', 'bmp', 'tiff', 'gif', 'ico'];
        return in_array($format, $convertible);
    }

    /**
     * Get basic supported formats list
     */
    public function getSupportedFormats()
    {
        return $this->supportedFormats;
    }

    /**
     * Get maximum file size
     */
    public function getMaxFileSize()
    {
        return $this->maxFileSize;
    }

    /**
     * Get maximum file size in human readable format
     */
    public function getMaxFileSizeHuman()
    {
        return $this->formatBytes($this->maxFileSize);
    }

    /**
     * Check if a specific format is supported
     */
    public function isFormatSupported($format)
    {
        return in_array(strtolower($format), $this->supportedFormats);
    }

    /**
     * Get processing statistics
     */
    public function getProcessingStats()
    {
        $logFile = __DIR__ . '/../logs/processing.log';
        
        if (!file_exists($logFile)) {
            return [
                'total_processed' => 0,
                'today_processed' => 0,
                'success_rate' => 0,
                'average_processing_time' => 0
            ];
        }
        
        $lines = file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $today = date('Y-m-d');
        $todayCount = 0;
        $totalProcessed = 0;
        $totalTime = 0;
        $timeCount = 0;
        
        foreach ($lines as $line) {
            if (strpos($line, 'Successfully processed') !== false) {
                $totalProcessed++;
                
                if (strpos($line, $today) !== false) {
                    $todayCount++;
                }
                
                // Extract processing time if available
                if (preg_match('/in ([\d.]+)s/', $line, $matches)) {
                    $totalTime += floatval($matches[1]);
                    $timeCount++;
                }
            }
        }
        
        return [
            'total_processed' => $totalProcessed,
            'today_processed' => $todayCount,
            'success_rate' => 100, // This would need more complex logic to calculate actual rate
            'average_processing_time' => $timeCount > 0 ? round($totalTime / $timeCount, 2) : 0,
            'total_log_entries' => count($lines)
        ];
    }

    /**
     * Validate system requirements
     */
    public function validateSystemRequirements()
    {
        $requirements = [
            'php_version' => [
                'required' => '7.4.0',
                'current' => PHP_VERSION,
                'status' => version_compare(PHP_VERSION, '7.4.0', '>=')
            ],
            'gd_extension' => [
                'required' => true,
                'current' => extension_loaded('gd'),
                'status' => extension_loaded('gd')
            ],
            'fileinfo_extension' => [
                'required' => true,
                'current' => extension_loaded('fileinfo'),
                'status' => extension_loaded('fileinfo')
            ],
            'imagick_extension' => [
                'required' => false,
                'current' => class_exists('Imagick'),
                'status' => true // Optional, so always true
            ],
            'memory_limit' => [
                'required' => '256M',
                'current' => ini_get('memory_limit'),
                'status' => $this->parseMemoryLimit(ini_get('memory_limit')) >= $this->parseMemoryLimit('256M')
            ],
            'upload_max_filesize' => [
                'required' => '100M',
                'current' => ini_get('upload_max_filesize'),
                'status' => $this->parseMemoryLimit(ini_get('upload_max_filesize')) >= $this->parseMemoryLimit('100M')
            ]
        ];
        
        $allPassed = true;
        foreach ($requirements as $req) {
            if (!$req['status']) {
                $allPassed = false;
                break;
            }
        }
        
        return [
            'all_passed' => $allPassed,
            'requirements' => $requirements
        ];
    }

    /**
     * Parse memory limit string to bytes
     */
    private function parseMemoryLimit($val)
    {
        $val = trim($val);
        $last = strtolower($val[strlen($val)-1]);
        $val = (int) $val;
        
        switch($last) {
            case 'g':
                $val *= 1024;
            case 'm':
                $val *= 1024;
            case 'k':
                $val *= 1024;
        }
        
        return $val;
    }

    /**
     * Log activity for debugging and monitoring
     */
    private function logActivity($message, $level = 'INFO')
    {
        $logFile = __DIR__ . '/../logs/activity.log';
        $logDir = dirname($logFile);
        
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }
        
        $timestamp = date('Y-m-d H:i:s');
        $logMessage = "[$timestamp] [$level] $message" . PHP_EOL;
        file_put_contents($logFile, $logMessage, FILE_APPEND | LOCK_EX);
    }

    /**
     * Destructor - cleanup and logging
     */
    public function __destruct()
    {
        // Log any final activities or cleanup
        // This is called when the object is destroyed
    }
}