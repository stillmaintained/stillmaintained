require File.expand_path(File.dirname(__FILE__) + '/acceptance_helper')

feature 'Users', %q{
  In order to add my projects
  As a guest
  I want a form to fill in my project's states
} do

  background do
    @user = User.make(:login => 'alice')
    Project.make(:name => 'project1', :user => 'alice', :visible => false)
    Project.make(:name => 'project2', :user => 'alice', :visible => false)
    Project.make(:name => 'project3', :user => 'alice', :visible => false)
    Project.make(:name => 'project4', :user => 'alice', :visible => false)

    OmniAuth.config.test_mode = true
    OmniAuth.config.add_mock(:github, {'info' => {'nickname' => 'alice'}})
  end

  context 'getting the projects from github' do
    background do
      mock_github_api '/users/alice/repos', [{name: 'fetched_project', owner: {login: 'alice'}}]
      mock_github_api '/users/alice/orgs', [{login: 'organization'}]
      mock_github_api '/orgs/organization/repos',
        [{name: 'organization_project', owner: {login: 'organization'}}]

      visit '/auth/github/callback'
    end

    scenario 'log in via Github' do
      page.should have_content 'Hi alice, here\'s a list of every Github project you started.'
    end

    scenario 'log in via Github after new organizations are added' do
      mock_github_api '/orgs/other_organization/repos', []
      mock_github_api '/users/alice/orgs', [{login: 'organization'}, {login: 'other_organization'}]

      visit '/auth/github/callback'

      User.count.should == 1
    end

    scenario 'show the projects in the form' do
      page.should have_content 'fetched_project'
    end

    scenario 'show the organization projects in the form' do
      page.should have_content 'organization_project'
    end

    scenario 'successfully save the form' do
      choose 'fetched_project_abandoned'
      choose 'organization_project_abandoned'
      click_button 'Submit'

      page.should have_content '1 projects by alice'

      visit '/organization'
      page.should have_content '1 projects by organization'
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
    choose 'project4_hide'
    click_button 'Submit'

    page.should have_no_content 'project4'

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
    Project.first.update_attributes(:state => 'maintained', :visible => false)
    visit "/users/#{@user.id}/edit"
    page.should have_css('input#project1_maintained[checked]')
  end

end
