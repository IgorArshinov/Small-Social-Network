<?php

namespace models;
class MessageModel implements MessageModelInterface
{
    private $pdo = null;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getAllMessages()
    {
        $statement = $this->pdo->prepare('SELECT * FROM messages');
        $statement->execute();
        $statement->bindColumn(1, $id, \PDO::PARAM_INT);
        $statement->bindColumn(2, $message, \PDO::PARAM_STR);
        $statement->bindColumn(3, $createdOn, \PDO::PARAM_STR);
        $messages = [];
        while ($statement->fetch(\PDO::FETCH_BOUND)) {
            $messages[] = ['id' => $id, 'message' => $message, 'createdOn' => $createdOn];
        }
        return $messages;
    }

    public function getMessageById($id)
    {
        $statement = $this->pdo->prepare('SELECT * FROM messages WHERE id=:id');
        $statement->bindParam(':id', $id, \PDO::PARAM_INT);
        $statement->execute();
        return $statement->fetch(\PDO::FETCH_OBJ);
    }

    public function addMessage($messageText)
    {
        $this->validateText($messageText);
        $statement = $this->pdo->prepare('INSERT into messages (message) VALUES (:message)');
        $statement->bindParam(':message', $messageText, \PDO::PARAM_STR);
        $statement->execute();
        return ['message' => $messageText];
    }

    private function validateText($text)
    {
        if (!(is_string($text) && strlen($text) >= 2)) {
            throw new \InvalidArgumentException("Text moet een string met minstens 2 karakters zijn");
        }
    }
}
