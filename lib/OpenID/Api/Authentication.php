<?php
/**
 * Copyright Zikula Foundation 2013 - Zikula Application Framework
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

        $openIdProvider = OpenID_Util::getAllOpenIdProvider();
        $sslEnabled = function_exists('openssl_open');

        foreach ($openIdProvider as $provider) {
            $authenticationMethod = new OpenID_Helper_AuthenticationMethod(
                $this->name,
                $provider->getProviderName(),
                $provider->getShortDescription(),
                $provider->getLongDescription(),
                true
            );
            $this->authenticationMethods[$provider->getProviderName()] = $authenticationMethod;

            if ($provider->needsSsl() && !$sslEnabled) {
                $authenticationMethod->disableForAuthentication();
                $authenticationMethod->disableForRegistration();
            } else {
                // Enabled method if modvar allows it.
                $authenticationMethod->tryToEnableForAuthentication();
                $authenticationMethod->tryToEnableForRegistration();
            }
        }
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
                    case Zikula_Api_AbstractAuthentication::FILTER_NONE:
                    case Zikula_Api_AbstractAuthentication::FILTER_ENABLED:
                    case Zikula_Api_AbstractAuthentication::FILTER_REGISTRATION_ENABLED:
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
                        $authenticationMethods[$authenticationMethod->getMethod()] = $authenticationMethod;
                    }
                }
                break;
            case Zikula_Api_AbstractAuthentication::FILTER_REGISTRATION_ENABLED:
                $authenticationMethods = array();
                foreach ($this->authenticationMethods as $index => $authenticationMethod) {
                    if ($authenticationMethod->isEnabledForRegistration()) {
                        $authenticationMethods[$authenticationMethod->getMethod()] = $authenticationMethod;
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
     * Registers a user account record or a user registration request with the authentication method.
     *
     * This is called during the user registration process to associate an authentication method provided by this authentication module
     * with a user (either a full user account, or a user's registration request).
     *
     * Parameters passed in the $args array:
     * -------------------------------------
     * array   'authentication_method' An array identifying the authentication method to associate with the user account or registration
     *                                      record. The array should contain two elements: 'modname' containing the authentication module's
     *                                      name (the name of this module), and 'method' containing the name of an authentication method
     *                                      defined by this module.
     * array   'authentication_info'   An array containing the authentication information for the user. For the OpenID module, this should
     *                                      contain the user's supplied id and claimed id.
     * numeric 'uid'                   The user id of the user account record or registration request to associate with the authentication method and
     *                                      authentication information.
     *
     * @param array $args All parameters passed to this function.
     *
     * @return boolean True if the user account or registration request was successfully associated with the authentication method and
     *                      authentication information; otherwise false.
     *
     * @throws Zikula_Exception_Fatal Thrown if the arguments array is invalid, or the user id, authentication method, or authentication information
     *                                      is invalid.
     */
    public function register(array $args)
    {
        if (!isset($args['uid']) || empty($args['uid']) || !is_numeric($args['uid']) || ((string)((int)$args['uid']) != $args['uid'])) {
            throw new Zikula_Exception_Fatal($this->__('Invalid user id has been received.'));
        }

        $uid = $args['uid'];

        if (!isset($args['authentication_info']) || empty($args['authentication_info']) || !is_array($args['authentication_info'])) {
            throw new Zikula_Exception_Fatal($this->__('Invalid authentication information has been received.'));
        }

        $authenticationInfo = $args['authentication_info'];
        if (!isset($authenticationInfo['claimed_id']) || empty($authenticationInfo['claimed_id'])) {
            throw new Zikula_Exception_Fatal($this->__('Invalid authentication information has been received. A claimed ID was not specified.'));
        }

        if (!isset($args['authentication_method']) || empty($args['authentication_method']) || !is_array($args['authentication_method'])) {
            throw new Zikula_Exception_Fatal($this->__('Invalid authentication method has been received.'));
        }

        $authenticationMethod = $args['authentication_method'];
        if (!isset($authenticationMethod['modname']) || empty($authenticationMethod['modname'])
                || !isset($authenticationMethod['method']) || empty($authenticationMethod['method'])
                ) {
            throw new Zikula_Exception_Fatal($this->__('Invalid authentication method has been received. Either an authentication module name or a method name was missing.'));
        }

        $openidHelper = OpenID_Helper_Builder::buildInstance($authenticationMethod['method'], $authenticationInfo);
        if ($openidHelper == false) {
            throw new Zikula_Exception_Fatal($this->__('The authentication method \'%1$s\' does not appear to be supported by the authentication module \'%2$s\'.', array($authenticationMethod['method'], $authenticationMethod['modname'])));
        }

        $claimedID = $authenticationInfo['claimed_id'];

        $thisUserCount = ModUtil::apiFunc($this->getName(), 'user', 'countAllForUser', array(
            'claimed_id'    => $claimedID,
            'uid'           => $uid,
        ));
        if ($thisUserCount === false) {
            throw new Zikula_Exception_Fatal($this->__f('Internal error: Unable to check for duplicate claimed id for %1$s = %2$s', array('uid', $uid)));
        }

        $otherUserCount = ModUtil::apiFunc($this->getName(), 'user', 'countAll', array(
            'claimed_id'    => $claimedID,
        ));
        if ($otherUserCount === false) {
            throw new Zikula_Exception_Fatal($this->__('Internal error: Unable to check for duplicate claimed id across all users'));
        }

        if ($thisUserCount > 0) {
            $this->registerError($this->__f('The claimed OpenID \'%1$s\' is already associated with the specified user account.', $claimedID));
            return false;
        } elseif ($otherUserCount > 0) {
            $this->registerError($this->__f('The claimed OpenID \'%1$s\' is already associated with another account. If this is your OpenID, then please contact the site administrator.', $claimedID));
            return false;
        } else {
            try {
                $userMap = new OpenID_Model_UserMap();
                $userMap->fromArray(array(
                    'uid'                   => $uid,
                    'authentication_method' => $authenticationMethod['method'],
                    'claimed_id'            => $claimedID,
                    'display_id'            => $openidHelper->getDisplayName($claimedID),
                ));
                $userMap->save();
                return true;
            } catch (Exception $e) {
                return false;
            }
        }
    }

    /**
     * Authenticates authentication info with the authenticating source.
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
     * @param array   $authenticationMethod An array identifying the selected authentication method by 'modname' and 'method'.
     * @param array   $authenticationInfo   An array containing the authentication information supplied by the user; for this module, a 'supplied_id'.
     * @param string  $reentrantURL         The URL to which an external OpenID Provider should return in order to reenter the authentication proces
     *                                          following a user's attempt to authenticate on the external server.
     * @param boolean $forRegistration      If true, then a simple registration request extension will be added to the OpenID authentication request,
     *                                          asking for the user's nickname and email address.
     *
     * @return array|boolean If the authentication info authenticates with the source, then an array containing the 'claimed_id', and any optional
     *                              simple registration fields; otherwise false on authentication failure or error.
     *
     * @throws Zikula_Exception_Fatal
     */
    protected function internalCheckPassword(array $authenticationMethod, array $authenticationInfo, $reentrantURL = null, $forRegistration = false)
    {
        $openidHelper = OpenID_Helper_Builder::buildInstance($authenticationMethod['method'], $authenticationInfo);
        if (!isset($openidHelper) || ($openidHelper === false)) {
            throw new Zikula_Exception_Fatal($this->__('The specified authentication method appears to be unsupported.'));
        }

        if (!isset($reentrantURL) || empty($reentrantURL)) {
            // TODO - Maybe we should error out, because there is no guarantee that the current URL is reentrant.
            $reentrantURL = System::getCurrentUrl();
        }

        $openidNamespace = $this->request->query->get('openid_ns', null);
        $openidConsumer = @new Auth_OpenID_Consumer(new OpenID_PHPOpenID_OpenIDStore(), new OpenID_PHPOpenID_SessionStore());

        if (!isset($openidNamespace) || empty($openidNamespace)) {
            // We are NOT returning from a previous redirect to the authorizing provider

            // Save the reentrantURL for later use
            SessionUtil::requireSession();
            $this->request->getSession()->clearNamespace('OpenID_Authentication_checkPassword');
            $this->request->getSession()->set('reentrant_url', $reentrantURL, 'OpenID_Authentication_checkPassword');

            // Build a request instance
            $openidAuthRequest = @$openidConsumer->begin($openidHelper->getSuppliedId());

            if ($openidAuthRequest instanceof Auth_OpenID_AuthRequest) {
                if ($forRegistration) {
                    $simpleRegistrationRequest = Auth_OpenID_SRegRequest::build(array('nickname', 'email'));
                    $openidAuthRequest->addExtension($simpleRegistrationRequest);
                }

                // For OpenID 1, send a redirect.  For OpenID 2, use a Javascript
                // form to send a POST request to the server.
                if ($openidAuthRequest->shouldSendRedirect()) {
                    $redirectURL = $openidAuthRequest->redirectURL(System::getBaseUrl(), $reentrantURL, false);

                    // If the redirect URL can't be built, display an error
                    // message.
                    if (Auth_OpenID::isFailure($redirectURL)) {
                        if (System::isDevelopmentMode()) {
                            $this->registerError($this->__f('Could not redirect to OpenID server: %1$s', $redirectURL->message));
                            return false;
                        } else {
                            $this->registerError($this->__("Could not redirect to OpenID server."));
                            return false;
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
                            return LogUtil::registerError($this->__f('Could not redirect to OpenID server: %1$s', $postFormHTML->message));
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
                    $this->registerError($this->__('Unable to contact the OpenID server to start the authorization process.'));
                    return false;
                } else {
                    $this->registerError($this->__('Unable to contact the OpenID server to start the authorization process. It is possible this is because outgoing SSL connections are not supported by this server.'));
                    return false;
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
                $this->registerError($this->__('OpenID authorization was canceled on the OpenID Server.'));
                return false;
            } else if ($response->status == Auth_OpenID_FAILURE) {
                // Authentication failed; display the error message.
                $this->registerError($this->__f('OpenID authorization failed. The message from the OpenID Server was: %1$s', array($response->message)));
                return false;
            } else if ($response->status == Auth_OpenID_SUCCESS) {
                // This means the authentication succeeded; extract the
                // identity URL and Simple Registration data (if it was
                // returned).
                $returnResult = array(
                    'claimed_id' => $response->getDisplayIdentifier(),
                );

                if ($forRegistration) {
                    $simpleRegistrationResponse = Auth_OpenID_SRegResponse::fromSuccessResponse($response);
                    if ($simpleRegistrationResponse) {
                        $registrationData = $simpleRegistrationResponse->contents();
                        foreach ($registrationData as $fieldName => $value) {
                            $returnResult[$fieldName] = $value;
                        }
                    }
                }

                return $returnResult;
            } else {
                $this->registerError('An unknown response was received from the OpenID Server.');
                return false;
            }
        }

        return false;
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
     *
     * @throws Zikula_Exception_Fatal
     */
    public function checkPassword(array $args)
    {
        $passwordAuthenticates = false;

        if (!isset($args['authentication_info']) || empty($args['authentication_info']) || !is_array($args['authentication_info'])) {
            throw new Zikula_Exception_Fatal($this->__('An invalid set of authentication information was received.'));
        }

        if (!isset($args['authentication_method']) || empty($args['authentication_method']) || !is_array($args['authentication_method'])) {
            throw new Zikula_Exception_Fatal($this->__('An invalid authentication method identifier was recieved.'));
        }

        $openidHelper = OpenID_Helper_Builder::buildInstance($args['authentication_method']['method'], $args['authentication_info']);
        if (!isset($openidHelper) || ($openidHelper === false)) {
            throw new Zikula_Exception_Fatal($this->__('The specified authentication method appears to be unsupported.'));
        }

        if (isset($args['reentrant_url']) && !empty($args['reentrant_url'])) {
            $reentrantURL = $args['reentrant_url'];
        } else {
            // TODO - Maybe we should error out, because there is no guarantee that the current URL is reentrant.
            $reentrantURL = System::getCurrentUrl();
        }

        $checkPasswordResult = $this->internalCheckPassword($args['authentication_method'], $args['authentication_info'], $reentrantURL, false);

        if ($checkPasswordResult) {
            $passwordAuthenticates = true;

            // Set a session variable, if necessary, with the claimed id.
            if (isset($args['set_claimed_id']) && is_string($args['set_claimed_id']) && !empty($args['set_claimed_id'])) {
                $this->request->getSession()->set('claimed_id', $checkPasswordResult['claimed_id'], $args['set_claimed_id']);
            }
        }

        return $passwordAuthenticates;
    }

    /**
     * Authenticates authentication info with the authenticating source, returning simple registration information.
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
     * Parameters passed in the $args array:
     * -------------------------------------
     *
     * @param array $args All arguments passed to this function.
     *                      array   authentication_info    The authentication info needed for this authmodule, including any user-entered password.
     *
     * @return array|boolean If the authentication info authenticates with the source, then an array is returned containing the user's 'claimed_id',
     *                              plus requested simple registration information from the OpenID server; otherwise false on authentication failure or error.
     *
     * @throws Zikula_Exception_Fatal
     */
    public function checkPasswordForRegistration(array $args)
    {
        $checkPasswordResult = false;

        if (!isset($args['authentication_info']) || empty($args['authentication_info']) || !is_array($args['authentication_info'])) {
            throw new Zikula_Exception_Fatal($this->__('An invalid set of authentication information was received.'));
        }

        if (!isset($args['authentication_method']) || empty($args['authentication_method']) || !is_array($args['authentication_method'])) {
            throw new Zikula_Exception_Fatal($this->__('An invalid authentication method identifier was recieved.'));
        }

        $openidHelper = OpenID_Helper_Builder::buildInstance($args['authentication_method']['method'], $args['authentication_info']);
        if (!isset($openidHelper) || ($openidHelper === false)) {
            throw new Zikula_Exception_Fatal($this->__('The specified authentication method appears to be unsupported.'));
        }

        if (isset($args['reentrant_url']) && !empty($args['reentrant_url'])) {
            $reentrantURL = $args['reentrant_url'];
        } else {
            // TODO - Maybe we should error out, because there is no guarantee that the current URL is reentrant.
            $reentrantURL = System::getCurrentUrl();
        }

        $checkPasswordResult = $this->internalCheckPassword($args['authentication_method'], $args['authentication_info'], $reentrantURL, true);

        if ($checkPasswordResult) {
            $authenticationInfo = $args['authentication_info'];
            $authenticationInfo['claimed_id'] = $checkPasswordResult['claimed_id'];
            //unset($checkPasswordResult['claimed_id']);

            $checkPasswordResult = array(
                'authentication_method' => $args['authentication_method'],
                'authentication_info'   => $authenticationInfo,
                'registration_info'     => $checkPasswordResult,
            );
       }

        return $checkPasswordResult;
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
     * Parameters passed in the $args array:
     * -------------------------------------
     * array   authentication_info The authentication information uniquely associated with a user. It should contain a 'claimed_id'.
     *
     * @param array $args All arguments passed to this function.
     *
     * @return integer|boolean The integer Zikula uid uniquely associated with the given authentication info;
     *                         otherwise false if user not found or error.
     *
     * @throws Zikula_Exception_Fatal
     */
    public function getUidForAuthenticationInfo(array $args)
    {
        $claimedUid = false;

        if (!isset($args['authentication_info']) || empty($args['authentication_info']) || !is_array($args['authentication_info'])) {
            throw new Zikula_Exception_Fatal($this->__('An invalid set of authentication information was received.'));
        }

        if (isset($args['authentication_info']['claimed_id'])) {
            try {
                $userMapTable = Doctrine_Core::getTable('OpenID_Model_UserMap');
                $userMap = $userMapTable->getByClaimedId($args['authentication_info']['claimed_id']);
                if ($userMap) {
                    $claimedUid = $userMap['uid'];
                }
            } catch (Exception $e) {
                // Something went wrong.
                return false;
            }
        }

        if ($claimedUid === false) {
            // TODO Create Zikula user account, which is not possible ATM.
        }

        return $claimedUid;
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
     *
     * @throws Zikula_Exception_Fatal
     */
    public function authenticateUser(array $args)
    {
        $authenticatedUid = false;

        if (!isset($args['authentication_info']) || empty($args['authentication_info']) || !is_array($args['authentication_info'])) {
            throw new Zikula_Exception_Fatal($this->__('An invalid set of authentication information was received.'));
        }

        if (!isset($args['authentication_method']) || empty($args['authentication_method']) || !is_array($args['authentication_method'])) {
            throw new Zikula_Exception_Fatal($this->__('An invalid authentication method identifier was received.'));
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

            $authenticatedUid = ModUtil::apiFunc($this->getName(), 'Authentication', 'getUidForAuthenticationInfo', $args, 'Zikula_Api_AbstractAuthentication');

            if (!$authenticatedUid) {
                $this->registerError($this->__('Sorry! The information you provided was incorrect. Please check the log-in service you selected and the id you entered, and make sure they match the information associated with your account on this site.'));
            }
        }

        return $authenticatedUid;
    }

    /**
     * Retrieve the account recovery information for the specified user.
     *
     * The array returned by this function should be an empty array (not null) if the specified user does not have any
     * authentication methods registered with the authentication module that are enabled for log-in.
     *
     * If the specified user does have one or more authentication methods, then the array should contain one or more elements
     * indexed numerically. Each element should be an associative array containing the following:
     *
     * - 'modname' The authentication module name.
     * - 'short_description' A brief (a few words) description or name of the authentication method.
     * - 'long_description' A longer description or name of the authentication method.
     * - 'uname' The user name _equivalent_ for the authentication method (e.g., the claimed OpenID).
     * - 'link' If the authentication method is for an external service, then a link to the user's account on that service, or a general link to the service,
     *            otherwise, an empty string (not null).
     *
     * For example:
     *
     * <code>
     * $accountRecoveryInfo[] = array(
     *     'modname'           => $this->name,
     *     'short_description' => $this->__('E-mail Address'),
     *     'long_description'  => $this->__('E-mail Address'),
     *     'uname'             => $userObj['email'],
     *     'link'              => '',
     * )
     * </code>
     *
     * Parameters passed in the $arg array:
     * ------------------------------------
     * numeric 'uid' The user id of the user for which account recovery information should be retrieved.
     *
     * @param array $args All parameters passed to this function.
     *
     * @return array An array of account recovery information.
     *
     * @throws Zikula_Exception_Fatal Thrown if the arguments array is invalid, if
     */
    public function getAccountRecoveryInfoForUid(array $args)
    {
        if (!isset($args) || empty($args)) {
            throw new Zikula_Exception_Fatal($this->__('An invalid parameter array was received.'));
        }

        $uid = isset($args['uid']) ? $args['uid'] : false;
        if (!isset($uid) || !is_numeric($uid) || ((string)((int)$uid) != $uid)) {
            throw new Zikula_Exception_Fatal($this->__('An invalid user id was received.'));
        }

        try {
            $userMapList = Doctrine_Core::getTable('OpenID_Model_UserMap')
                ->getAllForUid($uid);
        } catch (Exception $e) {
            $message = $this->__('There was a problem accessing the OpenID authentication user map.');
            if (System::isDevelopmentMode()) {
                $message .= ' ' . $this->__f('The error was: %1$s', array($e->getMessage()));
            }
            throw new Zikula_Exception_Fatal($message);
        }

        $lostUserNames = array();
        if (!empty($userMapList)) {
            foreach ($userMapList as $userMap) {
                if (isset($this->authenticationMethods[$userMap['authentication_method']])) {
                    $authenticationMethod = $this->authenticationMethods[$userMap['authentication_method']];
                    if ($authenticationMethod->isEnabledForAuthentication()) {
                        $lostUserNames[] = array(
                            'modname'           => $this->name,
                            'short_description' => $authenticationMethod->getShortDescription(),
                            'long_description'  => $authenticationMethod->getLongDescription(),
                            'uname'             => $userMap['display_id'],
                            'link'              => $userMap['claimed_id'],
                        );
                    }
                }
            }
        }

        return $lostUserNames;
    }
}
