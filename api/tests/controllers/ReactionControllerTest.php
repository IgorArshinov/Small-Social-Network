<?php

use PHPUnit\Framework\TestCase;
use \controllers\ReactionController;

class ReactionControllerTest extends TestCase
{
    public function setUp()
    {
        $this->reactionModel = $this->getMockBuilder('\models\ReactionModel')
                                    ->disableOriginalConstructor()
                                    ->getMock();
        $this->reactionView = $this->getMockBuilder('\views\ReactionView')
                                   ->disableOriginalConstructor()
                                   ->getMock();
        $this->reactionController = new ReactionController($this->reactionModel, $this->reactionView);
    }

    public function providerValidReactions()
    {
        return [['id' => '1', 'messageId' => '1', 'reaction' => 'testreaction1', 'createdOn' => '2018-11-09 13:57:06'], ['id' => '2', 'messageId' => '1', 'reaction' => 'testreaction2', 'createdOn' => '2018-11-09 14:57:06'], ['id' => '3', 'messageId' => '2', 'reaction' => 'testreaction3', 'createdOn' => '2018-11-09 11:57:06'], ['id' => '4', 'messageId' => '3', 'reaction' => 'testreaction3', 'createdOn' => '2018-11-09 11:57:06']];
    }

    public function providerInvalidReactions()
    {
        return [['id' => '1', 'messageId' => '1', 'reaction' => 't', 'createdOn' => '2018-11-09 13:57:06'], ['id' => '2', 'messageId' => '1', 'reaction' => '', 'createdOn' => '2018-11-09 14:57:06'], ['id' => '3', 'messageId' => '2', 'reaction' => '2', 'createdOn' => '2018-11-09 11:57:06'], ['id' => '4', 'messageId' => '3', 'reaction' => '/', 'createdOn' => '2018-11-09 11:57:06']];
    }

    /**
     * @dataProvider providerValidReactions
     **/
    public function testAddReactionToMessageWithId_typedValidReaction_showReactionAndStatus201($reactionText, $messageId)
    {
        $reaction = ['reaction' => $reactionText, 'messageId' => $messageId];
        $data = ['reaction' => $reaction, 'statuscode' => 201];

        $this->reactionModel->expects($this->atLeastOnce())
                            ->method('addReactionToMessageWithId')
                            ->with($this->equalTo($reactionText), $this->equalTo($messageId))
                            ->will($this->returnValue($reaction));
        $this->reactionView->expects($this->atLeastOnce())
                           ->method('showSingleEntity')
                           ->with($this->equalTo($data));
        $this->reactionController->addReactionToMessageWithId($reactionText, $messageId);
    }

    /**
     * @dataProvider providerInvalidReactions
     **/
    public function testAddReactionToMessageWithId_typedInvalidReaction_showNullAndStatus400($reactionText, $messageId)
    {
        $data = ['reaction' => null, 'statuscode' => 400];

        $this->reactionModel->expects($this->atLeastOnce())
                            ->method('addReactionToMessageWithId')
                            ->with($this->equalTo($reactionText), $this->equalTo($messageId))
                            ->will($this->throwException(new InvalidArgumentException));
        $this->reactionView->expects($this->atLeastOnce())
                           ->method('showSingleEntity')
                           ->with($this->equalTo($data));

        $this->reactionController->addReactionToMessageWithId($reactionText, $messageId);
    }

    /**
     * @dataProvider providerValidReactions
     **/
    public function testAddReactionToMessageWithId_PDOExceptionWasThrown_showNullAndStatus500($reactionText, $messageId)
    {
        $data = ['reaction' => null, 'statuscode' => 500];

        $this->reactionModel->expects($this->atLeastOnce())
                            ->method('addReactionToMessageWithId')
                            ->with($this->equalTo($reactionText), $this->equalTo($messageId))
                            ->will($this->throwException(new PDOException));
        $this->reactionView->expects($this->atLeastOnce())
                           ->method('showSingleEntity')
                           ->with($this->equalTo($data));
        $this->reactionController->addReactionToMessageWithId($reactionText, $messageId);
    }

    /**
     * @dataProvider providerValidReactions
     **/
    public function testGetAllReactionsByMessageId_PDOExceptionWasThrown_showNullAndStatus500($messageId)
    {
        $data = ['reactions' => [], 'statuscode' => 500];

        $this->reactionModel->expects($this->atLeastOnce())
                            ->method('getAllReactionsByMessageId')
                            ->with($this->equalTo($messageId))
                            ->will($this->throwException(new PDOException));
        $this->reactionView->expects($this->atLeastOnce())
                           ->method('showMultipleEntities')
                           ->with($this->equalTo($data));
        $this->reactionController->getAllReactionsByMessageId($messageId);
    }

    /**
     * @dataProvider providerValidReactions
     **/
    public function testGetAllReactionsByMessageId_clickedOnGetAllReactionsByMessageId_showReactionsAndStatus200($messageId)
    {
        $reactions = array_filter($this->providerValidReactions(),
            function ($reactionFromProvidedReactions) use ($messageId) {
                return ($reactionFromProvidedReactions['messageId'] == $messageId);
            });
        $data = ['reactions' => $reactions, 'statuscode' => 200];

        $this->reactionModel->expects($this->atLeastOnce())
                            ->method('getAllReactionsByMessageId')
                            ->with($this->equalTo($messageId))
                            ->will($this->returnValue($reactions));
        $this->reactionView->expects($this->atLeastOnce())
                           ->method('showMultipleEntities')
                           ->with($this->equalTo($data));
        $this->reactionController->getAllReactionsByMessageId($messageId);
    }

    public function testGetAllReactions_clickedOnGetAllReactions_showReactionWithSpecificIdAndStatus200()
    {
        $reactions = $this->providerValidReactions();
        $data = ['reactions' => $reactions, 'statuscode' => 200];

        $this->reactionModel->expects($this->atLeastOnce())
                            ->method('getAllReactions')
                            ->will($this->returnValue($reactions));
        $this->reactionView->expects($this->atLeastOnce())
                           ->method('showMultipleEntities')
                           ->with($this->equalTo($data));
        $this->reactionController->getAllReactions();
    }

    public function testGetAllReactions_PDOExceptionWasThrown_showNullAndStatus500()
    {
        $data = ['reactions' => [], 'statuscode' => 500];

        $this->reactionModel->expects($this->atLeastOnce())
                            ->method('getAllReactions')
                            ->will($this->throwException(new PDOException));
        $this->reactionView->expects($this->atLeastOnce())
                           ->method('showMultipleEntities')
                           ->with($this->equalTo($data));
        $this->reactionController->getAllReactions();
    }
}