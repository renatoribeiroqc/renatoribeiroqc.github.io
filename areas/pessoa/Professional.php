<?php

namespace Areas\Pessoa;

class Professional extends Pessoa
{
    const PROFESSIONAL = '1';
    const LEVEL = '1';
    
    public function Find($clause)
    {
        $type = $this::PROFESSIONAL;
        $level = $this::LEVEL;
        array_push($clause, " pes_tipo = $type ");
        array_push($clause, " pes_nivel = $level ");
        return parent::Find($clause);
    }
}