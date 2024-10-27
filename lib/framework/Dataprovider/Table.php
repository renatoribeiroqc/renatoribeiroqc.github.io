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
use Application\Log\Log;

use Exception;
use PDO;

class Table extends Dataprovider
{

   public $tableName;

   public function __construct($connectionString, $tableName = null)
   {
      parent::__construct($connectionString);
      if (!is_null($tableName)) {
         $this->tableName = $tableName;
         $this->connectionString = $connectionString;
      }
   }

   /**
    * Generates the SQL Statement for a given table 
    * and statement type ([S]elect, [I]nsert, [U]pdate, [D]elete)
    * @param string $tableName Table Name
    * @param string $type Type of SQL statement desired
    * @return string $sql String of SQL statement
    */
   public function SQL($type, $entity = null)
   {
      $strSQL = '';
      $fields = '';
      $values = '';
      $KeyField = '';
      if (is_null($entity)) {
         $dataset = $this->tableStructure();
         while ($row = $dataset->fetch(PDO::FETCH_ASSOC)) {
            if ($type == 'S' || $type == 'I') {
               $fields .= $row['Field'] . ' ,';
               $values .= ':' . $row['Field'] . ',';
            } elseif ($type == 'D' || $type == 'U') {
               if ($row['Key'] == 'PRI') {
                  if ($KeyField == '')
                     $KeyField .= $row['Field'] . ' = :' . $row['Field'];
                  else
                     $KeyField .= ' AND ' . $row['Field'] . ' = :' . $row['Field'];
               } else {
                  $fields .= $row['Field'] . ' = :' . $row['Field'] . ',';
               }
            }
         }
      } else {
         $properties = get_object_vars($entity);
         foreach ($properties as $key => $property) {

            if (isset($property->Value) && !$this->IsNullOrEmpty($property->Value)) {
               if ($type == 'S' || $type == 'I') {
                  $fields .= $key . ' ,';
                  $values .= ':' . $key . ',';
               } elseif ($type == 'D' || $type == 'U') {
                  if ($property->PK == 'PRI') {
                     if ($KeyField == '')
                        $KeyField .= $key . ' = :' . $key;
                     else
                        $KeyField .= ' AND ' . $key . ' = :' . $key;
                  } else {
                     $fields .= $key . ' = :' . $key . ',';
                  }
               }
            }
         }
      }

      $fields = substr($fields, 0, strlen($fields) - 1);
      $values = substr($values, 0, strlen($values) - 1);

      switch ($type) {
         case 'S':
            $strSQL = "SELECT $fields FROM $this->tableName ";
            break;
         case 'I':
            $strSQL = "INSERT INTO $this->tableName ($fields ) VALUES ( $values )";
            break;
         case 'U':
            $strSQL = "UPDATE $this->tableName SET $fields WHERE $KeyField";
            break;
         case 'D':
            $strSQL = "DELETE FROM $this->tableName WHERE $KeyField";
            break;
      }
      return $strSQL;
   }

   /**
    * Executes a query that returns a dataset;
    * @param string $strSQL
    * @return object dataset Filled dataset
    */
   public function execQuery($strSQL)
   {
      return $this->connection()->query($strSQL);
   }

   /**
    * Executes a query that return affected rows count 
    * @param string $strSQL
    * @return integer $rowCount Affected rows Count
    */
   public function execNonQuery($strSQL, $entity)
   {
      $stmt = $this->connection()->prepare($strSQL);
      $properties = get_object_vars($entity);
      foreach ($properties as $key => &$property) {
         if (isset($property->Value) && !$this->IsNullOrEmpty($property->Value)) {
            $this->bind($stmt, $key, $property->Value);
         }
      }
      try {
         $stmt->execute();
      } catch (Exception $ex) {
         print_r($ex);
      }

      $dataset = $stmt->rowCount();
      $stmt->closeCursor();
      return $dataset;
   }

   /**
    * Get all columns of the given table and return it as key/value array
    * @return  object $entity
    */
   public function Entity()
   {
      $entity = new \stdClass();
      $query = $this->tableStructure($this->tableName);
      $entity->entityName = $this->tableName;
      while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
         $type = explode('(', $row['Type']);
         $dataType = isset($type[0]) ? $type[0] : null;
         $size = count($type) == 2 ? str_replace(')', '', $type[1]) : 0;

         $entity->{$row['Field']} = (object)
         array(
            'Value'     => null,
            'Nullable'  => $row['Null'],
            'PK'        => $row['Key'],
            'Size'      => $size,
            'DataType'  => $dataType
         );
         $type = null;
      }
      return $entity;
   }

   public function AsObject()
   {
      $entity = new \stdClass();
      $query = $this->tableStructure($this->tableName);
      $entity->entityName = $this->tableName;
      while ($row = $query->fetch(PDO::FETCH_ASSOC)) {

         $entity->{$row['Field']} = '';
      }
      return $entity;
   }

   /**
    * Get the table structure from a given table name
    * @return PDOStatement $dataset Dataset with table structure
    */
   protected function tableStructure()
   {
      return $this->connection()->query("describe $this->tableName");
   }

   /**
    * Determines and applies custom bind
    * @param PDO Statement $stmt PDO Statement object
    * @param variant $parameter Parameter to be bind
    * @param variant $value Value to be bind inside the Parameter
    * @param variant $var_type Variable type to be bind
    */
   protected function bind($stmt, $parameter, $value, $var_type = null)
   {
      if (is_null($var_type)) {
         switch (true) {
            case is_bool($value):
               $var_type = PDO::PARAM_BOOL;
               break;

               case is_int($value):
               $var_type = PDO::PARAM_INT;
               break;
            case is_null($value):
               $var_type = PDO::PARAM_NULL;
               break;
            default:
               $var_type = PDO::PARAM_STR;
         }
      }
      $stmt->bindValue($parameter, $value, $var_type);
   }

   /**
    * Add a new record
    * @param object $entity
    * @return integer $lastInsertedId
    */
   public function Add($entity)
   {
      $entity->createDate->Value = date('Y-m-d H:i:s');
      $createUser = $this->getSessionUser();
      if ($createUser == ''){
         $createUser = $this->getVisitorIpAddress();
      }
      $entity->createUser->Value = $createUser;
      
      $insertSQL = $this->SQL('I', $entity);
      $rowCount = $this->execNonQuery($insertSQL, $entity);
      if ($rowCount > 0) {
         return $this->connection()->lastInsertId($this->tableName);
      }
      return -1;
   }

   /**
    * Update a record identified by its Id
    * @param object $entity
    * @return \PDOStatement $dataset
    */
   public function Update($entity)
   {
      // Inserido aqui para salvar o log do update
      $this->checkUpdate($entity);

      $entity->updateDate->Value = date('Y-m-d H:i:s');
      $entity->updateUser->Value = $this->getSessionUser();
      $updateSQL = $this->SQL('U', $entity);
      return $this->execNonQuery($updateSQL, $entity);
   }

   private function checkUpdate($entity) {
      $oTable = new Table($this->connectionString, $entity->entityName);
      $primaryKey = $this->getPrimaryKey($entity);

      if ($primaryKey) {
         $table = $oTable->Find(array($primaryKey['key'] . ' = ' . $entity->{$primaryKey['key']}->Value))->fetchObject();

         $logAtividade = 'Update';
         $logTabela = $entity->entityName;
         //$logTabelaColuna = $primaryKey['key'];
         $logTabelaId = $primaryKey['value'];
   
         foreach ($entity as $key => $value) {
            if (isset($value->Value)) {
               if ($table->{$key} != $value->Value) {
                  // $this->saveLog($logAtividade, $logTabela, $key, $logTabelaId, $table->{$key}, $value->Value);
               }
            }
         }
      }
   }

   private function saveLog($logAtividade, $logTabela, $logTabelaColuna, $logTabelaId, $logValorAntigo, $logValorNovo) {
      $oLog = new Log($this->connectionString);
      $log = $oLog->Entity();
      $log->log_data->Value = date('Y-m-d');
      $log->log_atividade->Value = $logAtividade;
      $log->log_tabela->Value = $logTabela;
      $log->log_tabela_coluna->Value = $logTabelaColuna;
      $log->log_tabela_id->Value = $logTabelaId;
      $log->log_valor_antigo->Value = $logValorAntigo;
      $log->log_valor_novo->Value = $logValorNovo;
      $oLog->Add($log);
   }

   private function getPrimaryKey($entity) {
      foreach ($entity as $key => $value) {
         if(isset($value->PK)) {
            if ($value->PK == 'PRI') {
               return array('key' => $key, 'value' => $value->Value);
            }
         }
      }
      
      return false;
   }

   /**
    * Delete a record identified by its Id
    * @param object $entity
    * @return \PDOStatement $dataset
    */
   public function Delete($entity)
   {
      $deleteSQL = $this->SQL('D', $entity);
      return $this->execNonQuery($deleteSQL, $entity);
   }

   /**
    * Return all records 
    * @return \PDOStatement $dataset
    */
   public function GetAll()
   {
      $selectSQL = $this->SQL('S');
      return $this->execQuery($selectSQL);
   }

   /**
    * Find a record according its where clause
    * @param string array $clause
    * @return \PDOStatement $dataset
    */
   public function Find($clause)
   {
      if (count($clause) == 1) {
         $where = $clause[count($clause) - 1];
      } else {
         $where = implode(' AND ', $clause);
      }
      $selectSQL = $this->SQL('S') . ' WHERE 1=1 AND ' . $where;
      return $this->execQuery($selectSQL);
   }

   /**
    * Returns if a variable is Null or Empty
    * @param variant $value Value to be checked
    * @return boolean
    */
   public function IsNullOrEmpty($value)
   {
      return (!isset($value) || is_null($value) || $value === '');
   }

   /**
    * Create and fill up an Entity with the Request Contents
    * @param array $request
    * @return object $entity
    */
   public function newEntity($request)
   {
      if (isset($request) && is_array($request)) {

         $dimensions = array_keys($request);

         $entity = $this->Entity();

         for ($ii = 0; $ii < count($dimensions); $ii++) {
            if (property_exists($entity, $dimensions[$ii])) {
               $entity->{$dimensions[$ii]}->Value = $request[$dimensions[$ii]];
            }
         }
         return $entity;
      }
      return null;
   }



   public function getSessionUser()
   {
      if (!isset($_SESSION)) {
//         session_start();
      }
      if (isset($_SESSION['user'])) {
         $userInfo = unserialize($_SESSION['user']);
         $query = new Table($this->connectionString, 'pessoa');
         $result = $query->Find(array("pes_uidativacao = '" . $userInfo['i'] . "'"));
         if ($result !== false) {
            $user = $result->fetchObject();
            return $user->pes_login;
         }
      }

      return '';
   }

   public function getVisitorIpAddress() {
      // Check for shared internet/ISP IP
      if (!empty($_SERVER['HTTP_CLIENT_IP']) && filter_var($_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP)) {
          return $_SERVER['HTTP_CLIENT_IP'];
      }
  
      // Check for IP address from a proxy
      if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
          $ipAddresses = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
          foreach ($ipAddresses as $ip) {
              if (filter_var($ip, FILTER_VALIDATE_IP)) {
                  return $ip;
              }
          }
      }
  
      // Check for remote IP address
      if (!empty($_SERVER['REMOTE_ADDR']) && filter_var($_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP)) {
          return $_SERVER['REMOTE_ADDR'];
      }
  
      // Return a fallback IP address
      return 'Unknown';
  }
}
