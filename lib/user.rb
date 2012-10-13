class User
  include Mongoid::Document

  field :login
  field :organizations, :type => Array, :default => []
end
