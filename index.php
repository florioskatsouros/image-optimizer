<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🎨 Image Optimizer Pro - Optimize & Convert Images</title>
    <meta name="description" content="Optimize and convert your images online. Support for 20+ formats including HEIC, RAW, PSD. Reduce file size up to 90% without losing quality.">
    <link rel="stylesheet" href="assets/style.css">
    <link rel="stylesheet" href="assets/enhanced-styles.css">
    <link rel="icon" href="/assets/images/logo.ico" type="image/x-icon">
</head>
<body>
    <div class="container">
        <header class="header">
            <div class="logo">
                <h1 class="title-with-logo">
                    <img src="assets/images/logo.jpg" alt="Image Optimizer Logo" class="inline-logo">
                    Image Optimizer Pro
                </h1>
                <p class="tagline">Optimize & Convert images - 20+ formats supported</p>
            </div>
            <div class="stats">
                <div class="stat">
                    <span class="stat-number" id="totalProcessed">0</span>
                    <span class="stat-label">Images Processed</span>
                </div>
                <div class="stat">
                    <span class="stat-number" id="totalSaved">0GB</span>
                    <span class="stat-label">Storage Saved</span>
                </div>
                <div class="stat">
                    <span class="stat-number" id="formatsSupported">20+</span>
                    <span class="stat-label">Formats Supported</span>
                </div>
            </div>
        </header>

        <main class="main-content">
            <!-- Mode Toggle Section -->
            <section class="mode-toggle-section">
                <div class="mode-toggle">
                    <div class="mode-labels">
                        <div class="mode-label active" id="optimizeLabel">
                            <span>⚡</span>
                            <span>Optimize</span>
                        </div>
                        
                        <label class="toggle-switch">
                            <input type="checkbox" id="modeToggle">
                            <span class="toggle-slider"></span>
                        </label>
                        
                        <div class="mode-label" id="convertLabel">
                            <span>🔄</span>
                            <span>Convert</span>
                        </div>
                    </div>
                </div>
                <p class="mode-description" id="modeDescription">
                    Compress images while maintaining quality. Supports JPG, PNG, WebP, AVIF, HEIC, RAW, and more.
                </p>
            </section>

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
                        <p>✅ Supported: <span id="supportedFormatsList">JPG, PNG, WebP, GIF, AVIF, HEIC, RAW, PSD, TIFF, BMP</span></p>
                        <p>✅ Max size: <span id="maxFileSize">100MB</span> per image</p>
                        <p>✅ Batch upload supported</p>
                    </div>
                    <input type="file" id="fileInput" multiple accept="image/*,.heic,.heif,.psd,.raw,.cr2,.nef,.orf,.arw,.dng,.tiff,.tif" hidden>
                </div>

                <!-- Optimize Options Panel -->
                <div class="options-panel" id="optimizePanel">
                    <h3>⚡ Optimization Options</h3>
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
                                <option value="3840">3840px (4K)</option>
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
                                    <span>WebP (25-35% smaller)</span>
                                </label>
                                <label class="checkbox">
                                    <input type="checkbox" id="createAvif">
                                    <span>AVIF (50% smaller) <span class="format-support-badge experimental">Beta</span></span>
                                </label>
                                <label class="checkbox">
                                    <input type="checkbox" id="createThumbnail" checked>
                                    <span>Thumbnail (300x300)</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Convert Options Panel -->
                <div class="convert-panel" id="convertPanel" style="display: none;">
                    <h3>🔄 Conversion Options</h3>
                    
                    <div class="format-section">
                        <h4>Output Format</h4>
                        <div class="format-toggle">
                            <input type="radio" id="singleFormat" name="convertMode" value="single" checked>
                            <label for="singleFormat">Convert to single format</label>
                        </div>
                        <div class="format-toggle">
                            <input type="checkbox" id="convertToMultiple">
                            <label for="convertToMultiple">Convert to multiple formats</label>
                        </div>
                    </div>

                    <!-- Single Format Selection -->
                    <div class="format-section">
                        <select id="outputFormat">
                            <option value="">Select output format</option>
                            <!-- Options will be populated by JavaScript -->
                        </select>
                    </div>

                    <!-- Multiple Format Selection -->
                    <div class="format-section" id="selectedFormats" style="display: none;">
                        <!-- Format checkboxes will be populated by JavaScript -->
                    </div>

                    <!-- Format Preview -->
                    <div class="format-preview" id="formatPreview">
                        <p>Select an output format above</p>
                    </div>

                    <div class="option-group">
                        <label for="convertQuality">Quality for lossy formats</label>
                        <div class="quality-slider">
                            <input type="range" id="convertQuality" min="20" max="100" value="80">
                            <div class="quality-labels">
                                <span>Small</span>
                                <span id="convertQualityValue">80%</span>
                                <span>Original</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- File Preview will be inserted here by JavaScript -->
            </section>

            <!-- Processing Section -->
            <section class="processing-section" id="processingSection" style="display: none;">
                <div class="processing-header">
                    <h2>⚡ Processing your images...</h2>
                    <div class="overall-progress">
                        <div class="progress-bar">
                            <div class="progress-fill" id="overallProgress"></div>
                        </div>
                        <span class="progress-text" id="progressText">0%</span>
                    </div>
                </div>
                
                <div class="conversion-progress" id="conversionProgress">
                    <div class="conversion-step active">
                        <span>📤</span>
                        <span>Uploading</span>
                    </div>
                    <div class="conversion-step">
                        <span>🔄</span>
                        <span>Processing</span>
                    </div>
                    <div class="conversion-step">
                        <span>💾</span>
                        <span>Saving</span>
                    </div>
                    <div class="conversion-step">
                        <span>✅</span>
                        <span>Complete</span>
                    </div>
                </div>

                <div class="files-list" id="filesList">
                    <!-- Files will be populated here -->
                </div>
            </section>

            <!-- Results Section -->
            <section class="results-section" id="resultsSection" style="display: none;">
                <div class="results-header">
                    <h2>🎉 Processing Complete!</h2>
                    <div class="results-summary" id="resultsSummary">
                        <!-- Summary will be populated here -->
                    </div>
                </div>

                <div class="results-actions">
                    <button class="btn btn-primary" id="downloadAllBtn">
                        📦 Download All Files
                    </button>
                    <button class="btn btn-secondary" id="optimizeMoreBtn">
                        🔄 Process More Images
                    </button>
                </div>

                <div class="results-list" id="resultsList">
                    <!-- Results will be populated here -->
                </div>
            </section>
        </main>

        <!-- How it Works -->
        <section class="how-it-works">
            <h2>Enhanced Features</h2>
            <div class="steps">
                <div class="step">
                    <div class="step-icon">📸</div>
                    <h3>20+ Formats</h3>
                    <p>Support for JPG, PNG, WebP, AVIF, HEIC, RAW (CR2, NEF, ORF), PSD, TIFF, BMP, and more.</p>
                </div>
                <div class="step">
                    <div class="step-icon">🔄</div>
                    <h3>Smart Conversion</h3>
                    <p>Convert between any supported formats with optimized settings for web, print, or storage.</p>
                </div>
                <div class="step">
                    <div class="step-icon">⚡</div>
                    <h3>Batch Processing</h3>
                    <p>Process multiple images simultaneously with different output formats and quality settings.</p>
                </div>
                <div class="step">
                    <div class="step-icon">🎯</div>
                    <h3>Advanced Options</h3>
                    <p>Fine-tune compression, resize images, and create multiple variants in one go.</p>
                </div>
            </div>
        </section>

        <!-- System Capabilities -->
        <section class="system-info" id="systemInfo" style="display: none;">
            <h2>🔧 System Capabilities</h2>
            <div class="capability-grid" id="capabilityGrid">
                <!-- Will be populated by JavaScript -->
            </div>
        </section>

        <!-- Enhanced Features -->
        <section class="features">
            <h2>Why choose our Enhanced Image Optimizer?</h2>
            <div class="features-grid">
                <div class="feature">
                    <div class="feature-icon">🌟</div>
                    <h3>Extended Format Support</h3>
                    <p>Process HEIC photos from iPhone, RAW files from cameras, PSD from Photoshop, and 15+ more formats.</p>
                </div>
                <div class="feature">
                    <div class="feature-icon">🔄</div>
                    <h3>Format Converter</h3>
                    <p>Convert images between any supported formats. Perfect for web optimization or print preparation.</p>
                </div>
                <div class="feature">
                    <div class="feature-icon">📱</div>
                    <h3>Modern Formats</h3>
                    <p>Generate WebP and AVIF versions for modern browsers, reducing bandwidth by up to 50%.</p>
                </div>
                <div class="feature">
                    <div class="feature-icon">🎨</div>
                    <h3>Professional Tools</h3>
                    <p>Handle professional formats like PSD, TIFF, and camera RAW files with advanced processing.</p>
                </div>
                <div class="feature">
                    <div class="feature-icon">🚀</div>
                    <h3>Intelligent Processing</h3>
                    <p>Automatic format detection, progressive JPEG creation, and smart compression algorithms.</p>
                </div>
                <div class="feature">
                    <div class="feature-icon">🔒</div>
                    <h3>Privacy & Security</h3>
                    <p>All processing happens on your server. Files are automatically deleted after 24 hours.</p>
                </div>
            </div>
        </section>

        <!-- Format Support Matrix -->
        <section class="format-matrix" id="formatMatrix" style="display: none;">
            <h2>📋 Supported Formats</h2>
            <div class="format-categories">
                <div class="format-category">
                    <h3>📸 Photography</h3>
                    <div class="format-list">
                        <span class="format-badge supported">JPG/JPEG</span>
                        <span class="format-badge supported">PNG</span>
                        <span class="format-badge supported">HEIC/HEIF</span>
                        <span class="format-badge supported">WebP</span>
                        <span class="format-badge supported">AVIF</span>
                    </div>
                </div>
                <div class="format-category">
                    <h3>📷 Camera RAW</h3>
                    <div class="format-list">
                        <span class="format-badge supported">CR2 (Canon)</span>
                        <span class="format-badge supported">NEF (Nikon)</span>
                        <span class="format-badge supported">ORF (Olympus)</span>
                        <span class="format-badge supported">ARW (Sony)</span>
                        <span class="format-badge supported">DNG (Adobe)</span>
                    </div>
                </div>
                <div class="format-category">
                    <h3>🎨 Professional</h3>
                    <div class="format-list">
                        <span class="format-badge supported">PSD (Photoshop)</span>
                        <span class="format-badge supported">TIFF/TIF</span>
                        <span class="format-badge supported">BMP</span>
                        <span class="format-badge supported">SVG</span>
                        <span class="format-badge supported">EPS</span>
                    </div>
                </div>
                <div class="format-category">
                    <h3>🌐 Web & System</h3>
                    <div class="format-list">
                        <span class="format-badge supported">GIF</span>
                        <span class="format-badge supported">ICO</span>
                        <span class="format-badge supported">PDF</span>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <footer class="footer">
        <div class="container">
            <p>&copy; 2025 Enhanced Image Optimizer Pro. Supporting 20+ formats with advanced conversion capabilities.</p>
            <div class="footer-links">
                <a href="#" onclick="toggleFormatMatrix()">Supported Formats</a>
                <a href="#" onclick="toggleSystemInfo()">System Info</a>
                <a href="#privacy">Privacy Policy</a>
                <a href="https://github.com/your-username/image-optimizer" target="_blank">GitHub</a>
            </div>
        </div>
    </footer>

    <script src="assets/app.js"></script>
    <script>
        // Toggle format matrix visibility
        function toggleFormatMatrix() {
            const matrix = document.getElementById('formatMatrix');
            matrix.style.display = matrix.style.display === 'none' ? 'block' : 'none';
        }

        // Toggle system info visibility
        function toggleSystemInfo() {
            const info = document.getElementById('systemInfo');
            if (info.style.display === 'none') {
                info.style.display = 'block';
                loadSystemCapabilities();
            } else {
                info.style.display = 'none';
            }
        }

        // Load and display system capabilities
        async function loadSystemCapabilities() {
            try {
                const response = await fetch('api/formats.php');
                const data = await response.json();
                
                if (data.success) {
                    displaySystemCapabilities(data);
                }
            } catch (error) {
                console.error('Failed to load system capabilities:', error);
            }
        }

        // Display system capabilities
        function displaySystemCapabilities(data) {
            const grid = document.getElementById('capabilityGrid');
            if (!grid) return;

            const capabilities = [
                {
                    name: 'PHP Version',
                    value: data.system_info.php_version,
                    status: 'supported'
                },
                {
                    name: 'ImageMagick',
                    value: data.system_info.has_imagick ? 'Available' : 'Not Available',
                    status: data.system_info.has_imagick ? 'supported' : 'limited'
                },
                {
                    name: 'GD Extension',
                    value: data.system_info.has_gd ? 'Available' : 'Not Available',
                    status: data.system_info.has_gd ? 'supported' : 'unsupported'
                },
                {
                    name: 'Memory Limit',
                    value: data.system_info.memory_limit,
                    status: 'supported'
                },
                {
                    name: 'Max File Size',
                    value: data.system_info.max_file_size,
                    status: 'supported'
                },
                {
                    name: 'Total Formats',
                    value: data.total_supported + ' formats',
                    status: 'supported'
                }
            ];

            grid.innerHTML = capabilities.map(cap => `
                <div class="capability-item">
                    <div class="capability-status ${cap.status}"></div>
                    <h4>${cap.name}</h4>
                    <p>${cap.value}</p>
                </div>
            `).join('');
        }

        // Update mode description
        document.getElementById('modeToggle').addEventListener('change', function(e) {
            const description = document.getElementById('modeDescription');
            const optimizeLabel = document.getElementById('optimizeLabel');
            const convertLabel = document.getElementById('convertLabel');
            
            if (e.target.checked) {
                description.textContent = 'Convert images between different formats. Support for web optimization, print preparation, and format standardization.';
                optimizeLabel.classList.remove('active');
                convertLabel.classList.add('active');
            } else {
                description.textContent = 'Compress images while maintaining quality. Supports JPG, PNG, WebP, AVIF, HEIC, RAW, and more.';
                convertLabel.classList.remove('active');
                optimizeLabel.classList.add('active');
            }
        });

        // Update quality display for convert mode
        document.getElementById('convertQuality')?.addEventListener('input', function() {
            const value = this.value;
            document.getElementById('convertQualityValue').textContent = value + '%';
        });

        // Auto-load system capabilities on page load
        document.addEventListener('DOMContentLoaded', function() {
            // Update formats supported counter
            fetch('api/formats.php')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('formatsSupported').textContent = data.total_supported;
                        
                        // Update supported formats list
                        const formatsList = data.formats.map(f => f.toUpperCase()).slice(0, 6).join(', ');
                        document.getElementById('supportedFormatsList').textContent = formatsList + (data.total_supported > 6 ? '...' : '');
                        
                        // Update max file size
                        document.getElementById('maxFileSize').textContent = data.system_info.max_file_size;
                    }
                })
                .catch(error => console.error('Failed to load format info:', error));
        });
    </script>

    <style>
        /* Additional styles for format matrix */
        .format-matrix {
            background: white;
            border-radius: var(--border-radius-xl);
            box-shadow: var(--shadow-lg);
            padding: var(--space-2xl);
            margin-bottom: var(--space-2xl);
        }

        .format-categories {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: var(--space-xl);
            margin-top: var(--space-lg);
        }

        .format-category h3 {
            color: var(--gray-800);
            margin-bottom: var(--space-md);
            padding-bottom: var(--space-sm);
            border-bottom: 2px solid var(--primary-color);
        }

        .format-list {
            display: flex;
            flex-wrap: wrap;
            gap: var(--space-sm);
        }

        .format-badge {
            background: var(--success-color);
            color: white;
            padding: var(--space-xs) var(--space-sm);
            border-radius: var(--border-radius);
            font-size: var(--font-size-sm);
            font-weight: 600;
        }

        .format-badge.limited {
            background: var(--warning-color);
        }

        .format-badge.experimental {
            background: var(--secondary-color);
        }
    </style>
</body>
</html>