<?php

namespace views;
class MessageView implements ViewInterface
{
    public function showSingleEntity(array $data)
    {
        header('Content-Type: application/json');
        http_response_code($data['statuscode']);
        $message = $data['message'];
        print(json_encode($message));
    }

    public function showMultipleEntities(array $data)
    {
        header('Content-Type: application/json');
        http_response_code($data['statuscode']);
        $messages = $data['messages'];
        print(json_encode($messages));
    }
}