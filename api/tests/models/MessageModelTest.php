<?php

use PHPUnit\Framework\TestCase;
use \models\MessageModel;

class MessageModelTest extends TestCase
{
    //    private $messageModel;

    public function setUp()
    {
        $this->connection = new PDO('sqlite::memory:');
        $this->connection->setAttribute(
            PDO::ATTR_ERRMODE,
            PDO::ERRMODE_EXCEPTION
        );
        $this->connection->exec('CREATE TABLE messages (
                        id INT, 
                        message VARCHAR(255),
                        createdOn DATETIME,
                        PRIMARY KEY (id)
                   )');

        $this->messageModel = new MessageModel($this->connection);

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

    public function providerExistingIds()
    {
        return [['1'], ['2'], ['3']];
    }

    public function providerUnexistingIds()
    {
        return [['4'], ['100']];
    }

    public function providerValidMessageTexts()
    {
        return [['aa'], ['Aa'], ['aaa'], ['aa11']];
    }

    public function providerInvalidMessageTexts()
    {
        return [[''], ['A'], [1.2], [1]];
    }

    public function testGetAllMessages_messagesInDatabase_arrayWithMessages()
    {
        $actualMessages = $this->messageModel->getAllMessages();
        $expectedMessages = $this->providerMessages();

        $this->assertEquals('array', gettype($actualMessages));
        $this->assertEquals(count($expectedMessages), count($actualMessages));
        foreach ($actualMessages as $actualMessage) {
            $this->assertContains($actualMessage, $expectedMessages);
        }
    }

    public function testGetAllMessages_noMessagesInDatabase_emptyArray()
    {


        $this->connection->exec('DROP TABLE messages');
        $this->connection->exec('CREATE TABLE messages (
                        id INT, 
                        message VARCHAR(255),
                        createdOn DATETIME,
                        PRIMARY KEY (id)
                   )');
        $actualMessages = $this->messageModel->getAllMessages();

        $this->assertEquals('array', gettype($actualMessages));
        $this->assertEquals(0, count($actualMessages));
    }

    /**
     * @expectedException \PDOException
     **/
    public function testGetAllMessages_noTableMessages_PDOException()
    {


        $this->connection->exec('DROP TABLE messages');
        $this->messageModel->getAllMessages();
    }

    /**
     * @dataProvider providerExistingIds
     **/
    public function testGetMessageById_messageWithSpecificIdIsInsideDatabase_messageObject($existingId)
    {
        global $expectedId;

        array_filter($this->providerMessages(), function ($messageFromProvidedMessages) use ($existingId) {
            if ($messageFromProvidedMessages['id'] == $existingId) {
                global $expectedId;
                $expectedId = $existingId;
                return true;
            }
        });

        $actualMessage = $this->messageModel->getMessageById($existingId);

        $this->assertEquals('object', gettype($actualMessage));
        $this->assertNotNull($actualMessage);
        $this->assertEquals($expectedId, $actualMessage->id);
    }

    /**
     * @dataProvider providerUnexistingIds
     **/
    public function testGetMessageById_messageWithSpecificIdIsNotInsideDatabase_noMessageObject($unexistingId)
    {

        $expectedMessageIsEmpty = array_filter($this->providerMessages(),
            function ($messageFromProvidedMessages) use ($unexistingId) {
                return ($messageFromProvidedMessages['id'] == $unexistingId);
            });

        $actualMessage = $this->messageModel->getMessageById($unexistingId);

        $this->assertFalse($actualMessage);
        $this->assertEquals(count($expectedMessageIsEmpty), 0);
    }

    /**
     * @expectedException \PDOException
     **/
    public function testGetMessageById_noTableMessages_PDOException()
    {


        $this->connection->exec('DROP TABLE messages');
        $this->messageModel->getMessageById(1);
    }

    /**
     * @dataProvider providerValidMessageTexts
     **/
    public function testAddMessage_messageIsValidAndIsAddedToDatabase_messageWithText($validMessageText)
    {

        $statement = $this->connection->prepare("SELECT * FROM messages WHERE message='" . $validMessageText . "'");

        $actualMessage = $this->messageModel->addMessage($validMessageText);
        $statement->execute();
        $actualMessageInDatabase = $statement->fetch(\PDO::FETCH_OBJ);

        $this->assertNotNull($actualMessage);
        $this->assertNotNull($actualMessageInDatabase);
        $this->assertEquals('object', gettype($actualMessageInDatabase));
        $this->assertEquals($validMessageText, $actualMessage['message']);
        $this->assertEquals($validMessageText, $actualMessageInDatabase->message);
        $this->assertEquals($actualMessageInDatabase->message, $actualMessage['message']);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @dataProvider providerInvalidMessageTexts
     **/
    public function testAddMessage_messageIsInvalid_InvalidArgumentException($invalidMessageText)
    {


        $this->messageModel->addMessage($invalidMessageText);

        $this->expectExceptionMessage("Text moet een string met minstens 2 karakters zijn");
    }

    /**
     * @expectedException \PDOException
     **/
    public function testAddMessage_noTableMessages_PDOException()
    {


        $this->connection->exec('DROP TABLE messages');

        $this->messageModel->addMessage("test");
    }
}
