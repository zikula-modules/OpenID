<?php
/**
 * Copyright Zikula Foundation 2009 - Zikula Application Framework
 *
 * This work is contributed to the Zikula Foundation under one or more
 * Contributor Agreements and licensed to You under the following license:
 *
 * @license GNU/LGPLv3 (or at your option, any later version).
 * @package Zikula
 *
 * Please see the NOTICE file distributed with this source code for further
 * information regarding copyright and licensing.
 */

class OpenID_Api_Auth extends Zikula_AuthApi
{
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
     * Authenticates authinfo with the authenticating source, returning a simple boolean result.
     *
     * ATTENTION: Any function that causes this function to be called MUST specify a return URL, and therefore
     * must be reentrant. In other words, in order to call this function, there must exist a controller function
     * (specified by the return URL) that the OpenID server can return to, and that function must restore the
     * pertinent state for the user as if he never left this site! Session variables should be used to store all
     * pertinent variables, and those must be re-read into the user's state when the return URL is called back
     * by the OpenID server.
     *
     * Note that, despite this function's name, there is no requirement that a password be part of the authinfo.
     * Merely that enough information be provided in the authinfo array to unequivocally authenticate the user. For
     * most authenticating authorities this will be the equivalent of a user name and password, but--again--there
     * is no restriction here. This is not, however, a "user exists in the system" function. It is expected that
     * the authenticating authority validate what ever is used as a password or the equivalent thereof.
     *
     * This function makes no attempt to match the given authinfo with a Zikula user id (uid). It simply asks the
     * authenticating authority to authenticate the authinfo provided. No "login" should take place as a result of
     * this authentication.
     *
     * This function may be called to initially authenticate a user during the registration process, or may be called
     * for a user already logged in to re-authenticate his password for a security-sensitive operation. This function
     * should merely authenticate the user, and not perform any additional login-related processes.
     *
     * This function differs from authenticateUser() in that no attempt is made to match the authinfo with and map to a
     * Zikula user account. It does not return a Zikula user id (uid).
     *
     * This function differs from login()  in that no attempt is made to match the authinfo with and map to a
     * Zikula user account. It does not return a Zikula user id (uid). In addition this function makes no attempt to
     * perform any login-related processes on the authenticating system.
     *
     * @param array $args All arguments passed to this function.
     *                      array   authinfo    The authinfo needed for this authmodule, including any user-entered password.
     *
     * @return boolean True if the authinfo authenticates with the source; otherwise false on authentication failure or error.
     */
    public function checkPassword($args)
    {
        // TODO - stub
        if (!isset($args['authinfo']) || empty($args['authinfo']) || !is_array($args['authinfo'])) {
            return LogUtil::registerArgsError();
        }

        if (!isset($args['authinfo']['openid_type']) || empty($args['authinfo']['openid_type'])){
            return LogUtil::registerArgsError();
        }

        $openidHelper = OpenID_HelperBuilder::buildInstance($args['authinfo']['openid_type'], $args['authinfo']);
        if (!isset($openidHelper) || ($openidHelper === false)){
            return LogUtil::registerArgsError();
        }

        if (isset($args['reentrant_url']) && !empty($args['reentrant_url'])) {
            $reentrantURL = $args['reentrant_url'];
        } else {
            // TODO - Maybe we should error out, because there is no guarantee that the current URL is reentrant.
            $reentrantURL = System::getCurrentUrl();
        }

        $openidNamespace = FormUtil::getPassedValue('openid_ns', null, 'GET');
        $openidConsumer = @new Auth_OpenID_Consumer(new OpenID_ZikulaOpenIDStore(), new OpenID_PHPSession());

        if (!isset($openidNamespace) || empty($openidNamespace)) {
            // We are NOT returing from a previous redirect to the authorizing provider

            // Save the reentrantURL for later use
            SessionUtil::requireSession();
            SessionUtil::setVar('reentrantURL', $reentrantURL, '/OpenID_Auth_checkPassword', true, true);

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
            $reentrantURL = SessionUtil::getVar('reentrantURL', '', '/OpenID_Auth_checkPassword', false, false);
            SessionUtil::delVar('OpenID_Auth_checkPassword');

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
                if (isset($args['set_claimed_id']) && $args['set_claimed_id']) {
                    SessionUtil::setVar('claimed_id', $claimedID, '/OpenID_Auth', true, true);
                }

                return true;
            } else {
                return LogUtil::registerError('An unknown response was received from the OpenID Server.');
            }
        }
    }

    /**
     * Retrieves the Zikula User ID (uid) for the given authinfo
     *
     * From the mapping maintained by this authmodule.
     *
     * Custom authmodules should pay extra special attention to the accurate association of authinfo and user
     * ids (uids). Returning the wrong uid for a given authinfo will potentially expose a user's account to
     * unauthorized access. Custom authmodules must also ensure that they keep their mapping table in sync with
     * the user's account.
     *
     * @param array $args All arguments passed to this function.
     *                      array   authinfo    The authentication information uniquely associated with a user.
     *
     * @return integer|boolean The integer Zikula uid uniquely associated with the given authinfo;
     *                         otherwise false if user not found or error.
     */
    public function getUidForAuthinfo($args)
    {
        if (!isset($args['authinfo']) || empty($args['authinfo']) || !is_array($args['authinfo'])) {
            return LogUtil::registerArgsError();
        }

        if (isset($args['authinfo']['claimed_id'])) {
            try {
                $userMapTable = Doctrine_Core::getTable('OpenID_Model_UserMap');
                $userMap = $userMapTable->getByClaimedId($args['authinfo']['claimed_id']);
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
     * Authenticates authinfo with the authenticating source, returning the matching Zikula user id.
     *
     * This function may be called to initially authenticate a user during the login process, or may be called
     * for a user already logged in to re-authenticate his password for a security-sensitive operation. This function
     * should merely authenticate the user, and not perform any additional login-related processes.
     *
     * This function differs from checkPassword() in that the authinfo must match and be mapped to a Zikula user account,
     * and therefore must return a Zikula user id (uid). If it cannot, then it should return false, even if the authinfo
     * provided would otherwise authenticate with the authenticating authority.
     *
     * This function differs from login() in that this function makes no attempt to perform any login-related processes
     * on the authenticating system. (If there is no login-related process on the authenticating system, then this and
     * login() are functionally equivalent, however they are still logically distinct in their intent.)
     *
     * @param array $args All arguments passed to this function.
     *                      array   authinfo    The authinfo needed for this authmodule, including any user-entered password.
     *
     * @return integer|boolean If the authinfo authenticates with the source, then the Zikula uid associated with that login ID;
     *                         otherwise false on authentication failure or error.
     */
    public function authenticateUser($args)
    {
        if (!isset($args['authinfo']) || empty($args['authinfo']) || !is_array($args['authinfo'])) {
            return LogUtil::registerArgsError();
        }

        $passwordValidates = ModUtil::apiFunc($this->getName(), 'auth', 'checkPassword', array(
            'authinfo'          => (isset($args['authinfo']) ? $args['authinfo'] : null),
            'set_claimed_id'    => true,
            'reentrant_url'     => (isset($args['reentrant_url']) ? $args['reentrant_url'] : null),
        ));

        if ($passwordValidates) {
            $claimedID = SessionUtil::getVar('claimed_id', false, '/OpenID_Auth', false, false);
            $args['authinfo']['claimed_id'] = $claimedID;

            $uid = ModUtil::apiFunc($this->getName(), 'auth', 'getUidForAuthinfo', $args);

            if ($uid) {
                return $uid;
            }
        }

        return false;
    }

}
