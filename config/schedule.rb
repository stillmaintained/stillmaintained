job_type :rake,    "cd :path && RACK_ENV=:environment bundle exec rake :task --silent :output"

set :output, '/home/stillmaintained/shared/log/cron.log'

#every 6.hours do
#  rake "github:refresh"
#end
