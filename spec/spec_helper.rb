require File.join(File.dirname(__FILE__), '..', 'application.rb')

require 'sinatra'
require 'rack/test'
require 'rspec'

set :environment, :test

Rspec.configure do |config|

  config.before(:each) do
    [User, Project].each { |model| model.delete_all }
  end

end
