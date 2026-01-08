#!/bin/bash
# Biyai Finance Tracker - Setup Script

echo "================================"
echo "Biyai Finance Tracker - PHP Setup"
echo "================================"
echo ""

# Check if PHP is installed
if ! command -v php &> /dev/null; then
    echo "❌ PHP tidak terinstall!"
    echo "Silakan install PHP 7.4 atau lebih tinggi"
    exit 1
fi

# Get PHP version
php_version=$(php -v | head -n 1)
echo "✅ PHP Found: $php_version"
echo ""

# Create data folder if not exists
if [ ! -d "data" ]; then
    echo "📁 Creating data folder..."
    mkdir -p data
    chmod 755 data
    echo "✅ Data folder created"
fi

# Create empty transaksi.json if not exists
if [ ! -f "data/transaksi.json" ]; then
    echo "📝 Creating transaksi.json..."
    echo "[]" > data/transaksi.json
    echo "✅ Data file created"
fi

# Set permissions
chmod 644 data/transaksi.json

echo ""
echo "================================"
echo "Setup Complete!"
echo "================================"
echo ""
echo "🚀 To start the application:"
echo ""
echo "1. Using PHP built-in server:"
echo "   php -S localhost:8000"
echo ""
echo "2. Then open in browser:"
echo "   http://localhost:8000/login.php"
echo ""
echo "3. Demo Account:"
echo "   Email: admin@example.com"
echo "   Password: admin123"
echo ""
echo "================================"
