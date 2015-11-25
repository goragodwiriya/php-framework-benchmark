<?php

namespace App\Controller;

use Cake\ORM\Entity;
use Cake\ORM\TableRegistry;
use App\Model\Entity\World;

class HelloController extends AppController
{
    public $autoRender = false;

    public function index()
    {
        $this->response->body('Hello World!');
        return $this->response;
    }

    public function orm()
    {
        $world = TableRegistry::get('world', array('cache' => false));
        $world->updateAll(array('name' => ''), array(1 => 1));
        for ($i = 0; $i < 2; $i++) {
            $rnd = mt_rand(1, 10000);
            $result = $world->get($rnd);
            $result->name = 'Hello World!';
            $world->save($result);
        }
        $result = $world->get($result->id);
        $this->response->body($result->name);
        return $this->response;
    }

    public function select()
    {
        $world = TableRegistry::get('world', array('cache' => false));
        $world->updateAll(array('name' => 'Hello World!'), array(1 => 1));
        for ($i = 0; $i < 2; $i++) {
            $rnd = mt_rand(1, 10000);
            $result = $world->get($rnd);
        }
        $result = $world->get($result->id);
        $this->response->body($result->name);
        return $this->response;
    }
}