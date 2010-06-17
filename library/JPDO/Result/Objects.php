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
* @subpackage Result
* @author Julien Pauli <jpauli@php.net>
* @copyright 2010 Julien Pauli <jpauli@php.net>
* @license http://www.opensource.org/licenses/bsd-license.php BSD License
*/

namespace JPDO\Result;

use JPDO\PDO;

/**
* Results class
*
* This class is meant to receive DB results as
* "active record" objects
*
* @package JPDO
* @subpackage Result
* @author Julien Pauli <jpauli@php.net>
* @copyright 2010 Julien Pauli <jpauli@php.net>
* @license http://www.opensource.org/licenses/bsd-license.php BSD License
* @version Release: @package_version@
*/
abstract class Objects
{
    /**
     * PDO Instance
     *
     * @var \JPDO\PDO
     */
    protected $pdo;

    /**
     * table name
     *
     * @var string
     */
    protected $tableName;

    /**
     * primary key
     *
     * @var string
     */
    protected static $pk;

    /**
     * Public attributes for this object
     * used to save it back to DB
     *
     * @var array
     */
    private $props = [];

    /**
     * Constructor
     * To extend, extend _init()
     *
     * @param \JPDO\PDO $pdo
     * @param string $tableName
     */
    final public function __construct(PDO $pdo, string $tableName)
    {
        $this->pdo       = $pdo;
        $this->tableName = $tableName;
        $this->_init();
    }

    /**
     * Constructor for children
     *
     * @return void
     */
    protected function _init()
    {

    }

    /**
     * Retrieves the table name used in SQL queries
     *
     * @return string
     */
    public function getTableName() : string
    {
        return $this->tableName;
    }

    /**
     * Retrieves all public attributes
     *
     * @return array
     */
    final public function fetchPublicMembers() : array
    {
        if ($this->props) { return $this->props; }
        $reflect = new \ReflectionObject($this);
        foreach ($reflect->getProperties(\ReflectionProperty::IS_PUBLIC) as $var) {
            $this->props[$var->getName()] = $this->{$var->getName()};
        }

        return $this->props;
    }

    /**
     * Allow stringification
     *
     * @return string
     */
    public function __toString()
    {
        return implode(' - ', $this->fetchPublicMembers());
    }

    /**
     * Should set the table's PK
     *
     * @param string $pk The primary key
     * @return void
     */
    public static function setPk(string $pk)
    {
        static::$pk = $pk;
    }

    /**
     * Should allow saving the object
     */
    abstract public function save() : void;
}
