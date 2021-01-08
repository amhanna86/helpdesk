<?php

namespace App\Serializer;

class CircularReference
{
    public function __invoke($object){
        $object->getId();
    }
}