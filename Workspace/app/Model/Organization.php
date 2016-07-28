<?php
App::uses('AppModel', 'Model');

class Organization extends AppModel {

	public $useTable = 'organizations';

	public $order = array("Organization.name" => "asc");

}