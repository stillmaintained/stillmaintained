require File.expand_path(File.dirname(__FILE__) + '/acceptance_helper')

feature 'Projects', %q{
  In order to get information about open source projects
  As a guest
  I want to be able to view project pages
} do

  background do
    @user = User.make(:login => 'alice')
  end

  context 'global project index' do
    before do
      Project.make(
        :name => "project1",
        :state => 'maintained',
        :user => 'alice',
        :description => 'project1 description'
      )
    end

    scenario 'show every project in a list' do
      Project.make(:name => "project2", :user => 'bob')

      visit '/projects'

      page.should have_content '2 projects'
      page.should have_content "alice/project1"
      page.should have_content "bob/project2"
    end

    scenario "show every project in json format" do
      Project.make(:name => "project2", :user => 'bob')

      visit '/projects.json'

      json = ActiveSupport::JSON.decode(page.body)
      json.map {|j| j['name']}.should ==  ['project1', 'project2']
      json.map {|j| j['user']}.should ==  ['alice', 'bob']
    end

    scenario 'do not show any invisible projects' do
      Project.make(:name => "project2", :user => 'bob', :visible => false)

      visit '/projects'

      page.should have_content '1 projects'
      page.should have_content "alice/project1"
      page.should have_no_content "bob/project2"
    end

    scenario 'do not show any forked projects' do
      Project.make(:name => "project2", :user => 'bob', :fork => true)
      visit '/projects'

      page.should have_content '1 projects'
      page.should have_content "alice/project1"
      page.should have_no_content "bob/project2"
    end

    scenario 'show the project descriptions' do
      visit '/projects'

      page.should have_content 'project1 description'
    end

    scenario 'click on a project name' do
      visit '/projects'

      click_link 'project1'

      page.should have_content 'project1 is still being maintained'
    end

    context 'state filtering' do
      before do
        %w{maintained searching abandoned}.each do |state|
          Project.make(
            :name => state,
            :state => state,
            :user => 'alice'
          )
        end
      end

      scenario 'show all by default' do
        visit '/projects'

        %w{maintained searching abandoned}.each do |state|
          page.should have_content state
        end
      end

      scenario 'only show abandoned projects' do
        visit '/projects?state=abandoned'

        page.should have_content '1 projects'

        within :css, 'ul' do
          page.should have_no_content 'maintained'
          page.should have_no_content 'searching'
          page.should have_content 'abandoned'
        end
      end
    end
  end

  context 'user specific project index' do
    before do
      Project.make(:name => "project1", :user => 'alice', :state => 'maintained')
    end

    scenario 'Show the projects in a list per user' do
      Project.make(:name => "project2", :user => 'alice')

      visit '/alice'

      page.should have_content '2 projects by alice'
      page.should have_content "alice/project1"
      page.should have_content "alice/project2"
    end

    scenario 'Show the projects list per user in JSON format' do
      visit '/alice.json'

      json = ActiveSupport::JSON.decode(page.body)
      json.length.should == 1
      json.map {|j| j['name']}.should include 'project1'
      json.map {|j| j['user']}.should include 'alice'
    end

    scenario 'Do not show any projects by different users' do
      Project.make(:name => "project2", :user => 'bob')

      visit '/alice'

      page.should have_content '1 projects by alice'
      page.should have_content "alice/project1"
      page.should have_no_content "bob/project2"
    end

    scenario 'Do not show any invisible projects' do
      Project.make(:name => "project2", :user => 'alice', :visible => false)

      visit '/alice'
      page.should have_content '1 projects by alice'
      page.should have_content "alice/project1"
      page.should have_no_content "alice/project2"
    end

    scenario 'click on a project name' do
      visit '/alice'

      click_link 'project1'

      page.should have_content 'project1 is still being maintained'
    end

    scenario 'click on a user name' do
      visit '/alice'

      click_link 'alice'

      page.should have_content '1 projects by alice'
    end

    scenario 'go to a non-existing user page' do
      visit '/bob'

      page.should have_no_content '0 projects by bob'
      page.should have_content 'Oh no! bob hasn\'t added any projects yet!'
      page.should have_content 'Why don\'t you send them a message about Still Maintained?'
    end

    scenario 'do not show any forked projects' do
      Project.make(:name => "project2", :user => 'alice', :fork => true)
      visit '/alice'

      page.should have_content '1 projects'
      page.should have_content "alice/project1"
      page.should have_no_content "alice/project2"
    end
  end

  context 'project pages' do
    scenario 'show a maintained project page' do
      Project.make(:name => "project1", :user => 'alice', :state => 'maintained', :description => 'project1 description')
      visit '/alice/project1'

      page.should have_content 'Yay! project1 is still being maintained.'
      page.should have_content 'project1 description'
    end

    scenario 'show a maintained project page in JSON format' do
      Project.make(:name => "project1", :user => 'alice', :state => 'maintained', :description => 'project1 description')

      visit '/alice/project1.json'

      json = ActiveSupport::JSON.decode(page.body)
      json['name'].should == 'project1'
      json['user'].should == 'alice'
    end

    scenario 'show a searching project page' do
      Project.make(:name => "project1", :user => 'alice', :state => 'searching', :description => 'project1 description')
      visit '/alice/project1'

      page.should have_content 'Hey! project1 is looking for a new maintainer.'
      page.should have_content 'project1 description'
    end

    scenario 'show a searching project page' do
      Project.make(:name => "project1", :user => 'alice', :state => 'abandoned', :description => 'project1 description')
      visit '/alice/project1'

      page.should have_content 'Sorry, project1 is abandoned.'
      page.should have_content 'project1 description'
    end

    scenario 'click the "show all projects by ..." link' do
      Project.make(:name => "project1", :user => 'alice', :state => 'abandoned')

      visit '/alice/project1'
      click_link 'show all projects by alice'

      page.should have_content '1 projects by alice'
    end

  end

  context 'search' do
    before do
      Project.make(
        :name => "project1",
        :state => 'maintained',
        :user => 'alice',
        :description => 'project1 description'
      )
    end

    scenario 'for project' do
      visit '/projects?q=project1'

      page.should have_content '1 projects'
      page.should have_content "alice/project1"
    end

    scenario 'for project with form' do
      visit '/projects'

      fill_in 'q', :with => 'project'
      click_button 'Search'

      page.should have_content '1 projects'
      page.should have_content "alice/project1"
    end

    scenario 'do not show any forked projects' do
      Project.make(:name => "project2", :user => 'bob', :fork => true)
      visit '/projects?q=project'

      page.should have_content '1 projects'
      page.should have_content "alice/project1"
      page.should have_no_content "bob/project2"
    end

  end

end
