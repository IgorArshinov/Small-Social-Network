<?php

namespace views;
class ReactionView implements ViewInterface
{
    public function showSingleEntity(array $data)
    {
        header('Content-Type: application/json');
        http_response_code($data['statuscode']);
        $reaction = $data['reaction'];
        print(json_encode($reaction));
    }

    public function showMultipleEntities(array $data)
    {
        header('Content-Type: application/json');
        http_response_code($data['statuscode']);
        $reactions = $data['reactions'];
        print(json_encode($reactions));
    }
}