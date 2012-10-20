set :output, '/home/stillmaintained/shared/log/cron.log'

every 6.hours do
  rake "github:refresh"
end
