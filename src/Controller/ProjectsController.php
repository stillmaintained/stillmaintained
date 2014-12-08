<?php

namespace App\Controller;

use Cake\Core\Configure;
use Cake\Error\NotFoundException;
use Cake\Event\Event;
use Cake\Utility\String;

class ProjectsController extends AppController {

	public $components = ['RequestHandler'];

	protected $_requestType = null;

	public function beforeFilter(Event $event) {
		$this->Auth->allow(['index', 'show']);

		foreach ((array) Configure::read('App.extensions') as $type) {
			if ($this->request->is($type)) {
				$this->_requestType = $type;
			}
		}
	}

	public function edit() {
		$username = $this->request->params['username'];
		$projects = $this->Projects->find('all')
			->select(['id', 'username', 'name', 'description', 'state'])
			->where(compact('username'));

		if ($this->request->is('post')) {
			foreach ($projects as $project) {
				if (empty($this->request->data[$username][$project->name])) {
					continue;
				}
				$state = $this->request->data[$username][$project->name];
				$project = $this->Projects->patchEntity($project, compact('state'));
				$this->Projects->save($project);
			}
			return $this->redirect('/' . $username);
		}

		$this->set(compact('projects'));
	}

	public function show() {
		$params = $this->request->params;
		$query = $this->Projects->find('visible')
			->select(['username', 'name', 'description', 'state', 'provider'])
			->where(['username' => $params['username']]);

		$count = $query->count();

		if ($count && $project = $query->where(['name' => $params['project']])->first()) {
			if ($this->request->is('svg')) {
				$this->autoLayout = false;
				$this->autoRender = false;
				$this->response->type('svg');
				return $this->_show($project->state);
			} else if ($this->request->is('json')) {
				$this->set('state', $project->state);
				$this->set('_serialize', ['state', 'project']);
			}
			$this->set(compact('project'));
			return;
		}

		if ($this->request->is('png')) {
			throw new NotFoundException();
		} else if (!$this->request->is('json')) {
			$this->render($count ? 'not_tracked' : 'not_found');
		}
	}

	public function index() {
		if ('png' == $this->_requestType) {
			throw new NotFoundException();
		}

		$title = '';
		$query = $this->Projects->find('visible')
			->select(['username', 'name', 'description', 'state'])
			->order('watchers DESC')
			->limit(100);

		if (!empty($this->request->params['username'])) {
			$query = $query->where(['username' => $this->request->params['username']]);
			$title = ' by ' . $this->request->params['username'];
		} else {
			if (!empty($this->request->query['q'])) {
				$q = explode('/', $this->request->query['q']);
				if (count($q) > 1) {
					$query = $query->where([
						'username' => $q[0],
						'name' => $q[1]
					]);
				} else {
					$q = $q[0];
					$query = $query->where(['name' => $q])
						->orWhere(['username' => $q]);
				}
			}
			if (!empty($this->request->query['state'])) {
				$query = $query->where(['state' => $this->request->query['state']]);
			}
		}

		$count = $query->count();
		$title = __n("project", "projects", $count) . $title;
		$this->set([
			'count' => $count,
			'projects' => $query->toArray(),
			'title' => "$count $title",
			'_serialize' => ['count', 'projects']
		]);
	}

	protected function _show($state) {
		$colors = [
			'maintained' => 'brightgreen',
			'searching' => 'orange',
			'abandoned' => 'red'
		];
		echo file_get_contents('http://img.shields.io/badge/project-' . $state . '-' . $colors[$state] . '.svg?style=flat-square');
	}

}
