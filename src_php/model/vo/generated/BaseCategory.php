<?php

/**
 * BaseCategory
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property integer $userId
 * @property string $title
 * @property timestamp $createDate
 * @property timestamp $modDate
 * @property User $User
 * @property Doctrine_Collection $posts
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 6820 2009-11-30 17:27:49Z jwage $
 */
abstract class BaseCategory extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('Category');
        $this->hasColumn('id', 'integer', 4, array(
             'type' => 'integer',
             'primary' => true,
             'autoincrement' => true,
             'length' => '4',
             ));
        $this->hasColumn('userId', 'integer', 4, array(
             'type' => 'integer',
             'notnull' => true,
             'length' => '4',
             ));
        $this->hasColumn('title', 'string', 45, array(
             'type' => 'string',
             'length' => '45',
             ));
        $this->hasColumn('createDate', 'timestamp', null, array(
             'type' => 'timestamp',
             ));
        $this->hasColumn('modDate', 'timestamp', null, array(
             'type' => 'timestamp',
             ));


        $this->index('fk_Category_User1', array(
             'fields' => 
             array(
              0 => 'userId',
             ),
             ));
        $this->option('collate', 'utf8_general_ci');
        $this->option('charset', 'utf8');
        $this->option('type', 'InnoDB');
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('User', array(
             'local' => 'userId',
             'foreign' => 'id'));

        $this->hasMany('Post as posts', array(
             'local' => 'id',
             'foreign' => 'categoryId'));
    }

    public function construct()
    {
        $this->mapValue('_explicitType', 'model.vo.Category');
    }
}