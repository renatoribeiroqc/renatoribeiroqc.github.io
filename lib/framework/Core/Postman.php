<?php
/* Copyright (C) conexperience.com.br, Inc - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * Written by Renato Ribeiro <renato.ribeiro@conexperience.com.br>, June 2023
 */

namespace Lib\Framework\Core;

use Application\Account\User;
use Application\Message\Message;
use PDO;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/**
 * Postman controls e-mail sending
 *
 * @author renato
 */
class Postman
{
   protected $emailHeaders;
   protected $emailClient;
   protected $emailProfessional;
   protected $emailSysAdmin;
   private $recipientRule;
   protected $mail;

   public function __construct()
   {
      $this->mail = new PHPMailer(TRUE);
      $this->initEmailSettings();
   }

   private function initEmailSettings()
   {
      $this->mail->isSMTP();
      $this->mail->Host = _EMAIL_HOST;
      $this->mail->SMTPAuth = _EMAIL_SMTPAuth;
      $this->mail->Username = _EMAIL_Username;
      $this->mail->Password = _EMAIL_Password;
      $this->mail->SMTPSecure = _EMAIL_SMTPSecure;
      $this->mail->Port = _EMAIL_Port;
   }

   private function getMessage($messageId)
   {
      $oSysMsg = new Message(CONNECTION_STRING);
      $dsMsg = $oSysMsg->Find(array("idmensagem = $messageId"))
         ->Fetch(PDO::FETCH_ASSOC);
      return array(
         'subject' => html_entity_decode($dsMsg['assunto']),
         'message' => $dsMsg['mensagem'],
         'ruleId'  => $dsMsg['regra']
      );
   }

   private function getPeopleToBeAdvised($ruleId)
   {
      $email_admin = $this->emailSysAdmin;
      $email_psicologo = $this->emailProfessional;
      $email_destinatario = $this->emailClient;

      $this->recipientRule = array(
         1 => array($email_destinatario),
         2 => array($email_psicologo),
         3 => array($email_destinatario, $email_psicologo),
         4 => array($email_admin),
         5 => array($email_admin, $email_destinatario),
         6 => array($email_admin, $email_psicologo),
         7 => array($email_admin, $email_psicologo, $email_destinatario)
      );

      return $this->recipientRule[$ruleId];
   }

   private function getDataFromRecipient($emailTo)
   {
      $oUSer = new User(CONNECTION_STRING);
      $dsUser = $oUSer->findAccountByEmail($emailTo);
      $this->emailClient = $dsUser['email'];
      $this->emailSysAdmin = _EMAIL_Username;
      return array(
         'userId' => $dsUser['id'],
         'hash'  => $dsUser['activationtoken'],
         'pwd'   => $dsUser['pwd']
      );
   }

   private function getActivationLink()
   {
      $http = 'https://';
      $rawServer = isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : BASE_SITE;
      $server = str_replace('https://', '', $rawServer);
      $uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : BASE_URI . 'Account/Account';

      $urlAccountActivation = dirname($http . $server. $uri);
      $baseUrl              = '/activate';
      return  "$urlAccountActivation$baseUrl/%s";
   }

   private function getUnlockAccountLink()
   {
      $http = 'https://';
      $urlAccountActivation = dirname($http . $_SERVER['SERVER_NAME'] . BASE_URI . '/Account');
      $baseUrl              = '/unlock';
      return  "$urlAccountActivation$baseUrl/%s";
   }

   private function getResetPasswordLink()
   {
      $http = 'https://';
      $urlAccountActivation = dirname($http . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI']);
      $baseUrl              = '/reset';
      return  "$urlAccountActivation$baseUrl/%s";
   }


   public function prepareMessageAndSend($emailTo, $messageId)
   {
      $messageData = $this->getMessage($messageId);
      $recipientData = $this->getDataFromRecipient($emailTo);

      $peopleToBeAdvised = $this->getPeopleToBeAdvised($messageData['ruleId']);
      $subject = $messageData['subject'];
      $actionLink = $this->getActivationLink();
      if ($messageId == 10) {
         $actionLink = $this->getResetPasswordLink();
      }

      if ($messageId == 18) {
         $actionLink = $this->getUnlockAccountLink();
      }

      $message = '<html><body>' . html_entity_decode(
         sprintf(
            $messageData['message'],
            sprintf($actionLink, $recipientData['hash']),
            $recipientData['userId'],
            $recipientData['pwd']
         )
      ) . '</body></html>';

      return $this->sendMessage($peopleToBeAdvised, $message, $subject);
   }

   public function sendMessage($emailTo, $message, $subject)
   {
      $this->mail->CharSet = 'UTF-8';
      $this->mail->Encoding = 'base64';
      $this->mail->setFrom(_EMAIL_Username, 'Equipe Conexperience Psicologia');
      $this->mail->addReplyTo(_EMAIL_Username, 'Equipe Conexperience Psicologia');

      foreach ($emailTo as $address) {
         $this->mail->addAddress($address);
      }

      $this->mail->SMTPOptions = array(
         'ssl' => array(
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
         )
      );
      
      $this->mail->isHTML(true);
      $this->mail->Subject = $subject;
      $this->mail->Body = $message;
      $this->mail->AltBody = htmlspecialchars(trim(strip_tags($message)));

      try {
         if (!$this->mail->send()) {
            echo 'Contato não pode ser enviado.';
            echo 'Erro: ' . $this->mail->ErrorInfo;
            return $this->mail->ErrorInfo;
         } else {
            return TRUE;
         }
      } catch (Exception $exc) {
         echo $this->mail->ErrorInfo . " \r\n" . $exc->getTraceAsString();
      }
   }
}
