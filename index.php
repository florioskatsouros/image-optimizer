<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Image Optimizer Pro - Optimize & Convert Images</title>
    <meta name="description" content="Optimize and convert your images online. Support for 20+ formats including HEIC, RAW, PSD. Reduce file size up to 90% without losing quality.">
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🎨</text></svg>">
    <style>
        /* ============================================
            Enhanced Image Optimizer Pro - Modern CSS
           ============================================ */

        :root {
            /* Color Palette */
            --primary-color: #667eea;
            --primary-dark: #5a6fd8;
            --secondary-color: #764ba2;
            --accent-color: #f093fb;
            --success-color: #4ade80;
            --warning-color: #fbbf24;
            --error-color: #f87171;
            
            /* Neutrals */
            --gray-50: #f9fafb;
            --gray-100: #f3f4f6;
            --gray-200: #e5e7eb;
            --gray-300: #d1d5db;
            --gray-400: #9ca3af;
            --gray-500: #6b7280;
            --gray-600: #4b5563;
            --gray-700: #374151;
            --gray-800: #1f2937;
            --gray-900: #111827;
            
            /* Spacing */
            --space-xs: 0.5rem;
            --space-sm: 0.75rem;
            --space-md: 1rem;
            --space-lg: 1.5rem;
            --space-xl: 2rem;
            --space-2xl: 3rem;
            
            /* Typography */
            --font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            --font-size-xs: 0.75rem;
            --font-size-sm: 0.875rem;
            --font-size-base: 1rem;
            --font-size-lg: 1.125rem;
            --font-size-xl: 1.25rem;
            --font-size-2xl: 1.5rem;
            --font-size-3xl: 1.875rem;
            --font-size-4xl: 2.25rem;
            
            /* Borders & Radius */
            --border-radius: 8px;
            --border-radius-lg: 12px;
            --border-radius-xl: 16px;
            --border-width: 1px;
            
            /* Shadows */
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            
            /* Animations */
            --transition-fast: 150ms ease-in-out;
            --transition-normal: 300ms ease-in-out;
            --transition-slow: 500ms ease-in-out;
        }

        /* ============================================
           🎯 Base Styles
           ============================================ */

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: var(--font-family);
            line-height: 1.6;
            color: var(--gray-800);
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            min-height: 100vh;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 var(--space-lg);
        }

        /* ============================================
           🏠 Header
           ============================================ */

        .header {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: var(--space-xl) 0;
            margin-bottom: var(--space-2xl);
        }

        .logo h1 {
            font-size: var(--font-size-3xl);
            font-weight: 800;
            color: white;
            margin-bottom: var(--space-xs);
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
            gap: var(--space-md);
        }

        .logo-icon {
            width: 40px;
            height: 40px;
            border-radius: var(--border-radius-lg);
            box-shadow: var(--shadow-md);
            object-fit: cover;
        }

        .tagline {
            font-size: var(--font-size-lg);
            color: rgba(255, 255, 255, 0.9);
            font-weight: 300;
            text-align: center;
        }

        /* Hide stats completely */
        .stats {
            display: none;
        }

        .stat {
            text-align: center;
            color: white;
        }

        .stat-number {
            display: block;
            font-size: var(--font-size-2xl);
            font-weight: 700;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .stat-label {
            font-size: var(--font-size-sm);
            opacity: 0.9;
        }

        /* ============================================
        📐 Container Width Matching
        ============================================ */

        /* Ensure upload section and options panels have same max width */
        .upload-section,
        .options-panel,
        .convert-panel {
            max-width: 1200px;
            margin-left: auto;
            margin-right: auto;
            width: 100%;
            box-sizing: border-box;
        }

        /* Override existing options-panel margin settings */
        .options-panel {
            margin-top: var(--space-xl) !important;
            margin-bottom: 0 !important;
        }

        .convert-panel {
            margin-top: var(--space-lg) !important;
            margin-bottom: 0 !important;
        }

        /* Ensure consistent padding */
        .upload-section {
            padding: var(--space-2xl);
        }

        .options-panel,
        .convert-panel {
            padding: var(--space-xl);
        }

        /* Make sure the upload zone doesn't exceed the container */
        .upload-zone {
            max-width: 100%;
            margin: 0 auto;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .upload-section,
            .options-panel,
            .convert-panel {
                padding-left: var(--space-md);
                padding-right: var(--space-md);
            }
        }

        @media (max-width: 480px) {
            .upload-section,
            .options-panel,
            .convert-panel {
                margin-left: -var(--space-md);
                margin-right: -var(--space-md);
                border-radius: 0;
            }
        }

        /* ============================================
           📤 Main Content
           ============================================ */

        .main-content {
            background: white;
            border-radius: var(--border-radius-xl);
            box-shadow: var(--shadow-xl);
            overflow: hidden;
            margin-bottom: var(--space-2xl);
        }

        /* ============================================
           🔄 Enhanced Mode Toggle
           ============================================ */

        .mode-toggle-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: var(--space-xl);
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .mode-toggle-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Ccircle cx='30' cy='30' r='4'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E") repeat;
            pointer-events: none;
        }

        .mode-toggle-container {
            position: relative;
            z-index: 1;
        }

        .mode-toggle-wrapper {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: var(--space-lg);
            margin-bottom: var(--space-lg);
        }

        .mode-option {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: var(--space-sm);
            padding: var(--space-md);
            border-radius: var(--border-radius-lg);
            transition: all var(--transition-normal);
            cursor: pointer;
            min-width: 120px;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 2px solid transparent;
        }

        .mode-option.active {
            background: rgba(255, 255, 255, 0.2);
            border-color: rgba(255, 255, 255, 0.3);
            transform: scale(1.05);
            box-shadow: var(--shadow-lg);
        }

        .mode-icon {
            font-size: var(--font-size-3xl);
            filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.2));
        }

        .mode-label {
            color: white;
            font-weight: 600;
            font-size: var(--font-size-lg);
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
        }

        .mode-sublabel {
            color: rgba(255, 255, 255, 0.8);
            font-size: var(--font-size-sm);
            text-align: center;
            line-height: 1.4;
        }

        /* Animated Toggle Switch */
        .toggle-switch-container {
            position: relative;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50px;
            padding: 4px;
            width: 80px;
            height: 40px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .toggle-switch {
            position: absolute;
            top: 4px;
            left: 4px;
            width: 32px;
            height: 32px;
            background: white;
            border-radius: 50%;
            transition: transform var(--transition-normal) cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
        }

        .toggle-switch.convert-mode {
            transform: translateX(40px);
        }

        .mode-description {
            color: rgba(255, 255, 255, 0.95);
            font-size: var(--font-size-base);
            max-width: 600px;
            margin: 0 auto;
            line-height: 1.6;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
        }

        /* ============================================
           📁 Upload Section - Enhanced
           ============================================ */

        .upload-section {
            padding: var(--space-2xl);
        }

        .upload-zone {
            border: 3px dashed var(--gray-300);
            border-radius: var(--border-radius-lg);
            padding: var(--space-2xl);
            text-align: center;
            cursor: pointer;
            transition: all var(--transition-normal);
            background: var(--gray-50);
            position: relative;
            overflow: hidden;
            min-height: 200px;
        }

        .upload-zone:hover {
            border-color: var(--primary-color);
            background: rgba(102, 126, 234, 0.05);
            transform: translateY(-2px);
        }

        .upload-zone.drag-over {
            border-color: var(--primary-color);
            background: rgba(102, 126, 234, 0.1);
            transform: scale(1.02);
        }

        /* Upload zone with files */
        .upload-zone.has-files {
            border-color: var(--primary-color);
            background: rgba(102, 126, 234, 0.05);
            border-style: solid;
        }

        .upload-zone.has-files .upload-initial-content {
            display: none;
        }

        .upload-zone.has-files .upload-files-content {
            display: block;
        }

        .upload-initial-content {
            display: block;
        }

        .upload-files-content {
            display: none;
        }

        .upload-icon {
            color: var(--gray-400);
            margin-bottom: var(--space-md);
            transition: all var(--transition-normal);
            font-size: 64px;
        }

        .upload-zone:hover .upload-icon {
            color: var(--primary-color);
            transform: scale(1.1);
        }

        .upload-zone h2 {
            font-size: var(--font-size-2xl);
            color: var(--gray-700);
            margin-bottom: var(--space-sm);
            font-weight: 600;
        }

        .upload-zone p {
            font-size: var(--font-size-lg);
            color: var(--gray-500);
            margin-bottom: var(--space-lg);
        }

        .browse-text {
            color: var(--primary-color);
            font-weight: 600;
            text-decoration: underline;
        }

        .upload-info {
            margin-top: var(--space-lg);
        }

        .upload-info p {
            font-size: var(--font-size-sm);
            color: var(--gray-600);
            margin-bottom: var(--space-xs);
        }

        /* Files display in upload zone */
        .files-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
            gap: var(--space-md);
            margin-bottom: var(--space-lg);
        }

        .file-item {
            position: relative;
            border-radius: var(--border-radius);
            overflow: hidden;
            background: white;
            box-shadow: var(--shadow-sm);
            transition: all var(--transition-fast);
        }

        .file-item:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        .file-thumbnail {
            position: relative;
            aspect-ratio: 1;
            overflow: hidden;
            background: var(--gray-100);
        }

        .thumbnail-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .file-icon-fallback {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            background: var(--gray-100);
            color: var(--gray-400);
        }

        .file-info {
            padding: var(--space-sm);
            text-align: left;
        }

        .file-name {
            font-size: var(--font-size-xs);
            font-weight: 600;
            color: var(--gray-800);
            margin-bottom: var(--space-xs);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .file-size {
            font-size: var(--font-size-xs);
            color: var(--gray-500);
        }

        .remove-file-btn {
            position: absolute;
            top: 4px;
            right: 4px;
            background: rgba(248, 113, 113, 0.9);
            color: white;
            border: none;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: var(--font-size-sm);
            transition: all var(--transition-fast);
            opacity: 0;
            transform: scale(0.8);
        }

        .file-item:hover .remove-file-btn {
            opacity: 1;
            transform: scale(1);
        }

        .remove-file-btn:hover {
            background: #dc2626;
            transform: scale(1.1);
        }

        /* Enhanced Progress Overlay */
        .upload-progress-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(102, 126, 234, 0.9);
            display: none;
            align-items: center;
            justify-content: center;
            border-radius: var(--border-radius);
            backdrop-filter: blur(4px);
        }

        .upload-progress-circle {
            position: relative;
            width: 60px;
            height: 60px;
        }

        .progress-ring {
            width: 60px;
            height: 60px;
            transform: rotate(-90deg);
        }

        .progress-ring-circle {
            stroke: white;
            stroke-width: 4;
            fill: transparent;
            stroke-dasharray: 163.36; /* 2 * π * 26 */
            stroke-dashoffset: 163.36;
            transition: stroke-dashoffset 0.3s ease;
        }

        .progress-percentage {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: white;
            font-weight: 700;
            font-size: var(--font-size-sm);
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
        }

        .add-more-btn {
            display: inline-flex;
            align-items: center;
            gap: var(--space-sm);
            padding: var(--space-sm) var(--space-lg);
            background: var(--primary-color);
            color: white;
            border: none;
            border-radius: var(--border-radius);
            font-size: var(--font-size-sm);
            font-weight: 600;
            cursor: pointer;
            transition: all var(--transition-fast);
            box-shadow: var(--shadow-sm);
        }

        .add-more-btn:hover {
            background: var(--primary-dark);
            transform: translateY(-1px);
            box-shadow: var(--shadow-md);
        }

        .files-summary {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: var(--space-md);
            padding: var(--space-sm) var(--space-md);
            background: rgba(102, 126, 234, 0.1);
            border-radius: var(--border-radius);
            border: 1px solid rgba(102, 126, 234, 0.2);
        }

        .files-count {
            font-weight: 600;
            color: var(--primary-color);
        }

        .clear-all-btn {
            background: none;
            border: none;
            color: var(--error-color);
            cursor: pointer;
            font-size: var(--font-size-sm);
            padding: var(--space-xs);
            border-radius: var(--border-radius);
            transition: all var(--transition-fast);
        }

        .clear-all-btn:hover {
            background: rgba(248, 113, 113, 0.1);
        }

        /* ============================================
           ⚙️ Options Panel
           ============================================ */

        .options-panel {
            margin-top: var(--space-2xl);
            padding: var(--space-xl);
            background: var(--gray-50);
            border-radius: var(--border-radius-lg);
            border: 1px solid var(--gray-200);
        }

        .options-panel h3 {
            font-size: var(--font-size-xl);
            color: var(--gray-800);
            margin-bottom: var(--space-lg);
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: var(--space-sm);
        }

        .options-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: var(--space-lg);
        }

        .option-group label {
            display: block;
            font-size: var(--font-size-sm);
            font-weight: 600;
            color: var(--gray-700);
            margin-bottom: var(--space-sm);
        }

        .quality-slider {
            margin-top: var(--space-sm);
        }

        .quality-slider input[type="range"] {
            width: 100%;
            height: 6px;
            border-radius: 3px;
            background: var(--gray-200);
            outline: none;
            -webkit-appearance: none;
        }

        .quality-slider input[type="range"]::-webkit-slider-thumb {
            -webkit-appearance: none;
            appearance: none;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: var(--primary-color);
            cursor: pointer;
            box-shadow: var(--shadow-md);
        }

        .quality-slider input[type="range"]::-moz-range-thumb {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: var(--primary-color);
            cursor: pointer;
            border: none;
            box-shadow: var(--shadow-md);
        }

        .quality-labels {
            display: flex;
            justify-content: space-between;
            margin-top: var(--space-sm);
            font-size: var(--font-size-xs);
            color: var(--gray-500);
        }

        #qualityValue, #convertQualityValue {
            font-weight: 600;
            color: var(--primary-color);
        }

        select, input[type="number"] {
            width: 100%;
            padding: var(--space-sm) var(--space-md);
            border: var(--border-width) solid var(--gray-300);
            border-radius: var(--border-radius);
            font-size: var(--font-size-base);
            transition: all var(--transition-fast);
            background: white;
        }

        select:focus, input[type="number"]:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .checkbox-group {
            display: flex;
            flex-direction: column;
            gap: var(--space-sm);
        }

        .checkbox {
            display: flex;
            align-items: center;
            cursor: pointer;
            font-size: var(--font-size-sm);
        }

        .checkbox input[type="checkbox"] {
            width: auto;
            margin-right: var(--space-sm);
            accent-color: var(--primary-color);
        }

        /* ============================================
           🎯 Buttons
           ============================================ */

        .btn {
            display: inline-flex;
            align-items: center;
            gap: var(--space-sm);
            padding: var(--space-sm) var(--space-lg);
            border: none;
            border-radius: var(--border-radius);
            font-size: var(--font-size-base);
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            transition: all var(--transition-fast);
            position: relative;
            overflow: hidden;
        }

        .btn:before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left var(--transition-normal);
        }

        .btn:hover:before {
            left: 100%;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            box-shadow: var(--shadow-md);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        .btn-secondary {
            background: white;
            color: var(--gray-700);
            border: 1px solid var(--gray-300);
            box-shadow: var(--shadow-sm);
        }

        .btn-secondary:hover {
            background: var(--gray-50);
            transform: translateY(-1px);
        }

        /* Hidden panels */
        .convert-panel {
            display: none;
        }

        .convert-panel.active {
            display: block;
        }

        .optimize-panel.hidden {
            display: none !important;
        }

        /* Format support badges */
        .format-badge {
            display: inline-block;
            background: var(--success-color);
            color: white;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: var(--font-size-xs);
            font-weight: 600;
            margin: 2px;
        }

        .format-badge.experimental {
            background: var(--warning-color);
        }

        /* ============================================
           📱 Responsive Design
           ============================================ */

        @media (max-width: 768px) {
            .container {
                padding: 0 var(--space-md);
            }
            
            .header {
                flex-direction: column;
                gap: var(--space-lg);
                text-align: center;
            }
            
            .logo h1 {
                font-size: var(--font-size-2xl);
            }
            
            .upload-zone {
                padding: var(--space-lg);
            }
            
            .upload-zone h2 {
                font-size: var(--font-size-xl);
            }
            
            .options-grid {
                grid-template-columns: 1fr;
            }
            
            .mode-toggle-wrapper {
                flex-direction: column;
                gap: var(--space-md);
            }
            
            .toggle-switch-container {
                order: -1;
            }

            .files-grid {
                grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
                gap: var(--space-sm);
            }

            .files-summary {
                flex-direction: column;
                gap: var(--space-sm);
                text-align: center;
            }
        }

        /* ============================================
           🎭 Animations
           ============================================ */

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.05);
            }
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .fade-in-up {
            animation: fadeInUp 0.6s ease-out;
        }

        .pulse {
            animation: pulse 2s infinite;
        }

        /* Notification styles */
        @keyframes slideInRight {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
    </style>
</head>
<body>
    <div class="container">
        <header class="header">
            <div class="logo">
                <h1>
                    <img src="assets/images/logo.jpg" alt="Image Optimizer Logo" class="logo-icon">
                    Image Optimizer Pro
                </h1>
                <p class="tagline">Optimize & Convert images - 20+ formats supported</p>
            </div>
        </header>

        <main class="main-content">
            <!-- Enhanced Mode Toggle Section -->
            <section class="mode-toggle-section">
                <div class="mode-toggle-container">
                    <div class="mode-toggle-wrapper">
                        <div class="mode-option active" id="optimizeMode" onclick="setMode('optimize')">
                            <div class="mode-icon">⚡</div>
                            <div class="mode-label">Optimize</div>
                            <div class="mode-sublabel">Compress & reduce file size</div>
                        </div>
                        
                        <div class="toggle-switch-container" onclick="toggleMode()">
                            <div class="toggle-switch" id="toggleSwitch">
                                <span id="toggleIcon">⚡</span>
                            </div>
                        </div>
                        
                        <div class="mode-option" id="convertMode" onclick="setMode('convert')">
                            <div class="mode-icon">🔄</div>
                            <div class="mode-label">Convert</div>
                            <div class="mode-sublabel">Change format & transform</div>
                        </div>
                    </div>
                    <p class="mode-description" id="modeDescription">
                        Compress images while maintaining quality. Supports JPG, PNG, WebP, AVIF, HEIC, RAW, and more.
                    </p>
                </div>
            </section>

            <!-- Upload Section -->
            <section class="upload-section" id="uploadSection">
                <div class="upload-zone" id="uploadZone">
                    <!-- Initial content (shown when no files) -->
                    <div class="upload-initial-content" id="uploadInitialContent">
                        <div class="upload-icon">🖼️</div>
                        <h2 id="uploadTitle">Drop your images here</h2>
                        <p>or <span class="browse-text">click to browse</span></p>
                        <div class="upload-info">
                            <p>✅ Supported: <strong>JPG, PNG, WebP, GIF, AVIF, HEIC, RAW, PSD, TIFF, BMP</strong></p>
                            <p>✅ Max size: <strong>100MB</strong> per image</p>
                            <p>✅ Batch upload supported</p>
                        </div>
                    </div>

                    <!-- Files content (shown when files are selected) -->
                    <div class="upload-files-content" id="uploadFilesContent">
                        <div class="files-summary">
                            <span class="files-count" id="filesCount">0 files selected</span>
                            <button class="clear-all-btn" onclick="clearFiles()">🗑️ Clear All</button>
                        </div>
                        <div class="files-grid" id="filesGrid"></div>
                        <button class="add-more-btn" onclick="document.getElementById('fileInput').click()">
                            ➕ Add More Images
                        </button>
                    </div>

                    <input type="file" id="fileInput" multiple accept="image/*,.heic,.heif,.psd,.raw,.cr2,.nef,.orf,.arw,.dng,.tiff,.tif" hidden>
                </div>

                <!-- FIXED: Optimize Options Panel -->
                <div class="options-panel" id="optimizePanel">
                    <h3>⚡ Optimization Options</h3>
                    <div class="options-grid">
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
                            <input type="number" id="customWidth" placeholder="Custom width" style="display: none; margin-top: 8px;">
                        </div>

                        <div class="option-group">
                            <label>Output Formats</label>
                            <div class="checkbox-group">
                                <label class="checkbox">
                                    <input type="checkbox" id="createThumbnail" checked>
                                    <span>Thumbnail (300x300)</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Convert Options Panel -->
                <div class="options-panel convert-panel" id="convertPanel">
                    <h3>🔄 Conversion Options</h3>
                    
                    <div class="options-grid">
                        <div class="option-group">
                            <label for="outputFormat">Convert to Format</label>
                            <select id="outputFormat">
                                <option value="">Select output format</option>
                                <optgroup label="Web Formats">
                                    <option value="jpg">JPEG (.jpg)</option>
                                    <option value="png">PNG (.png)</option>
                                    <option value="webp">WebP (.webp)</option>
                                    <option value="avif">AVIF (.avif)</option>
                                </optgroup>
                                <optgroup label="Print Formats">
                                    <option value="tiff">TIFF (.tiff)</option>
                                    <option value="bmp">Bitmap (.bmp)</option>
                                </optgroup>
                            </select>
                        </div>

                    

                        <div class="option-group">
                            <label>Additional Options</label>
                            <div class="checkbox-group">
                                <label class="checkbox">
                                    <input type="checkbox" id="convertThumbnail">
                                    <span>Create thumbnail</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Process Button -->
                <div style="text-align: center; margin-top: 2rem;">
                    <button class="btn btn-primary" id="processBtn" onclick="startProcessing()" style="font-size: 1.125rem; padding: 1rem 2rem;">
                        <span id="processBtnIcon">⚡</span>
                        <span id="processBtnText">Optimize Images</span>
                    </button>
                </div>
            </section>
        </main>
    </div>

    <script>
        // Application state
        let currentMode = 'optimize';
        let selectedFiles = [];

        // Initialize the application
        document.addEventListener('DOMContentLoaded', function() {
            setupEventListeners();
            updateModeDisplay();
        });

        function setupEventListeners() {
            // File input events
            const fileInput = document.getElementById('fileInput');
            const uploadZone = document.getElementById('uploadZone');
            
            if (fileInput) fileInput.addEventListener('change', handleFileSelect);
            if (uploadZone) {
                uploadZone.addEventListener('click', (e) => {
                    if (e.target === uploadZone || uploadZone.contains(e.target) && !e.target.closest('button') && !e.target.closest('.file-item')) {
                        fileInput?.click();
                    }
                });
                uploadZone.addEventListener('dragover', handleDragOver);
                uploadZone.addEventListener('dragleave', handleDragLeave);
                uploadZone.addEventListener('drop', handleDrop);
            }
            
            // Prevent default drag behaviors
            document.addEventListener('dragover', e => e.preventDefault());
            document.addEventListener('drop', e => e.preventDefault());
            
            // Quality sliders - ΠΡΟΣΘΕΤΩ ΕΛΕΓΧΟΥΣ
            const qualitySlider = document.getElementById('quality');
            const convertQualitySlider = document.getElementById('convertQuality');

            
            
            // Max width select
            const maxWidthSelect = document.getElementById('maxWidth');
            if (maxWidthSelect) maxWidthSelect.addEventListener('change', handleMaxWidthChange);

            if (convertQualitySlider) {
                convertQualitySlider.addEventListener('input', updateConvertQualityDisplay);
                updateConvertQualityDisplay(); // Αρχικοποίηση μόνο για το convert
            }
        }

        // Enhanced Mode Switching Functions with Complete Clear

        function setMode(mode) {
            currentMode = mode;
            updateModeDisplay();
            clearResults(); // Clear results when mode changes
        }

        function toggleMode() {
            currentMode = currentMode === 'optimize' ? 'convert' : 'optimize';
            updateModeDisplay();
            clearResults(); // Clear results when mode changes
        }

        function clearResults() {
            const resultsSection = document.getElementById('resultsSection');
            if (resultsSection) {
                resultsSection.remove();
            }
            
            // Also clear the files and reset upload zone
            clearFiles();
            
            // Hide upload progress if showing
            hideUploadProgress();
            
            // Reset processing button to normal state
            hideProcessing();
        }

        function updateModeDisplay() {
            const optimizeMode = document.getElementById('optimizeMode');
            const convertMode = document.getElementById('convertMode');
            const toggleSwitch = document.getElementById('toggleSwitch');
            const toggleIcon = document.getElementById('toggleIcon');
            const optimizePanel = document.getElementById('optimizePanel');
            const convertPanel = document.getElementById('convertPanel');
            const modeDescription = document.getElementById('modeDescription');
            const uploadTitle = document.getElementById('uploadTitle');
            const processBtnIcon = document.getElementById('processBtnIcon');
            const processBtnText = document.getElementById('processBtnText');

            if (currentMode === 'optimize') {
                // Update mode buttons
                if (optimizeMode) optimizeMode.classList.add('active');
                if (convertMode) convertMode.classList.remove('active');
                
                // Update toggle switch
                if (toggleSwitch) toggleSwitch.classList.remove('convert-mode');
                if (toggleIcon) toggleIcon.textContent = '⚡';
                
                // Update panels - ΠΡΟΣΘΕΤΩ ΕΛΕΓΧΟΥΣ
                if (optimizePanel) {
                    optimizePanel.style.display = 'block';
                    optimizePanel.classList.remove('hidden');
                }
                if (convertPanel) {
                    convertPanel.style.display = 'none';
                    convertPanel.classList.remove('active');
                }
                
                // Update descriptions
                if (modeDescription) modeDescription.textContent = 'Compress images while maintaining quality. Supports JPG, PNG, WebP, AVIF, HEIC, RAW, and more.';
                if (uploadTitle) uploadTitle.textContent = 'Drop your images here';
                
                // Update button
                if (processBtnIcon) processBtnIcon.textContent = '⚡';
                if (processBtnText) processBtnText.textContent = 'Optimize Images';
                
            } else {
                // Update mode buttons
                if (optimizeMode) optimizeMode.classList.remove('active');
                if (convertMode) convertMode.classList.add('active');
                
                // Update toggle switch
                if (toggleSwitch) toggleSwitch.classList.add('convert-mode');
                if (toggleIcon) toggleIcon.textContent = '🔄';
                
                // Update panels
                if (optimizePanel) {
                    optimizePanel.style.display = 'none';
                    optimizePanel.classList.add('hidden');
                }
                if (convertPanel) {
                    convertPanel.style.display = 'block';
                    convertPanel.classList.add('active');
                }
                
                // Update descriptions
                if (modeDescription) modeDescription.textContent = 'Convert images between different formats. Support for web optimization, print preparation, and format standardization.';
                if (uploadTitle) uploadTitle.textContent = 'Drop images to convert';
                
                // Update button
                if (processBtnIcon) processBtnIcon.textContent = '🔄';
                if (processBtnText) processBtnText.textContent = 'Convert Images';
            }
        }

        function handleFileSelect(e) {
            const files = Array.from(e.target.files);
            processSelectedFiles(files);
        }

        function handleDragOver(e) {
            e.preventDefault();
            e.stopPropagation();
            e.currentTarget.classList.add('drag-over');
        }

        function handleDragLeave(e) {
            e.preventDefault();
            e.stopPropagation();
            if (!e.currentTarget.contains(e.relatedTarget)) {
                e.currentTarget.classList.remove('drag-over');
            }
        }

        function handleDrop(e) {
            e.preventDefault();
            e.stopPropagation();
            e.currentTarget.classList.remove('drag-over');
            
            const files = Array.from(e.dataTransfer.files);
            processSelectedFiles(files);
        }

        function processSelectedFiles(files) {
            // Filter image files
            const imageFiles = files.filter(file => {
                const validTypes = [
                    'image/jpeg', 'image/jpg', 'image/png', 'image/gif', 
                    'image/webp', 'image/bmp', 'image/tiff', 'image/svg+xml',
                    'image/heic', 'image/heif', 'image/avif'
                ];
                
                // Also check by extension for files that might not have proper MIME types
                const extension = file.name.split('.').pop().toLowerCase();
                const validExtensions = [
                    'jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'tiff', 'tif',
                    'svg', 'heic', 'heif', 'avif', 'psd', 'raw', 'cr2', 'nef',
                    'orf', 'arw', 'dng', 'ico'
                ];
                
                return validTypes.includes(file.type) || validExtensions.includes(extension);
            });

            if (imageFiles.length === 0) {
                showNotification('Please select valid image files', 'error');
                return;
            }

            if (imageFiles.length !== files.length) {
                showNotification(`${files.length - imageFiles.length} non-image files were ignored`, 'warning');
            }

            // Add to existing files instead of replacing
            selectedFiles = [...selectedFiles, ...imageFiles];
            displayFilesInUploadZone();
        }

        function displayFilesInUploadZone() {
            const uploadZone = document.getElementById('uploadZone');
            const filesGrid = document.getElementById('filesGrid');
            const filesCount = document.getElementById('filesCount');

            if (selectedFiles.length === 0) {
                uploadZone.classList.remove('has-files');
                return;
            }

            // Update upload zone state
            uploadZone.classList.add('has-files');
            
            // Update files count
            filesCount.textContent = `${selectedFiles.length} file${selectedFiles.length !== 1 ? 's' : ''} selected`;

            // Create file items
            const fileItems = selectedFiles.map((file, index) => {
                const extension = file.name.split('.').pop().toLowerCase();
                const sizeFormatted = formatBytes(file.size);
                const thumbnailUrl = URL.createObjectURL(file);
                
                return `
                    <div class="file-item fade-in-up" style="animation-delay: ${index * 0.05}s">
                        <div class="file-thumbnail">
                            <img src="${thumbnailUrl}" alt="${file.name}" class="thumbnail-image" 
                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                            <div class="file-icon-fallback" style="display: none;">${getFileIcon(extension)}</div>
                            
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
                            <div class="file-name" title="${file.name}">${file.name}</div>
                            <div class="file-size">${sizeFormatted}</div>
                        </div>
                        <button class="remove-file-btn" onclick="removeFile(${index})" title="Remove file">×</button>
                    </div>
                `;
            }).join('');

            filesGrid.innerHTML = fileItems;
        }

        function getFileIcon(extension) {
            const icons = {
                'jpg': '📸', 'jpeg': '📸', 'png': '🖼️', 'gif': '🎞️',
                'webp': '🌐', 'avif': '⚡', 'bmp': '💾', 'tiff': '🖨️',
                'tif': '🖨️', 'svg': '🎨', 'heic': '📱', 'heif': '📱',
                'psd': '🎭', 'raw': '📷', 'cr2': '📷', 'nef': '📷',
                'orf': '📷', 'arw': '📷', 'dng': '📷', 'ico': '🔳'
            };
            return icons[extension] || '📄';
        }

        function clearFiles() {
            // Clean up object URLs to prevent memory leaks
            selectedFiles.forEach((file, index) => {
                const thumbnailImage = document.querySelector(`#progress-${index}`)?.parentElement?.querySelector('.thumbnail-image');
                if (thumbnailImage && thumbnailImage.src.startsWith('blob:')) {
                    URL.revokeObjectURL(thumbnailImage.src);
                }
            });
            
            selectedFiles = [];
            
            const uploadZone = document.getElementById('uploadZone');
            uploadZone.classList.remove('has-files');
            
            const fileInput = document.getElementById('fileInput');
            if (fileInput) {
                fileInput.value = '';
            }
            
            // Clear the files grid
            const filesGrid = document.getElementById('filesGrid');
            if (filesGrid) {
                filesGrid.innerHTML = '';
            }
            
            // Reset files count
            const filesCount = document.getElementById('filesCount');
            if (filesCount) {
                filesCount.textContent = '0 files selected';
            }
        }

        function removeFile(index) {
            // Clean up object URL for the removed file
            const thumbnailImage = document.querySelector(`#progress-${index}`)?.parentElement?.querySelector('.thumbnail-image');
            if (thumbnailImage && thumbnailImage.src.startsWith('blob:')) {
                URL.revokeObjectURL(thumbnailImage.src);
            }
            
            selectedFiles.splice(index, 1);
            displayFilesInUploadZone();
        }

       

        function updateConvertQualityDisplay() {
            const quality = document.getElementById('convertQuality');
            const qualityValue = document.getElementById('convertQualityValue');
            qualityValue.textContent = quality.value + '%';
        }

        function handleMaxWidthChange() {
            const maxWidth = document.getElementById('maxWidth');
            const customWidth = document.getElementById('customWidth');
            
            if (maxWidth.value === 'custom') {
                customWidth.style.display = 'block';
                customWidth.focus();
            } else {
                customWidth.style.display = 'none';
                customWidth.value = '';
            }
        }

        function startProcessing() {
            if (selectedFiles.length === 0) {
                showNotification('Please select files first', 'warning');
                return;
            }

            // Validate options based on mode
            if (currentMode === 'convert') {
                const outputFormat = document.getElementById('outputFormat');
                if (!outputFormat || !outputFormat.value) {
                    showNotification('Please select an output format', 'warning');
                    return;
                }
            }

            // Show upload progress for each file
            showUploadProgress();

            // Create form data
            const formData = new FormData();
            
            // Add files
            selectedFiles.forEach(file => {
                formData.append('images[]', file);
            });
            
            // Add mode
            formData.append('mode', currentMode);
            
            // Add options based on mode
            if (currentMode === 'optimize') {
                
                formData.append('quality', 100); 
                
                const maxWidth = document.getElementById('maxWidth');
                if (maxWidth && maxWidth.value) {
                    if (maxWidth.value === 'custom') {
                        const customWidth = document.getElementById('customWidth');
                        if (customWidth && customWidth.value) {
                            formData.append('max_width', customWidth.value);
                        }
                    } else {
                        formData.append('max_width', maxWidth.value);
                    }
                }
                
                const createWebp = document.getElementById('createWebp');
                if (createWebp) formData.append('create_webp', createWebp.checked);
                
                const createThumbnail = document.getElementById('createThumbnail');
                if (createThumbnail) formData.append('create_thumbnail', createThumbnail.checked);
                
            } else {
                const outputFormat = document.getElementById('outputFormat');
                if (outputFormat) formData.append('output_format', outputFormat.value);
                
                const convertQuality = document.getElementById('convertQuality');
                formData.append('quality', convertQuality ? convertQuality.value : 100);
                
                const convertThumbnail = document.getElementById('convertThumbnail');
                if (convertThumbnail) formData.append('create_thumbnail', convertThumbnail.checked);
            }

            // Show processing state
            showProcessing();

            // Send request
            uploadWithProgress(formData)
                .then(data => {
                    hideProcessing();
                    if (data && data.success) {
                        showResults(data);
                    } else {
                        throw new Error(data?.error || 'Processing failed');
                    }
                })
                .catch(error => {
                    console.error('Processing error:', error);
                    hideProcessing();
                    hideUploadProgress();
                    showNotification('Processing failed: ' + error.message, 'error');
                });
        }

        function uploadWithProgress(formData) {
            return new Promise((resolve, reject) => {
                const xhr = new XMLHttpRequest();
                
                // Track upload progress
                xhr.upload.addEventListener('progress', (e) => {
                    if (e.lengthComputable) {
                        const percentComplete = Math.round((e.loaded / e.total) * 100);
                        updateUploadProgress(percentComplete);
                    }
                });
                
                // Handle response
                xhr.addEventListener('load', () => {
                    if (xhr.status === 200) {
                        try {
                            const result = JSON.parse(xhr.responseText);
                            resolve(result);
                        } catch (error) {
                            reject(new Error('Failed to parse server response'));
                        }
                    } else {
                        reject(new Error(`Server error: ${xhr.status} - ${xhr.statusText}`));
                    }
                });
                
                // Handle errors
                xhr.addEventListener('error', () => {
                    reject(new Error('Network error occurred. Please check your connection.'));
                });
                
                // Handle timeout
                xhr.addEventListener('timeout', () => {
                    reject(new Error('Upload timed out. Please try again.'));
                });
                
                // Configure and send request
                xhr.timeout = 300000; // 5 minutes
                xhr.open('POST', 'process.php');
                xhr.send(formData);
            });
        }

        function showUploadProgress() {
            selectedFiles.forEach((file, index) => {
                const progressOverlay = document.getElementById(`progress-${index}`);
                if (progressOverlay) {
                    progressOverlay.style.display = 'flex';
                }
            });
        }

        function updateUploadProgress(percentage) {
            selectedFiles.forEach((file, index) => {
                const progressOverlay = document.getElementById(`progress-${index}`);
                if (progressOverlay) {
                    const circle = progressOverlay.querySelector('.progress-ring-circle');
                    const percentageText = progressOverlay.querySelector('.progress-percentage');
                    
                    if (circle && percentageText) {
                        const circumference = 2 * Math.PI * 26; // radius = 26
                        const offset = circumference - (percentage / 100) * circumference;
                        circle.style.strokeDashoffset = offset;
                        percentageText.textContent = percentage + '%';
                    }
                }
            });
        }

        function hideUploadProgress() {
            selectedFiles.forEach((file, index) => {
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
        }

        function showProcessing() {
            const processBtn = document.getElementById('processBtn');
            processBtn.disabled = true;
            
            const modeText = currentMode === 'convert' ? 'Converting' : 'Processing';
            
            processBtn.innerHTML = `
                <div class="spinner" style="
                    width: 20px; height: 20px; border: 2px solid rgba(255,255,255,0.3);
                    border-top: 2px solid white; border-radius: 50%;
                    animation: spin 1s linear infinite;
                "></div>
                ${modeText}...
            `;
        }

        function hideProcessing() {
            const processBtn = document.getElementById('processBtn');
            const processBtnIcon = document.getElementById('processBtnIcon');
            const processBtnText = document.getElementById('processBtnText');
            
            if (processBtn) {
                processBtn.disabled = false;
                
                // Get the correct icon and text based on current mode
                const iconText = currentMode === 'convert' ? '🔄' : '⚡';
                const buttonText = currentMode === 'convert' ? 'Convert Images' : 'Optimize Images';
                
                processBtn.innerHTML = `
                    <span id="processBtnIcon">${iconText}</span>
                    <span id="processBtnText">${buttonText}</span>
                `;
            }
        }

        function showResults(data) {
            // Create results section
            let resultsSection = document.getElementById('resultsSection');
            if (!resultsSection) {
                resultsSection = document.createElement('div');
                resultsSection.id = 'resultsSection';
                resultsSection.className = 'results-section';
                document.querySelector('.main-content').appendChild(resultsSection);
            }

            const isSuccess = data.success && data.data;
            const fileCount = data.type === 'batch' ? data.data.summary.successful : 1;
            const downloadLinks = data.data.download_links || [];

            resultsSection.innerHTML = `
                <div style="padding: var(--space-2xl); text-align: center;">
                    <h2 style="font-size: var(--font-size-3xl); color: var(--gray-800); margin-bottom: var(--space-lg); display: flex; align-items: center; justify-content: center; gap: var(--space-sm);">
                        🎉 ${currentMode === 'convert' ? 'Conversion' : 'Optimization'} Complete!
                    </h2>
                    
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: var(--space-lg); margin-bottom: var(--space-xl); max-width: 600px; margin-left: auto; margin-right: auto;">
                        <div style="text-align: center; padding: var(--space-lg); background: var(--gray-50); border-radius: var(--border-radius-lg); border: 1px solid var(--gray-200);">
                            <div style="font-size: var(--font-size-2xl); font-weight: 700; color: var(--primary-color); margin-bottom: var(--space-xs);">
                                ${fileCount}
                            </div>
                            <div style="font-size: var(--font-size-sm); color: var(--gray-600);">
                                Files ${currentMode === 'convert' ? 'Converted' : 'Optimized'}
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
                            <button class="btn btn-primary" onclick="downloadAll()">
                                📦 Download All Files
                            </button>
                        ` : ''}
                        <button class="btn btn-secondary" onclick="processMore()">
                            🔄 Process More Images
                        </button>
                    </div>

                    <div style="display: grid; gap: var(--space-md); max-width: 800px; margin: 0 auto;">
                        ${downloadLinks.map(link => `
                            <div style="display: flex; align-items: center; justify-content: space-between; background: white; padding: var(--space-md); border-radius: var(--border-radius); border: 1px solid var(--gray-200); box-shadow: var(--shadow-sm);">
                                <div style="display: flex; align-items: center; gap: var(--space-md);">
                                    <div style="font-size: var(--font-size-lg);">
                                        ${getFileIcon(link.format)}
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

        function downloadAll() {
            // This would trigger a batch download - implementation depends on your backend
            showNotification('Batch download feature coming soon!', 'info');
        }

        function processMore() {
            // Reset the application completely
            clearFiles();
            const resultsSection = document.getElementById('resultsSection');
            if (resultsSection) {
                resultsSection.remove();
            }
            
            // Hide any progress overlays
            hideUploadProgress();
            
            // Reset processing button
            hideProcessing();
            
            // Scroll to upload section
            document.getElementById('uploadSection').scrollIntoView({ behavior: 'smooth' });
        }

        function showNotification(message, type = 'info') {
            // Remove existing notifications
            const existingNotifications = document.querySelectorAll('.notification');
            existingNotifications.forEach(notification => notification.remove());
            
            const notification = document.createElement('div');
            notification.className = `notification notification-${type}`;
            notification.style.cssText = `
                position: fixed; top: 20px; right: 20px; z-index: 1000;
                background: white; border-radius: 8px; padding: 16px 20px;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
                border-left: 4px solid ${getNotificationColor(type)};
                max-width: 400px; animation: slideInRight 0.3s ease-out;
            `;
            
            notification.innerHTML = `
                <div style="display: flex; align-items: center; gap: 12px;">
                    <span style="font-size: 18px;">${getNotificationIcon(type)}</span>
                    <span style="flex: 1; font-size: 14px; color: #374151;">${message}</span>
                    <button onclick="this.parentElement.parentElement.remove()" 
                            style="background: none; border: none; font-size: 18px; cursor: pointer; color: #9ca3af; padding: 0; width: 24px; height: 24px;">×</button>
                </div>
            `;
            
            document.body.appendChild(notification);
            
            // Auto remove after 5 seconds
            setTimeout(() => {
                if (notification.parentElement) {
                    notification.remove();
                }
            }, 5000);
        }

        function getNotificationIcon(type) {
            const icons = {
                'info': 'ℹ️',
                'success': '✅',
                'warning': '⚠️',
                'error': '❌'
            };
            return icons[type] || 'ℹ️';
        }

        function getNotificationColor(type) {
            const colors = {
                'info': '#60a5fa',
                'success': '#4ade80',
                'warning': '#fbbf24',
                'error': '#f87171'
            };
            return colors[type] || '#60a5fa';
        }

        function formatBytes(bytes, decimals = 1) {
            if (bytes === 0) return '0 B';
            
            const k = 1024;
            const sizes = ['B', 'KB', 'MB', 'GB', 'TB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            
            return parseFloat((bytes / Math.pow(k, i)).toFixed(decimals)) + ' ' + sizes[i];
        }

        // Initialize quality displays
        //updateQualityDisplay();
        //updateConvertQualityDisplay();
    </script>
</body>
</html>