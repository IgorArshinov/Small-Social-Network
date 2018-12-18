<?php

use PHPUnit\Framework\TestCase;
use \views\MessageView;

class MessageViewTest extends TestCase
{
    private $messageView;

    public function setUp()
    {
        $this->messageView = new MessageView;
    }

    /**
     * @runInSeparateProcess
     **/
    public function testShowSingleEntity_singleMessageIsUsed_showMessageInOutput()
    {
        $data = ['message' => 'testmessage1', 'statuscode' => 201];

        $this->messageView->showSingleEntity($data);

        $this->expectOutputString(json_encode($data['message']));
    }

    /**
     * @runInSeparateProcess
     **/
    public function testShowMultipleEntities_multipleMessageAreUsed_showMessagesInOutput()
    {
        $data = ['messages' => ['id' => '1', 'message' => 'testmessage1', 'createdOn' => '2018-11-09 13:57:06'], 'statuscode' => 201];

        $this->messageView->showMultipleEntities($data);

        $this->expectOutputString(json_encode($data['messages']));
    }
}