<?php

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\Table;
use Github\Client as Github;

class ProjectsTable extends Table {

	public function initialize(array $config) {
		$this->addBehavior('Timestamp');

		$this->belongsToMany('Users');
		$this->belongsToMany('Organizations');
	}

	public function createOrUpdate($user, $data) {

		extract($data);

		$project = $this->find()
			->where(['username' => $owner['login'], 'name' => $name])
			->first();

		if ($project) {
			$project->description = $description;
			$project->watchers = $watchers;
		} else {
			$project = $this->newEntity([
				'username' => $owner['login'],
				'provider' => 'github',
			] + compact('name', 'description', 'watchers', 'fork'));
			$project->users[$user];
		}

		return $this->save($project);
	}

	public function findPopular(Query $query, array $options) {
		return $query->order(['watchers DESC']);
	}

	public function findRecent(Query $query, array $options) {
		return $query->order(['modified DESC']);
	}

	public function findTop(Query $query, array $options) {
		return $query->limit(25);
	}

	public function findVisible(Query $query, array $options) {
		return $query->where(['visible' => true]);
	}

	public function sync($user, $data) {
		$method = '_' . $data['provider'] . 'Repositories';
		$repos = $this->{$method}($user);
		$projects = [];

		foreach ($repos as $repo) {
			$project = $this->createOrUpdate($user, $repo);
			array_push($projects, $project);
		}

		$user->projects = $projects;
		$this->Users->save($user);
	}

	protected function _githubRepositories($user) {
		$github = new Github();
		return $github->api('user')->repositories($user->username, 'all');
	}

}
