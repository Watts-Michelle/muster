# config valid only for current version of Capistrano
lock '3.8.1'
#logger.level = Logger::IMPORTANT

set :repo_url, 'git@dev-git.flipsidegroup.com:michelle.flipsidegroup/muster-api.git'

# Default branch is :master
# ask :branch, proc { `git rev-parse --abbrev-ref HEAD`.chomp }.call

# Default value for :scm is :git
set :scm, :git

# dirs we want symlinking to shared
set :linked_dirs, %w{assets pma cometchat}

# Default value for :format is :pretty
# set :format, :pretty

# Default value for :log_level is :debug
# set :log_level, :debug

# Default value for :pty is false
# set :pty, true

# Default value for :linked_files is []
#set :linked_files, fetch(:linked_files, []).push('cometchat/integration.php')

# Default value for default_env is {}
# set :default_env, { path: "/opt/ruby/bin:$PATH" }

# Default value for keep_releases is 5
set :keep_releases, 10

namespace :deploy do
  after :restart, :clear_cache do
    on roles(:web1, :web2), in: :groups, limit: 3, wait: 10 do

      #this stuff runs last
      execute :composer, 'install'

    end
  end

end
