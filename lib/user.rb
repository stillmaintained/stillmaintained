class User
  include Mongoid::Document
  include Mongoid::Timestamps::Updated

  field :login
  field :organizations, :type => Array, :default => []
end
