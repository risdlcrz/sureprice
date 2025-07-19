#!/bin/bash

# SurePrice Deployment Script
# This script helps prepare your Laravel application for production deployment

echo "🚀 Starting SurePrice deployment preparation..."

# Check if we're in the right directory
if [ ! -f "artisan" ]; then
    echo "❌ Error: This doesn't appear to be a Laravel project. Please run this script from the project root."
    exit 1
fi

echo "📦 Installing/updating Node.js dependencies..."
npm install

echo "🔨 Building assets for production..."
npm run build

if [ $? -eq 0 ]; then
    echo "✅ Assets built successfully!"
else
    echo "❌ Asset building failed. Please check the errors above."
    exit 1
fi

echo "📋 Checking if .env file exists..."
if [ ! -f ".env" ]; then
    echo "⚠️  Warning: No .env file found. Please create one from .env.example"
    echo "   cp .env.example .env"
    echo "   php artisan key:generate"
fi

echo "🔧 Running Laravel optimization commands..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "📁 Setting proper permissions..."
chmod -R 755 storage bootstrap/cache

echo "🔗 Creating storage link..."
php artisan storage:link

echo "✅ Deployment preparation complete!"
echo ""
echo "📝 Next steps:"
echo "1. Update your .env file for production settings"
echo "2. Upload files to your hosting server"
echo "3. Run 'php artisan migrate --force' on the server"
echo "4. Set the document root to the 'public' directory"
echo "5. Test your application"
echo ""
echo "📖 For detailed instructions, see DEPLOYMENT.md" 