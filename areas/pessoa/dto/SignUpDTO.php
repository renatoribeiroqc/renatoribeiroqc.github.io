<?php

namespace Areas\Pessoa\Dto;

use Exception;
use Lib\Framework\Dataprovider\AbstractDTO;

class SignUpDTO extends AbstractDTO
{
    public string $pes_nome;
    public string $pes_login;
    public string $pes_pwd;
    public string $pes_telefone1;
    public string $pes_datanasc;
    public int $pes_brasil;
    public string $pes_pais;

    public function __construct($pes_nome, $pes_login, $pes_pwd, $pes_telefone1, $pes_brasil, $pes_datanasc, $pes_pais) {
        parent::__construct([
            'pes_nome' => $pes_nome,
            'pes_login' => $pes_login,
            'pes_pwd' => $pes_pwd,
            'pes_telefone1' => $pes_telefone1,
            'pes_datanasc' => $pes_datanasc,
            'pes_brasil' => $pes_brasil,
            'pes_pais' => $pes_pais
        ]);
    }

    public static function fromClient($data)
    {
        try {
            $client = new self(
                $data['pes_nome'] ?? null,
                $data['pes_login'] ?? null,
                $data['pes_pwd'] ?? null,
                $data['pes_telefone1'] ?? null,
                $data['pes_brasil'] ?? 0,
                $data['pes_datanasc'],
                $data['pes_pais']
            );
        } catch (Exception $e) {
            return ("Erro ao criar ClientDTO: " . $e->getMessage());
        }

        return $client;
    }
}