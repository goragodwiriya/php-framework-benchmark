<?php
/**
 * Description of MainController
 *
 * @author darkredz
 */

class MainController extends DooController{

    public function index(){
        $this->view()->render('index', array('content'=>'Hello World!'));
    }

}
?>
