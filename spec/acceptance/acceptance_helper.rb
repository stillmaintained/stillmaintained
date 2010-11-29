require File.dirname(__FILE__) + "/../spec_helper"
require "steak"
require 'capybara'
require 'capybara/dsl'

Rspec.configure do |config|
  config.include Capybara
   Capybara.app = Application
end

# Put your acceptance spec helpers inside /spec/acceptance/support
Dir["#{File.dirname(__FILE__)}/support/**/*.rb"].each {|f| require f}
