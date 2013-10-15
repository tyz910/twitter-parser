<?php
namespace App\Twitter;

use Doctrine\DBAL\Connection;

class Parser
{
    /**
     * @var Connection
     */
    private $db;

    /**
     * @var ApiAdapter
     */
    private $api;

    /**
     * @var TweetSaver
     */
    private $saver;

    /**
     * @param Connection $db
     * @param ApiAdapter $api
     * @param TweetSaver $saver
     */
    public function __construct(Connection $db, ApiAdapter $api, TweetSaver $saver)
    {
        $this->db = $db;
        $this->api = $api;
        $this->saver = $saver;
    }

    /**
     * @param int $userId
     * @return array
     * @throws \Exception
     */
    public function loadUserStat($userId)
    {
        $startTime = microtime(true);
        $response = $this->api->getUserInfoById($userId)->throwErrors();
        $parseTime = microtime(true) - $startTime;
        $date = new \DateTime();

        $this->db->beginTransaction();
        try {
            $newData = [
                'tweets'     => $response->getTweetsCount(),
                'followings' => $response->getFollowingCount(),
                'followers'  => $response->getFollowersCount(),
                'parse_time' => round($parseTime * 1000), // in ms
                'date'       => $date->format('Y-m-d H:i:s')
            ];

            $this->db->insert('twitterLenta_history', $newData + [
                'id' => $userId
            ]);

            $newData['lastparse'] = $newData['date'];
            unset($newData['date']);

            $lastRecordExists = (bool) $this->db->fetchColumn('SELECT id FROM twitterLenta WHERE id = :id', [
                'id' => $userId
            ]);

            if ($lastRecordExists) {
                $this->db->update('twitterLenta', $newData, ['id' => $userId]);
            } else {
                $this->db->insert('twitterLenta', $newData + ['id' => $userId]);
            }

            $this->db->commit();
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }

        return $newData;
    }

    /**
     * @param int $userId
     * @return int
     */
    public function loadNewTweets($userId)
    {
        $maxId = $this->db->fetchColumn('SELECT MAX(id) FROM twitterLenta_tweets WHERE user_id = :user_id', [
            'user_id' => $userId
        ]);
        $counter = 0;
        $saver = $this->saver->reset();

        do {
            $oldMaxId = $maxId;
            $tweets = $this->api->getNewTweets($userId, $maxId)->throwErrors()->getTweets();

            foreach ($tweets as $tweet) {
                if ($tweet->getId() != $maxId) {
                    $maxId = max($maxId, $tweet->getId());
                    $saver->addTweet($tweet);
                }
            }
            $counter += $saver->save();
        } while ($maxId != $oldMaxId);

        return $counter;
    }

    /**
     * @param int $userId
     * @return int
     */
    public function loadOldTweets($userId)
    {
        $minId = $this->db->fetchColumn('SELECT MIN(id) FROM twitterLenta_tweets WHERE user_id = :user_id', [
            'user_id' => $userId
        ]);
        $counter = 0;
        $saver = $this->saver->reset();

        do {
            $oldMinId = $minId;
            $tweets = $this->api->getOldTweets($userId, $minId)->throwErrors()->getTweets();

            foreach ($tweets as $tweet) {
                if ($tweet->getId() != $minId) {
                    if (!$minId) {
                        $minId = $tweet->getId();
                    } else {
                        $minId = min($minId, $tweet->getId());
                    }

                    $saver->addTweet($tweet);
                }
            }
            $counter += $saver->save();
        } while ($minId != $oldMinId);

        return $counter;
    }

    public function saveTweets()
    {
        $this->db->executeQuery('INSERT INTO twitterLenta_tweets(id, user_id, text, retweet_count, created_at)');
    }
}