<?php
/* Copyright (C) conexperience.com.br, Inc - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * Written by Renato Ribeiro <renato.ribeiro@conexperience.com.br>, June 2023
 */
namespace Lib\Framework\Dataprovider;

/**
 * Class responsible to connect the application to DB and perform all data
 * manipulation - SELECT, UPDATE, INSERT and DELETE*
 */

use Exception;
use PDO;

class Dataprovider {

   /**
    * Connection string array
    * @var string array $connectionString Array with connection parameters
    */
   public $connectionString;
   private $connection;

   /**
    * Class Constructor
    * @param string array $connectionString
    */
   public function __construct($connectionString) {
      if (!is_null($connectionString)) {
         if ($this->connection) {
            $this->connection = null;
            $dbh = null;
         }
         
         $dbh = new PDO(
            "mysql:host=" . $connectionString['host'] . ";"
            . "dbname=" . $connectionString['dbname'], 
            $connectionString['user'], 
            $connectionString['password'], 
            array(PDO::ATTR_PERSISTENT => true));
         
         if ($dbh) {
            $this->connection = $dbh;
         }
      }
   }

   public function connection() {
      return $this->connection;
   }

}
