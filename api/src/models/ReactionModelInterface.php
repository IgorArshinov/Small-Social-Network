<?php

namespace models;

interface ReactionModelInterface
{
    public function getAllReactions();

    public function getAllReactionsByMessageId($idOfMessage);

    public function getReactionById($id);

    public function addReactionToMessageWithId($reaction, $messageId);

    public function idExists($id, $table);
}