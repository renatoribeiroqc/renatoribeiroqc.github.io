<?php
/* 
 * Copyright (C) conexperience.com.br, Inc - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * Written by Renato Ribeiro <renato.ribeiro@conexperience.com.br>, December 2023
 */

namespace Lib\Framework\Core;

/**
 * Description of View
 *
 * @author renato
 */
class View {
   protected $partial = null;
   protected $master = null;
   public $viewBag;
   
   public function __construct($partial = null){
      if (!is_null($partial)){
         $this->partial = $partial;
      }
   }

   public function master($master){
      if (is_null($master)){
         return $this->master;
      }
      $this->master = $master;
   }

   public function partial($partial = null){
      if (is_null($partial)){
         return $this->partial;
      }
      $this->partial = $partial;
   }
   
   public function render($viewBag){
      $this->viewBag = $viewBag;
      include($this->master);
   }
}
