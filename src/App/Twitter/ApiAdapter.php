<?php
namespace App\Twitter;

use App\Twitter\Response\GetTweetsResponse;
use Guzzle\Http\Client;
use App\Twitter\Response\UsersShowResponse;

class ApiAdapter
{
    const API_URL_USERS_SHOW    = 'users/show.json';
    const API_URL_USER_TIMELINE = 'statuses/user_timeline.json';

    const API_QUERY_MAX_TWEETS = 200;

    /**
     * @var Client
     */
    private $httpClient;

    /**
     * @param Client $httpClient
     */
    public function __construct(Client $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * @param int $id
     * @param bool $includeEntities
     * @return UsersShowResponse
     */
    public function getUserInfoById($id, $includeEntities = false)
    {
        return $this->getUserInfo(['id' => $id], $includeEntities);
    }

    /**
     * @param string $screenName
     * @param bool $includeEntities
     * @return UsersShowResponse
     */
    public function getUserInfoByScreenName($screenName, $includeEntities = false)
    {
        return $this->getUserInfo(['screen_name' => $screenName], $includeEntities);
    }

    /**
     * @param array $filter
     * @param bool $includeEntities
     * @return UsersShowResponse
     */
    public function getUserInfo(array $filter, $includeEntities)
    {
        $request = $this->httpClient->get(self::API_URL_USERS_SHOW);
        $query = $request->getQuery();

        $query->set('include_entities', $this->toStringBool($includeEntities));
        foreach ($filter as $param => $value) {
            $query->set($param, $value);
        }

        return new UsersShowResponse($request->send());
    }

    /**
     * @param int $userId
     * @param int $sinceId
     * @return GetTweetsResponse
     */
    public function getNewTweets($userId, $sinceId = 0)
    {
        $filter = ['user_id' => $userId];
        if ($sinceId) {
            $filter['since_id'] = $sinceId;
        }

        return $this->geTweets($filter);
    }

    /**
     * @param int $userId
     * @param int $maxId
     * @return GetTweetsResponse
     */
    public function getOldTweets($userId, $maxId)
    {
        $filter = ['user_id' => $userId];
        if ($maxId) {
            $filter['max_id'] = $maxId;
        }

        return $this->geTweets($filter);
    }

    /**
     * @param array $filter
     * @param bool $isMinimal
     * @param int $count
     * @return GetTweetsResponse
     */
    public function geTweets(array $filter, $isMinimal = true, $count = self::API_QUERY_MAX_TWEETS)
    {
        $request = $this->httpClient->get(self::API_URL_USER_TIMELINE);
        $query = $request->getQuery();

        $filter['count'] = $count;
        if ($isMinimal) {
            $filter += [
                'trim_user'           => $this->toStringBool(true),
                'exclude_replies'     => $this->toStringBool(true),
                'contributor_details' => $this->toStringBool(false)
            ];
        }

        foreach ($filter as $param => $value) {
            $query->set($param, $value);
        }

        return new GetTweetsResponse($request->send());
    }

    /**
     * @param bool $value
     * @return string
     */
    private function toStringBool($value)
    {
        return ($value) ? 'true' : 'false';
    }
}