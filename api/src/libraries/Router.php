<?php

namespace libraries;

use models\MessageModel;
use models\ReactionModel;
use views\MessageView;
use views\ReactionView;
use controllers\MessageController;
use controllers\ReactionController;
use AltoRouter;

class Router
{
    private $router;
    private $reactionModel;
    private $messageModel1;
    private $messageView;
    private $reactionView;
    private $messageController;
    private $reactionController;

    public function __construct($pdo)
    {
        $this->router = new AltoRouter();
        $this->messageModel1 = new MessageModel($pdo);
        $this->reactionModel = new ReactionModel($pdo);
        $this->messageView = new MessageView();
        $this->reactionView = new ReactionView();
        $this->messageController = new MessageController($this->messageModel1, $this->messageView);
        $this->reactionController = new ReactionController($this->reactionModel, $this->reactionView);
    }

    public function initializeRoutes()
    {
        $this->router->setBasePath('/small-social-network/api/');

        $this->router->map(
            'GET',
            'messages/[i:messageId]/reactions/',
            function ($id) {
                $this->reactionController->getAllReactionsByMessageId($id);
            }
        );

        $this->router->map(
            'POST',
            'messages/[i:messageId]/reactions/',
            function ($messageId) {
                $entityBody = file_get_contents('php://input', 'r');
                $json = json_decode($entityBody);
                $reactionText = null;
                if (isset($json->reaction)) {
                    $reactionText = $json->reaction;
                }
                $this->reactionController->addReactionToMessageWithId($reactionText, $messageId);
            }
        );

        $this->router->map(
            'GET',
            'messages/',
            function () {
                $this->messageController->getAllMessages();
            }
        );
        $this->router->map(
            'POST',
            'messages/',
            function () {
                $entityBody = file_get_contents('php://input', 'r');
                $json = json_decode($entityBody);
                $text = null;
                if (isset($json->message)) {
                    $text = $json->message;
                }

                $this->messageController->addMessage($text);
            }
        );
        $this->router->map(
            'GET',
            'messages/[i:id]',
            function ($id) {
                $this->messageController->getMessageById($id);
            }
        );

        $match = $this->router->match();
        if ($match && is_callable($match['target'])) {
            call_user_func_array($match['target'], $match['params']);
        } else {
            http_response_code(400);
        }
    }
}