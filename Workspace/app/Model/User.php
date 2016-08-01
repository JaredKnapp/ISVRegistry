<?php

// app/Model/User.php
App::uses('AppModel', 'Model');
App::uses('BlowfishPasswordHasher', 'Controller/Component/Auth');

class User extends AppModel {

	public $useTable = 'users';

	public $displayField = 'firstname';

    public $belongsTo = array(
        'Organization' => array(
            'className' => 'Organization',
            'foreignKey' => 'organizations_id',
            'fields'=>array('id', 'name')
        )
    );

	public $hasMany = array(
		'SAPartners'=>array(
			'className'=>'Partner',
			'foreignKey'=>'sa_owner_id'
			)
		);

	public $validate = array(
		'firstname' => array(
			'required' => array(
				'rule' => 'notBlank',
				'message' => 'A first name is required'
				)
			),
        'lastname' => array(
			'required' => array(
				'rule' => 'notBlank',
				'message' => 'A last name is required'
				)
			),
		'email' => array(
			'email' => array(
				'rule' => array('email', true),
				'message' => 'Please supply a valid email address.'
				),
			'required' => array(
				'rule' => 'notBlank',
				'message' => 'An email address is required'
				)
			),
		/*		'password' => array(
					'length' => array(
						'rule' => array('between', 6, 15),
						'message' => 'The password must be between 6 and 15 characters.',
						'allowEmpty' => false
						)
					),*/
		'role' => array(
			'valid' => array(
				'rule' => array('inList', array('admin', 'editor', 'user')),
				'message' => 'Please enter a valid role',
				'allowEmpty' => false
				)
			),
        'organization' => array('required')
		);

	public function beforeSave($options = array()) {
		if (isset($this->data[$this->alias]['password'])) {
			$passwordHasher = new BlowfishPasswordHasher();
			$this->data[$this->alias]['password'] = $passwordHasher->hash( $this->data[$this->alias]['password'] );
		}
		return true;
	}

	function find($type='first', $query = array()) {
		switch ($type) {
			case 'superlist':
				if(!isset($query['fields']) || count($query['fields']) < 3) {
					return parent::find('list', $query);
				}

				if(!isset($query['separator'])) {
					$query['separator'] = ' ';
				}

				$query['recursive'] = -1;
				$list = parent::find('all', $query);

				for($i = 1; $i <= 2; $i++) {
					$field[$i] = str_replace($this->alias.'.', '', $query['fields'][$i]);
				}

				return Set::combine($list, '{n}.'.$this->alias.'.'.$this->primaryKey,
					array('%s'.$query['separator'].'%s',
						'{n}.'.$this->alias.'.'.$field[1],
						'{n}.'.$this->alias.'.'.$field[2]));
				break;

			default:
				return parent::find($type, $query);
				break;
		}
	}
}