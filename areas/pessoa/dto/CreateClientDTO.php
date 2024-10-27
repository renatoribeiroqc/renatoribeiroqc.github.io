<?php

namespace Areas\Pessoa\Dto;

use Exception;
use Lib\Framework\Dataprovider\AbstractDTO;

class CreateClientDTO extends AbstractDTO
{
    public string $pes_nome;
    public string $pes_login;
    public ?string $pes_pwd;
    public string $pes_telefone1;
    public string $pes_datanasc;
    public int $pes_brasil;

    public function __construct($pes_nome, $pes_login, $pes_pwd, $pes_telefone1, $pes_brasil, $pes_datanasc) {
        parent::__construct([
            'pes_nome' => $pes_nome,
            'pes_login' => $pes_login,
            'pes_pwd' => $pes_pwd,
            'pes_telefone1' => $pes_telefone1,
            'pes_datanasc' => $pes_datanasc,
            'pes_brasil' => $pes_brasil
        ]);
    }

    public static function fromClient($data, $signUp = false)
    {
        try {
            $client = new self(
                $data['pes_nome'] ?? null,
                $data['pes_login'] ?? null,
                $data['pes_pwd'] ?? null,
                $data['pes_telefone1'] ?? null,
                $data['pes_brasil'] ?? 0,
                $data['pes_datanasc']
            );
        } catch (Exception $e) {
            return ("Erro ao criar ClientDTO: " . $e->getMessage());
        }

        if ($signUp) {
            $validatePwd = self::validatePwd($client);

            if (!$validatePwd) {
                return "password is not defined";
            }
        }

        return $client;
    }

    public static function validatePwd($client)
    {
        return isset($client->pes_pwd);
    }
}