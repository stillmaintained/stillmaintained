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

    click_link 'alice/project1'
    page.should have_content 'abandoned'

    visit '/alice'
    click_link 'alice/project2'
    page.should have_content 'looking for a new maintainer'

    visit '/alice'
    click_link 'alice/project3'
    page.should have_content 'still being maintained'
  end

end
