require 'sinatra'
require 'rack/test'
require 'rspec'
require 'machinist/mongoid'
require 'fakeweb'

ENV['RACK_ENV'] = 'test'

require File.join(File.dirname(__FILE__), '..', 'application.rb')

RSpec.configure do |config|

  config.before(:each) do
    FakeWeb.clean_registry
    [User, Project].each { |model| model.delete_all }
  end

end

# Helper method
def mock_github_api(uri, json, options={})
  options[:rate_limit] ||= 5000
  FakeWeb.register_uri(:get, "https://api.github.com" + uri, body: json.to_json, content_type: 'text/json', 'X-RateLimit-Remaining' => options[:rate_limit])
end

User.blueprint {}
Project.blueprint do
  visible true
end
