<?php
namespace AppBundle\Controller;

use Hoa\Eventsource\Server;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Entity\Notification;
use AppBundle\Entity\User;

class NotificationController extends Controller
{
    /**
     * @Route("/notification/push/{user}",  name="notification_push", requirements={"user": "\d+"}, options={"expose": true})
     */
    public function pushAction(Request $request, User $user)
    {
        $stream = new Server();
        $em = $this->getDoctrine()->getManager();

        $notifications = $user->getNotifications()->toArray();

        foreach($notifications as $notification)
        {
            if($notification->getPushed() == 0)
            {
                $notification->setPushed(1);
                $em->flush();

                $view = $this->renderView('notification/content.html.twig', [
                    'notification' => $notification
                ]);

                $stream->send(json_encode($view));
            }
        }

        $response = new Response();
        $response->headers->set('Content-Type', 'text/event-stream');
        $response->headers->set('Cache-Control',  'no-cache');

        return $response;
    }

    /**
     * @Route("/notification/list/{user}",  name="notification_list", requirements={"user": "\d+"}, options={"expose": true})
     */
     public function listAction(Request $request, User $user)
     {
         $notifications = $user->getNotifications()->toArray();

         return $this->render('notification/list.html.twig', [
             'notifications' => $notifications
         ]);
     }

     /**
      * @Route("/notification/disactivate/{user}",  name="notification_disactivate", requirements={"user": "\d+"}, options={"expose": true})
      */
      public function disactivateAction(Request $request, User $user)
      {
          if(!$request->isXmlHttpRequest())
          {
              throw new \Exception('This action is forbidden');
          }

          $notifications = $user->getNotifications()->toArray();

          foreach ($notifications as $notification)
          {
              if($notification->getActive() == 1)
              {
                  $notification->setActive(0);
              }
          }

          $em = $this->getDoctrine()->getManager();
          $em->flush();

          return new JsonResponse();
      }
}
