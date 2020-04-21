<?php

namespace soc;

/**
 * Class OpenAM2020
 * @package soc
 */

// WP
defined('ABSPATH') or die();


class OpenAM2020
{


    protected $apigeeApiKey;
    protected $webSSOApi;
    // protected $cookieName;
    protected $returnURL;
    protected $ssoRedirectURL;
    // protected $requiresMFA;
    protected $DirectoryBasicSearchEndPoint;
    protected $DirectoryBasicSearchEndPointAPIKEY;

    /**
     * OpenAM2020 constructor.
     * @param $apigeeApiKey
     * @param $webSSOApi
     * @param $returnURL
     * @param $ssoRedirectURL
     * @param $DirectoryBasicSearchEndPoint
     * @param $DirectoryBasicSearchEndPointAPIKEY
     */
    public function __construct($apigeeApiKey, $webSSOApi, $returnURL, $ssoRedirectURL, $DirectoryBasicSearchEndPoint, $DirectoryBasicSearchEndPointAPIKEY)
    {
        $this->apigeeApiKey = $apigeeApiKey;
        $this->webSSOApi = $webSSOApi;
        //$this->cookieName = $cookieName;
        $this->returnURL = $returnURL;
        $this->ssoRedirectURL = $ssoRedirectURL;
        // $this->requiresMFA = $requiresMFA;
        $this->DirectoryBasicSearchEndPoint = $DirectoryBasicSearchEndPoint;
        $this->DirectoryBasicSearchEndPointAPIKEY = $DirectoryBasicSearchEndPointAPIKEY;

        // Determine $this->requiresMFA flag
        $this->requiresMFA = (stristr($this->ssoRedirectURL, 'ldap-and-duo') !== false) ? TRUE : FALSE;
    }


    /**
     * Send the user to the online passport login page.
     */
    public function redirectToLogin()
    {
        $redirect = urlencode($this->returnURL);
        @header($this->ssoRedirectURL . $redirect);
        exit;
    }

    /**
     * Get the value of a cookie, if it exists.
     * @param $name
     * @return mixed|null
     */
    public function getCookieValue($name)
    {
        $token = null;
        if (array_key_exists($name, $_COOKIE) == true) {
            $token = $_COOKIE[$name];
        }

        return $token;
    }

    public function runAction()
    {
        // Do we have a session?
        $token = $this->getCookieValue('nusso');
        if ($token == null) {
            $this->redirectToLogin();
        }

        $result = $this->getIsSessionValid($token);

        if ($result === false) {
            $this->redirectToLogin();
        }

        $result = json_decode($result, JSON_OBJECT_AS_ARRAY);
        if (array_key_exists('fault', $result)) {
            echo "Your apigee key is not valid:<br><pre>";
            print_r($result);
            echo "</pre>";
            die();
        }

        // if netid doesn't exist, redirect to login page
        if (array_key_exists('netid', $result) === false) {
            $this->redirectToLogin();
        }

        // Does this need to be MFAed?
        $this->ensureMFAedConnection($token);

        return [
            'netid' => $result['netid'],
            'email' => $this->getMailByNetID($result['netid'])
        ];
    }

    /**
     * @param $token
     * @return resource
     * Is the session valid?
     */
    public function getIsSessionValid($token)
    {

        $context = stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => implode("\r\n", [
                    "Content-Length: 0",
                    "apikey: " . $this->apigeeApiKey,
                    "webssotoken: $token",
                    "requiresMFA: " . $this->requiresMFA,
                    "goto: ", // not using this functionality
                ]),
                'ignore_errors' => false,
            ],
        ]);

        // If this fails, we need to explicitly return false
        try {
            return file_get_contents($this->webSSOApi, false, $context);
        } catch (\Exception $e) {
            return false;
        }
    }


    /**
     * @param string $netid
     * @return resource
     *
     * Gets information from Basic Dir Search based on netid.
     * Using same pattern as before with stream context to keep some consistency.
     *
     */
    public function getBasicDirectorySearchDataFromNetid(string $netid)
    {

        $context = stream_context_create([
            'http' => [
                'method' => 'GET',
                'header' => implode("\r\n", [
                    "Content-Length: 0",
                    "apikey: " . $this->DirectoryBasicSearchEndPointAPIKEY,
                ]),
                'ignore_errors' => false,
            ],
        ]);

        try {
            $search = $this->DirectoryBasicSearchEndPoint . $netid;
            $data = json_decode(file_get_contents($search, false, $context));

            return $data;
        } catch (\Exception $e) {
            return false;
        }
    }


    /**
     * @param string $netid
     * @return mixed
     *
     * Using $this->getBasicDirectorySearchDataFromNetid($netid) and filters down
     * to only the email address
     */
    public function getMailByNetID(string $netid)
    {
        $data = $this->getBasicDirectorySearchDataFromNetid($netid);
        return $data->results[0]->mail;
    }


    /**
     * A self contained function that was added post-hoc to ensure that
     * if MFA is set. That is, this attempts to minimize the any user
     * changes to authIndexValue in an attempt to bypass MFA. If the site
     * requires MFA and session-info says that DUO was not invoked, it will
     * clear any WP auth cookies and redirect back to the SSO.
     *
     * @param string $token
     * @return bool|void
     */
    protected function ensureMFAedConnection(string $token)
    {
        // If this site doesn't require DUO/MFA, this function
        // does nothing else.
        if (!$this->requiresMFA) {
            return;
        }

        // Set up context.
        $context = stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => implode("\r\n", [
                    "Content-Length: 0",
                    "apikey: " . $this->apigeeApiKey,
                    "webssotoken: $token",
                    "requiresMFA: " . $this->requiresMFA,
                    "goto: ", // not using this functionality
                ]),
                'ignore_errors' => false,
            ],
        ]);


        // switch out endpoint to session-info
        $session_info_endpoint = str_replace('validateWebSSOToken', 'session-info', $this->webSSOApi);

        // Make call to get information
        try {
            $session_info_data = json_decode(file_get_contents($session_info_endpoint, false, $context));
        } catch (\Exception $e) {
            error_log($e->getMessage());
            (new OpenAM2020CustomError())->DisplayCustomOpenAM2020ErrorAndDie('Error reaching Apigee', $e->getMessage());
            return false;
        }

        // Distill data
        $isMFAed = $session_info_data->properties->isDuoAuthenticated;

        // If isMFAed is FALSE, something was wrong and DUO was bypassed. Clear authcookie
        // and send back for a DUO auth.
        if (!$isMFAed) {
            wp_clear_auth_cookie();
            $this->redirectToLogin();
            die();
        }

    }

}

