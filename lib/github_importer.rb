class GithubImporter
  # Updates user and user's projects from github, and returns remaining github
  # rate limit (how many requests we can perform to github api).
  def self.update_user_and_projects(user)
    update_github_login user.login, 'users'

    result = HTTParty.get("https://api.github.com/users/#{user.login}/orgs")
    rate_limit = result.headers['X-RateLimit-Remaining']
    organizations = result.map{|organization| organization['login'] }

    organizations.each do |organization|
      rate_limit = update_github_login organization, 'orgs'
    end

    user.touch
    user.update_attributes(:organizations => organizations)

    rate_limit.to_i
  end

  def self.update_users
    updated_user_count = 0
    User.where(:updated_at.lt => Time.now - 7.days).each do |user|
      rate_limit = update_user_and_projects user
      updated_user_count = 0
      break if rate_limit < 3000
    end
    puts "#{updated_user_count} users updated"
  end

  private

  def self.update_github_login login, type
    result = HTTParty.get("https://api.github.com/#{type}/#{login}/repos")

    projects = []
    result.each do |repo|
      projects << Project.create_or_update_from_github_response(repo)
    end

    Project.where(:user => login).select { |project| not projects.include?(project) }.each do |project|
      project.destroy
    end

    result.headers['X-RateLimit-Remaining']
  end
end
