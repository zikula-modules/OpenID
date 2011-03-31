<?php
/**
 * Copyright Zikula Foundation 2011 - Zikula Application Framework
 *
 * This work is contributed to the Zikula Foundation under one or more
 * Contributor Agreements and licensed to You under the following license:
 *
 * @license GNU/LGPv3 (or at your option any later version).
 * @package OpenID
 *
 * Please see the NOTICE file distributed with this source code for further
 * information regarding copyright and licensing.
 */

/**
 * The user authentication services for the log-in process through the OpenID protocol.
 */
class OpenID_Api_Authentication extends Zikula_Api_AbstractAuthentication
{
    /**
     * The list of valid authentication methods that this module supports.
     *
     * This list is meant to be immutable, therefore it would be prudent to
     * only expose copies, and unwise to expose explicit references.
     *
     * @var array
     */
    protected $authenticationMethods = array();

    /**
     * Initialize the API instance with the list of valid authentication methods supported.
     * 
     * @return void
     */
    protected function  postInitialize() {
        parent::postInitialize();

        $authenticationMethod = new Users_Helper_AuthenticationMethod(
                $this->name,
                'Google',
                $this->__('Google Account'),
                $this->__('Google Account'));
        // Google requires an SSL connection.
        if (function_exists('openssl_open')) {
            $authenticationMethod->enableForAuthentication();
        } else {
            $authenticationMethod->disableForAuthentication();
        }
        $this->authenticationMethods['Google'] = $authenticationMethod;

        $authenticationMethod = new Users_Helper_AuthenticationMethod(
                $this->name,
                'OpenID',
                $this->__('OpenID'),
                $this->__('OpenID'));
        $authenticationMethod->enableForAuthentication();
        $this->authenticationMethods['OpenID'] = $authenticationMethod;

        $authenticationMethod = new Users_Helper_AuthenticationMethod(
                $this->name,
                'PIP',
                $this->__('Symantec PIP'),
                $this->__('Symantec (VeriSign) Personal Identity Portal'));
        $authenticationMethod->enableForAuthentication();
        $this->authenticationMethods['PIP'] = $authenticationMethod;
    }

    /**
     * Informs the calling function whether this authmodule is reentrant or not.
     *
     * The OpenID for Zikula module is reentrant. It must redirect to the OpenID provider for authorization.
     *
     * @return bool true.
     */
    public function isReentrant()
    {
        return true;
    }

    /**
     * Indicate whether this module supports the indicated authentication method.
     * 
     * Parameters passed in $args:
     * ---------------------------
     * string $args['method'] The name of the authentication method for which support is enquired.
     *
     * @param array $args All arguments passed to this function, see above.
     * 
     * @return boolean True if the indicated authentication method is supported by this module; otherwise false.
     * 
     * @throws Zikula_Exception_Fatal Thrown if invalid parameters are sent in $args.
     */
    public function supportsAuthenticationMethod(array $args)
    {
        if (isset($args['method']) && is_string($args['method'])) {
            $methodName = $args['method'];
        } else {
            throw new Zikula_Exception_Fatal($this->__('An invalid \'method\' parameter was received.'));
        }

        $isSupported = (bool)isset($this->authenticationMethods[$methodName]);

        return $isSupported;
    }

    /**
     * Indicates whether a specified authentication method that is supported by this module is enabled for use.
     * 
     * Parameters passed in $args:
     * ---------------------------
     * string $args['method'] The name of the authentication method for which support is enquired.
     *
     * @param array $args All arguments passed to this function, see above.
     * 
     * @return boolean True if the indicated authentication method is enabled by this module; otherwise false.
     * 
     * @throws Zikula_Exception_Fatal Thrown if invalid parameters are sent in $args.
     */
    public function isEnabledForAuthentication(array $args)
    {
        if (isset($args['method']) && is_string($args['method'])) {
            if (isset($this->authenticationMethods[$args['method']])) {
                $authenticationMethod = $this->authenticationMethods[$args['method']];
            } else {
                throw new Zikula_Exception_Fatal($this->__f('An unknown method (\'%1$s\') was received.', array($args['method'])));
            }
        } else {
            throw new Zikula_Exception_Fatal($this->__('An invalid \'method\' parameter was received.'));
        }

        return $authenticationMethod->isEnabledForAuthentication();
    }

    /**
     * Retrieves an array of authentication methods defined by this module, possibly filtered by only those that are enabled.
     * 
     * Parameters passed in $args:
     * ---------------------------
     * integer $args['filter'] Either {@link FILTER_ENABLED} (value 1), {@link FILTER_NONE} (value 0), or not present; allows the result to be filtered.
     *                              If this argument is FILTER_ENABLED, then only those authentication methods that are also enabled are returned.
     *
     * @param array $args All arguments passed to this function.
     * 
     * @return array An array containing the authentication methods defined by this module, possibly filtered by only those that are enabled.
     * 
     * @throws Zikula_Exception_Fatal Thrown if invalid parameters are sent in $args.
     */
    public function getAuthenticationMethods(array $args = null)
    {
        if (isset($args) && isset($args['filter'])) {
            if (is_numeric($args['filter']) && ((int)$args['filter'] == $args['filter'])) {
                switch($args['filter']) {
                    case Zikula_Api_AbstractAuthentication::FILTER_ENABLED:
                        $filter = $args['filter'];
                        break;
                    default:
                        throw new Zikula_Exception_Fatal($this->__f('An unknown value for the \'filter\' parameter was received (\'%1$d\').', array($args['filter'])));
                        break;
                }
            } else {
                throw new Zikula_Exception_Fatal($this->__f('An invalid value for the \'filter\' parameter was received (\'%1$s\').', array($args['filter'])));
            }
        } else {
            $filter = Zikula_Api_AbstractAuthentication::FILTER_NONE;
        }

        switch ($filter) {
            case Zikula_Api_AbstractAuthentication::FILTER_ENABLED:
                $authenticationMethods = array();
                foreach ($this->authenticationMethods as $index => $authenticationMethod) {
                    if ($authenticationMethod->isEnabledForAuthentication()) {
                        $authenticationMethods[$authenticationMethod->getMethod()] -> $authenticationMethod;
                    }
                }
                break;
            default:
                $authenticationMethods = $this->authenticationMethods;
                break;
        }

        return $authenticationMethods;
    }

    /**
     * Authenticates authentication info with the authenticating source, returning a simple boolean result.
     *
     * ATTENTION: Any function that causes this function to be called MUST specify a return URL, and therefore
     * must be reentrant. In other words, in order to call this function, there must exist a controller function
     * (specified by the return URL) that the OpenID server can return to, and that function must restore the
     * pertinent state for the user as if he never left this site! Session variables should be used to store all
     * pertinent variables, and those must be re-read into the user's state when the return URL is called back
     * by the OpenID server.
     *
     * Note that, despite this function's name, there is no requirement that a password be part of the authentication info.
     * Merely that enough information be provided in the authentication info array to unequivocally authenticate the user. For
     * most authenticating authorities this will be the equivalent of a user name and password, but--again--there
     * is no restriction here. This is not, however, a "user exists in the system" function. It is expected that
     * the authenticating authority validate what ever is used as a password or the equivalent thereof.
     *
     * This function makes no attempt to match the given authentication info with a Zikula user id (uid). It simply asks the
     * authenticating authority to authenticate the authentication info provided. No "login" should take place as a result of
     * this authentication.
     *
     * This function may be called to initially authenticate a user during the registration process, or may be called
     * for a user already logged in to re-authenticate his password for a security-sensitive operation. This function
     * should merely authenticate the user, and not perform any additional login-related processes.
     *
     * This function differs from authenticateUser() in that no attempt is made to match the authentication info with and map to a
     * Zikula user account. It does not return a Zikula user id (uid).
     *
     * This function differs from login()  in that no attempt is made to match the authentication info with and map to a
     * Zikula user account. It does not return a Zikula user id (uid). In addition this function makes no attempt to
     * perform any login-related processes on the authenticating system.
     *
     * @param array $args All arguments passed to this function.
     *                      array   authentication_info    The authentication info needed for this authmodule, including any user-entered password.
     *
     * @return boolean True if the authentication info authenticates with the source; otherwise false on authentication failure or error.
     */
    public function checkPassword(array $args)
    {
        if (!isset($args['authentication_info']) || empty($args['authentication_info']) || !is_array($args['authentication_info'])) {
            return LogUtil::registerArgsError();
        }

        if (!isset($args['authentication_method']) || empty($args['authentication_method']) || !is_array($args['authentication_method'])) {
            return LogUtil::registerArgsError();
        }

        $openidHelper = OpenID_Helper_Builder::buildInstance($args['authentication_method']['method'], $args['authentication_info']);
        if (!isset($openidHelper) || ($openidHelper === false)) {
            return LogUtil::registerArgsError();
        }

        if (isset($args['reentrant_url']) && !empty($args['reentrant_url'])) {
            $reentrantURL = $args['reentrant_url'];
        } else {
            // TODO - Maybe we should error out, because there is no guarantee that the current URL is reentrant.
            $reentrantURL = System::getCurrentUrl();
        }

        $openidNamespace = $this->request->getGet()->get('openid_ns', null);
        $openidConsumer = @new Auth_OpenID_Consumer(new OpenID_PHPOpenID_OpenIDStore(), new OpenID_PHPOpenID_SessionStore());

        if (!isset($openidNamespace) || empty($openidNamespace)) {
            // We are NOT returing from a previous redirect to the authorizing provider

            // Save the reentrantURL for later use
            SessionUtil::requireSession();
            $this->request->getSession()->clearNamespace('OpenID_Authentication_checkPassword');
            $this->request->getSession()->set('reentrant_url', $reentrantURL, 'OpenID_Authentication_checkPassword');

            // Build a request instance
            $openidAuthRequest = @$openidConsumer->begin($openidHelper->getSuppliedId());

            if ($openidAuthRequest instanceof Auth_OpenID_AuthRequest) {
                // For OpenID 1, send a redirect.  For OpenID 2, use a Javascript
                // form to send a POST request to the server.
                if ($openidAuthRequest->shouldSendRedirect()) {
                    $redirectURL = $openidAuthRequest->redirectURL(System::getBaseUrl(), $reentrantURL, false);

                    // If the redirect URL can't be built, display an error
                    // message.
                    if (Auth_OpenID::isFailure($redirectURL)) {
                        if (System::isDevelopmentMode()) {
                            return LogUtil::registerError($this->__f("Could not redirect to OpenID server: %1$s", $redirectURL->message));
                        } else {
                            return LogUtil::registerError($this->__("Could not redirect to OpenID server."));
                        }
                    } else {
                        // Send redirect.
                        header("Location: ".$redirectURL);
                        System::shutDown();
                    }
                } else {
                    // Generate form markup and render it.
                    $postFormID = 'openid_message';
                    $postFormHTML = $openidAuthRequest->htmlMarkup(System::getBaseUrl(), $reentrantURL,
                                                           false, array('id' => $postFormID));

                    // Display an error if the form markup couldn't be generated;
                    // otherwise, render the HTML.
                    if (Auth_OpenID::isFailure($postFormHTML)) {
                        if (System::isDevelopmentMode()) {
                            return LogUtil::registerError($this->__f("Could not redirect to OpenID server: %1$s", $postFormHTML->message));
                        } else {
                            return LogUtil::registerError($this->__("Could not redirect to OpenID server."));
                        }
                    } else {
                        print $postFormHTML;
                        System::shutDown();
                    }
                }
            } else {
                if (function_exists('openssl_open')) {
                    return LogUtil::registerError($this->__('Unable to contact the OpenID server to start the authorization process.'));
                } else {
                    return LogUtil::registerError($this->__('Unable to contact the OpenID server to start the authorization process. It is possible this is because outgoing SSL connections are not supported by this server.'));
                }
            }
        } else {
            // We ARE returning from a previous redirect to the OpenID server

            // Get the reentrantURL we saved earlier
            $reentrantURL = $this->request->getSession()->get('reentrant_url', '', 'OpenID_Authentication_checkPassword');
            $this->request->getSession()->clearNamespace('OpenID_Authentication_checkPassword');

            // Get the response status
            $response = $openidConsumer->complete($reentrantURL);

            // Check the response status.
            if ($response->status == Auth_OpenID_CANCEL) {
                // This means the authentication was cancelled.
                return LogUtil::registerError($this->__('OpenID authorization was canceled on the OpenID Server.'));
            } else if ($response->status == Auth_OpenID_FAILURE) {
                // Authentication failed; display the error message.
                return LogUtil::registerError($this->__f('OpenID authorization failed. The message from the OpenID Server was: %1$s', array($response->message)));
            } else if ($response->status == Auth_OpenID_SUCCESS) {
                // This means the authentication succeeded; extract the
                // identity URL and Simple Registration data (if it was
                // returned).
                $claimedID = $response->getDisplayIdentifier();

                // Set a session variable, if necessary, with the claimed id.
                if (isset($args['set_claimed_id']) && is_string($args['set_claimed_id']) && !empty($args['set_claimed_id'])) {
                    $this->request->getSession()->set('claimed_id', $claimedID, $args['set_claimed_id']);
                }

                return true;
            } else {
                return LogUtil::registerError('An unknown response was received from the OpenID Server.');
            }
        }
    }

    /**
     * Retrieves the Zikula User ID (uid) for the given authentication info
     *
     * From the mapping maintained by this authmodule.
     *
     * Custom authmodules should pay extra special attention to the accurate association of authentication info and user
     * ids (uids). Returning the wrong uid for a given authentication info will potentially expose a user's account to
     * unauthorized access. Custom authmodules must also ensure that they keep their mapping table in sync with
     * the user's account.
     *
     * @param array $args All arguments passed to this function.
     *                      array   authentication_info    The authentication information uniquely associated with a user.
     *
     * @return integer|boolean The integer Zikula uid uniquely associated with the given authentication info;
     *                         otherwise false if user not found or error.
     */
    public function getUidForAuthenticationInfo(array $args)
    {
        if (!isset($args['authentication_info']) || empty($args['authentication_info']) || !is_array($args['authentication_info'])) {
            return LogUtil::registerArgsError();
        }

        if (isset($args['authentication_info']['claimed_id'])) {
            try {
                $userMapTable = Doctrine_Core::getTable('OpenID_Model_UserMap');
                $userMap = $userMapTable->getByClaimedId($args['authentication_info']['claimed_id']);
                if ($userMap) {
                    return $userMap['uid'];
                } else {
                    return false;
                }
            } catch (Exception $e) {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * Authenticates authentication info with the authenticating source, returning the matching Zikula user id.
     *
     * This function may be called to initially authenticate a user during the login process, or may be called
     * for a user already logged in to re-authenticate his password for a security-sensitive operation. This function
     * should merely authenticate the user, and not perform any additional login-related processes.
     *
     * This function differs from checkPassword() in that the authentication info must match and be mapped to a Zikula user account,
     * and therefore must return a Zikula user id (uid). If it cannot, then it should return false, even if the authentication info
     * provided would otherwise authenticate with the authenticating authority.
     *
     * This function differs from login() in that this function makes no attempt to perform any login-related processes
     * on the authenticating system. (If there is no login-related process on the authenticating system, then this and
     * login() are functionally equivalent, however they are still logically distinct in their intent.)
     *
     * @param array $args All arguments passed to this function.
     *                      array   authentication_info    The authentication info needed for this authmodule, including any user-entered password.
     *
     * @return integer|boolean If the authentication info authenticates with the source, then the Zikula uid associated with that login ID;
     *                         otherwise false on authentication failure or error.
     */
    public function authenticateUser(array $args)
    {
        if (!isset($args['authentication_info']) || empty($args['authentication_info']) || !is_array($args['authentication_info'])) {
            return LogUtil::registerArgsError();
        }

        if (!isset($args['authentication_method']) || empty($args['authentication_method']) || !is_array($args['authentication_method'])) {
            return LogUtil::registerArgsError();
        }

        $passwordValidates = ModUtil::apiFunc($this->getName(), 'Authentication', 'checkPassword', array(
            'authentication_info'   => $args['authentication_info'],
            'authentication_method' => $args['authentication_method'],
            'set_claimed_id'        => 'OpenID_Authentication_authenticateUser',
            'reentrant_url'         => (isset($args['reentrant_url']) ? $args['reentrant_url'] : null),
        ));

        if ($passwordValidates) {
            $claimedID = $this->request->getSession()->get('claimed_id', false, 'OpenID_Authentication_authenticateUser');
            $this->request->getSession()->clearNamespace('OpenID_Authentication_authenticateUser');
            $args['authentication_info']['claimed_id'] = $claimedID;

            $uid = ModUtil::apiFunc($this->getName(), 'Authentication', 'getUidForAuthenticationInfo', $args, 'Zikula_Api_AbstractAuthentication');

            if ($uid) {
                return $uid;
            }
        }

        return false;
    }

}
