<?php

namespace App\Http\Controllers;

use App\World as World;

class HelloController extends Controller
{

    public function index()
    {
        return 'Hello World!';
    }

    public function orm()
    {
        $words = World::where('id', '>', 0)->update(array('name' => ''));
        World::where('id', '>', 0)->update(array('name' => ''));
        for ($i = 0; $i < 2; $i++) {
            $rnd = mt_rand(1, 10000);
            $result = World::find($rnd);
            $result->name = 'Hello World!';
            $result->save();
        }
        $result = World::find($result->id);
        return $result->name;
    }

    public function select()
    {
        World::where('id', '>', 0)->update(array('name' => 'Hello World!'));
        for ($i = 0; $i < 2; $i++) {
            $rnd = mt_rand(1, 10000);
            $result = World::find($rnd);
        }
        $result = World::find($result->id);
        return $result->name;
    }
}