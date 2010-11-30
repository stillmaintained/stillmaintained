class Project
  include Mongoid::Document
  include Mongoid::Timestamps

  field :description
  field :watchers, :type => Integer
  field :state
  field :visible, :type => Boolean
end
