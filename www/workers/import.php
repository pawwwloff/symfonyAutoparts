<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Service\QueueService;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

// init service container
$containerBuilder = new ContainerBuilder();

// init yaml file loader
$loader = new YamlFileLoader($containerBuilder, new FileLocator(__DIR__.'/../config'));

// load services from the yaml file
$loader->load('services.yaml');

//$containerBuilder->compile();

// fetch service from the service container
$serviceOne = $containerBuilder->get(QueueService::class);
//$service = new QueueService();
dd($serviceOne);
while (true) {

    # Чтение очереди beanstalkd
    $service->listen();
}