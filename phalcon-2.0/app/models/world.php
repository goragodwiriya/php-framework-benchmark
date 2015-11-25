<?php

use Phalcon\Mvc\Model;

class World extends Model
{
	public $id;
	public $name;
	public $updated_at;
	public $created_at;
	public $user_id;
	public $randomNumber;
}