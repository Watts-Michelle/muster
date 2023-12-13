# Simple Role Syntax
# ==================
# Supports bulk-adding hosts to roles, the primary server in each group
# is considered to be the first unless any hosts have the primary
# property set.  Don't declare `role :all`, it's a meta role.

set :branch, 'develop'
set :application, 'Muster'

role :web1, %w{muster@muster.dev.flipsidegroup.com}
set :deploy_to, '/var/www/muster.dev/html'