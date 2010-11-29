require File.join(File.dirname(__FILE__), '..', 'application.rb')

require 'sinatra'
require 'rack/test'
require 'rspec'

set :environment, :test

Rspec.configure do |config|
end
