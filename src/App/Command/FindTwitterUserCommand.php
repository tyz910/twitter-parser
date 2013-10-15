<?php
namespace App\Command;

use Symfony\Component\Console\Input\InputArgument;

class FindTwitterUserCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('twitter:user:find')
            ->addArgument('screen_name', InputArgument::REQUIRED)
        ;
    }

    /**
     * @return int|void
     */
    protected function doExecute()
    {
        $screenName = $this->input->getArgument('screen_name');
        $api = $this->container->getTwitterApiAdapter();
        $userId = $api->getUserInfoByScreenName($screenName)->getUserId();

        $this->output->writeln($screenName . ' - ' . $userId);
    }
}