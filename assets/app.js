/**
 * 🎨 Image Optimizer Pro - JavaScript Application
 * Modern drag & drop interface with real-time progress
 */

class ImageOptimizerApp {
    constructor() {
        this.files = [];
        this.isProcessing = false;
        this.currentStep = 'upload'; // upload, processing, results
        
        // DOM Elements
        this.uploadZone = document.getElementById('uploadZone');
        this.fileInput = document.getElementById('fileInput');
        this.uploadSection = document.getElementById('uploadSection');
        this.processingSection = document.getElementById('processingSection');
        this.resultsSection = document.getElementById('resultsSection');
        this.filesList = document.getElementById('filesList');
        this.resultsList = document.getElementById('resultsList');
        this.progressBar = document.getElementById('overallProgress');
        this.progressText = document.getElementById('progressText');
        
        // Options
        this.qualitySlider = document.getElementById('quality');
        this.qualityValue = document.getElementById('qualityValue');
        this.maxWidthSelect = document.getElementById('maxWidth');
        this.customWidthInput = document.getElementById('customWidth');
        this.createWebpCheck = document.getElementById('createWebp');
        this.createThumbnailCheck = document.getElementById('createThumbnail');
        
        // Buttons
        this.downloadAllBtn = document.getElementById('downloadAllBtn');
        this.optimizeMoreBtn = document.getElementById('optimizeMoreBtn');
        
        this.init();
    }

    init() {
        this.setupEventListeners();
        this.updateQualityDisplay();
        this.loadStatistics();
        console.log('🎨 Image Optimizer Pro initialized');
    }

    setupEventListeners() {
        // File input events
        this.fileInput.addEventListener('change', (e) => this.handleFileSelect(e));
        
        // Drag and drop events
        this.uploadZone.addEventListener('click', () => this.fileInput.click());
        this.uploadZone.addEventListener('dragover', (e) => this.handleDragOver(e));
        this.uploadZone.addEventListener('dragleave', (e) => this.handleDragLeave(e));
        this.uploadZone.addEventListener('drop', (e) => this.handleDrop(e));
        
        // Prevent default drag behaviors on document
        document.addEventListener('dragover', (e) => e.preventDefault());
        document.addEventListener('drop', (e) => e.preventDefault());
        
        // Quality slider
        this.qualitySlider.addEventListener('input', () => this.updateQualityDisplay());
        
        // Max width select
        this.maxWidthSelect.addEventListener('change', () => this.handleMaxWidthChange());
        
        // Buttons
        this.downloadAllBtn?.addEventListener('click', () => this.downloadAll());
        this.optimizeMoreBtn?.addEventListener('click', () => this.resetToUpload());
        
        // Keyboard shortcuts
        document.addEventListener('keydown', (e) => this.handleKeyboard(e));
    }

    handleDragOver(e) {
        e.preventDefault();
        e.stopPropagation();
        this.uploadZone.classList.add('drag-over');
    }

    handleDragLeave(e) {
        e.preventDefault();
        e.stopPropagation();
        // Only remove if actually leaving the upload zone
        if (!this.uploadZone.contains(e.relatedTarget)) {
            this.uploadZone.classList.remove('drag-over');
        }
    }

    handleDrop(e) {
        e.preventDefault();
        e.stopPropagation();
        this.uploadZone.classList.remove('drag-over');
        
        const files = Array.from(e.dataTransfer.files);
        this.processSelectedFiles(files);
    }

    handleFileSelect(e) {
        const files = Array.from(e.target.files);
        this.processSelectedFiles(files);
    }

    processSelectedFiles(files) {
        if (this.isProcessing) return;
        
        // Filter for image files
        const imageFiles = files.filter(file => file.type.startsWith('image/'));
        
        if (imageFiles.length === 0) {
            this.showNotification('Please select valid image files (JPG, PNG, WebP, GIF)', 'error');
            return;
        }
        
        if (imageFiles.length !== files.length) {
            this.showNotification(`${files.length - imageFiles.length} non-image files were ignored`, 'warning');
        }
        
        this.files = imageFiles;
        this.startOptimization();
    }

    async startOptimization() {
        if (this.files.length === 0) return;
        
        this.isProcessing = true;
        this.showProcessingView();
        this.displayFilesForProcessing();
        
        try {
            const formData = new FormData();
            
            // Add files
            this.files.forEach(file => {
                formData.append('images[]', file);
            });
            
            // Add options
            formData.append('quality', this.qualitySlider.value);
            if (this.getMaxWidth()) {
                formData.append('max_width', this.getMaxWidth());
            }
            formData.append('create_webp', this.createWebpCheck.checked);
            formData.append('create_thumbnail', this.createThumbnailCheck.checked);
            
            // Start upload with progress tracking
            const result = await this.uploadWithProgress(formData);
            
            if (result.success) {
                this.showResults(result);
                this.updateStatistics(result);
            } else {
                throw new Error(result.error || 'Optimization failed');
            }
            
        } catch (error) {
            console.error('Optimization error:', error);
            this.showNotification(`Optimization failed: ${error.message}`, 'error');
            this.resetToUpload();
        } finally {
            this.isProcessing = false;
        }
    }

    async uploadWithProgress(formData) {
        return new Promise((resolve, reject) => {
            const xhr = new XMLHttpRequest();
            
            // Track upload progress
            xhr.upload.addEventListener('progress', (e) => {
                if (e.lengthComputable) {
                    const percentComplete = (e.loaded / e.total) * 100;
                    this.updateProgress(percentComplete, 'Uploading...');
                }
            });
            
            // Handle response
            xhr.addEventListener('load', () => {
                try {
                    const response = JSON.parse(xhr.responseText);
                    resolve(response);
                } catch (error) {
                    reject(new Error('Invalid server response'));
                }
            });
            
            xhr.addEventListener('error', () => {
                reject(new Error('Network error occurred'));
            });
            
            xhr.addEventListener('timeout', () => {
                reject(new Error('Upload timeout'));
            });
            
            // Configure and send request
            xhr.open('POST', 'process.php');
            xhr.timeout = 300000; // 5 minutes
            xhr.send(formData);
            
            // Simulate processing progress after upload
            xhr.upload.addEventListener('loadend', () => {
                this.simulateProcessingProgress();
            });
        });
    }

    simulateProcessingProgress() {
        let progress = 0;
        const interval = setInterval(() => {
            progress += Math.random() * 15;
            if (progress >= 95) {
                progress = 95;
                clearInterval(interval);
            }
            this.updateProgress(progress, 'Optimizing images...');
        }, 200);
    }

    updateProgress(percent, message = '') {
        const clampedPercent = Math.min(100, Math.max(0, percent));
        this.progressBar.style.width = `${clampedPercent}%`;
        this.progressText.textContent = `${Math.round(clampedPercent)}%`;
        
        if (message) {
            const messageEl = document.querySelector('.processing-header h2');
            if (messageEl) {
                messageEl.textContent = `⚡ ${message}`;
            }
        }
    }

    displayFilesForProcessing() {
        this.filesList.innerHTML = '';
        
        this.files.forEach((file, index) => {
            const fileItem = this.createFileItem(file, index);
            this.filesList.appendChild(fileItem);
        });
    }

    createFileItem(file, index) {
        const div = document.createElement('div');
        div.className = 'file-item';
        div.innerHTML = `
            <div class="file-icon">📸</div>
            <div class="file-info">
                <div class="file-name">${this.escapeHtml(file.name)}</div>
                <div class="file-size">${this.formatBytes(file.size)}</div>
            </div>
            <div class="file-progress">
                <div class="progress-bar">
                    <div class="progress-fill" id="fileProgress${index}"></div>
                </div>
            </div>
            <div class="file-status status-processing" id="fileStatus${index}">
                Processing...
            </div>
        `;
        return div;
    }

    showProcessingView() {
        this.currentStep = 'processing';
        this.uploadSection.style.display = 'none';
        this.processingSection.style.display = 'block';
        this.resultsSection.style.display = 'none';
        
        // Add animation
        this.processingSection.classList.add('fade-in-up');
    }

    showResults(result) {
        this.currentStep = 'results';
        this.uploadSection.style.display = 'none';
        this.processingSection.style.display = 'none';
        this.resultsSection.style.display = 'block';
        
        // Update final progress
        this.updateProgress(100, 'Complete!');
        
        // Display results
        this.displayResultsSummary(result);
        this.displayResultsList(result);
        
        // Add animation
        this.resultsSection.classList.add('fade-in-up');
        
        // Show success notification
        this.showNotification('🎉 Images optimized successfully!', 'success');
    }

    displayResultsSummary(result) {
        const summaryEl = document.getElementById('resultsSummary');
        if (!summaryEl) return;
        
        let summary;
        if (result.type === 'batch') {
            summary = result.data.summary;
        } else {
            // Single file summary
            const original = result.data.original;
            const optimized = result.data.optimized[0];
            summary = {
                total_files: 1,
                successful: 1,
                total_original_size: this.formatBytes(original.size),
                total_optimized_size: optimized.size_human,
                total_savings: result.data.savings + '%'
            };
        }
        
        summaryEl.innerHTML = `
            <div class="summary-item">
                <span class="summary-number">${summary.total_files}</span>
                <span class="summary-label">Images Processed</span>
            </div>
            <div class="summary-item">
                <span class="summary-number">${summary.total_original_size}</span>
                <span class="summary-label">Original Size</span>
            </div>
            <div class="summary-item">
                <span class="summary-number">${summary.total_optimized_size}</span>
                <span class="summary-label">Optimized Size</span>
            </div>
            <div class="summary-item">
                <span class="summary-number">${summary.total_savings}</span>
                <span class="summary-label">Space Saved</span>
            </div>
        `;
    }

    displayResultsList(result) {
        this.resultsList.innerHTML = '';
        
        const results = result.type === 'batch' ? result.data.results : [result.data];
        
        results.forEach((item, index) => {
            if (item.success !== false) {
                const resultItem = this.createResultItem(item, index);
                this.resultsList.appendChild(resultItem);
            }
        });
        
        // Store download links for batch download
        this.downloadLinks = result.data.download_links || [];
    }

    createResultItem(item, index) {
        const div = document.createElement('div');
        div.className = 'result-item';
        
        const original = item.original;
        const primaryOptimized = item.optimized[0];
        const savings = item.savings || primaryOptimized.savings;
        
        div.innerHTML = `
            <div class="result-header">
                <span class="result-filename">${this.escapeHtml(original.name)}</span>
                <span class="result-savings">${savings}% saved</span>
            </div>
            <div class="result-comparison">
                <div class="comparison-item">
                    <div class="comparison-label">Before</div>
                    <div class="comparison-value">${this.formatBytes(original.size)}</div>
                </div>
                <div class="comparison-arrow">→</div>
                <div class="comparison-item">
                    <div class="comparison-label">After</div>
                    <div class="comparison-value">${primaryOptimized.size_human}</div>
                </div>
            </div>
            <div class="result-downloads">
                ${item.download_links ? item.download_links.map(link => `
                    <a href="${link.url}" class="btn btn-sm btn-success" download>
                        📥 ${link.format.toUpperCase()} (${link.size})
                    </a>
                `).join('') : ''}
            </div>
        `;
        
        return div;
    }

    downloadAll() {
        if (this.downloadLinks && this.downloadLinks.length > 1) {
            const filenames = this.downloadLinks.map(link => link.filename);
            const batchUrl = `download.php?batch=true&files=${encodeURIComponent(JSON.stringify(filenames))}`;
            
            // Create temporary link and click it
            const link = document.createElement('a');
            link.href = batchUrl;
            link.download = 'optimized_images.zip';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            
            this.showNotification('🗂️ Downloading all files as ZIP...', 'success');
        }
    }

    resetToUpload() {
        this.currentStep = 'upload';
        this.files = [];
        this.isProcessing = false;
        
        // Reset file input
        this.fileInput.value = '';
        
        // Show upload section
        this.uploadSection.style.display = 'block';
        this.processingSection.style.display = 'none';
        this.resultsSection.style.display = 'none';
        
        // Reset progress
        this.updateProgress(0);
        
        // Clear file lists
        this.filesList.innerHTML = '';
        this.resultsList.innerHTML = '';
    }

    updateQualityDisplay() {
        const value = this.qualitySlider.value;
        this.qualityValue.textContent = `${value}%`;
        
        // Update quality label
        let label = 'Custom';
        if (value >= 90) label = 'Maximum';
        else if (value >= 80) label = 'High';
        else if (value >= 65) label = 'Good';
        else if (value >= 50) label = 'Web';
        else label = 'Small';
        
        // You could add a label element to show this
    }

    handleMaxWidthChange() {
        const value = this.maxWidthSelect.value;
        const customInput = this.customWidthInput;
        
        if (value === 'custom') {
            customInput.style.display = 'block';
            customInput.focus();
        } else {
            customInput.style.display = 'none';
            customInput.value = '';
        }
    }

    getMaxWidth() {
        const selectValue = this.maxWidthSelect.value;
        if (selectValue === 'custom') {
            return this.customWidthInput.value ? parseInt(this.customWidthInput.value) : null;
        }
        return selectValue ? parseInt(selectValue) : null;
    }

    handleKeyboard(e) {
        // Escape key - reset to upload
        if (e.key === 'Escape' && this.currentStep !== 'upload') {
            this.resetToUpload();
        }
        
        // Enter key - start optimization if on upload step
        if (e.key === 'Enter' && this.currentStep === 'upload' && this.files.length > 0) {
            this.startOptimization();
        }
        
        // Ctrl+V - paste files (if supported)
        if (e.ctrlKey && e.key === 'v' && this.currentStep === 'upload') {
            this.handlePaste(e);
        }
    }

    async handlePaste(e) {
        try {
            const items = Array.from(e.clipboardData.items);
            const imageItems = items.filter(item => item.type.startsWith('image/'));
            
            if (imageItems.length > 0) {
                e.preventDefault();
                const files = await Promise.all(
                    imageItems.map(item => item.getAsFile())
                );
                this.processSelectedFiles(files.filter(file => file));
            }
        } catch (error) {
            console.warn('Paste not supported or failed:', error);
        }
    }

    showNotification(message, type = 'info') {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.textContent = message;
        
        // Style the notification
        Object.assign(notification.style, {
            position: 'fixed',
            top: '20px',
            right: '20px',
            padding: '12px 20px',
            borderRadius: '8px',
            color: 'white',
            fontWeight: '600',
            zIndex: '10000',
            transform: 'translateX(100%)',
            transition: 'transform 0.3s ease-in-out',
            maxWidth: '400px'
        });
        
        // Set colors based on type
        const colors = {
            success: '#22c55e',
            error: '#ef4444',
            warning: '#f59e0b',
            info: '#3b82f6'
        };
        notification.style.backgroundColor = colors[type] || colors.info;
        
        // Add to page
        document.body.appendChild(notification);
        
        // Animate in
        setTimeout(() => {
            notification.style.transform = 'translateX(0)';
        }, 100);
        
        // Remove after delay
        setTimeout(() => {
            notification.style.transform = 'translateX(100%)';
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 300);
        }, 4000);
    }

    updateStatistics(result) {
        // Update page statistics (could be stored in localStorage)
        try {
            const stats = JSON.parse(localStorage.getItem('optimizerStats') || '{"processed": 0, "saved": 0}');
            
            if (result.type === 'batch') {
                stats.processed += result.data.summary.successful;
                // Calculate saved bytes (approximate)
                stats.saved += 1000000; // Rough estimate
            } else {
                stats.processed += 1;
                stats.saved += result.data.original.size - result.data.optimized[0].size;
            }
            
            localStorage.setItem('optimizerStats', JSON.stringify(stats));
            this.displayStatistics(stats);
        } catch (error) {
            console.warn('Could not update statistics:', error);
        }
    }

    loadStatistics() {
        try {
            const stats = JSON.parse(localStorage.getItem('optimizerStats') || '{"processed": 0, "saved": 0}');
            this.displayStatistics(stats);
        } catch (error) {
            console.warn('Could not load statistics:', error);
        }
    }

    displayStatistics(stats) {
        const totalProcessedEl = document.getElementById('totalProcessed');
        const totalSavedEl = document.getElementById('totalSaved');
        
        if (totalProcessedEl) {
            totalProcessedEl.textContent = stats.processed.toLocaleString();
        }
        
        if (totalSavedEl) {
            totalSavedEl.textContent = this.formatBytes(stats.saved);
        }
    }

    formatBytes(bytes, decimals = 1) {
        if (bytes === 0) return '0 B';
        
        const k = 1024;
        const sizes = ['B', 'KB', 'MB', 'GB', 'TB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        
        return parseFloat((bytes / Math.pow(k, i)).toFixed(decimals)) + ' ' + sizes[i];
    }

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
}

// Initialize the application when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    window.optimizerApp = new ImageOptimizerApp();
});

// Add some global utility functions
window.optimizerUtils = {
    formatBytes: (bytes, decimals = 1) => {
        if (bytes === 0) return '0 B';
        const k = 1024;
        const sizes = ['B', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(decimals)) + ' ' + sizes[i];
    },
    
    downloadFile: (url, filename) => {
        const link = document.createElement('a');
        link.href = url;
        link.download = filename;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }
};