require 'bundler'
Bundler.setup

require 'sinatra'
require 'omniauth'
require 'mongoid'
require 'httparty'

require File.join(File.dirname(__FILE__), 'lib', 'user')
require File.join(File.dirname(__FILE__), 'lib', 'project')

class Application < Sinatra::Base

  config = YAML::load_file(File.join(File.dirname(__FILE__), 'config/settings.yml'))

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
    haml :home
  end

  get '/projects' do
    @projects = Project.all(:conditions => {:visible => true})

    haml :'projects/index'
  end

  get '/application.css' do
    sass :'style/application'
  end


  get '/auth/github/callback' do
    login = request.env['omniauth.auth']['user_info']['nickname']
    user = User.find_or_create_by(:login => login)

    result = HTTParty.get("http://github.com/api/v2/json/repos/show/#{user.login}")

    result['repositories'].select{ |repository| !repository['fork'] }.each do |repository|
      if project = Project.first(:conditions => {:name => repository['name'], :user => user.login})
        project.update_attributes(:description => repository['description'])
      else
        Project.create!(:name => repository['name'], :description => repository['description'], :user => user.login, :visible => false)
      end
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
    @projects = Project.all(:conditions => {:user => params[:user], :visible => true})
    @title = params[:user]

    haml :'projects/index'
  end

  get '/:user/:project' do
    @project = Project.first(:conditions => {:user => params[:user], :name => params[:project], :visible => true})
    @title = "#{@project.name} by #{@project.user}"

    haml :"projects/show/#{@project.state}"
  end
end
