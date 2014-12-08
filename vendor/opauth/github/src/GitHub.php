<?php
/**
 * GitHub strategy for Opauth
 *
 * More information on Opauth: http://opauth.org
 *
 * @copyright    Copyright © 2012 U-Zyn Chua (http://uzyn.com)
 * @link         http://opauth.org
 * @package      Opauth.GitHubStrategy
 * @license      MIT License
 */

namespace Opauth\GitHub\Strategy;

use Opauth\Opauth\AbstractStrategy;

/**
 * GitHub strategy for Opauth
 *
 * @package            Opauth.GitHub
 */
class GitHub extends AbstractStrategy
{

    /**
     * Compulsory config keys, listed as unassociative arrays
     */
    public $expects = array('client_id', 'client_secret');

    /**
     * Optional config keys, without predefining any default values.
     */
    public $optionals = array('redirect_uri', 'scope', 'state');

    public $responseMap = array(
        'uid' => 'id',
        'name' => 'login',
        'info.name' => 'name',
        'info.urls.blog' => 'blog',
        'info.image' => 'avatar_url',
        'info.description' => 'bio',
        'info.nickname' => 'login',
        'info.urls.github' => 'html_url',
        'info.email' => 'email',
        'info.location' => 'location',
        'info.urls.github_api' => 'url',
    );

    /**
     * Auth request
     */
    public function request()
    {
        $url = 'https://github.com/login/oauth/authorize';
        $params = array(
            'client_id' => $this->strategy['client_id'],
            'redirect_uri' => $this->callbackUrl()
        );
        $params = $this->addParams($this->optionals, $params);

        $this->redirect($url, $params);
    }

    /**
     * Internal callback, after OAuth
     */
    public function callback()
    {
        if (empty($_GET['code'])) {
            return $this->response($_GET, array('code' => 'callback_error'));
        }

        $response = $this->accessToken($_GET['code']);
        parse_str($response, $results);
        if (empty($results['access_token'])) {
            return $this->error(
                'Failed when attempting to obtain access token',
                'access_token_error',
                $response
            );
        }

        $data = array('access_token' => $results['access_token']);
        $user = $this->http->get('https://api.github.com/user', $data);
        $user = $this->recursiveGetObjectVars(json_decode($user));
        if (empty($user) || isset($user['message'])) {
            $message = 'Failed when attempting to query GitHub v3 API for user information';
            if (isset($user['message'])) {
                $message = $user['message'];
            }
            return $this->error($message, 'userinfo_error', $user);
        }

        $response = $this->response($user);
        $response->credentials = array('token' => $results['access_token']);
        return $response;
    }

    protected function accessToken($code)
    {
        $url = 'https://github.com/login/oauth/access_token';
        $params = array(
            'code' => $code,
            'client_id' => $this->strategy['client_id'],
            'client_secret' => $this->strategy['client_secret'],
            'redirect_uri' => $this->callbackUrl(),
        );
        $params = $this->addParams($this->optionals, $params);

        return $this->http->post($url, $params);
    }
}
