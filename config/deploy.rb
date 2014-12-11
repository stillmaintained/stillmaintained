# config valid only for current version of Capistrano
lock '3.3.3'

set :application, 'stillmaintained'
set :repo_url, 'git@github.com:stillmaintained/stillmaintained.git'

# Default deploy_to directory is /var/www/my_app_name
set :deploy_to, '/var/www/stillmaintained'

# Default value for keep_releases is 5
set :keep_releases, 3

set :linked_files, ['tmp/.env']


# Bower components to symlink
# TODO add symlinks to repository
set :component_dirs, [
    {
        source: 'bootstrap/fonts',
        target: 'webroot/fonts/glyphicons'
    },
    {
        source: 'bootstrap/dist/js',
        target: 'webroot/js/bootstrap'
    },
    {
        source: 'jquery/dist',
        target: 'webroot/js/jquery'
    },
    {
        source: 'masonry/dist',
        target: 'webroot/js/masonry'
    }
]

namespace :deploy do
    before 'bower:install', 'npm:install'                  # install npm
    after 'bower:install', 'deploy:symlink:bower'          # symlink bower components
    after 'deploy:updated', 'deploy:assets:build'          # build assets
    after 'deploy:updated', 'deploy:migrations:migrate'    # run migrations
end
