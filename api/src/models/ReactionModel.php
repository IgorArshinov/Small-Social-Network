<?php

namespace models;
class ReactionModel implements ReactionModelInterface
{
    private $pdo = null;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getAllReactions()
    {
        $statement = $this->pdo->prepare('SELECT * FROM reactions');
        $statement->execute();
        $statement->bindColumn(1, $id, \PDO::PARAM_INT);
        $statement->bindColumn(2, $messageId, \PDO::PARAM_INT);
        $statement->bindColumn(3, $reaction, \PDO::PARAM_STR);
        $statement->bindColumn(4, $createdOn, \PDO::PARAM_STR);
        $reactions = [];
        while ($statement->fetch(\PDO::FETCH_BOUND)) {
            $reactions[] = ['id' => $id, 'messageId' => $messageId, 'reaction' => $reaction, 'createdOn' => $createdOn];
        }
        return $reactions;
    }

    public function getAllReactionsByMessageId($idOfMessage)
    {
        $statement = $this->pdo->prepare('SELECT * FROM reactions WHERE messageId=:id');
        $statement->bindParam(':id', $idOfMessage, \PDO::PARAM_INT);
        $statement->execute();
        $statement->bindColumn(1, $id, \PDO::PARAM_INT);
        $statement->bindColumn(2, $messageId, \PDO::PARAM_INT);
        $statement->bindColumn(3, $reaction, \PDO::PARAM_STR);
        $statement->bindColumn(4, $createdOn, \PDO::PARAM_STR);
        $reactions = [];
        while ($statement->fetch(\PDO::FETCH_BOUND)) {
            $reactions[] = ['id' => $id, 'messageId' => $messageId, 'reaction' => $reaction, 'createdOn' => $createdOn];
        }
        return $reactions;
    }

    public function getReactionById($id)
    {
        $statement = $this->pdo->prepare('SELECT * FROM reactions WHERE id=:id');
        $statement->bindParam(':id', $id, \PDO::PARAM_INT);
        $statement->execute();
        return $statement->fetch(\PDO::FETCH_OBJ);
    }

    public function addReactionToMessageWithId($reaction, $messageId)
    {
        if (!$this->idExists($messageId, 'messages'))
            return FALSE;

        $this->validateText($reaction);
        $statement = $this->pdo->prepare('INSERT into reactions (reaction, messageId) VALUES (:reaction, :messageId)');
        $statement->bindParam(':reaction', $reaction, \PDO::PARAM_STR);
        $statement->bindParam(':messageId', $messageId, \PDO::PARAM_INT);
        $statement->execute();
        return ['reaction' => $reaction, 'messageId' => $messageId];
    }

    public function idExists($id, $table)
    {
        $this->validateId($id);
        $statement = $this->pdo->prepare("SELECT id FROM " . $table . " WHERE id=:id");
        $statement->bindParam(':id', $id, \PDO::PARAM_INT);
        $statement->execute();
        if ($statement->fetch() === FALSE) {
            return FALSE;
        }
        return TRUE;
    }

    private function validateId($id)
    {
        if (!(is_string($id) && preg_match("/^[0-9]+$/", $id) && (int)$id > 0)) {
            echo $id;
            throw new \InvalidArgumentException("Id moet een int > 0 bevatten");
        }
    }

    private function validateText($text)
    {
        if (!(is_string($text) && strlen($text) >= 2)) {
            throw new \InvalidArgumentException("Text moet een string met minstens 2 karakters zijn");
        }
    }
}
