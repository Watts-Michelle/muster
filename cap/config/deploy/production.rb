# Simple Role Syntax
# ==================
# Supports bulk-adding hosts to roles, the primary server in each group
# is considered to be the first unless any hosts have the primary
# property set.  Don't declare `role :all`, it's a meta role.

#set :branch, 'master'

#role :web1, %w{muster@muster.live}
#set :deploy_to, '/var/www/muster.live/html'


# Extended Server Syntax
# ======================
# This can be used to drop a more detailed server definition into the
# server list. The second argument is a, or duck-types, Hash and is
# used to set extended properties on the server.
#ask(:password, nil, echo: false)
#server 'muster.live', user: 'muster', roles: %w{web1}, port: 2020