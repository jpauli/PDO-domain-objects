<?php

use JPDO\Test\TestHelper;
use JPDO\Test\Messages;

class SavableObjectsTest extends TestHelper
{
    protected $_stmt;
    protected $_object;

    public function setup() : void
    {
        parent::setup();
        $this->_stmt   = $this->_pdo->query("SELECT * FROM messages");
        $this->_object = $this->_stmt->fetchObjectOfClass(Messages::class);
    }

    public function testSave()
    {
        Messages::setPk('id');
        $this->_object->message .= 'saved';
        $this->_object->save();
        $this->assertDataSetsEqual(
           $this->createFlatXMLDataSet(DATA_PATH . '/save.xml'),
           $this());
    }
}