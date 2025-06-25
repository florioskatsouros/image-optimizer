<div align="center">

# 🎨 Image Optimizer Pro

[![Free Alternative](https://img.shields.io/badge/🆓%20FREE%20Alternative%20to-TinyPNG%20%26%20ImageOptim-brightgreen?style=for-the-badge)](https://github.com/florioskatsouros/image-optimizer)
[![GitHub Stars](https://img.shields.io/github/stars/florioskatsouros/gdpr-cookie-consent?style=for-the-badge&color=yellow&label=STARS)](https://github.com/florioskatsouros/gdpr-cookie-consent/stargazers)

[![Bundle Size](https://img.shields.io/badge/SIZE-200KB-brightgreen?style=for-the-badge)](https://github.com/florioskatsouros/image-optimizer)
[![Dependencies](https://img.shields.io/badge/DEPENDENCIES-1-success?style=for-the-badge)](https://github.com/florioskatsouros/image-optimizer)
[![License](https://img.shields.io/badge/LICENSE-MIT-blue?style=for-the-badge)](https://github.com/florioskatsouros/image-optimizer/blob/main/LICENSE)

> **Modern drag & drop interface** • **Up to 90% size reduction** • **Multiple formats** • **Batch processing** • **Privacy-first**

🎯 **The FREE image optimization tool that reduces file sizes up to 90% without losing quality**

[🚀 Quick Start](#-quick-start-30-seconds) • [🎮 Try It Locally](#-try-it-locally) • [📖 Documentation](#-complete-setup-guide)

</div>

---

## 🚀 Why Choose Image Optimizer Pro?

| Feature | This Solution | TinyPNG | Squoosh | ImageOptim | Others |
|---------|---------------|---------|---------|------------|---------|
| **Setup Time** | 🟢 30 seconds | 🟡 Account needed | 🟢 Web only | 🔴 Desktop app | 🔴 Complex |
| **File Size Limit** | 🟢 50MB | 🔴 5MB | 🟡 Variable | 🟢 Unlimited | 🔴 Limited |
| **Batch Processing** | ✅ Unlimited | 🔴 20 files/month | ❌ One by one | ✅ Desktop only | ⚠️ Limited |
| **Privacy** | 🟢 Self-hosted | 🔴 Cloud-based | 🔴 Cloud-based | 🟢 Local | 🔴 Cloud-based |
| **Cost** | 🟢 Free | 🔴 $5-50/month | 🟢 Free | 🟢 Free | 🔴 Paid |
| **Format Support** | ✅ JPG, PNG, WebP, GIF | ✅ JPG, PNG | ✅ Many | ✅ Many | ⚠️ Limited |
| **API Access** | ✅ Included | 🔴 Extra cost | ❌ No | ❌ No | 🔴 Paid |

## 💰 Cost Comparison (Annual Savings)

| Solution | Monthly Files | **Annual Cost** | **Your Savings** |
|----------|---------------|-----------------|-------------------|
| **Image Optimizer Pro** | ∞ Unlimited | **$0** | - |
| TinyPNG Pro | 10,000 | **$300** | 💰 **Save $300** |
| Kraken.io Pro | 10,000 | **$540** | 💰 **Save $540** |
| ImageOptim API | 10,000 | **$120** | 💰 **Save $120** |
| Cloudinary | 25GB storage | **$1,200** | 💰 **Save $1,200** |

**💡 Total potential savings: $120-1,200 per year**

## 📊 Performance Stats

```
📸 Real Example Results:
Original: vacation.jpg (2.5MB, 4032×3024)
Optimized: vacation.jpg (340KB, 4032×3024)
Result: 86% smaller, same visual quality! 🎉
```

*Used by photographers, web developers, and businesses worldwide*

## ⚡ Quick Start (30 seconds)

### 1. Download & Extract
```bash
# Option 1: Download ZIP
wget https://github.com/florioskatsouros/image-optimizer/archive/main.zip
unzip main.zip

# Option 2: Git Clone
git clone https://github.com/florioskatsouros/image-optimizer.git
cd image-optimizer
```

### 2. Install Dependencies
```bash
composer install
```

### 3. Setup Permissions
```bash
chmod 755 uploads optimized temp logs
```

### 4. Start Using It!
```bash
# For local testing
php -S localhost:8000

# Then visit http://localhost:8000 and start optimizing! 🎉
```

## 🎮 Try It Locally

**This IS the demo!** No complex setup required:

### One-liner setup:
```bash
git clone https://github.com/florioskatsouros/image-optimizer.git && cd image-optimizer && composer install && php -S localhost:8000
```

Then open `http://localhost:8000` and start optimizing! 🚀

**This approach is BETTER than online demos because:**
- ✅ Shows how easy setup is
- ✅ Test with your own images
- ✅ No privacy concerns with uploads  
- ✅ Full feature access
- ✅ Works offline

| Demo Type | Description | How to Access |
|-----------|-------------|---------------|
| **🎯 Full Demo** | Complete drag & drop interface | `git clone` + `php -S localhost:8000` |
| **📱 Mobile Demo** | Responsive design on mobile | Same as above, test on phone |
| **🔗 API Demo** | REST API integration | See API examples below |
| **⚡ Batch Demo** | Upload multiple images | Drag multiple files to interface |

## ✨ Features That Actually Work

### 🎯 Core Features
- **🗜️ Smart Compression** - Up to 90% size reduction without quality loss
- **🎨 Multiple Formats** - JPG, PNG, WebP, GIF support with auto-conversion
- **📦 Batch Processing** - Upload and optimize dozens of images at once
- **📱 Mobile Optimized** - Perfect drag & drop experience on all devices
- **🔒 Privacy First** - Images processed locally, auto-deleted after 24 hours

### 🛡️ Advanced Features
- **⚡ Real-time Progress** - Live upload and processing feedback
- **🎯 Smart Resizing** - Automatic dimension optimization for web use
- **📊 Before/After Comparison** - Visual quality comparison with file size savings
- **🔄 Format Conversion** - Automatic WebP generation for modern browsers
- **📐 Custom Quality Settings** - Fine-tune compression levels (20-100%)

### 🎨 Developer Features
- **🔌 REST API** - Integrate with any application or workflow
- **📋 JSON Responses** - Structured data for easy integration
- **🎮 Webhook Support** - Get notified when processing completes
- **📊 Usage Analytics** - Track optimization statistics
- **🛠️ Easy Customization** - Modify settings, themes, and behavior

## 🔧 Installation Guide

### 📋 Requirements
- **PHP 8.0+** (with GD extension)
- **Apache/Nginx** web server
- **50MB+** free disk space
- **Composer** for dependency management

### 🚀 Detailed Setup

#### 1. Server Requirements Check
```bash
# Check PHP version
php --version

# Check required extensions
php -m | grep -E "(gd|fileinfo|zip)"

# Check Composer
composer --version
```

#### 2. Download and Install
```bash
# Clone repository
git clone https://github.com/florioskatsouros/image-optimizer.git
cd image-optimizer

# Install PHP dependencies
composer install

# Set proper permissions
chmod 755 uploads optimized temp logs
chmod 644 .htaccess
```

#### 3. Web Server Configuration

**For Apache** (automatic via .htaccess):
```apache
# Already configured! Just ensure mod_rewrite is enabled
a2enmod rewrite
systemctl restart apache2
```

**For Nginx** (add to server block):
```nginx
location / {
    try_files $uri $uri/ /index.php?$query_string;
}

location ~ \.php$ {
    fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
    fastcgi_index index.php;
    include fastcgi_params;
}

# Increase upload limits
client_max_body_size 50M;
```

#### 4. PHP Configuration
```ini
# Add to php.ini or .htaccess
upload_max_filesize = 50M
post_max_size = 200M
max_execution_time = 300
memory_limit = 512M
max_file_uploads = 20
```

## 📖 Usage Examples

### 🖱️ Web Interface Usage

#### Basic Optimization
1. **📤 Upload Images** - Drag & drop or click to browse
2. **⚙️ Choose Settings** - Quality, format, resize options
3. **⚡ Process** - Watch real-time progress
4. **📥 Download** - Get optimized images instantly

#### Advanced Options
```
Quality Settings:
• Maximum (95%) - Minimal compression, largest files
• High (85%) - Balanced quality and size
• Good (75%) - Recommended for most photos  
• Web (65%) - Optimized for websites
• Small (50%) - Maximum compression

Format Options:
• Keep Original - Maintain input format
• Auto WebP - Generate modern WebP versions
• Custom - Choose specific output format
```

### 🔌 API Integration

#### Basic API Usage
```php
// Single image optimization
$curl = curl_init();
curl_setopt_array($curl, [
    CURLOPT_URL => "https://your-domain.com/process.php",
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => [
        'images' => new CURLFile('path/to/image.jpg'),
        'quality' => 80,
        'create_webp' => 'true'
    ],
    CURLOPT_RETURNTRANSFER => true
]);

$response = curl_exec($curl);
$result = json_decode($response, true);

if ($result['success']) {
    echo "Optimized! Saved: " . $result['data']['savings'] . "%";
    echo "Download: " . $result['data']['download_links'][0]['url'];
}
```

#### JavaScript Integration
```javascript
// Upload with progress tracking
const formData = new FormData();
formData.append('images[]', file);
formData.append('quality', 80);

fetch('/process.php', {
    method: 'POST',
    body: formData
})
.then(response => response.json())
.then(result => {
    if (result.success) {
        console.log(`Saved ${result.data.savings}%`);
        // Download optimized image
        window.open(result.data.download_links[0].url);
    }
});
```

#### Batch Processing API
```python
import requests

# Python example for batch processing
files = [
    ('images[]', open('photo1.jpg', 'rb')),
    ('images[]', open('photo2.jpg', 'rb')),
    ('images[]', open('photo3.jpg', 'rb'))
]

data = {
    'quality': 75,
    'create_webp': 'true',
    'max_width': 1920
}

response = requests.post(
    'https://your-domain.com/process.php',
    files=files,
    data=data
)

result = response.json()
if result['success']:
    print(f"Processed {result['data']['summary']['total_files']} images")
    print(f"Total savings: {result['data']['summary']['total_savings']}")
```

## 🔧 API Reference

### 📤 Upload Endpoint
**POST** `/process.php`

#### Parameters
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `images[]` | File | ✅ | Image files to optimize |
| `quality` | Integer | ❌ | Compression quality (20-100, default: 80) |
| `max_width` | Integer | ❌ | Maximum width in pixels |
| `max_height` | Integer | ❌ | Maximum height in pixels |
| `create_webp` | Boolean | ❌ | Generate WebP version (default: true) |
| `create_thumbnail` | Boolean | ❌ | Generate thumbnail (default: true) |
| `format` | String | ❌ | Output format (jpg, png, webp) |

#### Response Format
```json
{
    "success": true,
    "type": "single|batch",
    "data": {
        "original": {
            "name": "vacation.jpg",
            "size": 2621440,
            "size_human": "2.5MB",
            "width": 4032,
            "height": 3024
        },
        "optimized": [
            {
                "format": "jpg",
                "filename": "vacation_abc123_optimized.jpg",
                "size": 358400,
                "size_human": "350KB",
                "savings": 86.3
            },
            {
                "format": "webp",
                "filename": "vacation_abc123_optimized.webp",
                "size": 294912,
                "size_human": "288KB",
                "savings": 88.7
            }
        ],
        "download_links": [
            {
                "format": "jpg",
                "filename": "vacation_abc123_optimized.jpg",
                "url": "download.php?file=vacation_abc123_optimized.jpg",
                "size": "350KB",
                "savings": 86.3
            }
        ]
    },
    "stats": {
        "processing_time": 2.34,
        "memory_used": 67108864,
        "files_processed": 1
    }
}
```

### 📥 Download Endpoint
**GET** `/download.php`

#### Single File Download
```
GET /download.php?file=filename.jpg
```

#### Batch Download (ZIP)
```
GET /download.php?batch=true&files=["file1.jpg","file2.jpg"]
```

## 🎨 Customization

### 🎭 Theme Customization
```css
/* assets/style.css - Customize colors */
:root {
    --primary-color: #667eea;
    --secondary-color: #764ba2;
    --success-color: #4ade80;
    --error-color: #f87171;
    --border-radius: 8px;
}

/* Custom upload zone */
.upload-zone {
    background: linear-gradient(135deg, #your-color1, #your-color2);
    border: 3px dashed #your-border-color;
}

/* Custom progress bars */
.progress-fill {
    background: linear-gradient(90deg, #your-start-color, #your-end-color);
}
```

### ⚙️ Configuration Options
```php
// src/ImageOptimizer.php - Modify settings
class ImageOptimizer {
    private $maxFileSize = 50 * 1024 * 1024; // 50MB
    private $allowedTypes = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
    private $defaultQuality = 80;
    
    // Add custom formats
    private $customFormats = [
        'thumbnail' => ['width' => 300, 'height' => 300],
        'mobile' => ['width' => 800, 'height' => null],
        'desktop' => ['width' => 1920, 'height' => null]
    ];
}
```

## 🌐 Integration Examples

### WordPress Integration
```php
// functions.php
function enqueue_image_optimizer() {
    wp_enqueue_script('image-optimizer', 
        get_template_directory_uri() . '/assets/app.js');
}
add_action('wp_enqueue_scripts', 'enqueue_image_optimizer');

// Shortcode for WordPress
function image_optimizer_shortcode() {
    return '<div id="image-optimizer-widget"></div>';
}
add_shortcode('image_optimizer', 'image_optimizer_shortcode');
```

### React/Next.js Integration
```jsx
import { useState, useCallback } from 'react';

const ImageOptimizer = () => {
    const [files, setFiles] = useState([]);
    const [optimizing, setOptimizing] = useState(false);

    const onDrop = useCallback(async (acceptedFiles) => {
        setOptimizing(true);
        
        const formData = new FormData();
        acceptedFiles.forEach(file => {
            formData.append('images[]', file);
        });
        
        const response = await fetch('/api/optimize', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        setOptimizing(false);
        
        // Handle results
    }, []);

    return (
        <div className="image-optimizer">
            {/* Your optimization UI */}
        </div>
    );
};

export default ImageOptimizer;
```

## 📱 Browser Support

| Browser | Minimum Version | Features Supported |
|---------|-----------------|-------------------|
| **Chrome** | 60+ | ✅ All features |
| **Firefox** | 55+ | ✅ All features |
| **Safari** | 12+ | ✅ All features |
| **Edge** | 79+ | ✅ All features |
| **Mobile Safari** | 12+ | ✅ Drag & drop, touch |
| **Chrome Mobile** | 60+ | ✅ All features |
| **Samsung Internet** | 8.0+ | ✅ All features |

## 🔒 Security Features

### 🛡️ Built-in Security
- **File Type Validation** - Only allows safe image formats
- **Size Limits** - Prevents oversized file uploads
- **Path Traversal Protection** - Prevents directory escape attacks
- **CSRF Protection** - Secure form submissions
- **Auto Cleanup** - Removes files after 24 hours
- **No Script Execution** - Upload directories secured against PHP execution

### 🔐 Security Headers
```apache
# Automatically applied via .htaccess
X-Content-Type-Options: nosniff
X-Frame-Options: DENY
X-XSS-Protection: 1; mode=block
Content-Security-Policy: default-src 'self'
```

## 🚀 Performance Optimization

### ⚡ Speed Optimizations
- **GZIP Compression** - Reduces bandwidth usage
- **Browser Caching** - Static assets cached for 30 days
- **Image Processing** - Optimized memory usage
- **Progress Streaming** - Real-time upload feedback
- **Batch Processing** - Efficient handling of multiple files

### 📊 Performance Metrics
```
Average Processing Times:
• Single 2MB image: ~2 seconds
• Batch of 10 images: ~15 seconds
• Memory usage: ~64MB per 5MB image
• Upload progress: Real-time updates
```

## 🐛 Troubleshooting

### Common Issues

#### Upload Fails
```bash
# Check file permissions
ls -la uploads/ optimized/

# Fix permissions
chmod 755 uploads optimized temp logs
chown www-data:www-data uploads optimized temp logs
```

#### Large File Issues
```ini
# Increase PHP limits in .htaccess or php.ini
upload_max_filesize = 100M
post_max_size = 500M
max_execution_time = 600
memory_limit = 1G
```

#### Processing Timeout
```php
// Increase timeout in process.php
set_time_limit(600); // 10 minutes
ini_set('memory_limit', '1G');
```

### 📝 Error Codes
| Code | Description | Solution |
|------|-------------|----------|
| **400** | Invalid file type | Use JPG, PNG, WebP, or GIF |
| **413** | File too large | Reduce file size or increase limits |
| **500** | Processing failed | Check server logs, increase memory |
| **503** | Server overloaded | Wait and retry, check server resources |

## 🏗️ Development

### 📁 Project Structure
```
image-optimizer/
├── 📄 index.php            # Main interface (ROOT)
├── 📄 process.php          # Image processing API
├── 📄 download.php         # File downloads
├── 📂 assets/              # CSS, JS, images
│   ├── style.css          # Main stylesheet
│   └── app.js             # JavaScript app
├── 📂 src/                 # Core PHP classes
│   └── ImageOptimizer.php  # Main optimization logic
├── 📂 uploads/             # Temporary uploads
├── 📂 optimized/           # Processed images
├── 📂 temp/                # Temporary files
├── composer.json          # Dependencies
├── .htaccess             # Server configuration
└── README.md             # This file
```

### 🛠️ Development Setup
```bash
# Clone for development
git clone https://github.com/florioskatsouros/image-optimizer.git
cd image-optimizer

# Install dependencies
composer install

# Start development server
php -S localhost:8000

# Run tests (if available)
composer test
```

### 🧪 Testing
```bash
# Test single image optimization
curl -X POST http://localhost:8000/process.php \
  -F "images[]=@test-image.jpg" \
  -F "quality=80"

# Test batch processing
curl -X POST http://localhost:8000/process.php \
  -F "images[]=@image1.jpg" \
  -F "images[]=@image2.jpg" \
  -F "quality=75"
```

## 📄 License & Credits

### 📜 License
This project is licensed under the **MIT License** - see the [LICENSE](LICENSE) file for details.

### 🙏 Credits & Acknowledgments
- **[Intervention Image](http://image.intervention.io/)** - Powerful PHP image processing library
- **Modern CSS Grid & Flexbox** - For responsive layouts
- **Vanilla JavaScript ES6+** - For modern browser compatibility
- **PHP 8+ Features** - For improved performance and security


## ⭐ Show Your Support

If this project helped you optimize your images, please give it a ⭐ on GitHub!

[![GitHub stars](https://img.shields.io/github/stars/florioskatsouros/image-optimizer?style=social)](https://github.com/florioskatsouros/image-optimizer/stargazers)


---

<div align="center">

**Made with ❤️ by [Florios Katsouros](https://github.com/florioskatsouros)**


*Built for developers, designers, and content creators who value quality and performance*

</div>
