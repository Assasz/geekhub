<?php
namespace AppBundle\Websocket;

use AppBundle\Websocket\Chat;
use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

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

        $server = IoServer::factory(new HttpServer(
            new WsServer(
                new Chat($this->getContainer())
            )
        ), 8888);

        $server->run();
    }
}
