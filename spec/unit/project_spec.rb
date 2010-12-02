require 'spec_helper'

describe Project do
  it 'should create a new Project' do
    lambda { Project.create }.should change(Project, :count).by(1)
  end

  describe '.create_or_update_from_github_response' do

    describe 'when this project does not exist yet' do

      it 'should create a new project' do
        lambda {
          Project.create_or_update_from_github_response({
            'owner' => 'alice', 'name' => 'project1'
          })
        }.should change(Project, :count).by(1)
      end

      it 'should save the user and project names' do
        project = Project.create_or_update_from_github_response({
          'owner' => 'alice', 'name' => 'project1'
        })
        project.reload
        project.user.should == 'alice'
        project.name.should == 'project1'
      end

      it 'should save the extra data from github' do
        project = Project.create_or_update_from_github_response({
          'owner' => 'alice',
          'name' => 'project1',
          'description' => 'description1',
          'watchers' => 123
        })

        project.description.should == 'description1'
        project.watchers.should == 123
      end

    end

    describe 'when this project already exists' do
      before do
        Project.create!(:name => 'project1', :user => 'alice')
      end

      it 'should not create a new project' do
        lambda {
          Project.create_or_update_from_github_response({
            'owner' => 'alice', 'name' => 'project1'
          })
        }.should_not change(Project, :count)
      end

      it 'should update the extra data from github' do
        project = Project.create_or_update_from_github_response({
          'owner' => 'alice',
          'name' => 'project1',
          'description' => 'description1',
          'watchers' => 123
        })

        project.description.should == 'description1'
        project.watchers.should == 123
      end


    end
  end

end
