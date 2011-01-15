set :application, 'stillmaintained'

default_run_options[:pty] = true

set :scm, :git
set :git_enable_submodules, 1
set :repository, 'git@github.com:jeffkreeftmeijer/stillmaintained.git'
set :branch, 'master'
set :ssh_options, { :forward_agent => true }

set :stage, :production
set :user, 'stillmaintained'
set :use_sudo, false
set :runner, 'deploy'
set :deploy_to, "/home/#{application}"
set :app_server, :passenger
set :domain, '67.23.79.117'

role :app, domain
role :web, domain
role :db, domain, :primary => true

after 'deploy:update_code', 'deploy:symlink_settings'

namespace :deploy do
  task :start, :roles => :app do
    run "touch #{current_release}/tmp/restart.txt"
  end

  task :stop, :roles => :app do
    # Do nothing.
  end

  desc 'Restart Application'
  task :restart, :roles => :app do
    run "touch #{current_release}/tmp/restart.txt"
  end

  desc 'Symlink the settings file'
  task :symlink_settings, :roles => :app do
    run "ln -s #{shared_path}/settings.yml #{current_release}/config/settings.yml"
  end
end
