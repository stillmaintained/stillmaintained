class GithubImporter
  # Updates user and user's projects from github, and returns remaining github
  # rate limit (how many requests we can perform to github api).
  def self.update_user_and_projects(user)
    update_github_login user.login, 'users'

    result = HTTParty.get("https://api.github.com/users/#{user.login}/orgs")
    organizations = result.map{|organization| organization['login'] }

    organizations.each do |organization|
      update_github_login organization, 'orgs'
    end

    user.update_attributes(:organizations => organizations)
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
  end
end
