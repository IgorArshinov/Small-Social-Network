<?php

use PHPUnit\Framework\TestCase;
use \controllers\MessageController;

class MessageControllerTest extends TestCase
{
    public function setUp()
    {
        $this->messageModel = $this->getMockBuilder('\models\MessageModel')
                                   ->disableOriginalConstructor()
                                   ->getMock();
        $this->messageView = $this->getMockBuilder('\views\MessageView')
                                  ->disableOriginalConstructor()
                                  ->getMock();
        $this->messageController = new MessageController($this->messageModel, $this->messageView);
    }

    public function providerValidMessages()
    {
        return [['id' => '1', 'message' => 'testmessage1', 'createdOn' => '2018-11-09 13:57:06'], ['id' => '2', 'message' => 'testmessage2', 'createdOn' => '2018-11-09 14:57:06'], ['id' => '3', 'message' => 'testmessage3', 'createdOn' => '2018-11-09 11:57:06']];
    }

    public function providerInvalidMessages()
    {
        return [['id' => '1', 'message' => 't', 'createdOn' => '2018-11-09 13:57:06'], ['id' => '2', 'message' => '', 'createdOn' => '2018-11-09 14:57:06'], ['id' => '3', 'message' => '3', 'createdOn' => '2018-11-09 11:57:06']];
    }

    /**
     * @dataProvider providerValidMessages
     **/
    public function testAddMessage_typedValidMessage_showMessageAndStatus201($messageText)
    {
        $message = ['message' => $messageText];
        $data = ['message' => $message, 'statuscode' => 201];

        $this->messageModel->expects($this->atLeastOnce())
                           ->method('addMessage')
                           ->with($this->equalTo($messageText))
                           ->will($this->returnValue($message));
        $this->messageView->expects($this->atLeastOnce())
                          ->method('showSingleEntity')
                          ->with($this->equalTo($data));
        $this->messageController->addMessage($messageText);
    }

    /**
     * @dataProvider providerInvalidMessages
     **/
    public function testAddMessage_typedInvalidMessage_showNullAndStatus400($messageText)
    {
        $data = ['message' => null, 'statuscode' => 400];

        $this->messageModel->expects($this->atLeastOnce())
                           ->method('addMessage')
                           ->with($this->equalTo($messageText))
                           ->will($this->throwException(new InvalidArgumentException));
        $this->messageView->expects($this->atLeastOnce())
                          ->method('showSingleEntity')
                          ->with($this->equalTo($data));
        $this->messageController->addMessage($messageText);
    }

    /**
     * @dataProvider providerValidMessages
     **/
    public function testAddMessage_PDOExceptionWasThrown_showNullAndStatus500($messageText)
    {
        $data = ['message' => null, 'statuscode' => 500];

        $this->messageModel->expects($this->atLeastOnce())
                           ->method('addMessage')
                           ->with($this->equalTo($messageText))
                           ->will($this->throwException(new PDOException));
        $this->messageView->expects($this->atLeastOnce())
                          ->method('showSingleEntity')
                          ->with($this->equalTo($data));
        $this->messageController->addMessage($messageText);
    }

    public function testGetAllMessages_PDOExceptionWasThrown_showNullAndStatus500()
    {
        $data = ['messages' => [], 'statuscode' => 500];

        $this->messageModel->expects($this->atLeastOnce())
                           ->method('getAllMessages')
                           ->will($this->throwException(new PDOException));
        $this->messageView->expects($this->atLeastOnce())
                          ->method('showMultipleEntities')
                          ->with($this->equalTo($data));
        $this->messageController->getAllMessages();
    }

    public function testGetAllMessages_clickedOnGetAllMessages_showMessagesAndStatus200()
    {
        $messages = $this->providerValidMessages();
        $data = ['messages' => $messages, 'statuscode' => 200];

        $this->messageModel->expects($this->atLeastOnce())
                           ->method('getAllMessages')
                           ->will($this->returnValue($messages));
        $this->messageView->expects($this->atLeastOnce())
                          ->method('showMultipleEntities')
                          ->with($this->equalTo($data));
        $this->messageController->getAllMessages();
    }

    /**
     * @dataProvider providerValidMessages
     **/
    public function testGetMessageById_clickedOnGetMessageById_showMessageWithSpecificIdAndStatus200($id, $messageText, $createdOn)
    {
        $message = ['id' => $id, 'message' => $messageText, 'createdOn' => $createdOn];
        $data = ['message' => $message, 'statuscode' => 200];

        $this->messageModel->expects($this->atLeastOnce())
                           ->method('getMessageById')
                           ->with($this->equalTo($id))
                           ->will($this->returnValue($message));
        $this->messageView->expects($this->atLeastOnce())
                          ->method('showSingleEntity')
                          ->with($this->equalTo($data));
        $this->messageController->getMessageById($id);
    }

    /**
     * @dataProvider providerValidMessages
     **/
    public function testGetMessageById_PDOExceptionWasThrown_showNullAndStatus500($id)
    {
        $data = ['message' => null, 'statuscode' => 500];

        $this->messageModel->expects($this->atLeastOnce())
                           ->method('getMessageById')
                           ->with($this->equalTo($id))
                           ->will($this->throwException(new PDOException));
        $this->messageView->expects($this->atLeastOnce())
                          ->method('showSingleEntity')
                          ->with($this->equalTo($data));
        $this->messageController->getMessageById($id);
    }
}