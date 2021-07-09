<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\Mailer\Mailer;
use Cake\Routing\Router;


/**
 * Users Controller
 *
 * @property \App\Model\Table\UsersTable $Users
 * @method \App\Model\Entity\User[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class UsersController extends AppController
{
    public function initialize(): void
    {
        parent::initialize();
        $this->loadComponent('RequestHandler');
        $this->loadComponent('Security');
    }
	
    public function beforeFilter(\Cake\Event\EventInterface $event)
    {
        parent::beforeFilter($event);
        // Configure certain actions to not require authentication
        $this->Authentication->addUnauthenticatedActions(['login', 'register', 'recover', 'renew']);

        // disable CSRF checks for ajax operations
		$this->Security->setConfig('unlockedActions', ['setbanner', 'setshowcase', 'remove']);
    }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Redirects according to user level.
     */
    public function index()
    {
        switch($this->Authentication->getIdentity()->get('level'))
        {
            case 1:
                return $this->redirect(['action' => 'edit']);
                break;
            case 2:
                return $this->redirect(['action' => 'admin']);
                break;
            default:
                $this->Authentication->logout();
                $this->Flash->error(__("Something went wrong, Sorry. Please try again later."));
                return $this->redirect(['action' => 'login']);
                break;
        }
    }

    /**
     * Register method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful register, renders view otherwise.
     */
    public function register()
    {
        $user = $this->Users->newEmptyEntity();
        if ($this->request->is('post')) {
            $data = $this->request->getData();

            if ($data['email'] !== $data['email2']) {
                $this->Flash->error(__("There seems to be a typo in your email address."));
                return;
            }

            $user = $this->Users->patchEntity($user, $this->request->getData());
			$user->data = '{"links":{},"name":"","about":"","tags":"","showcase":[],"icon":""}';
            if ($this->Users->save($user)) {
                $this->Flash->success(__("Thank you for signing up. Your account will be reviewed and enabled accordingly within 24 hours. Check back tomorrow!"));
                
				// notify tech support
                // $mailer = new Mailer('default');
                // $mailer
                    // ->setSubject(__("[EFO2021] new registration: {$user->email}"))
                    // ->deliver("New registration: \"{$user->email}\"; review at " . Router::url(['action' => 'admin'], true));
				
				return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__("There was some error. Please try again or contact tech support if this issue persists."));
        }
        $this->set(compact('user'));
    }

    public function recover()
    {
        $this->request->allowMethod(['get', 'post']);

        if ($this->request->is('post')) {
            $subject = $this->Users->find()
                ->where(['email' => $this->request->getData()['email']])
                ->first();
            
            if ($subject !== null) {
                // generate authorization token
                $token = uniqid();

                // associate token with account
                $subject->token = $token;
                $this->Users->save($subject);

                // construct recover link
                $link = Router::url(['controller' => 'users', 'action' => 'renew', $subject->email, $token], true);
                
                // construct email message
                $message = __("Hello,\n\nA request to reset your password for the Eurofurence Online 2021 Artist Registration has been received.\nTo do so, please follow the following link: {$link}.\n\nCheers,\ndraconigen, Eurofurence");

                // send email
                $mailer = new Mailer('default');
                $mailer
                    ->setSubject(__('[EFO2021] Artist Registration Password Recovery'))
                    ->deliver($message);
            }

            $this->Flash->success(__("Done. If the provided eMail address is registered, you'll shortly receive a recovery eMail from us. Check your spam folder and contact tech support if anything doesn't work."));
            return $this->redirect(['action' => 'login']);
        }
    }

    public function renew($email = null, $token = null) {
        if ($email == null || $token == null) {
            return $this->redirect(['action' => 'login']);
        }

        $user = $this->Users->find()->where(['email' => $email, 'token' => $token])->first();

        if ($user === null) {
            $this->Flash->error(__("The password recovery link seems to be expired. Try again or contact tech support."));
            return $this->redirect(['action' => 'login']);
        }

        $this->set('email', $user->email);

        if ($this->request->is('post')) {
            $user->password = $this->request->getData()['password'];
            $user->token = "";
            $this->Users->save($user);
            $this->Flash->success(__("Your password has been changed, you may now log in."));
            return $this->redirect(['action' => 'login']);
        }
    }

    public function login()
    {
        $this->request->allowMethod(['get', 'post']);
        $result = $this->Authentication->getResult();
        // regardless of POST or GET, redirect if user is logged in
        if ($result->isValid()) {
            if ($this->Authentication->getIdentity()->get('level') === 0) {
                $this->Authentication->logout();
                $this->Flash->error(__("Your account has not been enabled yet. If this takes unusually long, please contact tech support."));
                return $this->redirect(['action' => 'login']);
            }
            $redirect = $this->request->getQuery('redirect', [
                'action' => 'index',
            ]);

            return $this->redirect($redirect);
        }
        // display error if user submitted and authentication failed
        if ($this->request->is('post') && !$result->isValid()) {
            $this->Flash->error(__("Invalid username or password."));
        }
    }

    public function logout()
    {
        $result = $this->Authentication->getResult();
        // regardless of POST or GET, redirect if user is logged in
        if ($result->isValid()) {
            $this->Authentication->logout();
            return $this->redirect(['action' => 'login']);
        }
    }

    /**
     * Edit method
     *
     * @param string|null $override_id User id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $this->request->allowMethod(['get', 'post', 'put']);
		$errors = [];

        // get user associated with $id (or own user, if non-admin or $id == null)
        $subject = $this->get_user($id);

        // if user is invalid, fall back to own user entry
        if ($subject == null) {
            $this->Flash->error(__("Failed to retrieve user for edit."));
            $subject = $this->Users->get($ownid);
        }

        // save edit
        if ($this->request->is(['patch', 'post', 'put'])) {
            $subject = $this->Users->patchEntity($subject, $this->request->getData());
			
			$errors = $this->validate(json_decode($subject->data));
			
			if (empty($errors)) {
				if ($this->Users->save($subject)) {
					$this->Flash->success(__("Changes have been saved."));
					return $this->redirect(['action' => 'index']);
				}
				// debug($subject);
				$this->Flash->error(__("Unknown error, please contact tech support."));
			}
			else {
				$this->Flash->error(__("Changes could not be saved, please check the error details below."));
			}
		}
		
		// prepare data
		$subject->data = mb_ereg_replace("'", "\'", $subject->data);
		$subject->data = mb_ereg_replace('"', '\"', $subject->data);
        
        $this->set('subject', $subject);
		$this->set('errors', $errors);
		$this->set('banner_upload_url', Router::url(['action' => 'setbanner'], true));
		$this->set('showcase_upload_url', Router::url(['action' => 'setshowcase'], true));
		$this->set('remove_image_url', Router::url(['action' => 'remove'], true));
    }
	
	/**
	 * Upload an avatar / banner file.
	 * @param Number user id
	 * @return String url of the successfully uploaded image as JSON.
	 */
	public function setbanner($id = null) {
		return $this->upload($id, 'icon');
	}
	
	/**
	 * Upload files to user showcase.
	 * @param Number user id
	 * @return String url of the successfully uploaded images as JSON.
	 */
	public function setshowcase($id = null) {
		return $this->upload($id, 'showcase');
	}
		
	/**
	 * handle image file uploads
	 * @param Number user id
	 * @param Number mode: 0 => icon, 1 => showcase
	 * @return String url of the successfully uploaded images as JSON.
	 */
    private function upload($id, $mode) {
        $this->autoRender = false;
		
        if (!$this->request->is('post'))
			return $this->redirect(['action' => 'index']);

		$return = ['errors' => []];
		$status = 200;

		// get user associated with $id (or own user, if non-admin or $id == null)
        $subject = $this->get_user($id);
		
		$max_images = 6;
        $max_filesize = 5 * 1024 * 1024; // 5MB
		$files_dir = 'files';

		$files = $this->request->getData('files');

		if (!isset($files)) {
            $return['errors']['general'] = 'no files';
            return $this->response->withType('application/json')
            ->withStatus(400)
            ->withStringBody(json_encode($return));
        }

        $data = json_decode($subject->data);

        for ($i=0; $i < count($files); $i++) {
            $err  = $files[$i]->getError();
            
            if (!empty($err)) {
                $return['errors'][$i] = 'php file processing error: ' . $err;
                continue;
            }

            $orig = $files[$i]->getClientFilename();

            if ($mode === 'showcase' && count($data->showcase) >= $max_images) {
                $return['errors'][$i] = "{$orig}: showcase full";
                continue;
            }

            $size = $files[$i]->getSize();

            if ($size > $max_filesize) {
                $return['errors'][$i] = "{$orig}: file too large ({$size} bytes)";
                continue;
            }
            
            $type = $files[$i]->getClientMediaType();

            if (!in_array($type, ['image/jpeg', 'image/png', /*,'image/gif'*/])) {
                $return['errors'][$i] = "{$orig}: invalid file type \"{$type}\"";
                continue;
            }
            
		    $name  = uniqid();
            
            switch($type) {
                case 'image/jpeg': $name .= '.jpg'; break;
                case 'image/png' : $name .= '.png'; break;
                case 'image/gif' : $name .= '.gif'; break;
            }
            
            $path = $files_dir . DS . $subject->id;

            if (!file_exists($path))
                mkdir($path, 0777, true);

            $files[$i]->moveTo($path . DS . $name);

            switch($mode) {
                case 'icon':     $data->icon = $path . '/' . $name; break;
                case 'showcase': $data->showcase[] = $path . '/' . $name; break;
            }
        }

        $d = json_encode($data);

        if ($d) {
            $subject->data = $d;
        }
        else {
            $return['errors']['json'] = "json encoding error: " . serialize($data);
            return $this->response->withType('application/json')
            ->withStatus(500)
            ->withStringBody(json_encode($return));
        }

        if (!$this->Users->save($subject)) {
            $return['errors']['userdata'] = "failed to save userdata: " . serialize($data);
            return $this->response->withType('application/json')
            ->withStatus(500)
            ->withStringBody(json_encode($return));
        }

        // unless there's a general errors, return 200 for there might be successfully processed file
        // if (!empty($return['errors'])) {
            // $status = 400;
        // }

        $return['icon'] = $data->icon;
        $return['showcase'] = $data->showcase;

        return $this->response->withType('application/json')
        ->withStatus($status)
        ->withStringBody(json_encode($return));
    }

	/**
	 * remove an image from user data listing and filesystem
	 * @param String relative file path like files/13/icon.jpg
	 * @param Number user id
	 */
    public function remove($id = null) {
        $this->autoRender = false;
		
        if (!$this->request->is('delete'))
			return $this->redirect(['action' => 'index']);

		$return = ['errors' => []];
		$status = 200;

		// get user associated with $id (or own user, if non-admin or $id == null)
        $subject = $this->get_user($id);

        $filepath = $this->request->getData()[0];

        if ($filepath == null) {
            $return['errors']['general'] = 'missing payload';
			return $this->response->withType('application/json')
			->withStatus(400)
			->withStringBody(json_encode($return));
		}

        $data = json_decode($subject->data);

        // check if user is permitted to that file
        if (
			$filepath !== $data->icon &&
			!in_array($filepath, $data->showcase)
		) {
			$return['errors'][] = "{$filepath}: permission denied to user {$subject->id}";
            return $this->response->withType('application/json')
			->withStatus(403)
			->withStringBody(json_encode($return));
		}

        // delete if icon
        if ($filepath === $data->icon)
        $data->icon = "";
                    
        // delete if in showcase
        $offset = array_search($filepath, $data->showcase, true);
        if ($offset !== false) {
            array_splice($data->showcase, $offset, 1);
        }

        // save user data
        $d = json_encode($data);

        if ($d) {
            $subject->data = $d;
        }
        else {
            $return['errors']['json'] = "json encoding error: " . serialize($data);
            return $this->response->withType('application/json')
            ->withStatus(500)
            ->withStringBody(json_encode($return));
        }

        if (!$this->Users->save($subject)) {
            $return['errors']['userdata'] = "failed to save userdata: " . serialize($data);
            return $this->response->withType('application/json')
            ->withStatus(500)
            ->withStringBody(json_encode($return));
        }

        // remove from filesystem
        if (file_exists($filepath)) {
            if (!unlink($filepath)) {
                $return['errors']['filesystem'] = "{$filepath}: failed to delete";
			}
		}
        else 
            $return['errors']['filesystem'] = "{$filepath}: file not found";

        $return['icon'] = $data->icon;
        $return['showcase'] = $data->showcase;

        return $this->response->withType('application/json')
        ->withStatus($status)
        ->withStringBody(json_encode($return));
    }

	/**
	 * save image to user data in database
	 * @param User user to save data for
	 * @param String url / file name to save
	 * @return Array: [status => bool, msg = ""]
	 */
	private function save_user_image($user, $url, $set_banner) {
		$max_images = 6;
		$showcase = 'showcase';
		
		$data = json_decode($user->data);
			
		if ($set_banner)
			$data->icon = $url;
		
		else if (!$set_banner && count($data->{$showcase}) < $max_images)
			$data->{$showcase}[] = $url;
		
		$user->data = json_encode($data);
		
		$this->Users->save($user);
		
		return ['icon' => $data->icon, 'showcase' => $data->showcase];
	}

    /**
     * Admin method
     *
     * @return renders view.
     */
    public function admin()
    {
        if ($this->Authentication->getIdentity()->get('level') >= 2) {
            $this->set('users', $this->Users->find());
            $this->set('enableurl', Router::url(['controller' => 'users', 'action' => 'enable']));
            $this->set('editurl', Router::url(['controller' => 'users', 'action' => 'edit']));
            $this->set('deleteurl', Router::url(['controller' => 'users', 'action' => 'delete']));
        }
        else {
            $this->Flash->error(__("Authorization error."));
            return $this->redirect(['action' => 'index']);
        }
    }

    /**
     * Delete method
     *
     * @param string|null $id User id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function enable($id = null, $level = null)
    {
        if ($id == null || $level == null || $this->Authentication->getIdentity()->get('level') < 2) {
            $this->Flash->error(__("An error occurred."));
            return $this->redirect(['action' => 'index']);
        }

        $subject = $this->Users->get($id);

        if ($subject == null) {            
            $this->Flash->error(__("Invalid user."));
            return $this->redirect(['action' => 'index']);
        }

        $subject->level = $level;

        if ($this->Users->save($subject)) {
            $this->Flash->success(__("User level changed."));
            return $this->redirect(['action' => 'index']);
        }
        else {
            $this->Flash->error(__("Error saving user details."));
            return $this->redirect(['action' => 'index']);
        }
    }

     /**
     * Delete method
     *
     * @param string|null $id User id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['get']);
        $subject = $this->get_user($id);

        if ($subject == null) {
            $this->Flash->error(__("Error finding this user."));
            return $this->redirect(['action' => 'index']);
        }

        if ($subject->get('level') >= 2) {
            $this->Flash->error(__("You cannot delete an admin."));
            return $this->redirect(['action' => 'index']);
        }

        if ($this->Users->delete($subject)) {
            $this->Flash->success(__("Account deleted."));
            // if user deleted themselves, logout
            if ($this->Authentication->getIdentity()->get('id') === $subject->id) {
                $this->Authentication->logout();
            }
        } else {
            $this->Flash->error(__("Account could not be deleted. Please try again."));
        }

        return $this->redirect(['action' => 'index']);
    }

    /**
     * If called by an admin, returns the identity with $id.
     * If $id == null or current user is not an admin, returns own identity.
     * @param Number|Null $id of the desired user or null for own identity
     * @return User user entry of $id if current is admin, otherwise own identity 
     */
    private function get_user($override_id) {
        // get own $id
        $user = $this->Users->get($this->Authentication->getIdentity()->get('id'));
        $id = $user->id;

        // if an admin desires to edit another user, override $id 
        if ($override_id !== null && $user->level >= 2) {
            $id = $override_id;
        }

        // get user associated with $id
        return $this->Users->get($id);
    }
	
	/**
	 * Validate user input from the edit action.
	 * @param Object data payload of an user
	 * @return Array of error identifying strings
	 */
	private function validate($d) {
		$errors = [];
		
		if (strlen($d->name) < 3)
			$errors[] = 'name_too_short';
		
		if (strlen($d->name) > 100)
			$errors[] = 'name_too_long';
		
		if (strlen($d->about) > 4000)
			$errors[] = 'about_too_long';
		
		if (strlen($d->tags) > 300)
			$errors[] = 'tags_too_long';
		
		if (str_contains($d->tags, ','))
			$errors[] = 'tags_invalid_separator';
		
		if (str_contains($d->tags, ';'))
			$errors[] = 'tags_invalid_separator';
		
		if (str_contains($d->tags, '+'))
			$errors[] = 'tags_invalid_separator';
		
		foreach($d->links as $key => $value) {
			if (strlen($value) < 3)
				$errors[] = "links_${key}_too_short";
			
			if (strlen($value) > 100)
				$errors[] = "links_${key}_too_long";
		}
		
		return $errors;
	}
}