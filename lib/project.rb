class Project
  include Mongoid::Document

  field :description
  field :state
  field :visible, :type => Boolean
end
