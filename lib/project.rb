class Project
  include Mongoid::Document
  include Mongoid::Timestamps

  field :description
  field :state
  field :visible, :type => Boolean
end
