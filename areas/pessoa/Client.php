<?php

namespace Areas\Pessoa;

class Client extends Pessoa
{
    const CLIENT = '2';
    const LEVEL = '1';
    
    public function Find($clause)
    {
        $type = $this::CLIENT;
        $level = $this::LEVEL;
        array_push($clause, " pes_tipo = $type ");
        array_push($clause, " pes_nivel = $level ");
        return parent::Find($clause);
    }
}
