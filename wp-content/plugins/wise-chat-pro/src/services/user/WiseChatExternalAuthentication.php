<?php

/**
 * Wise Chat user external authentication service.
 */
class WiseChatExternalAuthentication {
    const FACEBOOK = 'fb';
    const FACEBOOK_API = 'v3.1';
    const TWITTER = 'tw';
    const GOOGLE = 'go';

    /**
     * @var WiseChatOptions
     */
    private $options;

    /**
     * @var WiseChatUsersDAO
     */
    private $usersDAO;

    /**
     * @var WiseChatAuthentication
     */
    private $authentication;

    /**
     * @var WiseChatHttpRequestService
     */
    private $httpRequestService;

    /**
     * @var WiseChatUserSessionDAO
     */
    private $userSessionDAO;

    public function __construct() {
        $this->options = WiseChatOptions::getInstance();
        $this->usersDAO = WiseChatContainer::get('dao/user/WiseChatUsersDAO');
        $this->authentication = WiseChatContainer::getLazy('services/user/WiseChatAuthentication');
        $this->httpRequestService = WiseChatContainer::getLazy('services/WiseChatHttpRequestService');
        $this->userSessionDAO = WiseChatContainer::getLazy('dao/user/WiseChatUserSessionDAO');
    }

    /**
     * Returns URL to external authentication through Facebook.
     *
     * @return string
     *
     * @throws Exception
     */
    public function getFacebookRedirectLoginURL() {
        $appID = $this->options->getOption('facebook_login_app_id');
        $appSecret = $this->options->getOption('facebook_login_app_secret');
        if (strlen($appID) == 0 || strlen($appSecret) == 0) {
            throw new Exception('Facebook App ID or App Secret is not defined');
        }

        $fb = new Facebook\Facebook([
            'app_id' => $appID,
            'app_secret' => $appSecret,
            'default_graph_version' => self::FACEBOOK_API,
        ]);
        $helper = $fb->getRedirectLoginHelper();
        $redirectUri = $this->httpRequestService->getCurrentURLWithParameter('wcExternalLogin', self::FACEBOOK);
        $this->userSessionDAO->set('wise_chat_fb_redirect_uri', $redirectUri);

        return $helper->getLoginUrl($redirectUri, array());
    }

    /**
     * Returns URL to external authentication through Twitter.
     *
     * @return string
     *
     * @throws Exception
     * @throws \Abraham\TwitterOAuth\TwitterOAuthException
     */
    public function getTwitterRedirectLoginURL() {
        $apiKey = $this->options->getOption('twitter_login_api_key');
        $apiSecret = $this->options->getOption('twitter_login_api_secret');
        if (strlen($apiKey) == 0 || strlen($apiSecret) == 0) {
            throw new Exception('Twitter API Key or API Secret is not defined');
        }

        $callbackUrl = $this->httpRequestService->getCurrentURLWithParameter('wcExternalLogin', self::TWITTER);
        $connection = new Abraham\TwitterOAuth\TwitterOAuth($apiKey, $apiSecret);
        $token = $connection->oauth('oauth/request_token', array('oauth_callback' => $callbackUrl));

        $this->userSessionDAO->set('twitter_oauth_token_secret_'.$token['oauth_token'], $token['oauth_token_secret']);

        return $connection->url('oauth/authorize', array('oauth_token' => $token['oauth_token']));
    }

    /**
     * Returns URL to external authentication through Google.
     *
     * @return string
     * @throws Exception
     */
    public function getGoogleRedirectLoginURL() {
        $clientId = $this->options->getOption('google_login_client_id');
        $clientSecret = $this->options->getOption('google_login_client_secret');
        if (strlen($clientId) == 0 || strlen($clientSecret) == 0) {
            throw new Exception('Google Client ID or Client Secret is not defined');
        }

        $callbackUrl = $this->httpRequestService->getCurrentURLWithParameter('wcExternalLogin', self::GOOGLE);
        $this->userSessionDAO->set('google_callback_url', $callbackUrl);

        $client = new Google_Client();
        $client->setClientId($clientId);
        $client->setClientSecret($clientSecret);
        $client->setRedirectUri($callbackUrl);
        $client->addScope("profile");

        return $client->createAuthUrl();
    }

    /**
     * Authenticate user using given method.
     *
     * @param string $method
     *
     * @return string
     * @throws Exception
     */
    public function authenticate($method) {
        switch ($method) {
            case self::FACEBOOK:
                return $this->facebookAuthenticate();
                break;
            case self::TWITTER:
                return $this->twitterAuthenticate();
                break;
            case self::GOOGLE:
                return $this->googleAuthenticate();
                break;
            default:
                throw new Exception('Unknown method in action');
        }
    }

    private function facebookAuthenticate() {
        $appID = $this->options->getOption('facebook_login_app_id');
        $appSecret = $this->options->getOption('facebook_login_app_secret');
        $fb = new Facebook\Facebook([
            'app_id' => $appID,
            'app_secret' => $appSecret,
            'default_graph_version' => self::FACEBOOK_API,
        ]);

        $helper = $fb->getRedirectLoginHelper();
        $accessToken = null;
        try {
            $accessToken = $helper->getAccessToken($this->userSessionDAO->get('wise_chat_fb_redirect_uri'));
        } catch(Facebook\Exceptions\FacebookResponseException $e) {
            throw new Exception('Facebook external login error: '.$e->getMessage());
        } catch(Facebook\Exceptions\FacebookSDKException $e) {
            throw new Exception('Facebook external login SDK error: '.$e->getMessage());
        }

        if (!isset($accessToken)) {
            if ($helper->getError()) {
                throw new Exception('Facebook external login helper error: '.$helper->getError().', '.$helper->getErrorCode().', '.$helper->getErrorReason());
            } else {
                throw new Exception('Facebook external login helper unknown error');
            }
        }

        $oAuth2Client = $fb->getOAuth2Client();
        $tokenMetadata = $oAuth2Client->debugToken($accessToken);
        $tokenMetadata->validateAppId($appID);
        $tokenMetadata->validateExpiration();

        if (!$accessToken->isLongLived()) {
            try {
                $accessToken = $oAuth2Client->getLongLivedAccessToken($accessToken);
            } catch (Facebook\Exceptions\FacebookSDKException $e) {
                throw new Exception('Facebook external login error getting long-lived access token: '.$e->getMessage());
            }
        }

        // get user's details:
        try {
            $response = $fb->get('/me?fields=id,name,picture,link', (string) $accessToken);
        } catch(Facebook\Exceptions\FacebookResponseException $e) {
            throw new Exception('Facebook Graph error: '.$e->getMessage());
        } catch(Facebook\Exceptions\FacebookSDKException $e) {
            throw new Exception('Facebook SDK error: '.$e->getMessage());
        }
        $fbUser = $response->getGraphUser();


        // authenticate user:
        $user = $this->usersDAO->getByExternalTypeAndId(self::FACEBOOK, $fbUser->getId());
        WiseChatContainer::load('model/WiseChatUser');
        if ($user === null) {
            $user = new WiseChatUser();
            $user->setName($fbUser->getName());
            $user->setExternalType(self::FACEBOOK);
            $user->setExternalId($fbUser->getId());
            $user->setAvatarUrl($fbUser->getPicture()->getUrl());
            $user->setProfileUrl($fbUser->getLink());
        } else {
            $user->setName($fbUser->getName());
            $user->setAvatarUrl($fbUser->getPicture()->getUrl());
            $user->setProfileUrl($fbUser->getLink());
        }
        $this->authentication->authenticateWithUser($user);

        /**
         * Fires once user has started its session in the chat.
         *
         * @since 2.3.2
         *
         * @param WiseChatUser $user The user object
         */
        do_action("wc_user_session_started", $user);

        return $this->httpRequestService->getCurrentURLWithoutParameters(array('wcExternalLogin', 'code', 'state'));
    }

    private function twitterAuthenticate() {
        $apiKey = $this->options->getOption('twitter_login_api_key');
        $apiSecret = $this->options->getOption('twitter_login_api_secret');
        $oauthToken = $this->httpRequestService->getParam('oauth_token');

        $connection = new Abraham\TwitterOAuth\TwitterOAuth(
            $apiKey, $apiSecret, $oauthToken, $this->userSessionDAO->get('twitter_oauth_token_secret_'.$oauthToken)
        );

        $accessToken = $connection->oauth("oauth/access_token", ["oauth_verifier" => $this->httpRequestService->getParam('oauth_verifier')]);

        $connection->setOauthToken($accessToken['oauth_token'], $accessToken['oauth_token_secret']);
        $content = $connection->get('account/verify_credentials');

        if (property_exists($content, 'id')) {
            // authenticate user:
            $user = $this->usersDAO->getByExternalTypeAndId(self::TWITTER, $content->id);
            WiseChatContainer::load('model/WiseChatUser');
            if ($user === null) {
                $user = new WiseChatUser();
                $user->setName($content->name);
                $user->setExternalType(self::TWITTER);
                $user->setExternalId($content->id);
                $user->setAvatarUrl($content->profile_image_url);
                $user->setProfileUrl('https://twitter.com/'.$content->screen_name);
            } else {
                $user->setName($content->name);
                $user->setAvatarUrl($content->profile_image_url);
                $user->setProfileUrl('https://twitter.com/'.$content->screen_name);
            }
            $this->authentication->authenticateWithUser($user);

            /**
             * Fires once user has started its session in the chat.
             *
             * @since 2.3.2
             *
             * @param WiseChatUser $user The user object
             */
            do_action("wc_user_session_started", $user);

        } else {
            throw new Exception('Twitter error: cannot get user profile');
        }

        return $this->httpRequestService->getCurrentURLWithoutParameters(array('wcExternalLogin', 'oauth_token', 'oauth_verifier'));
    }

    private function googleAuthenticate() {
        $clientId = $this->options->getOption('google_login_client_id');
        $clientSecret = $this->options->getOption('google_login_client_secret');

        $client = new Google_Client();
        $guzzleClient = $client->getHttpClient();
        $guzzleClientConfig = $guzzleClient->getConfig();
        $guzzleClientConfig['verify'] = false;
        $reconfiguredGuzzleClient = new GuzzleHttp\Client($guzzleClientConfig);
        $client->setHttpClient($reconfiguredGuzzleClient);

        $client->setClientId($clientId);
        $client->setClientSecret($clientSecret);
        $client->setRedirectUri($this->userSessionDAO->get('google_callback_url'));
        $client->addScope("profile");

        if ($this->httpRequestService->getParam('code') != null) {
            $client->authenticate($this->httpRequestService->getParam('code'));
            $service = new Google_Service_Oauth2($client);
            $googleUser = $service->userinfo->get();

            // authenticate user:
            $user = $this->usersDAO->getByExternalTypeAndId(self::GOOGLE, $googleUser->getId());
            WiseChatContainer::load('model/WiseChatUser');
            if ($user === null) {
                $user = new WiseChatUser();
                $user->setName($googleUser->getName());
                $user->setExternalType(self::GOOGLE);
                $user->setExternalId($googleUser->getId());
                $user->setAvatarUrl($googleUser->getPicture());
                $user->setProfileUrl($googleUser->getLink());
            } else {
                $user->setName($googleUser->getName());
                $user->setAvatarUrl($googleUser->getPicture());
                $user->setProfileUrl($googleUser->getLink());
            }
            $this->authentication->authenticateWithUser($user);

            /**
             * Fires once user has started its session in the chat.
             *
             * @since 2.3.2
             *
             * @param WiseChatUser $user The user object
             */
            do_action("wc_user_session_started", $user);

        } else {
            throw new Exception('Google error: no code provided');
        }

        return $this->httpRequestService->getCurrentURLWithoutParameters(array('wcExternalLogin', 'code'));
    }
}