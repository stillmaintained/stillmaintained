require File.expand_path(File.dirname(__FILE__) + '/acceptance_helper')

feature 'Users', %q{
  In order to add my projects
  As a guest
  I want a form to fill in my project's states
} do

  background do
    @user = User.create!(:login => 'alice')
    Project.create!(:name => 'project1', :user => 'alice')
    Project.create!(:name => 'project2', :user => 'alice')
    Project.create!(:name => 'project3', :user => 'alice')

    FakeWeb.register_uri(:post, 'https://github.com/login/oauth/access_token', :body => 'access_token=github')
    FakeWeb.register_uri(:get, 'https://github.com/api/v2/json/user/show?access_token=github', :body => '{"user": {"login": "alice"}}')
  end

  scenario 'log in via Github' do
    visit '/auth/github/callback'
    page.should have_content 'Hi alice, here\'s a list of every Github project you started.'
  end

  context 'getting the projects from github' do
    background do
      HTTParty.should_receive(:get).and_return(
        'repositories' => [
          {'name' => 'fetched_project', 'owner' => 'alice'},
          {'name' => 'forked_project', 'owner' => 'alice', 'fork' => true}
        ]
      )
    end

    scenario 'show the projects in the form' do
      visit '/auth/github/callback'
      page.should have_content 'fetched_project'
    end

    scenario 'do not show forked projects in the form' do
      visit '/auth/github/callback'
      page.should have_no_content 'forked_project'
    end

  end

  scenario 'Fill in the edit user form' do
    visit "/users/#{@user.id}/edit"

    choose 'project1_maintained'
    click_button 'Submit'

    page.should have_content '1 projects by alice'
  end

  scenario 'Update a project status' do
    visit "/users/#{@user.id}/edit"

    choose 'project1_abandoned'
    choose 'project2_searching'
    choose 'project3_maintained'
    click_button 'Submit'

    click_link 'project1'
    page.should have_content 'abandoned'

    visit '/alice'
    click_link 'project2'
    page.should have_content 'looking for a new maintainer'

    visit '/alice'
    click_link 'project3'
    page.should have_content 'still being maintained'
  end

  scenario 'return to the user update form' do
    Project.first.update_attributes(:state => 'maintained')
    visit "/users/#{@user.id}/edit"
    body.should include '<input checked=\'checked\' id=\'project1_maintained\''
  end

end
