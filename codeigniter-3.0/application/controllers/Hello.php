<?php

/**
 * Created by JetBrains PhpStorm.
 * User: Skamander
 * Date: 11.04.13
 * Time: 17:33
 * To change this template use File | Settings | File Templates.
 */
class Hello extends CI_Controller
{

    public function index()
    {
        $this->output->set_output('Hello World!');
    }

    public function select()
    {
        for ($i = 0; $i < 2; $i++) {
            $rnd = mt_rand(1, 10000);
            $query = $this->db->query('SELECT * FROM world WHERE id='.$rnd.' LIMIT 1');
            $row = $query->row();
        }
        $this->output->set_output($row->name);
    }

    public function orm()
    {
        for ($i = 0; $i < 2; $i++) {
            $rnd = mt_rand(1, 10000);
            $query = $this->db->query('SELECT * FROM world WHERE id='.$rnd.' LIMIT 1');
            $row = $query->row();
            $row->name = 'Hello World!';
            $sql = "UPDATE `world` SET `name`='".$row->name."' WHERE id=".$row->id;
            $this->db->query($sql);
        }
        $this->output->set_output($row->name);
    }
}