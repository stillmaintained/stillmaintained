require 'spec_helper'

describe GithubImporter do
  describe '.update_user' do
    let(:user) { User.create!(login: 'alice') }

    context 'for user without organizations' do
      before do
        mock_github_api '/user/orgs', []
      end

      it 'creates new project' do
        mock_github_api '/user/repos',
          [{name: 'fetched_project',
            owner: {login: 'alice'},
            permissions: {admin: true}}]

        GithubImporter.new(user).update_user_and_projects

        Project.all.should have(1).project
        Project.first.name.should == 'fetched_project'
        Project.first.user.should == 'alice'

        user.projects.should have(1).project
      end

      it 'updates already existing project' do
        Project.create!(name: 'fetched_project', user: 'alice')
        mock_github_api '/user/repos',
          [{name: 'fetched_project',
            owner: {login: 'alice'},
            permissions: {admin: true},
            description: 'new description'}]

        GithubImporter.new(user).update_user_and_projects

        Project.all.should have(1).project
        Project.first.description.should == 'new description'
      end

      it 'should ignore projects without permissions' do
        mock_github_api '/user/repos',
          [{name: 'fetched_project',
            owner: {login: 'alice'},
            permissions: {admin: false}}]

        GithubImporter.new(user).update_user_and_projects

        user.projects.should be_empty
        Project.count.should == 0
      end

      it 'should remove projects removed from github' do
        Project.create!(name: 'fetched_project', user: 'alice', users: [])
        mock_github_api '/user/repos', []

        GithubImporter.new(user).update_user_and_projects

        Project.all.should have(0).project
      end

      it 'should remove projects removed from github from active user only' do
        Project.create!(name: 'fetched_project', user: 'alice', users: [])
        Project.create!(name: 'fetched_project', user: 'bob', users: [])
        mock_github_api '/user/repos', []

        GithubImporter.new(user).update_user_and_projects

        Project.all.should have(1).project
      end
    end

    context 'for user with organization' do
      before do
        mock_github_api '/user/repos', []
        mock_github_api '/user/orgs', [{login: 'organization'}]
      end

      it 'updates users organizations list' do
        mock_github_api '/orgs/organization/repos', []

        GithubImporter.new(user).update_user_and_projects

        user.organizations.should == ['organization']
      end

      it 'creates organization project' do
        mock_github_api '/orgs/organization/repos',
          [{name: 'organization_project',
            owner: {login: 'organization'},
            permissions: {admin: true} }]

        GithubImporter.new(user).update_user_and_projects

        Project.all.should have(1).project
        Project.first.name.should == 'organization_project'
        Project.first.user.should == 'organization'
      end

      it 'updates organization project' do
        Project.create!(name: 'organization_project', user: 'organization')
        mock_github_api '/orgs/organization/repos',
          [{name: 'organization_project',
            owner: {login: 'organization'},
            permissions: {admin: true},
            description: 'new description'}]

        GithubImporter.new(user).update_user_and_projects

        Project.all.should have(1).project
        Project.first.description.should == 'new description'
      end

      it 'should remove projects removed from github' do
        Project.create!(name: 'organization_project', user: 'organization', users: [])
        mock_github_api '/orgs/organization/repos', []

        GithubImporter.new(user).update_user_and_projects

        Project.all.should have(0).project
      end
    end
  end
end
