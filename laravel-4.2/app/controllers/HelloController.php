<?php

class HelloController extends BaseController
{

    public function getIndex()
    {
        return 'Hello World!';
    }

    public function getOrm()
    {
        World::where('id', '>', 0)->update(array('name' => ''));
        for ($i = 0; $i < 100; $i++) {
            $rnd = mt_rand(1, 10000);
            $result = World::find($rnd);
            $result->name = 'Hello World!';
            $result->save();
        }
        $result = World::find($result->id);
        return $result->name;
    }

    public function getSelect()
    {
        World::where('id', '>', 0)->update(array('name' => 'Hello World!'));
        for ($i = 0; $i < 100; $i++) {
            $rnd = mt_rand(1, 10000);
            $result = World::find($rnd);
        }
        $result = World::find($result->id);
        return $result->name;
    }
}