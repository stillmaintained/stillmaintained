class User
  include Mongoid::Document

  field :login
  field :organizations, :type => Array

  def organizations
    read_attribute(:organizations) || []
  end
end
