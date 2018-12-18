<?php

use PHPUnit\Framework\TestCase;
use \models\ReactionModel;

class ReactionModelTest extends TestCase
{
    public function setUp()
    {
        $this->connection = new PDO('sqlite::memory:');
        $this->connection->setAttribute(
            PDO::ATTR_ERRMODE,
            PDO::ERRMODE_EXCEPTION
        );
        $this->connection->exec('CREATE TABLE reactions (
                        id INT, 
                        messageId INT,
                        reaction VARCHAR(255),
                        createdOn DATETIME,
                        PRIMARY KEY (id)
                   )');
        $this->connection->exec('CREATE TABLE messages (
                        id INT, 
                        message VARCHAR(255),
                        createdOn DATETIME,
                        PRIMARY KEY (id)
                   )');

        $this->reactionModel = new ReactionModel($this->connection);

        $reactions = $this->providerReactions();
        foreach ($reactions as $reaction) {
            $this->connection->exec("INSERT INTO reactions (id, messageId, reaction, createdOn) VALUES (" . $reaction['id'] . "," . $reaction['messageId'] . ",'" . $reaction['reaction'] . "','" . $reaction['createdOn'] . "');");
        }

        $messages = $this->providerMessages();
        foreach ($messages as $message) {
            $this->connection->exec("INSERT INTO messages (id, message, createdOn) VALUES (" . $message['id'] . ",'" . $message['message'] . "','" . $message['createdOn'] . "');");
        }
    }

    public function tearDown()
    {
        $this->connection = null;
    }

    public function providerMessages()
    {
        return [['id' => '1', 'message' => 'testmessage1', 'createdOn' => '2018-11-09 13:57:06'], ['id' => '2', 'message' => 'testmessage2', 'createdOn' => '2018-11-09 14:57:06'], ['id' => '3', 'message' => 'testmessage3', 'createdOn' => '2018-11-09 11:57:06']];
    }

    public function providerReactions()
    {
        return [['id' => '1', 'messageId' => '1', 'reaction' => 'testreaction1', 'createdOn' => '2018-11-09 13:57:06'], ['id' => '2', 'messageId' => '1', 'reaction' => 'testreaction2', 'createdOn' => '2018-11-09 14:57:06'], ['id' => '3', 'messageId' => '2', 'reaction' => 'testreaction3', 'createdOn' => '2018-11-09 11:57:06'], ['id' => '4', 'messageId' => '3', 'reaction' => 'testreaction3', 'createdOn' => '2018-11-09 11:57:06']];
    }

    public function providerValidExistingMessagesId()
    {
        return [['1'], ['2'], ['3']];
    }

    public function providerValidExistingReactionsId()
    {
        return [['1'], ['2'], ['3'], ['4']];
    }

    public function providerValidUnexistingMessagesId()
    {
        return [['4'], ['100']];
    }

    public function providerValidUnexistingReactionsId()
    {
        return [['5'], ['50']];
    }

    public function providerValidIds()
    {
        return [['1'], ['2'], ['3'], ['100'], ['1000']];
    }

    public function providerInvalidIds()
    {
        return [['0'], ['-1'], ['1.2'], ["aaa"], [12], [1.2]];
    }

    public function providerValidReactionTexts()
    {
        return [['aa'], ['Aa'], ['aaa'], ['aa11']];
    }

    public function providerInvalidReactionTexts()
    {
        return [[''], ['A'], [1.2], [1]];
    }

    public function testGetAllReactions_reactionsInDatabase_arrayWithReactions()
    {
        $actualReactions = $this->reactionModel->getAllReactions();
        $expectedReactions = $this->providerReactions();

        $this->assertEquals('array', gettype($actualReactions));
        $this->assertEquals(count($expectedReactions), count($actualReactions));
        foreach ($actualReactions as $actualReaction) {
            $this->assertContains($actualReaction, $expectedReactions);
        }
    }

    public function testGetAllReactions_noReactionsInDatabase_emptyArray()
    {
        $reactionModel = new ReactionModel($this->connection);

        $this->connection->exec('DROP TABLE reactions');
        $this->connection->exec('CREATE TABLE reactions (
                         id INT, 
                        messageId INT,
                        reaction VARCHAR(255),
                        createdOn DATETIME,
                        PRIMARY KEY (id)
                   )');
        $actualReactions = $this->reactionModel->getAllReactions();

        $this->assertEquals('array', gettype($actualReactions));
        $this->assertEquals(0, count($actualReactions));
    }

    /**
     * @expectedException \PDOException
     **/
    public function testGetAllReactions_noTableReactions_PDOException()
    {
        $reactionModel = new ReactionModel($this->connection);

        $this->connection->exec('DROP TABLE reactions');
        $this->reactionModel->getAllReactions();
    }

    /**
     * @dataProvider providerValidExistingReactionsId
     **/
    public function testGetReactionById_reactionWithSpecificIdIsInsideDatabase_reactionObject($existingId)
    {
        global $expectedId;
        $reactionModel = new ReactionModel($this->connection);
        array_filter($this->providerReactions(),
            function ($reactionFromProvidedReactions) use ($existingId) {
                if ($reactionFromProvidedReactions['id'] == $existingId) {
                    global $expectedId;
                    $expectedId = $existingId;
                    return true;
                }
            });

        $actualReaction = $this->reactionModel->getReactionById($existingId);

        $this->assertEquals('object', gettype($actualReaction));
        $this->assertNotNull($actualReaction);
        $this->assertEquals($expectedId, $actualReaction->id);
    }

    /**
     * @dataProvider providerValidUnexistingReactionsId
     **/
    public function testGetReactionById_reactionWithSpecificIdIsNotInsideDatabase_noReactionObject($unexistingId)
    {
        global $expectedId;
        $reactionModel = new ReactionModel($this->connection);
        $expectedReactionIsEmpty = array_filter($this->providerReactions(),
            function ($reactionFromProvidedReactions) use ($unexistingId, $expectedId) {
                return ($reactionFromProvidedReactions['id'] == $unexistingId);
            });

        $actualReaction = $this->reactionModel->getReactionById($unexistingId);

        $this->assertFalse($actualReaction);
        $this->assertEquals(count($expectedReactionIsEmpty), 0);
    }

    /**
     * @expectedException \PDOException
     **/
    public function testGetReactionById_noTableReactions_PDOException()
    {
        $reactionModel = new ReactionModel($this->connection);

        $this->connection->exec('DROP TABLE reactions');
        $this->reactionModel->getReactionById("1");
    }

    /**
     * @dataProvider providerValidReactionTexts
     **/
    public function testAddReactionToMessageWithId_reactionIsValidAndIsAddedToDatabase_reactionWithText($validReactionText)
    {
        $reactionModel = new ReactionModel($this->connection);
        $statement = $this->connection->prepare("SELECT * FROM reactions WHERE reaction='" . $validReactionText . "'");

        $actualReaction = $this->reactionModel->addReactionToMessageWithId($validReactionText, "1");
        $statement->execute();
        $actualReactionInDatabase = $statement->fetch(\PDO::FETCH_OBJ);

        $this->assertNotNull($actualReaction);
        $this->assertNotNull($actualReactionInDatabase);
        $this->assertEquals('object', gettype($actualReactionInDatabase));
        $this->assertEquals($validReactionText, $actualReaction['reaction']);
        $this->assertEquals($validReactionText, $actualReactionInDatabase->reaction);
        $this->assertEquals($actualReactionInDatabase->reaction, $actualReaction['reaction']);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @dataProvider providerInvalidReactionTexts
     **/
    public function testAddReactionToMessageWithId_reactionIsInvalid_InvalidArgumentException($invalidReactionText)
    {
        $reactionModel = new ReactionModel($this->connection);

        $this->reactionModel->addReactionToMessageWithId($invalidReactionText, "1");
        $this->expectExceptionReaction("Text moet een string met minstens 2 karakters zijn");
    }

    /**
     * @dataProvider providerValidUnexistingMessagesId
     **/
    public function testAddReactionToMessageWithId_messageIdDoesNotExist_false($unexistingId)
    {
        $reactionModel = new ReactionModel($this->connection);

        $this->assertFalse($this->reactionModel->addReactionToMessageWithId("test", $unexistingId));
    }

    /**
     * @expectedException \PDOException
     **/
    public function testAddReactionToMessageWithId_noTableReactions_PDOException()
    {
        $reactionModel = new ReactionModel($this->connection);

        $this->connection->exec('DROP TABLE reactions');
        $this->reactionModel->addReactionToMessageWithId("test", "1");
    }

    /**
     * @expectedException \InvalidArgumentException
     * @dataProvider providerInvalidIds
     **/
    public function testIdExists_invalidId_InvalidArgumentException($invalidId)
    {
        $reactionModel = new ReactionModel($this->connection);

        $this->reactionModel->idExists($invalidId, 'messages');

        $this->expectExceptionReaction("Id moet een int > 0 bevatten");
    }

    /**
     * @dataProvider providerValidExistingMessagesId
     **/

    public function testIdExists_existingId_True($existingId)
    {
        $reactionModel = new ReactionModel($this->connection);

        $this->assertTrue($this->reactionModel->idExists($existingId, 'messages'));
    }

    /**
     * @dataProvider providerValidUnexistingMessagesId
     **/
    public function testIdExists_unexistingId_False($unexistingId)
    {
        $reactionModel = new ReactionModel($this->connection);

        $this->assertFalse($this->reactionModel->idExists($unexistingId, 'messages'));
    }

    /**
     * @dataProvider providerValidExistingMessagesId
     **/
    public function testGetAllReactionsByMessageId_reactionsInDatabase_arrayWithReactions($existingId)
    {
        $reactionModel = new ReactionModel($this->connection);
        $expectedReactions = array_filter($this->providerReactions(),
            function ($reactionFromProvidedReactions) use ($existingId) {
                return ($reactionFromProvidedReactions['messageId'] == $existingId);
            });

        $actualReactions = $this->reactionModel->getAllReactionsByMessageId($existingId);

        $this->assertEquals('array', gettype($actualReactions));
        $this->assertEquals(count($expectedReactions), count($actualReactions));
        foreach ($actualReactions as $actualReaction) {
            $this->assertContains($actualReaction, $expectedReactions);
        }
    }

    public function testGetAllReactionsByMessageId_noReactionsInDatabase_emptyArray()
    {
        $reactionModel = new ReactionModel($this->connection);

        $this->connection->exec('DROP TABLE reactions');
        $this->connection->exec('CREATE TABLE reactions (
                         id INT, 
                        messageId INT,
                        reaction VARCHAR(255),
                        createdOn DATETIME,
                        PRIMARY KEY (id)
                   )');
        $actualReactions = $this->reactionModel->getAllReactionsByMessageId(1);

        $this->assertEquals('array', gettype($actualReactions));
        $this->assertEquals(0, count($actualReactions));
    }

    /**
     * @dataProvider providerValidUnexistingMessagesId
     **/
    public function testGetAllReactionsByMessageId_noReactionsWithValidMessageIdInDatabase_emptyArray($validUnexistingId)
    {
        $reactionModel = new ReactionModel($this->connection);

        $actualReactions = $this->reactionModel->getAllReactionsByMessageId($validUnexistingId);

        $this->assertEquals('array', gettype($actualReactions));
        $this->assertEquals(0, count($actualReactions));
    }

    /**
     * @expectedException \PDOException
     **/
    public function testGetAllReactionsByMessageId_noTableReactions_PDOException()
    {
        $reactionModel = new ReactionModel($this->connection);

        $this->connection->exec('DROP TABLE reactions');
        $this->reactionModel->getAllReactionsByMessageId(1);
    }
}
