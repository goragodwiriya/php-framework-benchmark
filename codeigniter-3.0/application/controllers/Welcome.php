<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends CI_Controller
{

    /**
     * Index Page for this controller.
     *
     * Maps to the following URL
     * 		http://example.com/index.php/welcome
     * 	- or -
     * 		http://example.com/index.php/welcome/index
     * 	- or -
     * Since this controller is set as the default controller in
     * config/routes.php, it's displayed at http://example.com/
     *
     * So any other public methods not prefixed with an underscore will
     * map to /index.php/welcome/<method_name>
     * @see http://codeigniter.com/user_guide/general/urls.html
     */
    public function index()
    {
        $this->output->set_output('Hello World!');
    }

    public function hello()
    {
        $this->output->set_output('Hello World!');
    }

    public function select()
    {
        for ($i = 0; $i < 100; $i++) {
            $rnd = mt_rand(1, 10000);
            $query = $this->db->query('SELECT * FROM world WHERE id='.$rnd.' LIMIT 1');
            $row = $query->row();
        }
        $this->output->set_output($row->name);
    }

    public function orm()
    {
        for ($i = 0; $i < 100; $i++) {
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