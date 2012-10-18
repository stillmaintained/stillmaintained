require 'bundler/setup'
require 'rspec/core/rake_task'

RSpec::Core::RakeTask.new do |t|
  t.pattern = "spec/**/*_spec.rb"
  t.rspec_opts = '--color --format progress'
  t.verbose = false
end

namespace :github do
  desc "Refresh users and projects from github"
  task :refresh do
    require './application'
    GithubImporter.update_users
  end
end

task :default => [:spec]
