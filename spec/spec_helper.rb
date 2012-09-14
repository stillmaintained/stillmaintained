require 'sinatra'
require 'rack/test'
require 'rspec'
require 'machinist/mongoid'

require File.join(File.dirname(__FILE__), '..', 'application.rb')

set :environment, :test

RSpec.configure do |config|

  config.before(:each) do
    [User, Project].each { |model| model.delete_all }
  end

end

User.blueprint {}
Project.blueprint do
  visible true
end
