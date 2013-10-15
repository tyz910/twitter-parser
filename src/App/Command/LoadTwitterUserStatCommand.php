<?php
namespace App\Command;

use Symfony\Component\Console\Helper\TableHelper;
use Symfony\Component\Console\Input\InputArgument;

class LoadTwitterUserStatCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('twitter:user:load_stat')
            ->addArgument('user_id', InputArgument::REQUIRED)
        ;
    }

    /**
     * @return int|void
     */
    protected function doExecute()
    {
        $userId = $this->input->getArgument('user_id');
        $parser = $this->container->getTwitterParser();

        $this->output->writeln(sprintf('Load stat for user #%s.', $userId));
        $data = $parser->loadUserStat($userId);

        /** @var TableHelper $table */
        $table = $this->getApplication()->getHelperSet()->get('table');
        $table->setHeaders(array_keys($data))
            ->addRow($data)
            ->render($this->output)
        ;
    }
}