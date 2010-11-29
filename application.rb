require 'bundler'
Bundler.setup

require 'sinatra'

class Application < Sinatra::Base
  get '/' do
    '<h1>Still Maintained?</h1>'
  end
end
