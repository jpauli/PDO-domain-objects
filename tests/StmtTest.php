<?php

use JPDO\Test\TestHelper;
use JPDO\Test\Messages;

class StmtTest extends TestHelper
{
    protected $_stmt;

    public function setup() : void
    {
        parent::setup();
        $this->_stmt = $this->_pdo->query("SELECT * FROM messages");
    }

    public function testExceptionWhenQueryingNonexistantObject()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("not exist");
        $this->_stmt->fetchObjectOfClass('foo');
    }

    public function testExceptionWhenQueryingObjectOfTheWrongClass()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("should extend");
        $this->_stmt->fetchObjectOfClass(stdClass::class);
    }

    public function testQueryFetchesAnObject()
    {
        $message = $this->_stmt->fetchObjectOfClass(Messages::class);
        $this->assertInstanceOf(Messages::class, $message);
    }

    public function testQueryFetchesAllObjects()
    {
        $message = $this->_stmt->fetchAllObjectOfClass(Messages::class);
        $this->assertIsArray($message);
        $this->assertGreaterThanOrEqual(1, count($message));
        $this->assertIsObject($message[0]);
        $this->assertInstanceOf(Messages::class, $message[0]);
    }

    public function test__call()
    {
        $messages = $this->_stmt->fetchAllMessages();
        $message  = $this->_stmt->fetchOneMessages();
        $this->assertIsObject($message);
        $this->assertIsArray($messages);
    }
}