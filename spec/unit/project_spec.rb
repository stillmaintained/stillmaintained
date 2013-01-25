require 'spec_helper'

describe Project do
  it 'should create a new Project' do
    lambda { Project.create }.should change(Project, :count).by(1)
  end

  describe 'search' do
    before do
      @project1 = Project.make(:name => "project2", :user => 'alice')
      @project2 = Project.make(:name => "project1", :user => 'bob')
    end

    it 'should find project by name' do
      results = Project.search_by_name('project2')
      results.should include(@project1)
      results.should_not include(@project2)

      results = Project.search_by_name('PROJECT2')
      results.should include(@project1)
      results.should_not include(@project2)


      results = Project.search_by_name('project')
      results.should include(@project1)
      results.should include(@project2)

      results = Project.search_by_name('proj')
      results.should include(@project1)
      results.should include(@project2)

      results = Project.search_by_name('ject')
      results.should include(@project1)
      results.should include(@project2)
    end
  end

  describe '.create_or_update_from_github_response' do

    describe 'when this project does not exist yet' do
      it 'should create a new project' do
        lambda {
          Project.create_or_update_from_github_response({
            'owner' => {'login' => 'alice'},
            'name' => 'project1',
            'permissions' => {'admin' => true}
          })
        }.should change(Project, :count).by(1)
      end

      it 'should save the user and project names' do
        project = Project.create_or_update_from_github_response({
          'owner' => {'login' => 'alice'},
          'name' => 'project1',
          'permissions' => {'admin' => true}
        })
        project.reload
        project.user.should == 'alice'
        project.name.should == 'project1'
      end

      it 'should save the extra data from github' do
        project = Project.create_or_update_from_github_response({
          'owner' => {'login' => 'alice'},
          'name' => 'project1',
          'description' => 'description1',
          'watchers' => 123,
          'permissions' => {'admin' => true}
        })

        project.description.should == 'description1'
        project.watchers.should == 123
      end

      it 'should not save project if user does not have permissions' do
        project = Project.create_or_update_from_github_response({
          'owner' => {'login' => 'alice'},
          'name' => 'project1',
          'description' => 'description1',
          'watchers' => 123,
          'permissions' => {'admin' => false}
        })
        project.should be_nil
      end

      describe 'when the project is a fork' do
        before do
          @project = Project.create_or_update_from_github_response({
            'owner' => {'login' => 'alice'},
            'name' => 'project1',
            'fork' => true,
            'permissions' => {'admin' => true}
          })
        end

        it 'should set the fork boolean' do
          @project.fork.should be_true
        end
      end

    end

    describe 'when this project already exists' do
      before do
        Project.make(:name => 'project1', :user => 'alice')
      end

      it 'should not create a new project' do
        lambda {
          Project.create_or_update_from_github_response({
            'owner' => {'login' => 'alice'},
            'name' => 'project1',
            'permissions' => {'admin' => true}
          })
        }.should_not change(Project, :count)
      end

      it 'should update the extra data from github' do
        project = Project.create_or_update_from_github_response({
          'owner' => {'login' => 'alice'},
          'name' => 'project1',
          'description' => 'description1',
          'watchers' => 123,
          'permissions' => {'admin' => true}
        })

        project.description.should == 'description1'
        project.watchers.should == 123
      end
    end
  end

  describe '.no_forks' do
    before do
      @projects = [
        Project.make(:fork => true),
        Project.make(:fork => nil),
        Project.make(:fork => false)
      ]
    end

    it 'should not return any forked projects' do
      Project.no_forks.should_not include @projects[0]
    end

    it 'should return any projects where fork == nil' do
      projects = Project.no_forks
      projects.should include @projects[1]
      projects.should include @projects[2]
    end
  end
end
