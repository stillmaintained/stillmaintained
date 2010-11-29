require File.expand_path(File.dirname(__FILE__) + '/acceptance_helper')

feature 'Homepage', %q{
  In order to feel welcome
  As a guest
  I want to have a nice homepage
} do

  context 'the homepage' do

    scenario "Visit the homepage" do
      visit '/'
      page.should have_content 'Still Maintained?'
    end

  end

end
