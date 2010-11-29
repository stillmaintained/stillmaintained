class Project
  include Mongoid::Document

  field :state
  field :visible, :type => Boolean
end
