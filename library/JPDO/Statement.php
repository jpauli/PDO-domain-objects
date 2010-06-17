<?php
/**
* PDO-domain-objects
*
* Copyright (c) 2010, Julien Pauli <jpauli@php.net>.
* All rights reserved.
*
* Redistribution and use in source and binary forms, with or without
* modification, are permitted provided that the following conditions
* are met:
*
* * Redistributions of source code must retain the above copyright
* notice, this list of conditions and the following disclaimer.
*
* * Redistributions in binary form must reproduce the above copyright
* notice, this list of conditions and the following disclaimer in
* the documentation and/or other materials provided with the
* distribution.
*
* * Neither the name of Julien Pauli nor the names of his
* contributors may be used to endorse or promote products derived
* from this software without specific prior written permission.
*
* THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
* "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
* LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
* FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
* COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
* INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
* BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
* LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
* CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
* LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
* ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
* POSSIBILITY OF SUCH DAMAGE.
*
* @package JPDO
* @author Julien Pauli <jpauli@php.net>
* @copyright 2010 Julien Pauli <jpauli@php.net>
* @license http://www.opensource.org/licenses/bsd-license.php BSD License
*/

namespace JPDO;

use JPDO\Result\Objects;

/**
* Statement class
*
* This class extends PDOStatement to add some code to
* fetch objects easily from a database
*
* @package JPDO
* @author Julien Pauli <jpauli@php.net>
* @copyright 2010 Julien Pauli <jpauli@php.net>
* @license http://www.opensource.org/licenses/bsd-license.php BSD License
* @version Release: @package_version@
*/
final class Statement extends \PDOStatement
{
    /**
     * PDO instance, passed to classes allowed by
     * fetchObjectOfClass() and fetchAllObjectOfClass()
     *
     * @var PDO
     */
    private static $pdo;

    /**
     * \PDOStatement doesn't allow a public constructor.
     * However, we need a way to pass the PDO instance.
     *
     * @param PDO $pdo
     */
    public static function setPDOInstance(PDO $pdo)
    {
        self::$pdo = $pdo;
    }

    /**
     * Retrieves the PDO instance
     *
     * @return PDO
     */
    public static function getPDOInstance() : PDO
    {
        return self::$pdo;
    }

    /**
     * Internal stuff to check for class validity and
     * discovering of table name
     *
     * @param string $className
     * @throws \Exception
     * @return array
     */
    private function _prepareFetchObject(string $className) : string
    {
        if (!preg_match("/.*FROM\s(.*)[\s|;]?/i", $this->queryString, $table)) {
            throw new \InvalidArgumentException("Could not find table name in query");
        }

        if (!class_exists($className, true)) {
            throw new \InvalidArgumentException("Class $className does not exist");
        }

        if (!is_subclass_of($className, Objects::class)) {
            throw new \InvalidArgumentException("Class $className should extend " . Objects::class);
        }

        return $table[1];
    }

    /**
     * Fetch a result as an object of a class extending
     * JPDO\Result\Objects. Those classes should allow their objects
     * to be saved back to the DB.
     *
     * @param string $className
     * @return Result\Objects
     */
    public function fetchObjectOfClass(string $className)
    {
        $table    = $this->_prepareFetchObject($className);
        $instance = new $className(self::$pdo, $table);
        $this->setFetchMode(\PDO::FETCH_INTO, $instance);

        return parent::fetch(\PDO::FETCH_INTO);
    }

    /**
     * Fetch a result as an object of a class extending
     * JPDO\Result\Objects. Those classes should allow their objects
     * to be saved back to the DB.
     *
     * @param string $className
     * @return array
     */
    public function fetchAllObjectOfClass($className) : array
    {
        $table = $this->_prepareFetchObject($className);

        return parent::fetchAll(\PDO::FETCH_CLASS, $className, [self::$pdo, $table[1]]);
    }

    /**
    * Interceptor for
    * # fetchAll<Classname>()
    * # fetchOne<Classname>()
    *
    * @param string $method
    * @param array $args
    * @return mixed
    * @throws \Exception
    */
    public function __call($method, $args)
    {
        if (preg_match("/^fetchAll(\w+)$/", $method, $matches)) {
            return $this->fetchAllObjectOfClass($matches[1]);
        } elseif (preg_match("/^fetchOne(\w+)$/", $method, $matches)) {
            return $this->fetchObjectOfClass($matches[1]);
        }
        throw new \BadMethodCallException("Call to undefined method $matches[1]");
    }
}
