namespace :deploy do
	namespace :symlink do
		desc <<-DESC
			Symlink bower components files
		DESC
		task :bower do
			on roles(:app) do
				dirs = fetch(:component_dirs, [])
				dirs.each do |symlink|
					component_path = release_path.join('bower_components')
					source = component_path.join(symlink[:source])
					target = release_path.join(symlink[:target])
					execute :ln, '-s', source, target
				end
			end
		end
	end
end
