mkdir $1
ln -s $PWD/app $1/app
ln -s $PWD/artisan $1/artisan
ln -s $PWD/composer.json $1/composer.json
ln -s $PWD/composer.lock $1/composer.lock
ln -s $PWD/config $1/config
ln -s $PWD/database $1/database
ln -s $PWD/fix.sh $1/fix.sh
ln -s $PWD/package.json $1/package.json
ln -s $PWD/package-lock.json $1/package-lock.json
ln -s $PWD/phpunit.xml $1/phpunit.xml
ln -s $PWD/README.md $1/README.md
ln -s $PWD/resources $1/resources
ln -s $PWD/routes $1/routes
ln -s $PWD/schema.sql $1/schema.sql
ln -s $PWD/server.php $1/server.php
ln -s $PWD/tailwind.config.js $1/tailwind.config.js
ln -s $PWD/test1.txt $1/test1.txt
ln -s $PWD/tests $1/tests
ln -s $PWD/vendor $1/vendor
ln -s $PWD/webpack.config.js $1/webpack.config.js
ln -s $PWD/webpack.mix.js $1/webpack.mix.js
cp $PWD/env-template $1/.env

#replace file placeholder with project name
sed -i "s/P_DBNAME/$2/g" $1/.env
sed -i "s/P_DBUSER/$3/g" $1/.env
sed -i "s/P_DBPASSWORD/$4/g" $1/.env

cd $1
php artisan migrate
php artisan db:seed
php artisan route:cache
