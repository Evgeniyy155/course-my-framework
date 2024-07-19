<?php

namespace Web\Framework\Console;

use League\Container\Container;
use Psr\Container\ContainerInterface;

class Kernel
{
    public function __construct(
        private ContainerInterface $container,
        private Application $application,
    ) {
    }

    public function handle(): int
    {
        // 1. Реєстрація команд за допомогою контейнера
        $this->registerCommands();
        // 2. Запуск команди
        $status = $this->application->run();
        // 3. Вертаємо код
        return $status;
    }

    private function registerCommands(): void
    {
        // Реєстрація системних команд

        // 1. Отримати всі файли з папки Commands
        $commandFiles = new \DirectoryIterator(__DIR__ . '/Commands');
        $namespace = $this->container->get('framework-commands-namespace');
        // 2. Пройтись во всім файлам
        foreach ($commandFiles as $commandFile){
            if(!$commandFile->isFIle()){
                continue;
            }
            // 3. Получить ім'я класа команди
            $command = $namespace . '\\' . pathinfo($commandFile, PATHINFO_FILENAME);
            // 4. Якщо це підклас CommandInterface
            if(is_subclass_of($command, CommandInterface::class)){
                // -> Добавить в контрейнер(використовуючи ім'я команди як ID)
                $name = (new \ReflectionClass($command))
                    ->getProperty('name')
                    ->getDefaultValue();
                $this->container->add("console:$name", $command);
            }
        }

        // Реєстрація команд користувача
    }
}