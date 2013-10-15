<?php
namespace App\Twitter\Response;

use App\Twitter\Tweet;

class GetTweetsResponse extends TwitterResponse
{
    /**
     * @return Tweet[]
     */
    public function getTweets()
    {
        // Тут хорошо бы смотрелся генератор
        $tweets = [];
        foreach ($this->getData() as $tweetData) {
            $tweets[] = (new Tweet())
                ->setId($tweetData['id'])
                ->setText($tweetData['text'])
                ->setCreatedAt(new \DateTime($tweetData['created_at']))
                ->setUserId($tweetData['user']['id'])
                ->setRetweetCount($tweetData['retweet_count'])
            ;
        }

        return $tweets;
    }
}