<?php
namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use AppBundle\Entity\ChatMessage;

class ChatMessageController extends Controller
{
    /**
     * @Route("/chatmessage/load/{offset}", name="chatmessage_load", options={"expose": true})
     */
    public function loadAction(Request $request, $offset = 0)
    {
        $messages = $this->getDoctrine()->getRepository(ChatMessage::class)
            ->findByOffset($offset);

        if($request->isXmlHttpRequest())
        {
            $newContent = $this->renderView('chat/content.html.twig', [
                'messages' => $messages
            ]);

            return new JsonResponse([
                'newContent' => $newContent,
                'resultsNumber' => count($messages)
            ]);
        }

        return $this->render('chat/content.html.twig', [
            'messages' => $messages
        ]);
    }
}
