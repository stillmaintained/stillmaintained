require 'spec_helper'

describe GithubImporter do
  describe '.update_user' do
    let(:user) { User.create!(:login => 'alice') }

    context 'for user without organizations' do
      before do
        HTTParty.stub!(:get).with('https://api.github.com/users/alice/orgs').and_return([])
      end

      it 'creates new project' do
        HTTParty.stub!(:get).with('https://api.github.com/users/alice/repos').and_return(
          [{'name' => 'fetched_project', 'owner' => {'login' => 'alice'}}])

        GithubImporter.update_user_and_projects user

        Project.all.should have(1).project
        Project.first.name.should == 'fetched_project'
        Project.first.user.should == 'alice'
      end

      it 'updates already existing project' do
        Project.create!(:name => 'fetched_project', :user => 'alice')
        HTTParty.stub!(:get).with('https://api.github.com/users/alice/repos').and_return(
          [{'name' => 'fetched_project', 'owner' => {'login' => 'alice'}, 'description' => 'new description'}])

        GithubImporter.update_user_and_projects user

        Project.all.should have(1).project
        Project.first.description.should == 'new description'
      end

      it 'removes non existing projects' do
        Project.create!(:name => 'fetched_project', :user => 'alice')
        HTTParty.stub!(:get).with('https://api.github.com/users/alice/repos').and_return([])

        GithubImporter.update_user_and_projects user

        Project.all.should have(0).project
      end
    end

    context 'for user with organization' do
      before do
        HTTParty.stub!(:get).with('https://api.github.com/users/alice/repos').and_return([])
        HTTParty.stub!(:get).with('https://api.github.com/users/alice/orgs').and_return(
          [{'login' => 'organization'}])
      end

      it 'updates users organizations list' do
        HTTParty.stub!(:get).with('https://api.github.com/orgs/organization/repos').and_return([])

        GithubImporter.update_user_and_projects user

        user.organizations.should == ['organization']
      end

      it 'creates organization project' do
        HTTParty.stub!(:get).with('https://api.github.com/orgs/organization/repos').and_return(
          [{'name' => 'organization_project', 'owner' => {'login' => 'organization'}}]
        )

        GithubImporter.update_user_and_projects user

        Project.all.should have(1).project
        Project.first.name.should == 'organization_project'
        Project.first.user.should == 'organization'
      end

      it 'updates organization project' do
        Project.create!(:name => 'fetched_project', :user => 'organization')
        HTTParty.stub!(:get).with('https://api.github.com/orgs/organization/repos').and_return(
          [{'name' => 'fetched_project', 'owner' => {'login' => 'organization'}, 'description' => 'new description'}])

        GithubImporter.update_user_and_projects user

        Project.all.should have(1).project
        Project.first.description.should == 'new description'
      end

      it 'removes the non existing organization projects' do
        Project.create!(:name => 'fetched_project', :user => 'organization')
        HTTParty.stub!(:get).with('https://api.github.com/orgs/organization/repos').and_return([])

        GithubImporter.update_user_and_projects user

        Project.all.should have(0).project
      end
    end
  end

  describe '.update_users' do
  end
end
