<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\Routing\Router;


/**
 * Artists JSON output Controller
 *
 * @property \App\Model\Table\UsersTable $Users
 */
class ArtistsController extends AppController
{
	public function initialize(): void
	{
        parent::initialize();
		$this->loadComponent('RequestHandler');
		$this->loadModel('Users');
	}

    public function beforeFilter(\Cake\Event\EventInterface $event)
    {
        parent::beforeFilter($event);
        $this->Authentication->addUnauthenticatedActions(['index', 'preview']);
    }

	public function index() {
		$this->set('artists', $this->fetch());
		$this->viewBuilder()->setClassName('Json');
		$this->viewBuilder()->setOption('serialize', 'artists');
	}

	public function preview($id) {
		$this->set('artists', $this->fetch($id));
		$this->viewBuilder()->setClassName('Json');
		$this->viewBuilder()->setOption('serialize', 'artists');
	}

	private function fetch($id = null) {
		$result = $this->Users->find()->select(['data'])->where(['level' => 1]);

		if ($id !== null) {
			$result = $this->Users->find()->select(['data'])->where(['level' => 1, 'id' => $id]);
		}

		$data = [];

		foreach ($result as $row) {
			if ($row->data !== null) {
				$d = json_decode(htmlspecialchars_decode($row->data));
				if ($d->name !== "")
					$data[] = $d;
			}
		}

		return $data;
	}
}