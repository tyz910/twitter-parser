<?php
namespace App\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class LoadTweetsCommand extends ContainerAwareCommand
{
    const SEARCH_DIRECTION_NEW = 'new';
    const SEARCH_DIRECTION_OLD = 'old';

    protected function configure()
    {
        $this->setName('twitter:user:load_tweets')
            ->addArgument('user_id', InputArgument::REQUIRED)
            ->addOption('dir', null, InputOption::VALUE_REQUIRED, null, self::SEARCH_DIRECTION_NEW)
        ;
    }

    /**
     * @throws \InvalidArgumentException
     * @return int|void
     */
    protected function doExecute()
    {
        $userId = $this->input->getArgument('user_id');
        $direction = $this->input->getOption('dir');
        $parser = $this->container->getTwitterParser();

        $this->output->write(sprintf('Search tweets for user %s - ', $userId));
        switch ($direction) {
            case self::SEARCH_DIRECTION_NEW:
                $count = $parser->loadNewTweets($userId);
                break;

            case self::SEARCH_DIRECTION_OLD:
                $count = $parser->loadOldTweets($userId);
                break;

            default:
                throw new \InvalidArgumentException($direction);
        }

        $this->output->writeln(sprintf('<info>%s found</info>', $count));
    }
}