require 'spec_helper'

describe Project do
  it 'should create a new Project' do
    lambda { Project.create }.should change(Project, :count).by(1)
  end
end
