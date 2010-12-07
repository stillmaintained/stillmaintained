require 'spec_helper'

describe User do
  it 'should create a new user' do
    lambda { User.create }.should change(User, :count).by(1)
  end

  describe '#organizations' do
    it 'should return an array or organization namese' do
      user = User.create(:organizations => %w{one two three})
      user.organizations.should == %w{one two three}
    end

    it 'should return an empty array if there are none' do
      User.create.organizations.should == []
    end
  end

end
