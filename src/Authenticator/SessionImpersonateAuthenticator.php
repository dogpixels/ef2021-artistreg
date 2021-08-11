<?php
// source: https://discourse.cakephp.org/t/cakephp-4-x-impersonate-login-as-different-user/9559/3
declare(strict_types=1);

namespace App\Authenticator;

use ArrayAccess;
use ArrayObject;
use Authentication\Authenticator\AbstractAuthenticator;
use Authentication\Authenticator\Result;
use Authentication\Authenticator\ResultInterface;
use Psr\Http\Message\ServerRequestInterface;

class SessionImpersonateAuthenticator extends AbstractAuthenticator
{
    /**
     * Default config for this object.
     * - `sessionKey` Session key.
     *
     * @var array
     */
    protected $_defaultConfig = [
        'sessionKey' => 'impersonating',
    ];

    /**
     * @inheritDoc
     */
    public function authenticate(ServerRequestInterface $request): ResultInterface
    {
        $sessionKey = $this->getConfig('sessionKey');
        /** @var \Cake\Http\Session $session */
        $session = $request->getAttribute('session');
        $user = $session->read($sessionKey);

        if (!$user) {
            return new Result(null, ResultInterface::FAILURE_IDENTITY_NOT_FOUND);
        }

        if (!($user instanceof ArrayAccess)) {
            $user = new ArrayObject($user);
        }

        return new Result($user, ResultInterface::SUCCESS);
    }
}