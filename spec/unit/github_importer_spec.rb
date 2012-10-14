require 'spec_helper'

describe GithubImporter do
  describe '.update_user' do
    let(:user) { User.create!(login: 'alice') }

    context 'for user without organizations' do
      before do
        mock_github_api '/users/alice/orgs', []
      end

      it 'creates new project' do
        mock_github_api '/users/alice/repos', [{name: 'fetched_project', owner: {login: 'alice'}}]

        GithubImporter.update_user_and_projects user

        Project.all.should have(1).project
        Project.first.name.should == 'fetched_project'
        Project.first.user.should == 'alice'
      end

      it 'updates already existing project' do
        Project.create!(name: 'fetched_project', user: 'alice')
        mock_github_api '/users/alice/repos', [{name: 'fetched_project', owner: {login: 'alice'}, description: 'new description'}]

        GithubImporter.update_user_and_projects user

        Project.all.should have(1).project
        Project.first.description.should == 'new description'
      end

      it 'removes non existing projects' do
        Project.create!(name: 'fetched_project', user: 'alice')
        mock_github_api '/users/alice/repos', []

        GithubImporter.update_user_and_projects user

        Project.all.should have(0).project
      end

      it 'returns remaining rate limit' do
        mock_github_api '/users/alice/repos', [], rate_limit: 5000
        mock_github_api '/users/alice/orgs', [], rate_limit: 4999

        rate_limit = GithubImporter.update_user_and_projects user

        rate_limit.should == 4999
      end
    end

    context 'for user with organization' do
      before do
        mock_github_api '/users/alice/repos', []
        mock_github_api '/users/alice/orgs', [{login: 'organization'}]
      end

      it 'updates users organizations list' do
        mock_github_api '/orgs/organization/repos', []

        GithubImporter.update_user_and_projects user

        user.organizations.should == ['organization']
      end

      it 'creates organization project' do
        mock_github_api '/orgs/organization/repos', [{name: 'organization_project', owner: {login: 'organization'}}]

        GithubImporter.update_user_and_projects user

        Project.all.should have(1).project
        Project.first.name.should == 'organization_project'
        Project.first.user.should == 'organization'
      end

      it 'updates organization project' do
        Project.create!(name: 'fetched_project', user: 'organization')
        mock_github_api '/orgs/organization/repos', [{name: 'organization_project', owner: {login: 'organization'}, description: 'new description'}]

        GithubImporter.update_user_and_projects user

        Project.all.should have(1).project
        Project.first.description.should == 'new description'
      end

      it 'removes the non existing organization projects' do
        Project.create!(:name => 'fetched_project', :user => 'organization')
        mock_github_api '/orgs/organization/repos', []

        GithubImporter.update_user_and_projects user

        Project.all.should have(0).project
      end

      it 'returns remaining rate limit' do
        mock_github_api '/users/alice/repos', [], rate_limit: 5000
        mock_github_api '/users/alice/orgs', [{login: 'organization'}], rate_limit: 4999
        mock_github_api '/orgs/organization/repos', [{name: 'organization_project', owner: {login: 'organization'}}], rate_limit: 4998

        rate_limit = GithubImporter.update_user_and_projects user
        rate_limit.should == 4998
      end
    end
  end

  describe '.update_users' do
  end
end
