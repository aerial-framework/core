<?php

/**
 * BasePost
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property integer $userId
 * @property integer $topicId
 * @property string $title
 * @property clob $message
 * @property timestamp $createDate
 * @property timestamp $modDate
 * @property User $User
 * @property Topic $Topic
 * @property Doctrine_Collection $comments
 * @property Doctrine_Collection $postTags
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id$
 */
abstract class BasePost extends Aerial_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('Post');
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
        $this->hasColumn('topicId', 'integer', 4, array(
             'type' => 'integer',
             'notnull' => true,
             'length' => '4',
             ));
        $this->hasColumn('title', 'string', 45, array(
             'type' => 'string',
             'notnull' => true,
             'length' => '45',
             ));
        $this->hasColumn('message', 'clob', 65535, array(
             'type' => 'clob',
             'length' => '65535',
             ));
        $this->hasColumn('createDate', 'timestamp', null, array(
             'type' => 'timestamp',
             ));
        $this->hasColumn('modDate', 'timestamp', null, array(
             'type' => 'timestamp',
             'notnull' => true,
             ));


        $this->index('fk_Post_User1', array(
             'fields' => 
             array(
              0 => 'userId',
             ),
             ));
        $this->index('fk_Post_Topic1', array(
             'fields' => 
             array(
              0 => 'topicId',
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
             'foreign' => 'id',
             'onDelete' => 'cascade',
             'onUpdate' => 'cascade'));

        $this->hasOne('Topic', array(
             'local' => 'topicId',
             'foreign' => 'id'));

        $this->hasMany('Comment as comments', array(
             'local' => 'id',
             'foreign' => 'postId'));

        $this->hasMany('PostTag as postTags', array(
             'local' => 'id',
             'foreign' => 'postId'));
    }
    public function construct()
    {
        $this->mapValue('_explicitType', 'org.aerial.vo.Post');
    }
}