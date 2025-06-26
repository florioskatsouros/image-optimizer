/**
 *  Enhanced Image Optimizer Pro - JavaScript Application
 * With Format Converter and Extended Format Support
 */

class ImageOptimizerApp {
    constructor() {
        this.files = [];
        this.isProcessing = false;
        this.currentStep = 'upload';
        this.currentMode = 'optimize'; 
        this.supportedFormats = [];
        this.formatDetails = {};
        this.processedResults = [];
        
        // DOM Elements - Updated for our HTML structure
        this.uploadZone = document.getElementById('uploadZone');
        this.fileInput = document.getElementById('fileInput');
        this.uploadSection = document.getElementById('uploadSection');
        this.processingSection = document.getElementById('processingSection');
        this.resultsSection = document.getElementById('resultsSection');
        this.filesList = document.getElementById('filesList');
        this.resultsList = document.getElementById('resultsList');
        this.progressBar = document.getElementById('overallProgress');
        this.progressText = document.getElementById('progressText');
        
        // Mode switching elements - Updated for toggle buttons
        this.optimizePanel = document.getElementById('optimizePanel');
        this.convertPanel = document.getElementById('convertPanel');
        
        // Options - Optimize Mode
        this.qualitySlider = document.getElementById('quality');
        this.qualityValue = document.getElementById('qualityValue');
        this.maxWidthSelect = document.getElementById('maxWidth');
        this.customWidthInput = document.getElementById('customWidth');
        this.createWebpCheck = document.getElementById('createWebp');
        this.createAvifCheck = document.getElementById('createAvif');
        this.createThumbnailCheck = document.getElementById('createThumbnail');
        
        // Options - Convert Mode
        this.outputFormatSelect = document.getElementById('outputFormat');
        this.convertQualitySlider = document.getElementById('convertQuality');
        this.convertQualityValue = document.getElementById('convertQualityValue');
        this.convertToMultiple = document.getElementById('convertMultiple');
        this.convertThumbnail = document.getElementById('convertThumbnail');
        
        // Buttons
        this.downloadAllBtn = document.getElementById('downloadAllBtn');
        this.optimizeMoreBtn = document.getElementById('optimizeMoreBtn');
        this.processBtn = document.getElementById('processBtn');
        
        this.init();
    }

    async init() {
        await this.loadSupportedFormats();
        this.setupEventListeners();
        this.updateQualityDisplay();
        this.loadStatistics();
        this.setupFormatSelector();
        console.log('🎨 Enhanced Image Optimizer Pro initialized');
    }

    /**
     * Load supported formats from server
     */
    async loadSupportedFormats() {
        try {
            const response = await fetch('api/formats.php');
            const data = await response.json();
            this.supportedFormats = data.formats || [];
            this.formatDetails = data.details || {};
            this.populateFormatSelectors();
        } catch (error) {
            console.warn('Could not load format information:', error);
            // Fallback to basic formats
            this.supportedFormats = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        }
    }

    /**
     * Populate format selectors with supported formats
     */
    populateFormatSelectors() {
        if (!this.outputFormatSelect) return;

        // Clear existing options
        this.outputFormatSelect.innerHTML = '<option value="">Select output format</option>';

        // Group formats by category
        const categories = {
            'Web Formats': ['jpg', 'jpeg', 'png', 'gif', 'webp', 'avif'],
            'Print Formats': ['tiff', 'tif', 'bmp'],
            'Other Formats': ['ico']
        };

        Object.entries(categories).forEach(([categoryName, formats]) => {
            const availableFormats = formats.filter(format => 
                this.supportedFormats.includes(format)
            );

            if (availableFormats.length > 0) {
                const optgroup = document.createElement('optgroup');
                optgroup.label = categoryName;

                availableFormats.forEach(format => {
                    const option = document.createElement('option');
                    option.value = format;
                    option.textContent = this.getFormatDisplayName(format);
                    optgroup.appendChild(option);
                });

                this.outputFormatSelect.appendChild(optgroup);
            }
        });
    }

    /**
     * Get display name for format
     */
    getFormatDisplayName(format) {
        const names = {
            'jpg': 'JPEG (.jpg)',
            'jpeg': 'JPEG (.jpeg)',
            'png': 'PNG (.png)',
            'gif': 'GIF (.gif)',
            'webp': 'WebP (.webp)',
            'avif': 'AVIF (.avif)',
            'bmp': 'Bitmap (.bmp)',
            'tiff': 'TIFF (.tiff)',
            'tif': 'TIFF (.tif)',
            'ico': 'Icon (.ico)'
        };
        return names[format] || format.toUpperCase();
    }

    /**
     * Setup format selector with multiple selection
     */
    setupFormatSelector() {
        if (!this.selectedFormats) return;

        // Create format checkboxes
        const webFormats = ['jpg', 'png', 'webp', 'avif'];
        const printFormats = ['tiff', 'bmp'];

        const createFormatGroup = (title, formats) => {
            const group = document.createElement('div');
            group.className = 'format-group';
            group.innerHTML = `<h4>${title}</h4>`;

            formats.forEach(format => {
                if (this.supportedFormats.includes(format)) {
                    const label = document.createElement('label');
                    label.className = 'format-checkbox';
                    label.innerHTML = `
                        <input type="checkbox" value="${format}" data-format="${format}">
                        <span class="format-name">${this.getFormatDisplayName(format)}</span>
                        <span class="format-desc">${this.getFormatDescription(format)}</span>
                    `;
                    group.appendChild(label);
                }
            });

            return group;
        };

        this.selectedFormats.appendChild(createFormatGroup('Web Formats', webFormats));
        this.selectedFormats.appendChild(createFormatGroup('Print Formats', printFormats));

        // Add event listeners
        this.selectedFormats.addEventListener('change', (e) => {
            if (e.target.type === 'checkbox') {
                this.updateFormatPreview();
            }
        });
    }

    /**
     * Get format description
     */
    getFormatDescription(format) {
        const descriptions = {
            'jpg': 'Best for photos, smaller files',
            'png': 'Lossless, supports transparency',
            'webp': 'Modern web format, 25-35% smaller',
            'avif': 'Next-gen format, up to 50% smaller',
            'tiff': 'Print quality, uncompressed',
            'bmp': 'Windows bitmap, large files'
        };
        return descriptions[format] || '';
    }

    /**
     * Update format preview
     */
    updateFormatPreview() {
        if (!this.formatPreview) return;

        const selectedFormats = Array.from(
            this.selectedFormats.querySelectorAll('input[type="checkbox"]:checked')
        ).map(cb => cb.value);

        if (selectedFormats.length === 0) {
            this.formatPreview.innerHTML = '<p>Select formats to convert to</p>';
            return;
        }

        const preview = selectedFormats.map(format => {
            return `
                <div class="format-preview-item">
                    <span class="format-icon">${this.getFormatIcon(format)}</span>
                    <span class="format-name">${this.getFormatDisplayName(format)}</span>
                </div>
            `;
        }).join('');

        this.formatPreview.innerHTML = `
            <h4>Will convert to:</h4>
            <div class="format-preview-list">${preview}</div>
        `;
    }

    /**
     * Get format icon
     */
    getFormatIcon(format) {
        const icons = {
            'jpg': '📸',
            'png': '🖼️',
            'webp': '🌐',
            'avif': '⚡',
            'tiff': '🖨️',
            'bmp': '💾'
        };
        return icons[format] || '📄';
    }

    setupEventListeners() {
        // File input events
        this.fileInput?.addEventListener('change', (e) => this.handleFileSelect(e));
        
        // Drag and drop events - Updated to work with our upload zone structure
        this.uploadZone?.addEventListener('click', (e) => {
            // Only trigger file input if clicking on upload zone, not on buttons/thumbnails
            if (e.target === this.uploadZone || (this.uploadZone.contains(e.target) && !e.target.closest('button') && !e.target.closest('.file-item'))) {
                this.fileInput?.click();
            }
        });
        this.uploadZone?.addEventListener('dragover', (e) => this.handleDragOver(e));
        this.uploadZone?.addEventListener('dragleave', (e) => this.handleDragLeave(e));
        this.uploadZone?.addEventListener('drop', (e) => this.handleDrop(e));
        
        // Prevent default drag behaviors on document
        document.addEventListener('dragover', (e) => e.preventDefault());
        document.addEventListener('drop', (e) => e.preventDefault());
        
        // Mode toggle - Connect to existing global functions
        // Note: Mode switching is handled by global setMode() and toggleMode() functions
        // We'll create a bridge to connect them to our class
        window.appSetMode = (mode) => {
            this.currentMode = mode;
            this.handleModeChange();
        };
        
        window.appToggleMode = () => {
            this.currentMode = this.currentMode === 'optimize' ? 'convert' : 'optimize';
            this.handleModeChange();
        };
        
        // Quality sliders - Support both optimize and convert mode sliders
        this.qualitySlider?.addEventListener('input', () => this.updateQualityDisplay());
        this.convertQualitySlider?.addEventListener('input', () => this.updateConvertQualityDisplay());
        
        // Max width select
        this.maxWidthSelect?.addEventListener('change', () => this.handleMaxWidthChange());
        
        // Output format change
        this.outputFormatSelect?.addEventListener('change', () => this.handleOutputFormatChange());
        
        // Multiple format toggle
        this.convertToMultiple?.addEventListener('change', (e) => this.handleMultipleFormatToggle(e));
        
        // Buttons
        this.downloadAllBtn?.addEventListener('click', () => this.downloadAll());
        this.optimizeMoreBtn?.addEventListener('click', () => this.resetToUpload());
        
        // Process button - connect to global function
        window.appStartProcessing = () => this.startProcessing();
        
        // Keyboard shortcuts
        document.addEventListener('keydown', (e) => this.handleKeyboard(e));
    }

    /**
     * Update convert quality display
     */
    updateConvertQualityDisplay() {
        if (!this.convertQualitySlider || !this.convertQualityValue) return;
        
        const value = this.convertQualitySlider.value;
        this.convertQualityValue.textContent = `${value}%`;
    }

    /**
     * Handle mode change between optimize and convert
     */
    handleModeChange(e) {
        // Update based on the toggle buttons, not checkbox
        // This gets called by setMode() and toggleMode() functions
        
        // Update UI panels
        if (this.optimizePanel && this.convertPanel) {
            if (this.currentMode === 'convert') {
                this.optimizePanel.style.display = 'none';
                this.optimizePanel.classList.add('hidden');
                this.convertPanel.style.display = 'block';
                this.convertPanel.classList.add('active');
                this.updateUploadZoneText('converter');
            } else {
                this.optimizePanel.style.display = 'block';
                this.optimizePanel.classList.remove('hidden');
                this.convertPanel.style.display = 'none';
                this.convertPanel.classList.remove('active');
                this.updateUploadZoneText('optimizer');
            }
        }

        // Update process button text
        this.updateProcessButton();
        
        // Clear any existing results and files when mode changes
        this.clearResults();
    }

    /**
    * Update process button based on current mode
    */
    updateProcessButton() {
        const processBtn = document.getElementById('processBtn');
        const processBtnIcon = document.getElementById('processBtnIcon');
        const processBtnText = document.getElementById('processBtnText');
        
        if (processBtn) {
            if (this.currentMode === 'convert') {
                if (processBtnIcon) processBtnIcon.textContent = '🔄';
                if (processBtnText) processBtnText.textContent = 'Convert Images';
            } else {
                if (processBtnIcon) processBtnIcon.textContent = '⚡';
                if (processBtnText) processBtnText.textContent = 'Optimize Images';
            }
        }
    }

    /**
     * Clear results and files when mode changes
     */
    clearResults() {
        // Clear files
        this.files = [];
        this.processedResults = [];
        this.isProcessing = false;
        
        // Clear file input
        if (this.fileInput) {
            this.fileInput.value = '';
        }
        
        // Remove file preview
        const previewSection = document.getElementById('filePreview');
        if (previewSection) {
            previewSection.remove();
        }
        
        // Remove results section
        const resultsSection = document.getElementById('resultsSection');
        if (resultsSection) {
            resultsSection.remove();
        }
        
        // Reset upload zone
        const uploadZone = document.getElementById('uploadZone');
        if (uploadZone) {
            uploadZone.classList.remove('has-files');
        }
        
        // Clear files grid
        const filesGrid = document.getElementById('filesGrid');
        if (filesGrid) {
            filesGrid.innerHTML = '';
        }
    }

    /**
     * Update upload zone text based on mode
     */
    updateUploadZoneText(mode) {
        const titleEl = this.uploadZone?.querySelector('h2');
        const descEl = this.uploadZone?.querySelector('p');
        
        if (mode === 'converter') {
            if (titleEl) titleEl.textContent = 'Drop images to convert';
            if (descEl) descEl.innerHTML = 'or <span class="browse-text">click to browse</span>';
        } else {
            if (titleEl) titleEl.textContent = 'Drop your images here';
            if (descEl) descEl.innerHTML = 'or <span class="browse-text">click to browse</span>';
        }
    }

    /**
     * Handle output format change
     */
    handleOutputFormatChange() {
        const selectedFormat = this.outputFormatSelect?.value;
        
        if (selectedFormat && this.formatPreview) {
            this.formatPreview.innerHTML = `
                <div class="format-preview-single">
                    <span class="format-icon">${this.getFormatIcon(selectedFormat)}</span>
                    <span class="format-name">${this.getFormatDisplayName(selectedFormat)}</span>
                    <span class="format-desc">${this.getFormatDescription(selectedFormat)}</span>
                </div>
            `;
        }
    }

    /**
     * Handle multiple format toggle
     */
    handleMultipleFormatToggle(e) {
        if (!this.outputFormatSelect || !this.selectedFormats) return;

        if (e.target.checked) {
            // Switch to multiple format selection
            this.outputFormatSelect.style.display = 'none';
            this.selectedFormats.style.display = 'block';
            this.updateFormatPreview();
        } else {
            // Switch to single format selection
            this.outputFormatSelect.style.display = 'block';
            this.selectedFormats.style.display = 'none';
            this.handleOutputFormatChange();
        }
    }

    /**
     * Enhanced file processing with mode support
     */
    processSelectedFiles(files) {
        if (this.isProcessing) return;
        
        // Filter image files
        const imageFiles = files.filter(file => {
            const validTypes = [
                'image/jpeg', 'image/jpg', 'image/png', 'image/gif', 
                'image/webp', 'image/bmp', 'image/tiff', 'image/svg+xml',
                'image/heic', 'image/heif', 'image/avif'
            ];
            
            const extension = file.name.split('.').pop().toLowerCase();
            const validExtensions = [
                'jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'tiff', 'tif',
                'svg', 'heic', 'heif', 'avif', 'psd', 'raw', 'cr2', 'nef',
                'orf', 'arw', 'dng', 'ico'
            ];
            
            return validTypes.includes(file.type) || validExtensions.includes(extension);
        });

        if (imageFiles.length === 0) {
            this.showNotification('Please select valid image files', 'error');
            return;
        }

        if (imageFiles.length !== files.length) {
            this.showNotification(`${files.length - imageFiles.length} non-image files were ignored`, 'warning');
        }

        // Add to existing files instead of replacing
        this.files = [...this.files, ...imageFiles];
        this.displayFilePreview();
    }

    /**
     * Enhanced file validation
     */
    validateFiles(files) {
        const validFiles = [];
        
        files.forEach(file => {
            const extension = file.name.split('.').pop().toLowerCase();
            
            if (this.supportedFormats.includes(extension)) {
                // Check file size (100MB limit for advanced formats)
                if (file.size <= 100 * 1024 * 1024) {
                    validFiles.push(file);
                } else {
                    this.showNotification(`${file.name} is too large (max 100MB)`, 'warning');
                }
            }
        });
        
        return validFiles;
    }

    /**
     * Display file preview with format info
     */
    displayFilePreview() {
        if (!this.files.length) return;

        const uploadZone = document.getElementById('uploadZone');
        const filesGrid = document.getElementById('filesGrid');
        const filesCount = document.getElementById('filesCount');

        if (!uploadZone || !filesGrid || !filesCount) return;

        // Update upload zone state
        uploadZone.classList.add('has-files');
        
        // Update files count
        filesCount.textContent = `${this.files.length} file${this.files.length !== 1 ? 's' : ''} selected`;

        // Create file items using existing structure
        const fileItems = this.files.map((file, index) => {
            const extension = file.name.split('.').pop().toLowerCase();
            const sizeFormatted = this.formatBytes(file.size);
            const thumbnailUrl = URL.createObjectURL(file);
            
            return `
                <div class="file-item fade-in-up" style="animation-delay: ${index * 0.05}s">
                    <div class="file-thumbnail">
                        <img src="${thumbnailUrl}" alt="${this.escapeHtml(file.name)}" class="thumbnail-image" 
                            onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        <div class="file-icon-fallback" style="display: none;">${this.getFormatIcon(extension)}</div>
                        
                        <!-- Enhanced Progress Overlay -->
                        <div class="upload-progress-overlay" id="progress-${index}">
                            <div class="upload-progress-circle">
                                <svg class="progress-ring" viewBox="0 0 60 60">
                                    <circle class="progress-ring-circle" cx="30" cy="30" r="26" 
                                            fill="transparent" stroke="white" stroke-width="4" 
                                            stroke-dasharray="163.36" stroke-dashoffset="163.36"/>
                                </svg>
                                <span class="progress-percentage">0%</span>
                            </div>
                        </div>
                    </div>
                    <div class="file-info">
                        <div class="file-name" title="${this.escapeHtml(file.name)}">${this.escapeHtml(file.name)}</div>
                        <div class="file-size">${sizeFormatted}</div>
                    </div>
                    <button class="remove-file-btn" onclick="optimizerApp.removeFile(${index})" title="Remove file">×</button>
                </div>
            `;
        }).join('');

        filesGrid.innerHTML = fileItems;
    }

    /**
     * Remove file from preview
     */
    removeFile(index) {
        this.files.splice(index, 1);
        if (this.files.length > 0) {
            this.displayFilePreview();
        } else {
            this.clearFiles();
        }
    }

    /**
     * Clear all files
     */
    clearFiles() {
        // Clean up object URLs to prevent memory leaks
        this.files.forEach((file, index) => {
            const thumbnailImage = document.querySelector(`#progress-${index}`)?.parentElement?.querySelector('.thumbnail-image');
            if (thumbnailImage && thumbnailImage.src.startsWith('blob:')) {
                URL.revokeObjectURL(thumbnailImage.src);
            }
        });
        
        this.files = [];
        
        const uploadZone = document.getElementById('uploadZone');
        const filesGrid = document.getElementById('filesGrid');
        const filesCount = document.getElementById('filesCount');
        
        if (uploadZone) uploadZone.classList.remove('has-files');
        if (filesGrid) filesGrid.innerHTML = '';
        if (filesCount) filesCount.textContent = '0 files selected';
        
        if (this.fileInput) {
            this.fileInput.value = '';
        }
    }

    /**
     * Enhanced processing with mode support
     */
    async startProcessing() {
        if (this.files.length === 0) {
            this.showNotification('Please select files first', 'warning');
            return;
        }

        // Validate options based on mode
        const options = this.getProcessingOptions();
        if (!options) {
            return; // Error already shown
        }

        this.isProcessing = true;
        this.showProcessingView();
        this.displayFilesForProcessing();
        
        try {
            const formData = new FormData();
            
            // Add files
            this.files.forEach(file => {
                formData.append('images[]', file);
            });
            
            // Add mode and options
            formData.append('mode', this.currentMode);
            Object.entries(options).forEach(([key, value]) => {
                if (Array.isArray(value)) {
                    formData.append(key, JSON.stringify(value));
                } else {
                    formData.append(key, value);
                }
            });
            
            // Start upload with progress tracking
            const result = await this.uploadWithProgress(formData);
            
            if (result.success) {
                this.showResults(result);
                this.updateStatistics(result);
            } else {
                throw new Error(result.error || 'Processing failed');
            }
            
        } catch (error) {
            console.error('Processing error:', error);
            this.showNotification(`Processing failed: ${error.message}`, 'error');
            this.resetToUpload();
        } finally {
            this.isProcessing = false;
        }
    }

    /**
     * Get processing options based on current mode
     */
    getProcessingOptions() {
        if (this.currentMode === 'optimize') {
            return {
                quality: this.qualitySlider?.value || 80,
                max_width: this.getMaxWidth(),
                create_webp: this.createWebpCheck?.checked || false,
                create_avif: this.createAvifCheck?.checked || false,
                create_thumbnail: this.createThumbnailCheck?.checked || false
            };
        } else if (this.currentMode === 'convert') {
            const convertToMultiple = this.convertToMultiple?.checked || false;
            
            if (convertToMultiple) {
                // Multiple format conversion
                const selectedFormats = Array.from(
                    this.selectedFormats?.querySelectorAll('input[type="checkbox"]:checked') || []
                ).map(cb => cb.value);
                
                if (selectedFormats.length === 0) {
                    this.showNotification('Please select at least one output format', 'warning');
                    return null;
                }
                
                return {
                    convert_to: selectedFormats,
                    quality: this.qualitySlider?.value || 80
                };
            } else {
                // Single format conversion
                const outputFormat = this.outputFormatSelect?.value;
                
                if (!outputFormat) {
                    this.showNotification('Please select an output format', 'warning');
                    return null;
                }
                
                return {
                    output_format: outputFormat,
                    quality: this.qualitySlider?.value || 80
                };
            }
        }
        
        return {};
    }

    /**
     * Upload files with progress tracking
     */
    async uploadWithProgress(formData) {
        return new Promise((resolve, reject) => {
            const xhr = new XMLHttpRequest();
            
            // Track upload progress
            xhr.upload.addEventListener('progress', (e) => {
                if (e.lengthComputable) {
                    const percentComplete = (e.loaded / e.total) * 50; // Upload is 50% of total
                    this.updateProgress(percentComplete, 'Uploading files...');
                }
            });
            
            // Handle response
            xhr.addEventListener('load', () => {
                if (xhr.status === 200) {
                    try {
                        const result = JSON.parse(xhr.responseText);
                        this.updateProgress(100, 'Processing complete!');
                        resolve(result);
                    } catch (error) {
                        reject(new Error('Failed to parse server response'));
                    }
                } else {
                    reject(new Error(`Upload failed with status ${xhr.status}`));
                }
            });
            
            // Handle errors
            xhr.addEventListener('error', () => {
                reject(new Error('Network error occurred'));
            });
            
            // Handle timeout
            xhr.addEventListener('timeout', () => {
                reject(new Error('Upload timed out'));
            });
            
            // Configure and send request
            xhr.timeout = 300000; // 5 minutes
            xhr.open('POST', 'api/process.php');
            xhr.send(formData);
            
            // Start processing simulation
            this.simulateProcessingProgress();
        });
    }

    /**
     * Show processing view
     */
    showProcessingView() {
        if (this.uploadSection) this.uploadSection.style.display = 'none';
        if (this.resultsSection) this.resultsSection.style.display = 'none';
        if (this.processingSection) this.processingSection.style.display = 'block';
        
        this.currentStep = 'processing';
        this.updateProgress(0, 'Preparing files...');
    }

    /**
     * Display files for processing
     */
    displayFilesForProcessing() {
        if (!this.filesList) return;
        
        const fileItems = this.files.map((file, index) => {
            const extension = file.name.split('.').pop().toLowerCase();
            return `
                <div class="file-item" data-index="${index}">
                    <div class="file-icon">${this.getFormatIcon(extension)}</div>
                    <div class="file-info">
                        <div class="file-name">${this.escapeHtml(file.name)}</div>
                        <div class="file-size">${this.formatBytes(file.size)}</div>
                    </div>
                    <div class="file-progress">
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: 0%"></div>
                        </div>
                    </div>
                    <div class="file-status status-processing">
                        <span>⏳</span>
                        <span>Waiting...</span>
                    </div>
                </div>
            `;
        }).join('');
        
        this.filesList.innerHTML = fileItems;
    }

    /**
     * Update progress bar and text
     */
    updateProgress(percent, message) {
        if (this.progressBar) {
            this.progressBar.style.width = `${Math.min(100, Math.max(0, percent))}%`;
        }
        
        if (this.progressText) {
            this.progressText.textContent = `${Math.round(percent)}%`;
        }
        
        // Update progress text message
        const progressTextEl = document.querySelector('.processing-header h2');
        if (progressTextEl && message) {
            progressTextEl.textContent = message;
        }
        
        // Update conversion progress steps
        this.updateConversionSteps(percent);
    }

    /**
     * Update conversion progress steps
     */
    updateConversionSteps(percent) {
        const steps = document.querySelectorAll('.conversion-step');
        if (!steps.length) return;
        
        steps.forEach((step, index) => {
            step.classList.remove('active', 'completed');
            
            const stepPercent = (index + 1) * 25; // 4 steps, so 25% each
            
            if (percent >= stepPercent) {
                step.classList.add('completed');
            } else if (percent >= stepPercent - 25) {
                step.classList.add('active');
            }
        });
    }

    /**
     * Simulate processing progress for better UX
     */
    simulateProcessingProgress() {
        let progress = 50; // Start from 50% (after upload)
        const mode = this.currentMode;
        const message = mode === 'convert' ? 'Converting images...' : 'Optimizing images...';
        
        const interval = setInterval(() => {
            progress += Math.random() * 10;
            if (progress >= 95) {
                progress = 95;
                clearInterval(interval);
            }
            this.updateProgress(progress, message);
        }, 300);
    }

    /**
     * Show results section
     */
    showResults(result) {
        // Create results section using existing styles
        let resultsSection = document.getElementById('resultsSection');
        if (!resultsSection) {
            resultsSection = document.createElement('div');
            resultsSection.id = 'resultsSection';
            resultsSection.className = 'results-section';
            document.querySelector('.main-content').appendChild(resultsSection);
        }

        this.currentStep = 'results';
        this.processedResults = result;
        
        // Use the existing results display format
        const isSuccess = result.success && result.data;
        const fileCount = result.type === 'batch' ? result.data.summary.successful : 1;
        const downloadLinks = result.data.download_links || [];

        resultsSection.innerHTML = `
            <div style="padding: var(--space-2xl); text-align: center;">
                <h2 style="font-size: var(--font-size-3xl); color: var(--gray-800); margin-bottom: var(--space-lg); display: flex; align-items: center; justify-content: center; gap: var(--space-sm);">
                    🎉 ${this.currentMode === 'convert' ? 'Conversion' : 'Optimization'} Complete!
                </h2>
                
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: var(--space-lg); margin-bottom: var(--space-xl); max-width: 600px; margin-left: auto; margin-right: auto;">
                    <div style="text-align: center; padding: var(--space-lg); background: var(--gray-50); border-radius: var(--border-radius-lg); border: 1px solid var(--gray-200);">
                        <div style="font-size: var(--font-size-2xl); font-weight: 700; color: var(--primary-color); margin-bottom: var(--space-xs);">
                            ${fileCount}
                        </div>
                        <div style="font-size: var(--font-size-sm); color: var(--gray-600);">
                            Files ${this.currentMode === 'convert' ? 'Converted' : 'Optimized'}
                        </div>
                    </div>
                    <div style="text-align: center; padding: var(--space-lg); background: var(--gray-50); border-radius: var(--border-radius-lg); border: 1px solid var(--gray-200);">
                        <div style="font-size: var(--font-size-2xl); font-weight: 700; color: var(--success-color); margin-bottom: var(--space-xs);">
                            ${downloadLinks.length}
                        </div>
                        <div style="font-size: var(--font-size-sm); color: var(--gray-600);">
                            Files Created
                        </div>
                    </div>
                </div>

                <div style="display: flex; gap: var(--space-md); justify-content: center; margin-bottom: var(--space-xl); flex-wrap: wrap;">
                    ${downloadLinks.length > 1 ? `
                        <button class="btn btn-primary" onclick="optimizerApp.downloadAll()">
                            📦 Download All Files
                        </button>
                    ` : ''}
                    <button class="btn btn-secondary" onclick="optimizerApp.processMore()">
                        🔄 Process More Images
                    </button>
                </div>

                <div style="display: grid; gap: var(--space-md); max-width: 800px; margin: 0 auto;">
                    ${downloadLinks.map(link => `
                        <div style="display: flex; align-items: center; justify-content: space-between; background: white; padding: var(--space-md); border-radius: var(--border-radius); border: 1px solid var(--gray-200); box-shadow: var(--shadow-sm);">
                            <div style="display: flex; align-items: center; gap: var(--space-md);">
                                <div style="font-size: var(--font-size-lg);">
                                    ${this.getFormatIcon(link.format)}
                                </div>
                                <div>
                                    <div style="font-weight: 600; color: var(--gray-800); margin-bottom: var(--space-xs);">
                                        ${link.format.toUpperCase()} Format
                                    </div>
                                    <div style="font-size: var(--font-size-sm); color: var(--gray-600);">
                                        ${link.size}${link.savings !== 'thumbnail' ? ` • ${link.savings}% smaller` : ' • Thumbnail'}
                                    </div>
                                </div>
                            </div>
                            <a href="${link.url}" class="btn btn-primary" download style="text-decoration: none;">
                                📥 Download
                            </a>
                        </div>
                    `).join('')}
                </div>
            </div>
        `;

        // Scroll to results
        resultsSection.scrollIntoView({ behavior: 'smooth' });
    }

    /**
     * Process More Images - Connect to existing functionality
     */
    processMore() {
        // Reset the application completely
        this.clearFiles();
        const resultsSection = document.getElementById('resultsSection');
        if (resultsSection) {
            resultsSection.remove();
        }
        
        // Hide any progress overlays
        this.hideUploadProgress();
        
        // Reset processing button
        this.hideProcessing();
        
        // Scroll to upload section
        document.getElementById('uploadSection').scrollIntoView({ behavior: 'smooth' });
    }

    /**
     * Display results summary
     */
    displayResultsSummary(result) {
        const summaryEl = document.getElementById('resultsSummary');
        if (!summaryEl) return;
        
        let summary;
        if (result.type === 'batch') {
            summary = result.data.summary;
        } else {
            const original = result.data.original;
            const optimized = result.data.optimized[0];
            summary = {
                total_files: 1,
                successful: 1,
                total_original_size_human: this.formatBytes(original.size),
                total_optimized_size_human: optimized.size_human,
                total_savings: result.data.savings + '%',
                conversions_created: result.data.conversions_created || 1
            };
        }
        
        const modeText = this.currentMode === 'convert' ? 'Converted' : 'Optimized';
        
        summaryEl.innerHTML = `
            <div class="summary-item">
                <span class="summary-number">${summary.total_files || summary.successful}</span>
                <span class="summary-label">Images ${modeText}</span>
            </div>
            <div class="summary-item">
                <span class="summary-number">${summary.conversions_created || summary.total_files}</span>
                <span class="summary-label">Files Created</span>
            </div>
            <div class="summary-item">
                <span class="summary-number">${summary.total_original_size_human}</span>
                <span class="summary-label">Original Size</span>
            </div>
            <div class="summary-item">
                <span class="summary-number">${summary.total_optimized_size_human}</span>
                <span class="summary-label">${modeText} Size</span>
            </div>
            ${this.currentMode === 'optimize' ? `
                <div class="summary-item">
                    <span class="summary-number">${summary.total_savings}</span>
                    <span class="summary-label">Space Saved</span>
                </div>
            ` : ''}
        `;
    }

    /**
     * Display results list
     */
    displayResultsList(result) {
        const resultsListEl = document.getElementById('resultsList');
        if (!resultsListEl) return;
        
        let results = [];
        if (result.type === 'batch') {
            results = result.data.results;
        } else {
            results = [result.data];
        }
        
        const resultItems = results.map((item, index) => {
            if (!item.success) {
                return `
                    <div class="result-item error">
                        <div class="result-header">
                            <span class="result-filename">❌ ${item.original.name}</span>
                            <span class="result-error">Failed</span>
                        </div>
                        <div class="error-details">
                            ${item.errors ? item.errors.join(', ') : 'Unknown error'}
                        </div>
                    </div>
                `;
            }
            
            const downloads = item.download_links || [];
            const downloadButtons = downloads.map(download => `
                <a href="${download.url}" class="btn btn-sm btn-success" download>
                    ${download.format.toUpperCase()} (${download.size})
                    ${download.savings !== 'thumbnail' ? ` - ${download.savings}%` : ''}
                </a>
            `).join('');
            
            return `
                <div class="result-item">
                    <div class="result-header">
                        <span class="result-filename">✅ ${item.original.name}</span>
                        <span class="result-savings">${item.savings}% saved</span>
                    </div>
                    <div class="result-comparison">
                        <div class="comparison-item">
                            <div class="comparison-label">Original</div>
                            <div class="comparison-value">${item.original.size_human}</div>
                        </div>
                        <div class="comparison-arrow">→</div>
                        <div class="comparison-item">
                            <div class="comparison-label">${this.currentMode === 'convert' ? 'Converted' : 'Optimized'}</div>
                            <div class="comparison-value">${downloads[0]?.size || 'N/A'}</div>
                        </div>
                    </div>
                    <div class="result-downloads">
                        ${downloadButtons}
                    </div>
                </div>
            `;
        }).join('');
        
        resultsListEl.innerHTML = resultItems;
    }

    /**
     * Download all files as ZIP
     */
    downloadAll() {
        if (!this.processedResults) return;
        
        let downloadLinks = [];
        if (this.processedResults.type === 'batch') {
            downloadLinks = this.processedResults.data.download_links || [];
        } else {
            downloadLinks = this.processedResults.data.download_links || [];
        }
        
        if (downloadLinks.length === 0) {
            this.showNotification('No files available for download', 'warning');
            return;
        }
        
        if (downloadLinks.length === 1) {
            // Single file - direct download
            window.open(downloadLinks[0].url, '_blank');
        } else {
            // Multiple files - batch download
            const filenames = downloadLinks.map(link => link.filename);
            const batchUrl = `download.php?batch=true&files=${encodeURIComponent(JSON.stringify(filenames))}`;
            window.open(batchUrl, '_blank');
        }
    }

    /**
     * Reset to upload view
     */
    resetToUpload() {
        this.files = [];
        this.processedResults = [];
        this.isProcessing = false;
        this.currentStep = 'upload';
        
        // Clear file input
        if (this.fileInput) {
            this.fileInput.value = '';
        }
        
        // Remove file preview
        const previewSection = document.getElementById('filePreview');
        if (previewSection) {
            previewSection.remove();
        }
        
        // Show upload section, hide others
        if (this.uploadSection) this.uploadSection.style.display = 'block';
        if (this.processingSection) this.processingSection.style.display = 'none';
        if (this.resultsSection) this.resultsSection.style.display = 'none';
        
        // Reset progress
        this.updateProgress(0, 'Ready to process images');
    }

    /**
     * Show notification to user
     */
    showNotification(message, type = 'info') {
        // Remove existing notifications
        const existingNotifications = document.querySelectorAll('.notification');
        existingNotifications.forEach(notification => notification.remove());
        
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.innerHTML = `
            <div class="notification-content">
                <span class="notification-icon">${this.getNotificationIcon(type)}</span>
                <span class="notification-message">${message}</span>
                <button class="notification-close" onclick="this.parentElement.parentElement.remove()">×</button>
            </div>
        `;
        
        // Add to page
        document.body.appendChild(notification);
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            if (notification.parentElement) {
                notification.remove();
            }
        }, 5000);
        
        console.log(`[${type.toUpperCase()}] ${message}`);
    }

    /**
     * Get notification icon
     */
    getNotificationIcon(type) {
        const icons = {
            'info': 'ℹ️',
            'success': '✅',
            'warning': '⚠️',
            'error': '❌'
        };
        return icons[type] || 'ℹ️';
    }

    /**
     * Load and display statistics
     */
    async loadStatistics() {
        try {
            // This would load from a stats endpoint
            const stats = this.getLocalStats();
            this.displayStatistics(stats);
        } catch (error) {
            console.warn('Could not load statistics:', error);
        }
    }

    /**
     * Get local statistics from localStorage
     */
    getLocalStats() {
        try {
            const stats = localStorage.getItem('optimizer_stats');
            return stats ? JSON.parse(stats) : {
                totalProcessed: 0,
                totalSaved: '0GB',
                formatsSupported: this.supportedFormats.length
            };
        } catch (error) {
            return {
                totalProcessed: 0,
                totalSaved: '0GB',
                formatsSupported: this.supportedFormats.length
            };
        }
    }

    /**
     * Display statistics in header
     */
    displayStatistics(stats) {
        const totalProcessedEl = document.getElementById('totalProcessed');
        const totalSavedEl = document.getElementById('totalSaved');
        const formatsSupportedEl = document.getElementById('formatsSupported');
        
        if (totalProcessedEl) totalProcessedEl.textContent = stats.totalProcessed;
        if (totalSavedEl) totalSavedEl.textContent = stats.totalSaved;
        if (formatsSupportedEl) formatsSupportedEl.textContent = stats.formatsSupported || this.supportedFormats.length;
    }

    /**
     * Update statistics after processing
     */
    updateStatistics(result) {
        try {
            const stats = this.getLocalStats();
            
            if (result.type === 'batch') {
                stats.totalProcessed += result.data.summary.successful;
                // Add saved space calculation if available
            } else {
                stats.totalProcessed += 1;
            }
            
            localStorage.setItem('optimizer_stats', JSON.stringify(stats));
            this.displayStatistics(stats);
        } catch (error) {
            console.warn('Could not update statistics:', error);
        }
    }

    /**
     * Handle drag over event
     */
    handleDragOver(e) {
        e.preventDefault();
        e.stopPropagation();
        this.uploadZone?.classList.add('drag-over');
    }

    /**
     * Handle drag leave event
     */
    handleDragLeave(e) {
        e.preventDefault();
        e.stopPropagation();
        if (!this.uploadZone?.contains(e.relatedTarget)) {
            this.uploadZone?.classList.remove('drag-over');
        }
    }

    /**
     * Handle drop event
     */
    handleDrop(e) {
        e.preventDefault();
        e.stopPropagation();
        this.uploadZone?.classList.remove('drag-over');
        
        const files = Array.from(e.dataTransfer.files);
        this.processSelectedFiles(files);
    }

    /**
     * Handle file select event
     */
    handleFileSelect(e) {
        const files = Array.from(e.target.files);
        this.processSelectedFiles(files);
    }

    /**
     * Update quality display
     */
    updateQualityDisplay() {
        if (!this.qualitySlider || !this.qualityValue) return;
        
        const value = this.qualitySlider.value;
        this.qualityValue.textContent = `${value}%`;
        
        // Update quality label
        let label = 'Custom';
        if (value >= 90) label = 'Maximum';
        else if (value >= 80) label = 'High';
        else if (value >= 65) label = 'Good';
        else if (value >= 50) label = 'Web';
        else label = 'Small';
        
        // Update quality description if element exists
        const qualityDesc = document.querySelector('.quality-description');
        if (qualityDesc) {
            qualityDesc.textContent = label;
        }
    }

    /**
     * Handle max width change
     */
    handleMaxWidthChange() {
        if (!this.maxWidthSelect || !this.customWidthInput) return;
        
        const value = this.maxWidthSelect.value;
        
        if (value === 'custom') {
            this.customWidthInput.style.display = 'block';
            this.customWidthInput.focus();
        } else {
            this.customWidthInput.style.display = 'none';
            this.customWidthInput.value = '';
        }
    }

    /**
     * Get max width value
     */
    getMaxWidth() {
        if (!this.maxWidthSelect) return null;
        
        const selectValue = this.maxWidthSelect.value;
        if (selectValue === 'custom') {
            return this.customWidthInput?.value ? parseInt(this.customWidthInput.value) : null;
        }
        return selectValue ? parseInt(selectValue) : null;
    }

    /**
     * Handle keyboard shortcuts
     */
    handleKeyboard(e) {
        // Ctrl/Cmd + U: Upload files
        if ((e.ctrlKey || e.metaKey) && e.key === 'u') {
            e.preventDefault();
            this.fileInput?.click();
        }
        
        // Escape: Reset to upload
        if (e.key === 'Escape') {
            if (this.currentStep === 'results') {
                this.resetToUpload();
            }
        }
        
        // Enter: Start processing (if files selected)
        if (e.key === 'Enter') {
            if (this.files.length > 0 && !this.isProcessing) {
                this.startProcessing();
            }
        }
    }

    /**
     * Format bytes to human readable format
     */
    formatBytes(bytes, decimals = 1) {
        if (bytes === 0) return '0 B';
        
        const k = 1024;
        const sizes = ['B', 'KB', 'MB', 'GB', 'TB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        
        return parseFloat((bytes / Math.pow(k, i)).toFixed(decimals)) + ' ' + sizes[i];
    }

    /**
     * Escape HTML to prevent XSS
     */
    escapeHtml(text) {
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.replace(/[&<>"']/g, (m) => map[m]);
    }

    /**
     * Check if browser supports a feature
     */
    supportsFeature(feature) {
        switch (feature) {
            case 'drag-drop':
                return 'draggable' in document.createElement('div');
            case 'file-api':
                return window.File && window.FileReader && window.FileList && window.Blob;
            case 'xhr2':
                return 'upload' in new XMLHttpRequest();
            default:
                return false;
        }
    }

    /**
     * Initialize feature detection
     */
    detectFeatures() {
        const features = {
            dragDrop: this.supportsFeature('drag-drop'),
            fileApi: this.supportsFeature('file-api'),
            xhr2: this.supportsFeature('xhr2')
        };
        
        console.log('Browser features:', features);
        
        if (!features.fileApi) {
            this.showNotification('Your browser does not support the File API. Please use a modern browser.', 'warning');
        }
        
        return features;
    }

    /**
     * Handle application errors
     */
    handleError(error, context = 'general') {
        console.error(`[${context}] Error:`, error);
        
        let userMessage = 'An unexpected error occurred. Please try again.';
        
        switch (context) {
            case 'upload':
                userMessage = 'Failed to upload files. Please check your connection and try again.';
                break;
            case 'processing':
                userMessage = 'Failed to process images. Please try with different files.';
                break;
            case 'network':
                userMessage = 'Network error. Please check your internet connection.';
                break;
        }
        
        this.showNotification(userMessage, 'error');
        
        // Reset state if in processing
        if (this.isProcessing) {
            this.isProcessing = false;
            this.resetToUpload();
        }
    }

    /**
     * Debug function for development
     */
    debug() {
        return {
            files: this.files,
            currentMode: this.currentMode,
            currentStep: this.currentStep,
            supportedFormats: this.supportedFormats,
            isProcessing: this.isProcessing,
            processedResults: this.processedResults
        };
    }
}

// Initialize the enhanced application
document.addEventListener('DOMContentLoaded', () => {
    window.optimizerApp = new ImageOptimizerApp();
    
    // Override existing global functions to work with the app
    window.originalSetMode = window.setMode;
    window.originalToggleMode = window.toggleMode;
    window.originalStartProcessing = window.startProcessing;
    window.originalClearFiles = window.clearFiles;
    window.originalProcessMore = window.processMore;
    
    // Bridge mode switching
    window.setMode = function(mode) {
        window.originalSetMode(mode);
        if (window.optimizerApp) {
            window.optimizerApp.currentMode = mode;
            window.optimizerApp.handleModeChange();
        }
    };
    
    window.toggleMode = function() {
        window.originalToggleMode();
        if (window.optimizerApp) {
            window.optimizerApp.currentMode = window.currentMode;
            window.optimizerApp.handleModeChange();
        }
    };
    
    // Bridge processing
    window.startProcessing = function() {
        if (window.optimizerApp && window.optimizerApp.files.length > 0) {
            window.optimizerApp.startProcessing();
        } else {
            window.originalStartProcessing();
        }
    };
    
    // Bridge file clearing
    window.clearFiles = function() {
        window.originalClearFiles();
        if (window.optimizerApp) {
            window.optimizerApp.clearFiles();
        }
    };
    
    // Bridge process more
    window.processMore = function() {
        if (window.optimizerApp) {
            window.optimizerApp.processMore();
        } else {
            window.originalProcessMore();
        }
    };
    
    // Global error handlers
    window.addEventListener('error', (e) => {
        console.error('Global error:', e.error);
        if (window.optimizerApp) {
            window.optimizerApp.handleError(e.error, 'global');
        }
    });
    
    window.addEventListener('unhandledrejection', (e) => {
        console.error('Unhandled promise rejection:', e.reason);
        if (window.optimizerApp) {
            window.optimizerApp.handleError(e.reason, 'promise');
        }
    });
});

// Add helper methods for progress tracking
ImageOptimizerApp.prototype.hideUploadProgress = function() {
    this.files.forEach((file, index) => {
        const progressOverlay = document.getElementById(`progress-${index}`);
        if (progressOverlay) {
            progressOverlay.style.display = 'none';
            
            // Reset progress circle
            const circle = progressOverlay.querySelector('.progress-ring-circle');
            const percentageText = progressOverlay.querySelector('.progress-percentage');
            
            if (circle && percentageText) {
                circle.style.strokeDashoffset = 163.36; // Reset to 0%
                percentageText.textContent = '0%';
            }
        }
    });
};

ImageOptimizerApp.prototype.hideProcessing = function() {
    const processBtn = document.getElementById('processBtn');
    if (processBtn) {
        processBtn.disabled = false;
        
        const iconText = this.currentMode === 'convert' ? '🔄' : '⚡';
        const buttonText = this.currentMode === 'convert' ? 'Convert Images' : 'Optimize Images';
        
        processBtn.innerHTML = `
            <span id="processBtnIcon">${iconText}</span>
            <span id="processBtnText">${buttonText}</span>
        `;
    }
};

// Add CSS for notifications
const notificationStyles = `
.notification {
    position: fixed;
    top: 20px;
    right: 20px;
    background: white;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    z-index: 1000;
    animation: slideInRight 0.3s ease-out;
    max-width: 400px;
}

.notification-content {
    display: flex;
    align-items: center;
    padding: 16px;
    gap: 12px;
}

.notification-success {
    border-left: 4px solid #4ade80;
}

.notification-warning {
    border-left: 4px solid #fbbf24;
}

.notification-error {
    border-left: 4px solid #f87171;
}

.notification-info {
    border-left: 4px solid #60a5fa;
}

.notification-message {
    flex: 1;
    font-size: 14px;
    color: #374151;
}

.notification-close {
    background: none;
    border: none;
    font-size: 18px;
    cursor: pointer;
    color: #9ca3af;
    padding: 0;
    width: 24px;
    height: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.notification-close:hover {
    color: #374151;
}

@keyframes slideInRight {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}
`;

// Inject notification styles
const styleSheet = document.createElement('style');
styleSheet.textContent = notificationStyles;
document.head.appendChild(styleSheet);