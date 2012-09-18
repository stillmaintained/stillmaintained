require 'sinatra'
require 'omniauth'
require 'mongoid'
require 'httparty'
require 'airbrake'
require 'will_paginate'
require 'will_paginate/array'

require File.join(File.dirname(__FILE__), 'lib', 'user')
require File.join(File.dirname(__FILE__), 'lib', 'project')
Rack::Mime::MIME_TYPES.merge!(".safariextz" => "application/x-safari-extension")


class Application < Sinatra::Base
  set :root, File.dirname(__FILE__)

  use Airbrake::Rack

  config = YAML::load_file(File.join(File.dirname(__FILE__), 'config/settings.yml'))

  Airbrake.configure do |airbrake_config|
    airbrake_config.api_key = config['airbrake']['key']
  end

  configure do
    Mongoid.database = Mongo::Connection.new(
      config['database']['host'],
      config['database']['port']
    ).db(config['database']['database'])
  end

  use Rack::Session::Cookie
  use OmniAuth::Strategies::Developer
  use OmniAuth::Builder do
    provider :github, config['github']['id'], config['github']['secret']
  end

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

    result = HTTParty.get("https://api.github.com/users/#{login}/repos")

    result.each do |repo|
      Project.create_or_update_from_github_response(repo)
    end

    result = HTTParty.get("https://api.github.com/users/#{login}/orgs")
    organizations = result.map{|organization| organization['login'] }

    organizations.each do |organization|
      result = HTTParty.get("https://api.github.com/orgs/#{organization}/repos")

      result.each do |repo|
        Project.create_or_update_from_github_response(repo)
      end
    end

    user = User.find_or_create_by(:login => login, :organizations => organizations)
    redirect "/users/#{user.id}/edit"
  end

  get '/users/:id/edit' do
    @user = User.find(params[:id])
    @projects = Project.all(:conditions => {:user => @user.login})
    @user.organizations.each do |organization|
      @projects |= Project.all(:conditions => {:user => organization})
    end
    haml :'users/edit'
  end

  post '/users/:id' do
    params['projects'].each do |user, projects|
      projects.each do |name, state|
        project = Project.first(:conditions => {:user => user, :name => name})
        project.update_attributes(:state => state, :visible => state != 'hide')
      end
    end

    redirect "/#{User.find(params[:id]).login}"
  end

  ['/:user.json', '/:user'].each do |path|
    get path do
      @projects = Project.all(
        :conditions => {:user => params[:user]}
      ).visible.order_by([:watchers, :desc])

      @title = params[:user]

      case path
        when /\.json$/ then @projects.to_json
        else haml :"projects/index"
      end
    end
  end

  ['/:user/:project.png', '/:user/:project.json', '/:user/:project'].each do |path|
    get path do
      @project = Project.first(:conditions => {:user => params[:user], :name => params[:project], :visible => true})

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
