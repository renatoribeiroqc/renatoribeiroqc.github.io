<?php

namespace Areas\User;

use Areas\Pessoa\Client;
use Areas\Pessoa\Professional;
use Areas\Roles\Roles;
use Lib\Framework\Dataprovider\Table;
use Lib\Framework\Helpers\Html;
use DateTime;
use Lib\Framework\Core\Postman;
use PDO;

class User extends Table
{
    const TABLENAME = 'users';
    const LEVEL = '1';
    const UID_LEN = 250;
    public $detailedError;

    public function __construct()
    {
        parent::__construct(CONNECTION_STRING, 'users');
        $helper = new Html();
        $this->detailedError = array(
            $helper->text(array('label' => 'AUTH_MESSAGE_100')),
            $helper->text(array('label' => 'AUTH_MESSAGE_101')),
            $helper->text(array('label' => 'AUTH_MESSAGE_102')),
            $helper->text(array('label' => 'AUTH_MESSAGE_103')),
            $helper->text(array('label' => 'AUTH_MESSAGE_104')),
            $helper->text(array('label' => 'AUTH_MESSAGE_105')),
            $helper->text(array('label' => 'AUTH_MESSAGE_107')),
            $helper->text(array('label' => 'AUTH_MESSAGE_108')),
            $helper->text(array('label' => 'AUTH_MESSAGE_109')),
            $helper->text(array('label' => 'AUTH_MESSAGE_110')),
            $helper->text(array('label' => 'AUTH_MESSAGE_111')),
            $helper->text(array('label' => 'AUTH_MESSAGE_112')),
            $helper->text(array('label' => 'AUTH_MESSAGE_115'))
        );
    }

    public function createUser($userEntity) {
        $id = $this->Add($userEntity);

        if ($id > -1) {
            return $id;
        }

        return false;
    }

    // public function createAccount($user, $type = null, $profile = null, $isAdmin = false) {
    //     $oRoles = new Roles(CONNECTION_STRING);
    //     $oClient = new Client(CONNECTION_STRING);

    //     $personEntity = $oClient->newEntity($profile);

    //     $return = [
    //         'id' => '',
    //         'error' => ''
    //     ];

    //     $isValid = $this->validateUserFields($personEntity, $isAdmin);

    //     if ($isValid !== true) {
    //         $return['error'] = $isValid;
    //         return $return;
    //     }

    //     $user->role_id->Value = $oRoles->getRoleIdByUserType($type);
    //     $user->activationtoken->Value = substr($this->generateHash(), 0, 250);
    //     $user->status->Value = isset($profile['pes_ativo']) ? $profile['pes_ativo'] : 0;
    //     $user->pwd->Value = password_hash($user->pwd->Value, PASSWORD_BCRYPT);

    //     $id = $this->createUser($user);

    //     if ($id) {
    //         $profileId = $this->createProfile($user, $id, $type, $profile);
    //         $return['id'] = $profileId;

    //         //Data usage consent // Reativar depois
    //         // $oLgpd = new Lgpd(CONNECTION_STRING);
    //         // $entityLgpd = $oLgpd->Entity();
    //         // $entityLgpd->idpessoa->Value = $Id;
    //         // $oLgpd->Add($entityLgpd);
    //     }
    
    //     return $return;
    // }

    public function getPermissions($userId) {
        $roleId = $this->Find(array('id = ' . $userId))->fetch(PDO::FETCH_ASSOC)['role_id'];

        if ($roleId) {
            $sql = "select classe, metodo from role_permissions inner join permissions on role_permissions.permission_id = permissions.id where role_permissions.role_id = '$roleId' and role_permissions.enabled = 1";
            $permissions = $this->execQuery($sql)->fetchAll(PDO::FETCH_ASSOC);
            return $permissions;
        }

        return false;
    }

    public function findAccountByEmail($email) {
        $user = $this->Find(array("email = '$email' "))->fetch(PDO::FETCH_ASSOC);

        if ($user !== false) {
            return $user;
        }

        return False;
    }

    public function findAccountByHash($hash)
    {
        $ds = $this->Find(array("activationtoken = '$hash' "))->fetch(PDO::FETCH_ASSOC);

        if ($ds !== false) {
            return $ds;
        }

        return False;
    }

    public function userIsAuthorized($controllerName, $method) {
        $userInfo = unserialize($_SESSION['user']);
        $activationToken = $userInfo['i'];
        $user = $this->findAccountByHash($activationToken);

        if ($user) {
            $userId = $user['id'];
            $permissions = $this->getPermissions($userId);

            if ($permissions) {
                foreach ($permissions as $permission) {
                    if (strtolower($controllerName) == strtolower($permission['classe']) && strtolower($method) == strtolower($permission['metodo'])) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    public function loginUser($email, $password) {
        //0. Are there any data?
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->detailedError[0];
        }
        if (is_null($password) || empty($password) || $password == '') {
            return $this->detailedError[1];
        }
        //1. Email exists?
        $userInfo = $this->findAccountByEmail($email);
        if ($userInfo) {
            //2. Account active ?
            if ($userInfo['status'] == 1) {
                //3. Password match ?      
                if (password_verify($password, $userInfo['pwd'])) {
                    $this->chargeUserProfile($userInfo);
                    return TRUE;
                } else {
                    // $this->hasToBeLocked($email);
                    return 'invalid password';
                }
            } else {
               return 'user inactive';
            }
        } else {
            return "user doesn't exist";
        }
    }

    public function createProfile($user, $userId, $type = null, $profileDTO = null)
    {
        if (is_null($type) || $type == 2) {
            $oProfile = new Client($this->connectionString);
            $pes_tipo = $oProfile::CLIENT;
        }  else if ($type == 3) {
            // $oProfile = new Consultor(CONNECTION_STRING);
            // $pes_tipo = $oProfile::CONSULTOR;
        } else {
            $oProfile = new Professional($this->connectionString);
            $pes_tipo = $oProfile::PROFESSIONAL;
        }
 
        $entProfile = $oProfile->Entity();
 
        if (!is_null($profileDTO)) {
            $entProfile = $oProfile->newEntity((array) $profileDTO);
            $entProfile->pes_permission_granted_data_usage->Value = 'true';
        }
    
        $entProfile->pes_tipo->Value = $pes_tipo;
        $entProfile->pes_idusuario->Value = $userId;
        $entProfile->pes_login->Value = $user->email->Value;
        $entProfile->pes_pwd->Value = $user->pwd->Value;
        $entProfile->pes_ativo->Value = $user->status->Value;
        $entProfile->pes_uidativacao->Value = $user->activationtoken->Value;
        $entProfile->pes_nivel->Value = $oProfile::LEVEL;
        $entProfile->pes_brasil->Value = isset($profileDTO->pes_brasil) ? $profileDTO->pes_brasil : null;
        $entProfile->pes_ind_cons_bonus->Value = 0;
        $profileId = $oProfile->Add($entProfile);
    
        // Ativar de volta quando for pra produção
        // if (MODE == 'live') {
        //     if ($pes_tipo === '2') {
        //         $contactNitroNews = new Nitronewssync();
        //         $contactNitroNews->syncContactsWithAgendaAndNitro($profileId);
        //     }
        // }
        return $profileId;
    }

    private function chargeUserProfile($user) 
    {
        $oProfile = new Table($this->connectionString, 'pessoa');
        $dsProfile = $oProfile->Find(array('pes_idusuario = ' . $user['id']))->fetch(PDO::FETCH_ASSOC);
        $colaborador = $this->isUserColaborator($user);
 
        $userProfile = array(
            'i' => $user['activationtoken'],   // id
            'l' => $dsProfile['pes_nivel'],    // level 
            't' => $dsProfile['pes_tipo']      // tipo
        );
 
        if ($colaborador !== false) {
            $userProfile['ct'] = $colaborador->tipo;        // 1. Colaborador, 2. Gestor
            $userProfile['cs'] = $colaborador->status;      // 1. Ativo, 2. Inativo
            $userProfile['ce'] = $colaborador->empresa_id;  // Código da empresa para descontar as consultas pré-pagas
            $userProfile['cid'] = $colaborador->id;         // Código do colaborador
        }
    
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['user'] = serialize($userProfile);
    }

    private function isUserColaborator($user) {
        $sql = "select colaborador.* from users, pessoa, colaborador where 
        users.id = pessoa.pes_idusuario and colaborador.status = 1 and colaborador.pessoa_idpessoa = pessoa.idpessoa and pessoa.pes_idusuario = " . $user['id'];
        $query = new Table($this->connectionString, 'pessoa');
        return $query->execQuery($sql)->fetchObject();
    }

    public function validateUserFields($entity, $isAdmin = false, $isUpdate = false)
    {
        if (!$isUpdate) {
            $emailExists = $this->Find(array("email = '" . $entity->pes_login . "'"))->fetch(PDO::FETCH_ASSOC);
            if ($emailExists !== FALSE) {
                $return = $this->detailedError[5];
                return $return;
            }
        
            $emailIsValid = $this->validateEmailAddress($entity->pes_login);
            if (!$emailIsValid) {
                $return = $this->detailedError[0];
                return $return;
            }

            // Campos que só são validados se o usuário não for admin
            if (!$isAdmin) {
                $passwordIsValid = $this->validatePassword($entity->pes_pwd);
                if (!$passwordIsValid) {
                    $return = $this->detailedError[1];
                    return $return;
                }

                if (isset($_POST['g-recaptcha-response'])) {
                    $recaptchaIsValid = $this->validateRecaptcha($_POST['g-recaptcha-response']);
                    if (!$recaptchaIsValid) {
                    $return = $this->detailedError[10];
                    return $return;
                    }
                }

                if (!isset($_POST['pes_permission_granted_data_usage'])) {
                    $return = $this->detailedError[11];
                    return $return;
                }
            }
        }

        $emailAccountIsValid = $this->validateEmailAccount($entity->pes_login);
        if (!$emailAccountIsValid) {
            $return = $this->detailedError[0];
            return $return;
        }
        
        if (isset($entity->pes_cpf) && $entity->pes_cpf !== "") {
            $cpfIsValid = $this->validateCPF($entity->pes_cpf);
            if (!$cpfIsValid) {
                $return = $this->detailedError[12];
                return $return;
            }
        }

        if (isset($entity->pes_datanasc)) {
            $ageIsValid = $this->validateBirthDate($entity->pes_datanasc);
            if (!$ageIsValid) {
                $return = $this->detailedError[7];
                return $return;
            }
        }

        if (isset($entity->pes_nome)) {
            $nameIsValid = $this->validateFullName($entity->pes_nome);
            if (!$nameIsValid) {
                $return = $this->detailedError[8];
                return $return;
            }
        }

        if (isset($entity->pes_telefone1)) {
            $whatsappIsValid = $this->validatePhoneNumber($entity->pes_telefone1);
            if (!$whatsappIsValid) {
                $return = $this->detailedError[9];
                return $return;
            }
        }

        return true;
    }

    public function validateEmailAddress($email)
    {
        // Check if email address is valid format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        // Extract domain from email address
        $domain = explode('@', $email)[1];

        // if (MODE == 'live') {
        //     // Check if domain has DNS MX records
        //     if (!checkdnsrr($domain, 'MX')) {
        //         return false;
        //     }
        // }

        return true;
    }

    public function validateEmailAccount($email)
    {
        if (MODE == 'live') {
            // Extract domain from email address
            $domain = explode('@', $email)[1];

            // Get MX records for the domain
            $mxRecords = [];
            if (getmxrr($domain, $mxRecords)) {
                // Connect to the first MX server
                $mxServer = $mxRecords[0];
                $socket = @fsockopen($mxServer, 25, $errno, $errstr, 5);

                if ($socket) {
                    $sender = _EMAIL_Username;
                    $hostName = _EMAIL_HOST;
                    // Read the server response
                    $response = fgets($socket);

                    // Send the HELO command
                    fputs($socket, "HELO $hostName\r\n");
                    $response = fgets($socket);
    
                    // Send the MAIL FROM command
                    fputs($socket, "MAIL FROM: <$sender>\r\n");
                    $response = fgets($socket);
    
                    // Send the RCPT TO command with the email to be verified
                    fputs($socket, "RCPT TO: <$email>\r\n");
                    $response = fgets($socket);
    
                    // Close the socket connection
                    fputs($socket, "QUIT\r\n");
                    fclose($socket);

                    // Check if the email account exists (250 response code)
                    if (strpos($response, '250') === 0) {
                        return true;
                    }
                }
            }

            return false;
        } else {
            return true;
        }
    }

    function validatePassword($password)
    {
        // Define the OWASP password requirements
        $minimumLength = 8; // Minimum password length
        $maximumLength = 64; // Maximum password length
        $requireUppercase = true; // Require at least one uppercase letter
        $requireLowercase = true; // Require at least one lowercase letter
        $requireNumeric = true; // Require at least one numeric digit
        $requireSpecialChar = true; // Require at least one special character

        // Validate password length
        $passwordLength = strlen($password);
        if ($passwordLength < $minimumLength || $passwordLength > $maximumLength) {
            return false;
        }

        // Validate password composition
        if ($requireUppercase && !preg_match('/[A-Z]/', $password)) {
            return false;
        }

        if ($requireLowercase && !preg_match('/[a-z]/', $password)) {
            return false;
        }

        if ($requireNumeric && !preg_match('/\d/', $password)) {
            return false;
        }

        if ($requireSpecialChar && !preg_match('/[^a-zA-Z0-9]/', $password)) {
            return false;
        }

        // Password meets all the requirements
        return true;
    }

    public function validateCPF($cpf)
    {
        $cpf = preg_replace('/[^0-9]/', '', $cpf); // Remove non-digit characters

        // CPF must not be blank and have 11 digits
        if (empty($cpf) || strlen($cpf) !== 11) {
            return false;
        }

        // Check for sequences with all numbers equal
        if (preg_match('/^(\d)\1{10}$/', $cpf)) {
            return false;
        }

        // Validate CPF using Brazilian CPF number generation rules
        $sum = 0;
        for ($i = 0; $i < 9; $i++) {
            $sum += intval($cpf[$i]) * (10 - $i);
        }

        $remainder = $sum % 11;
        $digit1 = ($remainder < 2) ? 0 : 11 - $remainder;

        if (intval($cpf[9]) !== $digit1) {
            return false;
        }

        $sum = 0;
        for ($i = 0; $i < 10; $i++) {
            $sum += intval($cpf[$i]) * (11 - $i);
        }

        $remainder = $sum % 11;
        $digit2 = ($remainder < 2) ? 0 : 11 - $remainder;

        if (intval($cpf[10]) !== $digit2) {
            return false;
        }

        // Validation passed, CPF is valid
        return true;
    }

    public function validateFullName($fullName)
    {
        // Expressão regular para validar nome completo
        $pattern = "/^[a-zA-Z]+([\s-]?[a-zA-Z]+)+$/";
        return preg_match($pattern, $fullName);
        // return true;
    }

    public function validatePhoneNumber($phoneNumber)
    {
       // Expressão regular para validar número de telefone
       $pattern = "/^(\+(\d{1,4})\s?)?(\()?(\d{1,4})(?(3)\))([-.\s]?)(\d{3,4})([-.\s]?)(\d{2,4})$/";
       return preg_match($pattern, $phoneNumber);
    }

    public function validateBirthDate($birthdate)
    {
        // Validate date format
        $date = DateTime::createFromFormat('Y-m-d', $birthdate);
        if (!$date || $date->format('Y-m-d') !== $birthdate) {
            return false; // Invalid date format
        }

        // Calculate age based on the provided birth date
        $today = new DateTime();
        $age = $today->diff($date)->y;

        // Validate age range
        if ($age < 18 || $age > 115) {
            return false; // Age is outside the valid range
        }

        return true;
    }

    function validateRecaptcha($recaptchaResponse)
    {
        $recaptchaSecret = GOOGLE_SECRET_KEY;

        $response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$recaptchaSecret&response=$recaptchaResponse");
        $responseKeys = json_decode($response, true);

        if (intval($responseKeys["success"]) !== 1) {
            return false;
        } else {
            return true;
        }
    }
}
