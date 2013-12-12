class User
  include Mongoid::Document
  include Mongoid::Timestamps::Updated

  field :login
  field :email
  field :organizations, type: Array, default: []
  field :token

  # List of projects the user has permission to.
  has_and_belongs_to_many :projects
end
