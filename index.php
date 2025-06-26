<?php
require_once 'vendor/autoload.php';

use ImageOptimizer\ImageOptimizer;

$optimizer = new ImageOptimizer();
$maxFileSize = $optimizer->getMaxFileSizeHuman();
$supportedFormats = implode(', ', $optimizer->getSupportedFormats());
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🎨 Image Optimizer Pro - Compress Images Online</title>
    <meta name="description" content="Optimize and compress your images online. Reduce file size up to 90% without losing quality. Support for JPG, PNG, WebP formats.">
    <link rel="stylesheet" href="assets/style.css">
    <link rel="icon" href="/assets/images/logo.ico" type="image/x-icon">
    <link rel="shortcut icon" href="/assets/images/logo.ico" type="image/x-icon">
</head>
<body>
    <div class="container">
        <header class="header">
            <div class="logo">
                <h1>🎨 Image Optimizer Pro</h1>
                <p class="tagline">Compress images without losing quality</p>
            </div>
            <div class="stats">
                <div class="stat">
                    <span class="stat-number" id="totalProcessed">0</span>
                    <span class="stat-label">Images Optimized</span>
                </div>
                <div class="stat">
                    <span class="stat-number" id="totalSaved">0GB</span>
                    <span class="stat-label">Storage Saved</span>
                </div>
            </div>
        </header>

        <main class="main-content">
            <!-- Upload Section -->
            <section class="upload-section" id="uploadSection">
                <div class="upload-zone" id="uploadZone">
                    <div class="upload-icon">
                        <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                            <circle cx="8.5" cy="8.5" r="1.5"/>
                            <polyline points="21,15 16,10 5,21"/>
                        </svg>
                    </div>
                    <h2>Drop your images here</h2>
                    <p>or <span class="browse-text">click to browse</span></p>
                    <div class="upload-info">
                        <p>✅ Supported: <?php echo $supportedFormats; ?></p>
                        <p>✅ Max size: <?php echo $maxFileSize; ?> per image</p>
                        <p>✅ Batch upload supported</p>
                    </div>
                    <input type="file" id="fileInput" multiple accept="image/*" hidden>
                </div>

                <div class="options-panel" id="optionsPanel">
                    <h3>🎛️ Optimization Options</h3>
                    <div class="options-grid">
                        <div class="option-group">
                            <label for="quality">Quality</label>
                            <div class="quality-slider">
                                <input type="range" id="quality" min="20" max="100" value="80">
                                <div class="quality-labels">
                                    <span>Small</span>
                                    <span id="qualityValue">80%</span>
                                    <span>Original</span>
                                </div>
                            </div>
                        </div>

                        <div class="option-group">
                            <label for="maxWidth">Max Width (optional)</label>
                            <select id="maxWidth">
                                <option value="">Keep original</option>
                                <option value="1920">1920px (HD)</option>
                                <option value="1200">1200px (Web)</option>
                                <option value="800">800px (Mobile)</option>
                                <option value="custom">Custom...</option>
                            </select>
                            <input type="number" id="customWidth" placeholder="Custom width" style="display: none;">
                        </div>

                        <div class="option-group">
                            <label>Output Formats</label>
                            <div class="checkbox-group">
                                <label class="checkbox">
                                    <input type="checkbox" id="createWebp" checked>
                                    <span>WebP (smaller files)</span>
                                </label>
                                <label class="checkbox">
                                    <input type="checkbox" id="createThumbnail" checked>
                                    <span>Thumbnail (300x300)</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Processing Section -->
            <section class="processing-section" id="processingSection" style="display: none;">
                <div class="processing-header">
                    <h2>⚡ Optimizing your images...</h2>
                    <div class="overall-progress">
                        <div class="progress-bar">
                            <div class="progress-fill" id="overallProgress"></div>
                        </div>
                        <span class="progress-text" id="progressText">0%</span>
                    </div>
                </div>
                <div class="files-list" id="filesList">
                    <!-- Files will be populated here -->
                </div>
            </section>

            <!-- Results Section -->
            <section class="results-section" id="resultsSection" style="display: none;">
                <div class="results-header">
                    <h2>🎉 Optimization Complete!</h2>
                    <div class="results-summary" id="resultsSummary">
                        <!-- Summary will be populated here -->
                    </div>
                </div>

                <div class="results-actions">
                    <button class="btn btn-primary" id="downloadAllBtn">
                        📦 Download All
                    </button>
                    <button class="btn btn-secondary" id="optimizeMoreBtn">
                        🔄 Optimize More
                    </button>
                </div>

                <div class="results-list" id="resultsList">
                    <!-- Results will be populated here -->
                </div>
            </section>
        </main>

        <!-- How it Works -->
        <section class="how-it-works">
            <h2>How it works</h2>
            <div class="steps">
                <div class="step">
                    <div class="step-icon">📸</div>
                    <h3>Upload</h3>
                    <p>Drop your images or click to browse. Supports JPG, PNG, WebP, and GIF formats.</p>
                </div>
                <div class="step">
                    <div class="step-icon">⚡</div>
                    <h3>Optimize</h3>
                    <p>We compress your images using advanced algorithms while preserving quality.</p>
                </div>
                <div class="step">
                    <div class="step-icon">📥</div>
                    <h3>Download</h3>
                    <p>Get your optimized images in multiple formats. Up to 90% size reduction!</p>
                </div>
            </div>
        </section>

        <!-- Features -->
        <section class="features">
            <h2>Why choose Image Optimizer Pro?</h2>
            <div class="features-grid">
                <div class="feature">
                    <div class="feature-icon">🚀</div>
                    <h3>Lightning Fast</h3>
                    <p>Process images in seconds with our optimized compression engine.</p>
                </div>
                <div class="feature">
                    <div class="feature-icon">🎯</div>
                    <h3>Smart Compression</h3>
                    <p>Advanced algorithms maintain quality while reducing file size up to 90%.</p>
                </div>
                <div class="feature">
                    <div class="feature-icon">📱</div>
                    <h3>Multiple Formats</h3>
                    <p>Get WebP, JPEG, PNG versions plus thumbnails for different use cases.</p>
                </div>
                <div class="feature">
                    <div class="feature-icon">🔒</div>
                    <h3>Privacy First</h3>
                    <p>Images are processed locally and automatically deleted after 24 hours.</p>
                </div>
                <div class="feature">
                    <div class="feature-icon">📦</div>
                    <h3>Batch Processing</h3>
                    <p>Upload and optimize multiple images at once to save time.</p>
                </div>
                <div class="feature">
                    <div class="feature-icon">💎</div>
                    <h3>100% Free</h3>
                    <p>No registration, no watermarks, no limits. Just pure optimization power.</p>
                </div>
            </div>
        </section>
    </div>

    <footer class="footer">
        <div class="container">
            <p>&copy; 2025 Image Optimizer Pro. Made with ❤️ for better web performance.</p>
            <div class="footer-links">
                <a href="#privacy">Privacy Policy</a>
                <a href="#terms">Terms of Service</a>
                <a href="https://github.com/your-username/image-optimizer" target="_blank">GitHub</a>
            </div>
        </div>
    </footer>

    <script src="assets/app.js"></script>
</body>
</html>