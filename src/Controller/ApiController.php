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
class ApiController extends AppController
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
        $this->Authentication->addUnauthenticatedActions(['login', 'register', 'recover', 'renew']);
    }

    public function index()
    {
        $status = 200;
        $data = ['errors' => [], 'messages' => [], 'redirect' => "", 'payload' => ""];

        switch($this->Authentication->getIdentity()->get('level'))
        {
            case 1:
                $data['redirect'] = Router::url(['action' => 'edit']);
                break;
            case 2:
                $data['redirect'] = Router::url(['action' => 'admin']);
                break;
            default:
                $this->Authentication->logout();
                $data['errors'][] = "Something went wrong, Sorry. Please try again later.";
                $data['redirect'] = Router::url(['action' => 'login']);
                $status = 500;
                break;
        }

        return $this->response->withType('application/json')->withStatus($status)->withStringBody(json_encode($data));
    }

    public function register()
    {
        $status = 200;
        $data = ['errors' => [], 'messages' => [], 'redirect' => "", 'payload' => ""];

        $user = $this->Users->newEmptyEntity();

        if ($this->request->is('post')) {
            $data = $this->request->getData();

            if ($data['email'] !== $data['email2']) {
                $status = 400;
                $data['errors'][] = "There seems to be a typo in your email address.";
                return $this->response->withType('application/json')->withStatus($status)->withStringBody(json_encode($data));
            }
            else {
                $user = $this->Users->patchEntity($user, $this->request->getData());
                if ($this->Users->save($user)) {
                    $data['messages'][] = "Thank you for signing up. Your account will be reviewed and enabled accordingly within 24 hours. Check back tomorrow!";
                    $data['redirect'] = Router::url(['action' => 'index']);
                    return $this->response->withType('application/json')->withStatus($status)->withStringBody(json_encode($data));
                }
            }
            $data['errors'][] = "There was some error. Please try again or contact tech support if this issue persists.";
        }
        $data['payload'] = compact('user');        
        return $this->response->withType('application/json')->withStatus($status)->withStringBody(json_encode($data));
    }

    public function recover()
    {
        $status = 200;
        $data = ['errors' => [], 'messages' => [], 'redirect' => "", 'payload' => ""];

        $this->request->allowMethod(['post']);

        $subject = $this->Users->find()
            ->where(['email' => $this->request->getData()['email']])
            ->first();
        
        if ($subject !== null) {
            // generate authorization token
            $token = uniqid('', true);

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

            $data['messages'][] = "Done. If the provided eMail address is registered, you'll shortly receive a recovery eMail from us. Check your spam folder and contact tech support if anything doesn't work.";
            $data['redirect'] = Router::url(['action' => 'login']);
        } 
        else {
            $data['errors'][] = "Invalid data.";
            $status = 404;
        }

        return $this->response->withType('application/json')->withStatus($status)->withStringBody(json_encode($data));
    }

    public function renew($email = null, $token = null) {
        $status = 200;
        $data = ['errors' => [], 'messages' => [], 'redirect' => "", 'payload' => ""];

        if ($email == null || $token == null) {
            $status = 400;
            $data['errors'] = "Invalid data.";
            $data['redirect'] = Router::url(['action' => 'login']);
            return $this->response->withType('application/json')->withStatus($status)->withStringBody(json_encode($data));
        }

        $user = $this->Users->find()->where(['email' => $email, 'token' => $token])->first();

        if ($user === null) {
            $status = 401;
            $data['errors'][] = "The password recovery link seems to be expired. Try again or contact tech support.";
            $data['redirect'] = Router::url(['action' => 'login']);
            return $this->response->withType('application/json')->withStatus($status)->withStringBody(json_encode($data));
        }

        $data['payload'] = ["email" => $user->email];

        if ($this->request->is('post')) {
            $user->password = $this->request->getData()['password'];
            $user->token = "";
            $this->Users->save($user);
            $data['messages'][] = "Your password has been changed, you may now log in.";
            $data['redirect'] = Router::url(['action' => 'login']);
        }

        return $this->response->withType('application/json')->withStatus($status)->withStringBody(json_encode($data));
    }

    /**
     * 200: login successful
     * 403: login successful, but user level 0 (not enabled yet) and logged out again
     * 401: login not successful (wrong username or password)
     */
    public function login()
    {
        $this->request->allowMethod(['get', 'post']);

        $status = 200;
        $data = ['errors' => [], 'messages' => [], 'redirect' => "", 'payload' => ""];

        if($this->request->is('get')) {
            // debug($this->request->getParam('_csrfToken'));
            // $data['csrf'] = $this->request->getParam('_csrfToken');
            
            return $this->response->withType('application/json')->withStatus($status)->withStringBody(json_encode($data));
        }

        $result = $this->Authentication->getResult();
        if ($result->isValid()) {
            if ($this->Authentication->getIdentity()->get('level') === 0) {
                $this->Authentication->logout();
                $status = 403;
                $data['errors'][] = "Your account has not been enabled yet. If this takes unusually long, please contact tech support.";
                $data['redirect'] = Router::url(['action' => 'login']);
            }
        }
        else {
            $status = 401;
            $data['errors'][] = "Invalid username or password.";
        }

        return $this->response->withType('application/json')->withStatus($status)->withStringBody(json_encode($data));
    }

    /**
     * 200: user found, logout successful
     * 401: user was not logged in
     */
    public function logout()
    {
        $status = 200;
        $data = ['errors' => [], 'messages' => [], 'redirect' => "", 'payload' => ""];

        $result = $this->Authentication->getResult();
        if ($result->isValid()) {
            $this->Authentication->logout();
            $data['redirect'] = Router::url(['action' => 'login']);
        }
        else {
            $status = 401;
        }
        
        return $this->response->withType('application/json')->withStatus($status)->withStringBody(json_encode($data));
    }

    /**
     * 200 user edit successful
     * 404 user not found, falling back to logged in user
     * 500 user edit was not successful (patch failed)
     */
    public function edit($id = null)
    {
        $status = 200;
        $data = ['errors' => [], 'messages' => [], 'redirect' => "", 'payload' => ""];

        $this->request->allowMethod(['get', 'post', 'put']);

        // get user associated with $id (or own user, if non-admin or $id == null)
        $subject = $this->get_user($id);

        // if user is invalid, fall back to own user entry
        if ($subject == null) {
            $data['errors'][] = "Failed to retrieve user for edit.";
            $status = 404;
            $subject = $this->Users->get($ownid);
        }

        // save edit
        if ($this->request->is(['patch', 'post', 'put'])) {
            $subject = $this->Users->patchEntity($subject, $this->request->getData());
            if ($this->Users->save($subject)) {
                $data['messages'][] = "Changes have been saved.";
                $data['redirect'] = Router::url(['action' => 'index']);
            }
            else {
                $status = 500;
                $data['errors'][] = "Error saving this user.";
            }
        }

        $data['payload'] = $subject;

        return $this->response->withType('application/json')->withStatus($status)->withStringBody(json_encode($data));
    }

    /**
     * 200 OK
     * 401 user level below 2
     */
    public function admin()
    {
        $status = 200;
        $data = ['errors' => [], 'messages' => [], 'redirect' => "", 'payload' => ""];

        if ($this->Authentication->getIdentity()->get('level') >= 2) {
            $data['payload'] = $this->Users->find();
        }
        else {
            $status = 401;
            $data['redirect'] = Router::url(['action' => 'index']);
        }

        return $this->response->withType('application/json')->withStatus($status)->withStringBody(json_encode($data));
    }

    /**
     * 200 subject user level set
     * 400 parameters not specified or current user level below 2
     * 404 subject user not found
     * 500 error saving subject user
     */
    public function enable($id = null, $level = null)
    {
        $status = 200;
        $data = ['errors' => [], 'messages' => [], 'redirect' => "", 'payload' => ""];

        if ($id == null || $level == null || $this->Authentication->getIdentity()->get('level') < 2) {
            $status = 400;
            $data['errors'][] = "An error occurred.";
            $data['redirect'] = Router::url(['action' => 'index']);
            return $this->response->withType('application/json')->withStatus($status)->withStringBody(json_encode($data));
        }

        $subject = $this->Users->get($id);

        if ($subject == null) {
            $status = 404;
            $data['errors'][] = "Invalid user.";
            $data['redirect'] = Router::url(['action' => 'index']);
            return $this->response->withType('application/json')->withStatus($status)->withStringBody(json_encode($data));
        }

        $subject->level = $level;

        if ($this->Users->save($subject)) {
            $data['messages'][] = "User level changed.";
            $data['redirect'] = Router::url(['action' => 'index']);
        }
        else {
            $status = 500;
            $data['errors'][] = "Error saving user details.";
            $data['redirect'] = Router::url(['action' => 'index']);
        }
        
        return $this->response->withType('application/json')->withStatus($status)->withStringBody(json_encode($data));
    }

    /**
     * 200 subject user deleted
     * 401 current user level below 2
     * 404 subject user not found
     * 500 error saving subject user
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['get']);

        $status = 200;
        $data = ['errors' => [], 'messages' => [], 'redirect' => "", 'payload' => ""];

        $subject = $this->get_user($id);

        if ($subject == null) {
            $status = 404;
            $data['errors'][] = "Error finding this user.";
            $data['redirect'] = Router::url(['action' => 'index']);
            return $this->response->withType('application/json')->withStatus($status)->withStringBody(json_encode($data));
        }

        if ($subject->get('level') >= 2) {
            $status = 401;
            $data['errors'][] = "You cannot delete an admin.";
            $data['redirect'] = Router::url(['action' => 'index']);
            return $this->response->withType('application/json')->withStatus($status)->withStringBody(json_encode($data));
        }

        if ($this->Users->delete($subject)) {
            $data['messages'][] = "Account deleted.";
            // if user deleted themselves, logout
            if ($this->Authentication->getIdentity()->get('id') === $subject->id) {
                $this->Authentication->logout();
            }
        } else {
            $status = 500;
            $data['errors'][] = "Account could not be deleted. Please try again.";
        }

        $data['redirect'] = Router::url(['action' => 'index']);
        
        return $this->response->withType('application/json')->withStatus($status)->withStringBody(json_encode($data));
    }

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
}
