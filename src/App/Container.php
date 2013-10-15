<?php
namespace App;

use App\Twitter\ApiAdapter;
use App\Twitter\Parser;
use App\Twitter\TweetSaver;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Guzzle\Http\Client;
use Guzzle\Plugin\Oauth\OauthPlugin;

class Container extends \Pimple
{
    public function __construct(array $values = array())
    {
        parent::__construct($values);
        $this->registerServices();
    }

    /**
     * @return Connection
     */
    public function getDb()
    {
        return $this['db'];
    }

    /**
     * @return Client
     */
    public function getHttpClient()
    {
        return $this['http_client'];
    }

    /**
     * @return ApiAdapter
     */
    public function getTwitterApiAdapter()
    {
        return $this['twitter.api_adapter'];
    }

    /**
     * @return Parser
     */
    public function getTwitterParser()
    {
        return $this['twitter.parser'];
    }

    /**
     * @return TweetSaver
     */
    public function getTweetSaver()
    {
        return $this['twitter.tweet_saver'];
    }

    private function registerServices()
    {
        $this->regDb();
        $this->regHttpClient();
        $this->regTwitterApiAdapter();
        $this->regTwitterParser();
        $this->regTweetSaver();
    }

    private function regDb()
    {
        $this['db'] = $this->share(function ($this) {
            return DriverManager::getConnection($this['params']['database']);
        });
    }

    private function regHttpClient()
    {
        $this['http_client'] = $this->share(function ($this) {
            $params = $this['params']['twitter'];

            $client = new Client($params['api_url']);
            $oauth = new OauthPlugin($params['oauth']);
            $client->addSubscriber($oauth);

            return $client;
        });
    }

    private function regTwitterApiAdapter()
    {
        $this['twitter.api_adapter'] = $this->share(function ($this) {
            return new ApiAdapter($this['http_client']);
        });
    }

    private function regTwitterParser()
    {
        $this['twitter.parser'] = $this->share(function ($this) {
            return new Parser($this['db'], $this['twitter.api_adapter'], $this['twitter.tweet_saver']);
        });
    }

    private function regTweetSaver()
    {
        $this['twitter.tweet_saver'] = $this->share(function ($this) {
            return new TweetSaver($this['db']);
        });
    }
}