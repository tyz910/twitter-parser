<?php
namespace App\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version1 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $twitterLenta = $schema->createTable('twitterLenta');
        $twitterLenta->addColumn('id', 'integer');
        $twitterLenta->addColumn('tweets', 'integer');
        $twitterLenta->addColumn('followings', 'integer');
        $twitterLenta->addColumn('followers', 'integer');
        $twitterLenta->addColumn('lastparse', 'datetime');
        $twitterLenta->addColumn('parse_time', 'integer');

        $twitterLentaHistory = $schema->createTable('twitterLenta_history');
        $twitterLentaHistory->addColumn('id', 'integer');
        $twitterLentaHistory->addColumn('date', 'datetime');
        $twitterLentaHistory->addColumn('tweets', 'integer');
        $twitterLentaHistory->addColumn('followings', 'integer');
        $twitterLentaHistory->addColumn('followers', 'integer');
        $twitterLentaHistory->addColumn('parse_time', 'integer');

        $twitterLentaTweets = $schema->createTable('twitterLenta_tweets');
        $twitterLentaTweets->addColumn('id', 'bigint');
        $twitterLentaTweets->addColumn('user_id', 'integer');
        $twitterLentaTweets->addColumn('text', 'text');
        $twitterLentaTweets->addColumn('retweet_count', 'integer');
        $twitterLentaTweets->addColumn('created_at', 'datetime');
    }

    public function down(Schema $schema)
    {
        $schema->dropTable('twitterLenta');
        $schema->dropTable('twitterLenta_history');
        $schema->dropTable('twitterLenta_tweets');
    }
}