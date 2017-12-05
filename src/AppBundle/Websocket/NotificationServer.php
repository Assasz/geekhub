<?php
namespace AppBundle\Websocket;

use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use AppBundle\Entity\User;
use AppBundle\Entity\Notification;

class NotificationServer implements MessageComponentInterface
{
    protected $clients;
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->clients = new \SplObjectStorage;
    }

    /**
     * A new websocket connection
     *
     * @param ConnectionInterface $conn
     */
    public function onOpen(ConnectionInterface $conn)
    {
        $this->clients->attach($conn);

        echo "New connection! ({$conn->resourceId})\n";
    }

    /**
     * Handle message sending
     *
     * @param ConnectionInterface $from
     * @param string $msg
     */
    public function onMessage(ConnectionInterface $from, $msg)
    {
        $numRecv = count($this->clients) - 1;

        echo sprintf('Connection %d sending message "%s" to %d other connection%s' . "\n"
            , $from->resourceId, $msg, $numRecv, $numRecv == 1 ? '' : 's');

        // $response = [
        //
        // ];
        //
        // $response = json_encode($response);
        //
        // foreach ($this->clients as $client)
        // {
        //     $client->send($response);
        // }
    }

    /**
     * A connection is closed
     * @param ConnectionInterface $conn
     */
    public function onClose(ConnectionInterface $conn)
    {
        $this->clients->detach($conn);

        echo "Connection {$conn->resourceId} has disconnected\n";
    }

    /**
     * Error handling
     *
     * @param ConnectionInterface $conn
     * @param \Exception $e
     */
    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        $conn->close();

        echo "An error has occurred: {$e->getMessage()}\n";
    }

    public function getUser($id)
    {
        $em = $this->container->get('doctrine.orm.entity_manager');

        $user = $em->getRepository(User::class)
            ->find($id);

        return $user;
    }
}
