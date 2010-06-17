<?php

use JPDO\Test\TestHelper;
use JPDO\Test\Messages;

class ObjectsTest extends TestHelper
{
    protected $_stmt;
    protected $_object;

    public function setup() : void
    {
        parent::setup();
        $this->_stmt   = $this->_pdo->query("SELECT * FROM messages");
        $this->_object = $this->_stmt->fetchObjectOfClass(Messages::class);
    }

    public function testPublicMembersPopulatedByStatement()
    {
        $this->assertObjectHasAttribute('id', $this->_object);
        $this->assertObjectHasAttribute('message', $this->_object);
    }

    public function testTablenameShared()
    {
        $this->assertEquals("messages", $this->_object->getTableName());
    }
    
    public function testFetchingPublicMembersPopulatedByStatement()
    {
        $this->assertArrayHasKey('id', $this->_object->fetchPublicMembers());
        $this->assertArrayHasKey('message', $this->_object->fetchPublicMembers());
    }
    
    public function testStringification()
    {
        $this->assertStringContainsString('- foo', (string)$this->_object);
    }
}