<?php

/**
 * BasePostTag
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $postId
 * @property integer $tagId
 * @property Post $Post
 * @property Tag $Tag
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id$
 */
abstract class BasePostTag extends Aerial_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('PostTag');
        $this->hasColumn('postId', 'integer', 4, array(
             'type' => 'integer',
             'primary' => true,
             'length' => '4',
             ));
        $this->hasColumn('tagId', 'integer', 4, array(
             'type' => 'integer',
             'primary' => true,
             'length' => '4',
             ));


        $this->index('fk_Post_has_Tag_Tag1', array(
             'fields' => 
             array(
              0 => 'tagId',
             ),
             ));
        $this->option('collate', 'utf8_general_ci');
        $this->option('charset', 'utf8');
        $this->option('type', 'InnoDB');
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('Post', array(
             'local' => 'postId',
             'foreign' => 'id'));

        $this->hasOne('Tag', array(
             'local' => 'tagId',
             'foreign' => 'id'));
    }
}