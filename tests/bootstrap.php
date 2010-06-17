<?php
namespace JPDO\Test;

use PHPUnit\Framework\TestCase;
use PHPUnit\DbUnit\TestCaseTrait;
use JPDO\Result\SavableObjects;
use JPDO\PDO;

require __DIR__ . '/../vendor/autoload.php';

abstract class TestHelper extends TestCase
{
    use TestCaseTrait;
    
    /**
     * @var PDO
     */
    protected $_pdo;

    public function getConnection()
    {
        $this->_pdo = new PDO('sqlite::memory:');
        $this->_pdo->exec("CREATE table messages (id integer, message text);");
        return $this->createDefaultDBConnection($this->_pdo, ':memory:');
    }

    public function getDataSet()
    {
        return $this->createFlatXmlDataset(DATA_PATH . '/dataset.xml');
    }

    public function __invoke()
    {
        return $this->getConnection()->createDataSet();
    }

    /*protected function setup()
    {
        $this->_pdo = new PDO('sqlite::memory:');
        $this->_pdo->exec("CREATE table messages (id integer, message text);");
        parent::setup();
    }*/
}

class Messages extends SavableObjects { }
\class_alias(Messages::class, 'Messages');