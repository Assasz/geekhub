<?php
namespace AppBundle\Websocket;

use AppBundle\Websocket\ChatServer;
use AppBundle\Websocket\NotificationServer;
use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;
use Ratchet\App;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Fr\DiffSocket;

class ServerCommand extends ContainerAwareCommand
{
    /**
     * Configure a new Command Line
     */
    protected function configure()
    {
        $this
            ->setName('websocket:server:run')
            ->setDescription('Start the websocket server.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Websocket server has stated!');

        $app = new App('localhost', 8888);

        $app->route('/chat', new ChatServer($this->getContainer()));
        $app->route('/notification', new NotificationServer($this->getContainer()));

        $app->run();
    }
}
