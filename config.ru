require 'rubygems'
require 'bundler'

Bundler.require

if ENV['RACK_ENV'] == 'production'
  log = File.new("log/sinatra.log", "w")
  STDOUT.reopen(log)
  STDERR.reopen(log)
  STDOUT.sync = true
  STDERR.sync = true
end

require './application'
run Application
