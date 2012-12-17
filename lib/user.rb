class User
  include Mongoid::Document
  include Mongoid::Timestamps::Updated

  field :login
  field :email
  field :organizations, :type => Array, :default => []
  field :token
end
