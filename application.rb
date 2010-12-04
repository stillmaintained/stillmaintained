require 'sinatra'
require 'sinatra/respond_to'

require 'omniauth'
require 'mongoid'
require 'httparty'
require 'hoptoad_notifier'


require File.join(File.dirname(__FILE__), 'lib', 'user')
require File.join(File.dirname(__FILE__), 'lib', 'project')

class Application < Sinatra::Base
  register Sinatra::RespondTo

  use HoptoadNotifier::Rack
  enable :raise_errors

  config = YAML::load_file(File.join(File.dirname(__FILE__), 'config/settings.yml'))

  HoptoadNotifier.configure do |hoptoad|
    hoptoad.api_key = config['hoptoad']['key']
  end

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
    @projects = Project.visible.order_by([:created_at, :desc]).limit(25)

    haml :home
  end

  get '/application' do
    sass :'style/application'
  end

  get '/projects' do
    @projects = Project.visible.order_by([:watchers, :desc])
    respond_to do |wants|
      wants.html { haml :'projects/index' }
      wants.json { @projects.to_json }
    end
  end

  get '/auth/github/callback' do
    login = request.env['omniauth.auth']['user_info']['nickname']
    user = User.find_or_create_by(:login => login)

    result = HTTParty.get("http://github.com/api/v2/json/repos/show/#{user.login}")

    result['repositories'].select{ |repo| !repo['fork'] }.each do |repo|
      Project.create_or_update_from_github_response(repo)
    end

    redirect "/users/#{user.id}/edit"
  end

  get '/users/:id/edit' do
    @user = User.find(params[:id])
    @projects = Project.all(:conditions => {:user => @user.login})

    haml :'users/edit'
  end

  post '/users/:id' do
    @user = User.find(params[:id])

    params['projects'].each do |name, state|
      project = Project.first(:conditions => {:user => @user.login, :name => name})
      project.update_attributes(:state => state, :visible => true)
    end

    redirect "/#{@user.login}"
  end

  get '/:user' do
    @projects = Project.all(
      :conditions => {:user => params[:user], :visible => true}
    ).order_by([:watchers, :desc])

    @title = params[:user]
    respond_to do |wants|
      wants.html { haml :'projects/index' }
      wants.json { @projects.to_json }
    end
  end

  get '/:user/:project' do
    @project = Project.first(:conditions => {:user => params[:user], :name => params[:project], :visible => true})
    @title = "#{@project.name} by #{@project.user}"

    respond_to do |wants|
      wants.html { haml :"projects/show" }
      wants.json { @project.to_json }
    end

  end
end
