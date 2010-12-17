<?php

/**
 * BasePhonenumber
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property string $name
 * @property integer $user_id
 * @property User $User
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id$
 */
abstract class BasePhonenumber extends Aerial_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('phonenumber');
        $this->hasColumn('id', 'integer', 4, array(
             'type' => 'integer',
             'primary' => true,
             'autoincrement' => true,
             'length' => '4',
             ));
        $this->hasColumn('name', 'string', 255, array(
             'type' => 'string',
             'length' => '255',
             ));
        $this->hasColumn('user_id', 'integer', 4, array(
             'type' => 'integer',
             'length' => '4',
             ));

        $this->option('collate', 'utf8_general_ci');
        $this->option('charset', 'utf8');
        $this->option('type', 'InnoDB');
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('User', array(
             'local' => 'user_id',
             'foreign' => 'id'));
    }
    public function construct()
    {
        $this->mapValue('_explicitType', 'org.aerial.vo.Phonenumber');
    }
}