#!/bin/bash
set -e

echo "=========================================="
echo "🚀 [Keuangan Gereja] Starting Deployment"
echo "=========================================="

# 1. Pull image terbaru dari GHCR
echo "📥 1/5 Pulling latest Docker image from GHCR..."
docker compose pull app

# 2. Jalankan database migration ke Supabase
echo "📦 2/5 Running Database Migrations (Supabase)..."
docker compose run --rm app php artisan migrate --force

# 3. Restart container dengan image baru
echo "🔄 3/5 Recreating application container..."
docker compose up -d --remove-orphans app

# 4. Optimasi cache Laravel & Filament
echo "⚡ 4/5 Optimizing Laravel caches..."
docker compose exec -T app php artisan optimize
docker compose exec -T app php artisan filament:cache-components || true

# 5. Membersihkan image usang
echo "🧹 5/5 Cleaning up unused Docker images..."
docker image prune -f

echo "=========================================="
echo "🎉 Deployment successfully finished!"
echo "=========================================="
