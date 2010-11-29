require 'bundler'
Bundler.setup

require 'sinatra'
require 'omniauth'
require 'mongoid'

require 'lib/user'

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

    redirect "/user/#{user.id}/edit"
  end

end
