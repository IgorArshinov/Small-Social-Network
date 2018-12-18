<?php

use PHPUnit\Framework\TestCase;
use \views\ReactionView;

class ReactionViewTest extends TestCase
{
    private $reactionView;

    public function setUp()
    {
        $this->reactionView = new ReactionView;
    }

    /**
     * @runInSeparateProcess
     **/
    public function testShowSingleEntity_singleReactionIsUsed_showReactionInOutput()
    {
        $data = ['reaction' => 'testreaction1', 'statuscode' => 201];

        $this->reactionView->showSingleEntity($data);

        $this->expectOutputString(json_encode($data['reaction']));
    }

    /**
     * @runInSeparateProcess
     **/
    public function testShowMultipleEntities_multipleReactionAreUsed_showReactionsInOutput()
    {
        $data = ['reactions' => ['id' => '1', 'reaction' => 'testreaction1', 'createdOn' => '2018-11-09 13:57:06'], 'statuscode' => 201];

        $this->reactionView->showMultipleEntities($data);
        
        $this->expectOutputString(json_encode($data['reactions']));
    }
}