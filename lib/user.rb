class User
  include Mongoid::Document

  field :login
  field :organizations, :type => Array

  def organizations
    read_attribute(:organizations) || []
  end

  def update_projects_from_github
    result = HTTParty.get("https://api.github.com/users/#{login}/repos")

    result.each do |repo|
      Project.create_or_update_from_github_response(repo)
    end

    result = HTTParty.get("https://api.github.com/users/#{login}/orgs")
    organizations = result.map{|organization| organization['login'] }

    organizations.each do |organization|
      result = HTTParty.get("https://api.github.com/orgs/#{organization}/repos")

      result.each do |repo|
        Project.create_or_update_from_github_response(repo)
      end
    end

    update_attributes(:organizations => organizations)
  end
end
