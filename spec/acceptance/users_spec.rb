require File.expand_path(File.dirname(__FILE__) + '/acceptance_helper')

feature 'Users', %q{
  In order to add my projects
  As a guest
  I want a form to fill in my project's states
} do

  scenario 'Fill in the edit user form' do
    user = User.create!(:login => 'alice')
    Project.create!(:name => 'project1', :user => 'alice')

    visit "/users/#{user.id}/edit"

    choose 'project1_maintained'
    click_button 'Submit'

    page.should have_content '1 projects by alice'
  end

end