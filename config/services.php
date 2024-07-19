<?php

use Doctrine\DBAL\Connection;
use League\Container\Container;
use Symfony\Component\Dotenv\Dotenv;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Web\Framework\Console\Application;
use Web\Framework\Console\Commands\MigrateCommand;
use Web\Framework\Controller\AbstractController;
use Web\Framework\Dbal\ConnectionFactory;
use Web\Framework\Http\Kernel;
use Web\Framework\Routing\RouterInterface;
use Web\Framework\Routing\Router;
use \League\Container\Argument\Literal\ArrayArgument;
use \League\Container\ReflectionContainer;
use \League\Container\Argument\Literal\StringArgument;
use \Web\Framework\Console\Kernel as ConsoleKernel;


// Application parameters
$routes = include BASE_PATH . '/routes/web.php';
$appEnv = $_ENV['APP_ENV'] ?? 'local';
$viewsPath = BASE_PATH . '/views';
$databaseUrl = 'pdo-mysql://root:@MySQL-8.0:3306/framework?charset=utf8mb4';

// Application services

$dotenv = new Dotenv();
$dotenv->load(BASE_PATH . '/.env');


$container = new Container();
$container->delegate(new ReflectionContainer(true));

$container->add('framework-commands-namespace', new StringArgument('Web\\Framework\\Console\\Commands'));

$container->add('APP_ENV', new StringArgument($appEnv));

$container->add(RouterInterface::class, Router::class);
$container->extend(RouterInterface::class)
    ->addMethodCall('registerRoutes', [new ArrayArgument($routes)]);

$container->add(Kernel::class)
    ->addArgument(RouterInterface::class)
    ->addArgument($container);

$container->addShared('twig-loader', FilesystemLoader::class)
    ->addArgument(new StringArgument($viewsPath));

$container->addShared('twig',Environment::class)
    ->addArgument('twig-loader');

$container->inflector(AbstractController::class)
    ->invokeMethod('setContainer', [$container]);

$container->add(ConnectionFactory::class)
    ->addArgument(new StringArgument($databaseUrl));

$container->addShared(Connection::class, function () use ($container): Connection {
    return $container->get(ConnectionFactory::class)->create();
});

$container->add(Application::class)
    ->addArgument($container);

$container->add(ConsoleKernel::class)
    ->addArgument($container)
    ->addArgument(Application::class);

$container->add('console:migrate', MigrateCommand::class)
    ->addArgument(Connection::class)
    ->addArgument(new StringArgument(BASE_PATH .'/database/migrations'));

return $container;
