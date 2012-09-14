class Project
  include Mongoid::Document
  include Mongoid::Timestamps

  field :user
  field :name
  field :description
  field :watchers, :type => Integer
  field :fork, :type => Boolean
  field :source
  field :parent
  field :state
  field :visible, :type => Boolean

  scope :visible, where(:visible => true)
  scope :no_forks, where(:fork.ne => true)

  scope :search, lambda { |query|
    query = /#{query}/i
    where({:name => query})
  }

  def self.create_or_update_from_github_response(data)
    if project = first(:conditions => { :name => data['name'], :user => data['owner']['login'] })
      project.update_attributes!(
        :description => data['description'],
        :watchers => data['watchers']
      )
    else
      project = create!(
        :name => data['name'],
        :user => data['owner']['login'],
        :description => data['description'],
        :watchers => data['watchers'],
        :fork => data['fork'],
        :source => data['source'],
        :parent => data['parent']
      )
    end
    project
  end
end
