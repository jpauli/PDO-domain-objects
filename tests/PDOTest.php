<?php
use JPDO\Statement;
use JPDO\Test\TestHelper;

class PDOTest extends TestHelper
{
    public function testStatementClassShared()
    {
        $this->assertEquals([Statement::class], $this->_pdo->getAttribute(\PDO::ATTR_STATEMENT_CLASS));
        $this->assertSame($this->_pdo, Statement::getPDOInstance());
    }

    public function testThrowsException()
    {
        $this->assertEquals(\PDO::ERRMODE_EXCEPTION, $this->_pdo->getAttribute(\PDO::ATTR_ERRMODE));
    }
}