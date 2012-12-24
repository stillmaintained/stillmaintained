class User
  include Mongoid::Document
  include Mongoid::Timestamps::Updated

  field :login
  field :email
  field :organizations, type: Array, default: []
  field :token

  has_and_belongs_to_many :projects
end
