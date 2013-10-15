<?php
namespace App;

use App\Command\ContainerAwareCommand;
use App\Command\FindTwitterUserCommand;
use App\Command\LoadTweetsCommand;
use App\Command\LoadTwitterUserStatCommand;
use Doctrine\DBAL\Migrations\Tools\Console\Command\MigrateCommand;
use Doctrine\DBAL\Migrations\Tools\Console\Command\StatusCommand;
use Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper;
use Symfony\Component\Console\Application as ConsoleApplication;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;

class Application extends ConsoleApplication
{
    /**
     * @var Container
     */
    private $container;

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    public function run(InputInterface $input = null, OutputInterface $output = null)
    {
        $this->boot();
        $this->registerCommands();
        return parent::run($input, $output);
    }

    /**
     * @param Command $command
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function doRunCommand(Command $command, InputInterface $input, OutputInterface $output)
    {
        /** @var ContainerAwareCommand $command */
        if ($command instanceof ContainerAwareCommand) {
            $command->setContainer($this->container);
        }

        return parent::doRunCommand($command, $input, $output);
    }


    public function boot()
    {
        if (!$this->container) {
            $config = $this->readConfig();
            $this->container = new Container(['params' => $config]);
        }
    }

    private function registerCommands()
    {
        $this->getHelperSet()->set(new ConnectionHelper($this->getContainer()->getDb()), 'db');

        $this->addMigrationCommands();
        $this->addCustomCommands();
    }

    private function addMigrationCommands()
    {
        $this->addCommands([
            new MigrateCommand(),
            new StatusCommand()
        ]);
    }

    private function addCustomCommands()
    {
        $this->addCommands([
            new LoadTwitterUserStatCommand(),
            new FindTwitterUserCommand(),
            new LoadTweetsCommand()
        ]);
    }

    /**
     * @return Container
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * @return string
     */
    public function getBaseDir()
    {
        return __DIR__ . '/../../';
    }

    /**
     * @return array
     */
    private function readConfig()
    {
        return Yaml::parse($this->getBaseDir() . 'config.yml');
    }
}