require 'sinatra'
require 'rack/test'
require 'rspec'
require 'machinist/mongoid'
require 'fakeweb'

require File.join(File.dirname(__FILE__), '..', 'application.rb')

set :environment, :test

RSpec.configure do |config|

  config.before(:each) do
    [User, Project].each { |model| model.delete_all }
  end

end

# Helper method
def mock_github_api(uri, json)
  FakeWeb.register_uri(:get, "https://api.github.com" + uri, body: json.to_json, content_type: 'text/json')
end

User.blueprint {}
Project.blueprint do
  visible true
end
