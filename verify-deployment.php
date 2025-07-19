<?php

/**
 * SurePrice Deployment Verification Script
 * Run this script to verify your deployment is properly configured
 */

// Load Laravel environment
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔍 SurePrice Deployment Verification\n";
echo "=====================================\n\n";

// Check Laravel installation
if (!file_exists('artisan')) {
    echo "❌ Error: Laravel not found. Please run this script from the project root.\n";
    exit(1);
}

// Check if .env exists
if (!file_exists('.env')) {
    echo "⚠️  Warning: .env file not found. Please create one from .env.example\n";
} else {
    echo "✅ .env file found\n";
}

// Check if assets are built
if (!file_exists('public/build/.vite/manifest.json')) {
    echo "❌ Error: Assets not built. Run 'npm run build' first.\n";
    exit(1);
} else {
    echo "✅ Assets built successfully\n";
}

// Check storage link
if (!file_exists('public/storage')) {
    echo "⚠️  Warning: Storage link not created. Run 'php artisan storage:link'\n";
} else {
    echo "✅ Storage link created\n";
}

// Check permissions
$storageWritable = is_writable('storage');
$bootstrapWritable = is_writable('bootstrap/cache');

if (!$storageWritable) {
    echo "⚠️  Warning: storage directory not writable\n";
} else {
    echo "✅ storage directory writable\n";
}

if (!$bootstrapWritable) {
    echo "⚠️  Warning: bootstrap/cache directory not writable\n";
} else {
    echo "✅ bootstrap/cache directory writable\n";
}

// Check if key is set
$envContent = file_get_contents('.env');
if (strpos($envContent, 'APP_KEY=base64:') === false) {
    echo "⚠️  Warning: APP_KEY not set. Run 'php artisan key:generate'\n";
} else {
    echo "✅ APP_KEY is set\n";
}

// Check database connection
try {
    $pdo = new PDO(
        'mysql:host=' . env('DB_HOST', '127.0.0.1') . 
        ';port=' . env('DB_PORT', '3306') . 
        ';dbname=' . env('DB_DATABASE', 'sureprice'),
        env('DB_USERNAME', 'root'),
        env('DB_PASSWORD', '')
    );
    echo "✅ Database connection successful\n";
} catch (PDOException $e) {
    echo "❌ Database connection failed: " . $e->getMessage() . "\n";
}

// Check if cache directories exist and are writable
$cacheDirs = [
    'storage/framework/cache',
    'storage/framework/sessions',
    'storage/framework/views',
    'bootstrap/cache'
];

foreach ($cacheDirs as $dir) {
    if (!is_dir($dir)) {
        echo "⚠️  Warning: $dir directory missing\n";
    } elseif (!is_writable($dir)) {
        echo "⚠️  Warning: $dir directory not writable\n";
    } else {
        echo "✅ $dir directory ready\n";
    }
}

// Check asset files
$manifest = json_decode(file_get_contents('public/build/.vite/manifest.json'), true);
if ($manifest) {
    echo "✅ Asset manifest found with " . count($manifest) . " entries\n";
} else {
    echo "❌ Error: Invalid asset manifest\n";
}

echo "\n📋 Summary:\n";
echo "If you see any ❌ errors, fix them before deployment.\n";
echo "If you see any ⚠️  warnings, address them for optimal performance.\n";
echo "If everything shows ✅, your deployment is ready!\n\n";

echo "📖 For detailed deployment instructions, see DEPLOYMENT.md\n";
echo "🚀 For automated deployment preparation, run: bash deploy.sh\n"; 