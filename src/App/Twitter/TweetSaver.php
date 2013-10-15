<?php
namespace App\Twitter;

use Doctrine\DBAL\Connection;

class TweetSaver
{
    /**
     * @var Connection
     */
    private $db;

    /**
     * @var string[]
     */
    private $values;

    /**
     * @var int
     */
    private $counter;

    /**
     * @param Connection $db
     */
    public function __construct(Connection $db)
    {
        $this->db = $db;
        $this->reset();
    }

    /**
     * @return $this
     */
    public function reset()
    {
        $this->values = [];
        $this->counter = 0;

        return $this;
    }

    /**
     * @param Tweet $tweet
     */
    public function addTweet(Tweet $tweet)
    {
        $this->values = array_merge($this->values, [
            $tweet->getId(),
            $tweet->getUserId(),
            $tweet->getText(),
            $tweet->getRetweetCount(),
            $tweet->getCreatedAt()->format('Y-m-d H:i:s')
        ]);

        $this->counter++;
    }

    /**
     * @return int
     */
    public function save()
    {
        if ($count = $this->counter) {
            $query = 'INSERT INTO twitterLenta_tweets(id, user_id, text, retweet_count, created_at) VALUES '
                . implode (',', array_fill(0, $this->counter, '(?, ?, ?, ?, ?)'));
            $this->db->executeQuery($query, $this->values);
        }

        $this->reset();
        return $count;
    }
}