<?php


namespace App\Command;


use App\Service\QueueService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class LoadProductsCommand extends Command
{

    /**
     * @var QueueService
     */
    private $queueService;

    public function __construct(QueueService $queueService)
    {
        parent::__construct();
        $this->queueService = $queueService;
    }

    protected function configure()
    {

        $this
            // имя команды (часть после "bin/console")
            ->setName('app:download-csv');

            // краткое описание, отображающееся при запуске "php bin/console list"
            //->setDescription('Creates a new user.')

            // полное описание команды, отображающееся при запуске команды
            // с опцией "--help"
            //->setHelp('This command allows you to create a user...')
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        //while (true) {

            # Чтение очереди beanstalkd
            $this->queueService->listen();
       // }
    }
}