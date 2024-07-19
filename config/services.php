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
use Web\Framework\Http\Middleware\RequestHandler;
use Web\Framework\Http\Middleware\RequestHandlerInterface;
use Web\Framework\Http\Middleware\RouterDispatch;
use Web\Framework\Routing\RouterInterface;
use Web\Framework\Routing\Router;
use \League\Container\Argument\Literal\ArrayArgument;
use \League\Container\ReflectionContainer;
use \League\Container\Argument\Literal\StringArgument;
use \Web\Framework\Console\Kernel as ConsoleKernel;
use Web\Framework\Session\Session;
use Web\Framework\Session\SessionInterface;
use Web\Framework\Template\TwigFactory;


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

$container->add(RequestHandlerInterface::class, RequestHandler::class)
    ->addArgument($container);

$container->add(Kernel::class)
    ->addArguments([
        RouterInterface::class,
        $container,
        RequestHandlerInterface::class
    ]);

//$container->addShared('twig-loader', FilesystemLoader::class)
//    ->addArgument(new StringArgument($viewsPath));
//
//$container->addShared('twig',Environment::class)
//    ->addArgument('twig-loader');

$container->addShared(SessionInterface::class, Session::class);

$container->add('twig-factory', TwigFactory::class)
    ->addArguments([new StringArgument($viewsPath), SessionInterface::class]);

$container->addShared('twig', function () use ($container) {
    return $container->get('twig-factory')->create();
});

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

$container->add(RouterDispatch::class)
    ->addArguments([
        RouterInterface::class,
        $container
    ]);

return $container;
