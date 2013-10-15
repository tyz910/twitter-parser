<?php
namespace App\Twitter\Response;

use App\Api\HttpJsonResponse;

class UsersShowResponse extends HttpJsonResponse
{
    /**
     * @return int
     */
    public function getUserId()
    {
        return $this->getData()['id'];
    }

    /**
     * @return string
     */
    public function getScreenName()
    {
        return $this->getData()['screen_name'];
    }

    /**
     * @return int
     */
    public function getFollowersCount()
    {
        return $this->getData()['followers_count'];
    }

    /**
     * @return int
     */
    public function getFollowingCount()
    {
        return $this->getData()['friends_count'];
    }

    /**
     * @return int
     */
    public function getTweetsCount()
    {
        return $this->getData()['statuses_count'];
    }
}