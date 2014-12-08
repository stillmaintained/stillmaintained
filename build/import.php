#!/usr/bin/php

<?php
require dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'bootstrap' . DIRECTORY_SEPARATOR . 'start.php';
use Cake\ORM\TableRegistry;
use League\Monga;

// MongdoDB database connection
$mongo = Monga::connection('mongodb://localhost:27017')->database('stillmaintained');

// MongoDB collections
$collection = [
	'users' => $mongo->collection('users'),
	'projects' => $mongo->collection('projects'),
];

// MySQL tables
$table = [
	'users' => TableRegistry::get('Users'),
	'projects' => TableRegistry::get('Projects'),
];

// Users to import
$orig_users = $collection['users']->find()->toArray();
$orig_users_cnt = count($orig_users);

foreach ($orig_users as $orig_user) {
	$timestamp = date('Y-m-d H:i:s', $orig_user['updated_at']->sec);
	$user = [
		'username' => $orig_user['login'],
		'created' => $timestamp,
		'modified' => $timestamp,
	];

	if (!empty($orig_user['email'])) {
		$user['email'] = $orig_user['email'];
	}

	$user = $table['users']->newEntity($user);

	if (!empty($orig_user['token'])) {
		$user->credentials = [$table['users']->Credentials->newEntity([
			'token' => $orig_user['token'],
			'provider' => 'github',
			'created' => $timestamp,
			'modified' => $timestamp,
		])];
	}

	if (!empty($orig_user['organizations'])) {
		$user->organizations = [];
		foreach ($orig_user['organizations'] as $name) {
			$organization = $table['users']->organizations->findByName($name)->first();
			if (empty($organization)) {
				$organization = $table['users']->organizations->newEntity(compact('name'));
			}
			$user->organizations[] = $organization;
		}
	}

	if (!empty($orig_user['project_ids'])) {
		$user->projects = [];
		foreach ($orig_user['project_ids'] as $id) {
			$orig_project = $collection['projects']->findOne(['_id' => $id]);
			$conditions = ['username' => $orig_project['user'], 'name' => $orig_project['name']];
			$project = $table['projects']->find()->where($conditions)->first();
			if (empty($project)) {
				$project = $conditions + ['provider' => 'github'];
				foreach (['description', 'state', 'fork', 'visible', 'watchers'] as $k) {
					if (array_key_exists($k, $orig_project)) {
						$project[$k] = $orig_project[$k];
					}
				}

				if (!empty($orig_project['created_at'])) {
					$project['created'] = date('Y-m-d H:i:s', $orig_project['created_at']->sec);
				}

				if (!empty($orig_project['updated_at'])) {
					$project['modified'] = date('Y-m-d H:i:s', $orig_project['updated_at']->sec);
				}

				$project = $table['projects']->newEntity($project);
			}
			$user->projects[] = $project;
		}

	}

	$table['users']->save($user);
}

// Projects to import
$orig_projects = $collection['projects']->find()->toArray();
$orig_projects_cnt = count($orig_projects);

$orphan_projects = [];
foreach ($orig_projects as $orig_project) {
	$conditions = ['username' => $orig_project['user'], 'name' => $orig_project['name']];
	$project = $table['projects']->exists($conditions);
	if ($project) {
		// don't create dups
		continue;
	}

	$user = $table['users']->findByUsername($orig_project['user'])->first();
	if (!$user) {
		$organization = $table['users']->Organizations->findByName($orig_project['user'])->first();
		if (!$organization) {
			$orphan_projects[] = $orig_project['user'] . '/' . $orig_project['name'];
			// skip orphan projects
			continue;
		}
	}

	$project = $conditions + ['provider' => 'github'];
	foreach (['description', 'state', 'fork', 'visible', 'watchers'] as $k) {
		if (array_key_exists($k, $orig_project)) {
			$project[$k] = $orig_project[$k];
		}
	}

	if (!empty($orig_project['created_at'])) {
		$project['created'] = date('Y-m-d H:i:s', $orig_project['created_at']->sec);
	}

	if (!empty($orig_project['updated_at'])) {
		$project['modified'] = date('Y-m-d H:i:s', $orig_project['updated_at']->sec);
	}

	$project = $table['projects']->newEntity($project);

	if ($user) {
		$project->users = [$user];
	} else {
		$project->organizations = [$organization];
	}

	$table['projects']->save($project);
}

$users_cnt = $table['users']->find()->count();
$projects_cnt = $table['projects']->find()->count();
$organizations_cnt = $table['users']->Organizations->find()->count();
$orphan_projects_cnt = count($orphan_projects);

printf(<<<TEXT
Originally:
%s users - %s projects

Imported:
%s users - %s projects

Found:
%s orphan projects that were dropped
%s organizations that were created
TEXT
,
$orig_users_cnt,
$orig_projects_cnt,
$users_cnt,
$projects_cnt,
$orphan_projects_cnt,
$organizations_cnt
);

print('====================================');
print(implode("\n", $orphan_projects));

exit(0);
