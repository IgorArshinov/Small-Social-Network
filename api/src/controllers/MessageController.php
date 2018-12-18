<?php

namespace controllers;

use models\MessageModel;
use views\ViewInterface;

class MessageController
{
    private $messageModel;
    private $messageView;

    public function __construct(MessageModel $messageModel, ViewInterface $жessageView)
    {
        $this->messageModel = $messageModel;
        $this->messageView = $жessageView;
    }

    public function getAllMessages()
    {
        $statuscode = 200;
        $messages = [];
        try {
            $messages = $this->messageModel->getAllMessages();
        } catch (\PDOException $exception) {
            $statuscode = 500;
        }
        $this->messageView->showMultipleEntities(['messages' => $messages, 'statuscode' => $statuscode]);
    }

    public function getMessageById($id)
    {
        $statuscode = 200;
        $message = null;

        try {
            $message = $this->messageModel->getMessageById($id);
        } catch (\PDOException $exception) {
            $statuscode = 500;
        }
        $this->messageView->showSingleEntity(['message' => $message, 'statuscode' => $statuscode]);
    }

    public function addMessage($messageText)
    {
        $statuscode = 201;
        $message = null;

        try {
            $message = $this->messageModel->addMessage($messageText);
        } catch (\InvalidArgumentException $exception) {
            $statuscode = 400;
        } catch (\PDOException $exception) {
            $statuscode = 500;
        }
        $this->messageView->showSingleEntity(['message' => $message, 'statuscode' => $statuscode]);
    }
}
