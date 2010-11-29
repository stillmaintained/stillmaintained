require 'bundler'
Bundler.setup

require 'sinatra'
require 'omniauth'
require 'mongoid'
require 'httparty'

require File.join(File.dirname(__FILE__), 'lib', 'user')
require File.join(File.dirname(__FILE__), 'lib', 'project')

class Application < Sinatra::Base

  config = YAML::load_file('config/settings.yml')

  configure do
    Mongoid.database = Mongo::Connection.new(
      config['database']['host'],
      config['database']['port']
    ).db(config['database']['database'])
  end

  use OmniAuth::Builder do
    provider :github, config['github']['id'], config['github']['secret']
  end

  get '/' do
    '<h1>Still Maintained?</h1>

    <a href="/auth/github">Log in via Github</a>'
  end

  get '/auth/github/callback' do
    login = request.env['omniauth.auth']['user_info']['nickname']
    user = User.find_or_create_by(:login => login)

    result = HTTParty.get("http://github.com/api/v2/json/repos/show/#{user.login}")
    result['repositories'].each do |repository|
      Project.create!(:name => repository['name'], :user => user.login, :visible => false)
    end

    redirect "/user/#{user.id}/edit"
  end

end
