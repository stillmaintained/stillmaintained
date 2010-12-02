class Project
  include Mongoid::Document
  include Mongoid::Timestamps

  field :user
  field :name
  field :description
  field :watchers, :type => Integer
  field :state
  field :visible, :type => Boolean

  def self.create_or_update_from_github_response(data)
    if project = first(:conditions => { :name => data['name'], :user => data['owner'] })
      project.update_attributes!(
        :description => data['description'],
        :watchers => data['watchers']
      )
    else
      project = create!(
        :name => data['name'],
        :user => data['owner'],
        :description => data['description'],
        :watchers => data['watchers']
      )
    end
    project
  end
end
