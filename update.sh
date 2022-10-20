for f in /var/www/html/*; do
    #if f is directory and starts with "site"
    if [ -d "$f" ] && [[ "$f" == *IM_* ]]; then
        #go to folder
        cd "$f"
        #run composer update
        php artisan migrate
        php artisan route:cache
        cd ..
    fi
done