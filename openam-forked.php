<?php


class OpenAMForkedUtilities
{


    /**
     * @param string $netid
     * @param string $email
     * @return int|WP_Error
     *
     * Checks if a user with given email address exists. If not creates a subscriber.
     */
    static public function createIfNotExistAsSubscriber(string $netid, string $email)
    {

        // does user exist?
        $user = get_user_by('email', $email);

        // if empty create the user role. Do so as a subscriber.
        if (empty($user)) {

            // create user

            $result = wp_insert_user([
                'user_login' => sanitize_textarea_field($netid),
                'user_email' => sanitize_textarea_field($email),
                'user_pass' => wp_generate_password(),
                'role' => 'subscriber',
            ]);

            return $result;

        }
    }


    public static function listOfAllUsersByNetID()
    {
        $return = [];
        foreach (get_users() as $user) {
            $return[] = $user->user_login;
        }
        return $return;
    }

    /**
     * This is the insertion point that will handle the processing of all newer
     * agentless SSO.
     *
     * OPENAM_API_VERSION == 'forked'
     * @return void|WP_Error
     */
    public static function openam_forked_decision_point()
    {


        // Get the base information of everything below.
        $route = $_SERVER['REQUEST_URI'];
        $is_user_logged_in = is_user_logged_in();


        //   $cookieName = get_option('openam_forked_cookieName');

        // If the user is already loged in this is not necessary. So
        // just send the user back and let the page continue processing.
        if ($is_user_logged_in) {
            return;
        }


        // Format return URL. This was a setting but it's still better to automatically
        // set this from $_SERVER variables.
        // $returnURL = 'https://' . $_SERVER['SERVER_NAME'];

        // Perhaps we need to render this to be from the home variable
        // $returnURL = get_home_url( get_current_network_id()) ;

        // Perhaps we need to render this to be from the home variable. The following gets the host domain from the home_url(),
        // and then prepends with secure-https:// and followed by the full REQUEST_URI. This is done so in case the home_url()
        // contains a subpath. In this way we get the domain + the full request URI to make a complete URL. This should work
        // in either URLs with folders or as the base.
        $parts_of_url = parse_url(home_url());
        $returnURL = 'https://' . $parts_of_url['host'] . $_SERVER['REQUEST_URI'];

        // create object with all necessary information, keys, etc
        $o = new \soc\OpenAM2020(
            get_option('openam_forked_apigeeApiKey'), get_option('openam_forked_webSSOApi'), $returnURL, get_option('openam_forked_ssoRedirectURL'), get_option('openam_forked_DirectoryBasicSearchEndPoint'), get_option('openam_forked_DirectoryBasicSearchEndPointAPIKEY')
        );


        // grab email and netid›
        $run = $o->runAction();
        $netid = trim($run['netid']);
        $email = trim($run['email']);

        // Assuming we've got a good email and netid, find out if the user exists. If not create the user
        self::createIfNotExistAsSubscriber($netid, $email);

        // Get User and test if it's a valid data by testing for the existence of the ID. If the user object fails to
        // load, that's when the endless redirect happens.
        $user = get_user_by('login', $netid);
        if (! isset($user->id)) {
            return new WP_Error('User failed to load', "Are you sure $netid exists in this site?");
        }

        // login this user
        // reference: https://developer.wordpress.org/reference/functions/wp_set_auth_cookie/
        wp_set_auth_cookie($user->id    , 1, true);

        @header("Location: $returnURL");
        die(); // always die after header


    }

    /**
     * @param string $calledfrom
     * @param bool $is_user_admin
     * @param bool $is_user_logged_in
     * @param $netid
     * @param $email
     * @param $route
     */
    public static function ShowRelevantDebugingInfomation(string $calledfrom, $netid, $email, $route)
    {
        self::debug([
            'openam_forked_webSSOApi' => get_option('openam_forked_webSSOApi'),
            'openam_forked_cookieName' => get_option('openam_forked_cookieName'),
            'openam_forked_ssoRedirectURL' => get_option('openam_forked_ssoRedirectURL'),
            'openam_forked_requiresMFA' => get_option('openam_forked_requiresMFA'),
            'openam_forked_DirectoryBasicSearchEndPoint' => get_option('openam_forked_DirectoryBasicSearchEndPoint'),
            'openam_forked_DirectoryBasicSearchEndPointAPIKEY' => substr(get_option('openam_forked_DirectoryBasicSearchEndPointAPIKEY'), 0, 4),
            'is_user_admin(),' => is_user_admin() ? 'YES' : 'NO',
            'is_user_logged_in()' => is_user_logged_in() ? 'YES' : "NO",
            'netid' => $netid,
            'email' => $email,

            'list of users' => OpenAMForkedUtilities::listOfAllUsersByNetID(),
            'called from' => $calledfrom,
            'request uri' => $route,
        ]);
    }


    /**
     * Convience function to spit out debug
     */
    static public function debug($message)
    {
        echo '<pre>';
        print_r($message);
        echo '</pre>';
    }


    static public function logout()
    {
        foreach (explode(';', $_SERVER['HTTP_COOKIE']) as $cookie) {
            $cookie = explode('=', $cookie)[0];
            setcookie($cookie, '', time() - 1000);
            setcookie($cookie, '', time() - 1000, '/');

        }
    }

    public static function development()
    {

    }


}
