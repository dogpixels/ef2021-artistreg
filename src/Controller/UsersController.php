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
    public function beforeFilter(\Cake\Event\EventInterface $event)
    {
        parent::beforeFilter($event);
        // Configure the login action to not require authentication, preventing
        // the infinite redirect loop issue
        $this->Authentication->addUnauthenticatedActions(['login', 'register', 'recover', 'renew']);
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
            if ($this->Users->save($user)) {
                $this->Flash->success(__("Thank you for signing up. Your account will be reviewed and enabled accordingly within 24 hours. Check back tomorrow!"));
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
                $token = uniqid('', true);

                // associate token with account
                $subject->token = $token;
                $this->Users->save($subject);

                // construct recover link
                $link = Router::url(['controller' => 'users', 'action' => 'renew', $subject->id, $token]);
                
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

    public function renew($id = null, $token = null) {
        if ($id == null || $token == null) {
            return $this->redirect(['action' => 'login']);
        }

        $user = $this->Users->find()->where(['id' => $id, 'token' => $token])->first();

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
            if ($this->Users->save($subject)) {
                $this->Flash->success(__("Changes have been saved."));
                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__("Error saving this user."));
        }
        
        $this->set('subject', $subject);
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
}
