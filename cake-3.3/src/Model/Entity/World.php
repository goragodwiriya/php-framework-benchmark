<?php namespace App\Model\Entity;

use Cake\ORM\Entity;

class World extends Entity
{

    public function id()
    {
        return $this->id;
    }

    public function name()
    {
        return $this->name;
    }
}