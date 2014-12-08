#!/bin/bash

# install dependencies
cd /vagrant; npm install
cd /vagrant; bower install
cd /vagrant; composer install

# symlink bower components' assets
ln -s /vagrant/component/bootstrap/dist/css /vagrant/webroot/css/bootstrap
ln -s /vagrant/component/bootstrap/dist/js /vagrant/webroot/js/bootstrap
ln -s /vagrant/component/jquery/dist /vagrant/webroot/js/jquery
ln -s /vagrant/component/masonry/dist /vagrant/webroot/js/masonry

# put application in debug mode
touch .debug

# run database migrations
cd /vagrant; bin/cake Migrations.migrations migrate
