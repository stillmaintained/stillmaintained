require 'sinatra'
require 'omniauth'
require 'mongoid'
require 'httparty'
require 'airbrake'
require 'will_paginate'
require 'will_paginate/array'

require File.join(File.dirname(__FILE__), 'lib', 'user')
require File.join(File.dirname(__FILE__), 'lib', 'project')
require File.join(File.dirname(__FILE__), 'lib', 'github_importer')
Rack::Mime::MIME_TYPES.merge!(".safariextz" => "application/x-safari-extension")

class Application < Sinatra::Base
  set :logging, true
  set :environment, ENV['RACK_ENV'] || 'development'
  set :root, File.dirname(__FILE__)

  use Airbrake::Rack

  config = YAML::load_file(File.join(File.dirname(__FILE__), 'config/settings.yml'))

  Airbrake.configure do |airbrake_config|
    airbrake_config.api_key = config['airbrake']['key']
  end

  configure do
    Mongoid.load! File.join(File.dirname(__FILE__), 'config/mongoid.yml')
  end

  use Rack::Session::Cookie
  use OmniAuth::Strategies::Developer
  use OmniAuth::Builder do
    provider :github, config['github']['id'], config['github']['secret']
  end
  GithubImporter.config config['github']['id'], config['github']['secret']

  error { haml :error }

  get '/' do
    @projects = Project.visible.no_forks.order_by([:created_at, :desc]).limit(25)

    haml :home
  end

  get '/application.css' do
    sass :'style/application'
  end

  ['/projects.json', '/projects'].each do |path|
    get path do
      if params[:q]
        @projects = Project.search_by_name(
          params[:q]
        ).visible.no_forks.order_by(
          [:watchers, :desc]
        )
        @project_count = @projects.count
      else
        @projects = Project.visible.no_forks.order_by([:watchers, :desc])

        if params[:state] && %w{maintained searching abandoned}.include?(params[:state])
          @projects = @projects.where(:state => params[:state])
        end

        @project_count = @projects.count

        @projects = @projects.paginate(
          :per_page => 100,
          :page => params[:page]
        )
      end

      case path
        when /\.json$/ then @projects.to_json
        else haml :"projects/index"
      end
    end
  end

  get '/auth/github/callback' do
    login = request.env['omniauth.auth']['info']['nickname']

    token = request.env['omniauth.auth']['credentials']['token']
    email = request.env['omniauth.auth']['info']['email']

    user = User.find_or_create_by(login: login)
    user.update_attributes!(token: token, email: email)

    GithubImporter.update_user_and_projects user

    redirect "/users/#{user.id}/edit"
  end

  get '/users/:id/edit' do
    @user = User.find(params[:id])
    @projects = Project.where(user: @user.login)
    @user.organizations.each do |organization|
      @projects |= Project.where(user: organization)
    end
    haml :'users/edit'
  end

  post '/users/:id' do
    params['projects'].each do |user, projects|
      projects.each do |name, state|
        project = Project.where(user: user, name: name).first
        project.update_attributes(:state => state, :visible => state != 'hide')
      end
    end

    redirect "/#{User.find(params[:id]).login}"
  end

  ['/:user.json', '/:user'].each do |path|
    get path do
      @projects = Project.where(user: params[:user]).visible.order_by([:watchers, :desc])

      @title = params[:user]

      case path
        when /\.json$/ then @projects.to_json
        else haml :"projects/index"
      end
    end
  end

  ['/:user/:project.png', '/:user/:project.json', '/:user/:project'].each do |path|
    get path do
      @project = Project.where(user: params[:user], name: params[:project], visible: true).first

      case path
      when /\.png$/
        send_file("public/images/#{@project ? @project.state : 'unknown'}.png")
      when /\.json$/
        @project.to_json
      else
        if @project
          @title = "#{@project.name} by #{@project.user}"
          haml :"projects/show"
        elsif Project.where(:user => params[:user]).count > 0
          haml :project_missing
        else
          haml :not_found
        end
      end

    end
  end

end
