<?php
App::uses('AppModel', 'Model');

class Notification extends AppModel {

	public $useTable = 'notifications';

    public $belongsTo = array(
        'Validation'=>array(
            'className'=>'Validation',
            'foreignKey'=>'validations_id'
        ),
        'Owner'=>array(
            'className'=>'User',
            'foreignKey'=>'owner_id'
        )
    );

}