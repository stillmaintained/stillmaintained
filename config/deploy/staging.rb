# Global options
# --------------
set :ssh_options, {
    keys: %w(~/.ssh/deployer),
    forward_agent: false,
    auth_methods: %w(publickey)
}
#
# And/or per server (overrides global)
# ------------------------------------
server '66.175.215.31', user: 'deployer', roles: %w{web app}

ask :branch, proc { `git rev-parse --abbrev-ref HEAD`.chomp }.call
set :deploy_to, '/var/www/stillmaintained_staging'
