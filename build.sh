#!/bin/bash

echo "🚀 Building Laravel Docker Staging..."

# Create docker directory if not exists
mkdir -p docker

# Copy nginx config to docker directory if not exists
if [ ! -f "docker/nginx.conf" ]; then
    echo "⚠️  nginx.conf not found in docker/ directory"
    exit 1
fi

# Check if .env.docker exists
if [ ! -f ".env.docker" ]; then
    echo "⚠️  .env.docker not found! Please create it first."
    exit 1
fi

# Stop existing container
echo "🛑 Stopping existing container..."
docker-compose down

# Build and start
echo "🔨 Building Docker image..."
docker-compose build --no-cache

echo "🚀 Starting container..."
docker-compose up -d

echo "⏳ Waiting for container to be ready..."
sleep 5

echo ""
echo "✅ Laravel staging is running at http://localhost:4567"
echo "📊 Check logs: docker-compose logs -f"
echo "🔑 APP_KEY otomatis di-generate jika belum ada"
echo ""
echo "Database Configuration:"
echo "  Host: host.docker.internal (MySQL di laptop Anda)"
echo "  Update DB credentials di .env.docker sebelum build"