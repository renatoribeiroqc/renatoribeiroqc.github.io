<?php
namespace Areas\Pessoa;

use Lib\Framework\Dataprovider\Table;
use PDO;

class Pessoa extends Table
{
    const TABLENAME = 'pessoa';

    public function __construct()
    {
        parent::__construct(CONNECTION_STRING, 'pessoa');
    }

    public function getProfileByHash($userHash)
    {
        $profile = $this->Find(array("pes_uidativacao = '$userHash'"))->fetch(PDO::FETCH_ASSOC);
        return $profile;
    }
}