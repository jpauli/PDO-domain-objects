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

/**
* Results class
*
* This class is meant to receive DB results as
* "active record" objects. It implements a simple
* save() method.
*
* @package JPDO
* @subpackage Result
* @author Julien Pauli <jpauli@php.net>
* @copyright 2010 Julien Pauli <jpauli@php.net>
* @license http://www.opensource.org/licenses/bsd-license.php BSD License
* @version Release: @package_version@
*/
class SavableObjects extends Objects
{
    /**
     * This method saves the object
     * in a simple maner
     * Override with your implementation
     *
     * @return bool|int
     * @throws Exception
     */
    public function save() : void
    {
        if (self::$pk == null || !property_exists($this, self::$pk)) {
            throw new \InvalidArgumentException("Primary key must exist before saving");
        }
        foreach ($this->fetchPublicMembers() as $col => $val) {
            $set[] = sprintf("%s=%s", $col, $this->pdo->quote($val));
        }
        $query = sprintf("UPDATE %s SET %s WHERE %s = %s",
                         $this->tableName,
                         implode(',', $set),
                         self::$pk,
                         $this->{self::$pk});
        try {
            $this->pdo->exec($query);
        } catch (\PDOException $e) {
            throw new Exception("Error occured while saving your object", null, $e);
        }
    }
}