<?php

namespace models;

interface MessageModelInterface
{
    public function getAllMessages();

    public function getMessageById($id);

    public function addMessage($message);
}