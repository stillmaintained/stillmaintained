require File.join(File.dirname(__FILE__), 'application.rb')

set :environment, :production
disable :run

use Rack::ShowExceptions
use Rack::Static, :urls => [ '/favicon.ico', '/css' ], :root => "public"

$LOAD_PATH.unshift("#{File.dirname(__FILE__)}/lib")
Dir.glob("#{File.dirname(__FILE__)}/lib/*.rb") { |lib| require File.basename(lib, '.*') }

FileUtils.mkdir_p 'log' unless File.exists?('log')
log = File.new("log/sinatra.log", "a")
$stdout.reopen(log)
$stderr.reopen(log)

run Application
