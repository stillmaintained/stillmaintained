require 'sinatra'
require 'omniauth'
require 'mongoid'
require 'httparty'
require 'hoptoad_notifier'

require File.join(File.dirname(__FILE__), 'lib', 'user')
require File.join(File.dirname(__FILE__), 'lib', 'project')

class Application < Sinatra::Base

  use HoptoadNotifier::Rack

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

  error { haml :error }

  get '/' do
    @projects = Project.visible.order_by([:created_at, :desc]).limit(25)

    haml :home
  end

  get '/application.css' do
    sass :'style/application'
  end

  ['/projects.json', '/projects'].each do |path|
    get path do
      if params[:q]
        @projects = Project.search(
          params[:q]
        ).visible.order_by(
          [:watchers, :desc]
        )
        @project_count = @projects.count
      else
        @projects = Project.visible.order_by([:watchers, :desc])

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
    login = request.env['omniauth.auth']['user_info']['nickname']

    result = HTTParty.get("http://github.com/api/v2/json/repos/show/#{login}")

    result['repositories'].select{ |repo| !repo['fork'] }.each do |repo|
      Project.create_or_update_from_github_response(repo)
    end

    result = HTTParty.get("http://github.com/api/v2/json/user/show/#{login}/organizations")
    organizations = result['organizations'].map{|organization| organization['login'] }

    organizations.each do |organization|
      result = HTTParty.get("http://github.com/api/v2/json/organizations/#{organization}/public_repositories")

      result['repositories'].select{ |repo| !repo['fork'] }.each do |repo|
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
        :conditions => {:user => params[:user], :visible => true}
      ).order_by([:watchers, :desc])

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
        @title = "#{@project.name} by #{@project.user}"
        haml :"projects/show"
      end

    end
  end

end
