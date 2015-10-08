<?php
namespace App\Provider;

use Github\Client;
use League\OAuth2\Client\Provider\Github;

class GithubProvider extends Github
{
    protected $_client;

    public function __construct(array $options = [], array $collaborators = [])
    {
        $this->_client = new Client();
        parent::__construct($options, $collaborators);
    }

    public function listAllRepositories($username)
    {
        return $this->_client->api('user')->repositories($username, 'all');
    }

}
