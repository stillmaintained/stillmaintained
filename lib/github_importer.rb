require 'gh'

class GithubImporter
  def initialize user
    @github = GH::DefaultStack.build token: user.token
    @user = user
  end

  def update_user_and_projects
    projects = update_repos @github['user/repos']

    organizations = @github['user/orgs'].map{|organization| organization['login'] }

    organizations.each do |organization|
      projects |= update_repos @github["orgs/#{organization}/repos"]
    end

    @user.touch
    @user.update_attributes!(organizations: organizations, projects: projects)

    Project.destroy_all(user_ids: [], user: @user.login)
    Project.where(user_ids: []).in(user: organizations).destroy_all
  end

  private

  def update_repos repos
    repos.map do |repo|
      Project.create_or_update_from_github_response(repo)
    end.flatten
  end
end
