<?php
/* Copyright (C) conexperience.com.br, Inc - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * Written by Renato Ribeiro <renato.ribeiro@conexperience.com.br>, June 2023
 */
namespace Lib\Framework\Core;
/**
 * Manages the Internationalization and multi-language 
 *
 * @author renato
 */
class ResourceManager {
   public static $culture = LANG;
   public static function TextFor($keyField){
      $lang = self::$culture;
		$fileName = "resource_$lang.ini";
      $fileWithPath = RESOURCEPATH . $fileName;      
      if(file_exists($fileWithPath)){      
         
         $resourceFile = parse_ini_file($fileWithPath, false, INI_SCANNER_RAW);
         if(array_key_exists($keyField, $resourceFile)){
            return $resourceFile[$keyField];  
         }else{
            return $keyField;
         }
      }else{
         return $keyField;
      }		
   }
}