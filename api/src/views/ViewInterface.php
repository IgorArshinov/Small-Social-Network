<?php

namespace views;

interface ViewInterface
{
    public function showSingleEntity(array $data);
    public function showMultipleEntities(array $data);
}
