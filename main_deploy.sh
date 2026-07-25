set -e
echo "Deploying the application..."

php artisan down
echo "Putting the application into maintenance mode..."

git pull origin ${1:-main}
echo "Pulling the latest changes from the branch: ${1:-main}..."

php artisan migrate --force
echo "Running database migrations..."

php artisan up
echo "Bringing the application back up..."

chmod -R 775 storage bootstrap/cache
echo "Setting permissions..."

php artisan optimize:clear
echo "Clearing caches..."
php artisan config:cache
echo "Caching configuration..."

echo "Deployment completed successfully!"