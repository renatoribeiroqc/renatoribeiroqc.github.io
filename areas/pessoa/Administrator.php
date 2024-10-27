<?php

namespace Areas\Pessoa;

class Administrator extends Pessoa
{
    const ADMINISTRATOR = '0';
    const LEVEL = '0';
    
    public function Find($clause)
    {
        $type = $this::ADMINISTRATOR;
        $level = $this::LEVEL;
        array_push($clause, " pes_tipo = $type ");
        array_push($clause, " pes_nivel = $level ");
        return parent::Find($clause);
    }
    
}