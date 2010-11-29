require File.expand_path(File.dirname(__FILE__) + '/acceptance_helper')

feature 'Projects', %q{
  In order to get information about open source projects
  As a guest
  I want to be able to view project pages
} do

  background do
    @user = User.create!(:login => 'alice')
  end

  context 'global project index' do
    scenario 'show every project in a list' do
      Project.create!(:name => "project1", :user => 'alice', :visible => true)
      Project.create!(:name => "project2", :user => 'bob', :visible => true)

      visit '/projects'

      page.should have_content '2 projects'
      page.should have_content "alice/project1"
      page.should have_content "bob/project2"
    end

    scenario 'do not show any invisible projects' do
      Project.create!(:name => "project1", :user => 'alice', :visible => true)
      Project.create!(:name => "project2", :user => 'bob', :visible => false)

      visit '/projects'

      page.should have_content '1 projects'
      page.should have_content "alice/project1"
      page.should have_no_content "bob/project2"
    end
  end

  context 'user specific project index' do
    scenario 'Show the projects in a list per user' do
      Project.create!(:name => "project1", :user => 'alice', :visible => true)
      Project.create!(:name => "project2", :user => 'alice', :visible => true)

      visit '/alice'

      page.should have_content '2 projects by alice'
      page.should have_content "alice/project1"
      page.should have_content "alice/project2"
    end

    scenario 'Do not show any projects by different users' do
      Project.create!(:name => "project1", :user => 'alice', :visible => true)
      Project.create!(:name => "project2", :user => 'bob', :visible => true)

      visit '/alice'

      page.should have_content '1 projects by alice'
      page.should have_content "alice/project1"
      page.should have_no_content "bob/project2"
    end

    scenario 'Do not show any invisible projects' do
      Project.create!(:name => "project1", :user => 'alice', :visible => true)
      Project.create!(:name => "project2", :user => 'alice')

      visit '/alice'

      page.should have_content '1 projects by alice'
      page.should have_content "alice/project1"
      page.should have_no_content "alice/project2"
    end
  end

end
