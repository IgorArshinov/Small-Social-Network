<?php

namespace controllers;

use models\ReactionModel;
use views\ViewInterface;

class ReactionController
{
    private $reactionModel;
    private $reactionView;

    public function __construct(ReactionModel $reactionModel, ViewInterface $reactionView)
    {
        $this->reactionModel = $reactionModel;
        $this->reactionView = $reactionView;
    }

    public function getAllReactions()
    {
        $statuscode = 200;
        $messages = [];
        try {
            $messages = $this->reactionModel->getAllReactions();
        } catch (\PDOException $exception) {
            $statuscode = 500;
        }
        $this->reactionView->showMultipleEntities(['reactions' => $messages, 'statuscode' => $statuscode]);
    }

    public function getAllReactionsByMessageId($id)
    {
        $statuscode = 200;
        $reactions = [];
        try {
            $reactions = $this->reactionModel->getAllReactionsByMessageId($id);
        } catch (\PDOException $exception) {
            $statuscode = 500;
        }
        $this->reactionView->showMultipleEntities(['reactions' => $reactions, 'statuscode' => $statuscode]);
    }

    public function addReactionToMessageWithId($text, $messageId)
    {
        $statuscode = 201;
        $reaction = null;
        try {
            $reaction = $this->reactionModel->addReactionToMessageWithId($text, $messageId);
        } catch (\InvalidArgumentException $exception) {
            $statuscode = 400;
        } catch (\PDOException $exception) {
            $statuscode = 500;
        }
        $this->reactionView->showSingleEntity(['reaction' => $reaction, 'statuscode' => $statuscode]);
    }
}
