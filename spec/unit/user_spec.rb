require 'spec_helper'

describe User do
  it 'should create a new user' do
    lambda { User.create }.should change(User, :count).by(1)
  end
end
