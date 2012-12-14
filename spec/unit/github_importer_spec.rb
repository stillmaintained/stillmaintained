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

      pending 'returns remaining rate limit' do
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

      pending 'returns remaining rate limit' do
        mock_github_api '/users/alice/repos', [], rate_limit: 5000
        mock_github_api '/users/alice/orgs', [{login: 'organization'}], rate_limit: 4999
        mock_github_api '/orgs/organization/repos', [{name: 'organization_project', owner: {login: 'organization'}}], rate_limit: 4998

        rate_limit = GithubImporter.update_user_and_projects user
        rate_limit.should == 4998
      end
    end
  end

  describe '.update_users' do

    it 'does not updates user that were updated within week' do
      user = User.create!

      GithubImporter.should_receive(:update_user_and_projects).never

      GithubImporter.update_users
    end
    context 'when user was not updated for a week' do
      let!(:user) do
        user = User.new
        user.updated_at = Time.now - 8.days
        user.save!
        user
      end

      let!(:other_user) do
        user = User.new
        user.updated_at = Time.now - 8.days
        user.save!
        user
      end

      it 'updates users updated later than week ago' do
        GithubImporter.should_receive(:update_user_and_projects).with(user).once.and_return(5000)
        GithubImporter.should_receive(:update_user_and_projects).with(other_user).once.and_return(5000)

        GithubImporter.update_users
      end

      it 'updates user when rate limit remaining is greater than or equal to 3000' do
        GithubImporter.should_receive(:update_user_and_projects).with(user).and_return(3000)
        GithubImporter.should_receive(:update_user_and_projects).with(other_user).once.and_return(2900)

        GithubImporter.update_users
      end

      it 'does not update users when rate limit remaining is less than 3000' do
        GithubImporter.should_receive(:update_user_and_projects).with(user).and_return(2999)
        GithubImporter.should_receive(:update_user_and_projects).with(other_user).never

        GithubImporter.update_users
      end
    end
  end
end
