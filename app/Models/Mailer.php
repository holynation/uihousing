<?php

namespace App\Models;

use CodeIgniter\Model;

class Mailer extends Model
{

    private $mailer;
    private $ccMailAddress = '';
    private $templateBody='';

    const COMPANY_URL = 'equipro.io';
    const COMPANY_NAME = 'Equipro';
    const COMPANY_SUPPORT = 'support@equipro.io';
    const COMPANY_EMAIL = "noreply@equipro.io";

    function __construct()
    {
        helper('string');
        $senderMail = self::COMPANY_EMAIL;

        $this->mailer = \Config\Services::email();

        $config = $this->privateMailConfig($senderMail);
        $this->mailer->initialize($config);
    }

    /**
     * @param string $senderMail
     * @return array
     */
    private function privateMailConfig(string $senderMail=null){
        $config['protocol'] = 'smtp';
        $config['mailPath'] = '/usr/sbin/sendmail';
        $config['charset'] = 'utf-8';
        $config['wordWrap'] = false;
        $config['SMTPHost'] = getenv('mailUser');
        $config['SMTPPort'] = 465;
        $config['SMTPCrypto '] = 'ssl';
        $config['SMTPUser'] = $senderMail;
        $config['SMTPPass'] = getenv('mailKey');
        $config['mailType'] = 'html';
        $config['CRLF'] = "\r\n";
        $config['newline'] = "\r\n";
        $config['wordWrap'] = true;

        return $config;
    }

    public function setCcMail(string $name): void
    {
        $this->ccMailAddress = $name;
    }

    public function getCcMail(): string
    {
        return $this->ccMailAddress;
    }

    public function setTemplateBody(string $name): void
    {
        $this->templateBody = $name;
    }

    public function getTemplateBody(): string
    {
        return $this->templateBody;
    }

    /**
     * @param array     $data
     * @param string    $page
     * @return string \App\Views\{page}
     */
    public function mailTemplateRender(array $data,string $page)
    {
        $view = \Config\Services::renderer();
        $view->setData($data);
        $page = "$page".'.php';
        $templateMsg = $view->render('emails/'.$page);
        $this->setTemplateBody($templateMsg);
        return $templateMsg;
    }

    /**
     * This is the main func that send the mail to the client
     * @param  string|null $recipient [description]
     * @param  string|null $subject   [description]
     * @param  string|null $message   [description]
     * @return [type]                 [description]
     */
    private function mailerSend(string $recipient = null, string $subject = null, string $message = null)
    {
        $this->mailer->setFrom(self::COMPANY_EMAIL,self::COMPANY_NAME);
        $this->mailer->setTo($recipient);
        if ($this->ccMailAddress != '') {
            $this->mailer->setCC($this->ccMailAddress);
        }
        $this->mailer->setSubject($subject);
        $this->mailer->setMessage($message);
        if ($this->mailer->send()) {
            unset($this->ccMailAddress);
            return true;
        } else {
            echo 'Mailer Error: ' . $this->mailer->printDebugger();
            return false;
        }
    }

    public function sendAdminMail($message)
    {
        $recipient = 'info@dummy.com';
        $subject = 'Contact Message From A User';
        if (!$this->mailerSend($recipient, $subject, $message)) {
            return false;
        }
        return true;
    }

    /**
     * This is to get the subject mail
     * @param  string $type [description]
     * @return [type]       [description]
     */
    private function mailSubject(string $type)
    {
        $result  = array(
            'verify_account' => 'Verification of account from Equipro',
            'welcome' => 'Welcome On Board To Equipro Platform',
            'payment_invoice' => 'Notice On Your Payment Invoice on Equipro',
            'password_reset' => 'Request to Reset your Password!',
            'password_app_token' => 'Equipro password Recovery OTP',
            'password_reset_success' => 'Equipro Password Recovery Success',
            'booking_request' => "You've Equipment Request From Equipro",
            'booking_request_update' => "Equipment Request From Equipro Updated",
            'booking_request_status' => "Equipment Request Approval Status",
            'booking_request_pickup' => "Equipment Request Booking Pick Up Date",
            'booking_request_extended' => "Request for Extension on Equipment Booking",
            'booking_request_returned' => "Returned of Equipment(s)",
        );
        return $result[$type];
    }

    /**
     * This is function to send mail out to client
     * @param  string       $recipient [description]
     * @param  string       $subject   [description]
     * @param  int|string   $type      [description]
     * @param  string       $customer  [description]
     * @param  array|string $info      [description]
     * @return [type]                  [description]
     */
    public function sendCustomerMail(string $recipient,string $subject,int $type=null,string $customer=null,array $info = null)
    {
        # it property templateBody take precedence over the mailBody method
        if ($this->templateBody != '')
        {
            $message = $this->templateBody;
        }
        else
        {
            $message = $this->formatMsg($recipient, $type, $customer, $info);
        }
        $recipient = trim($recipient);
        $subject = $this->mailSubject($subject);

        if (!$this->mailerSend($recipient, $subject, $message)) {
            return false;
        }
        return true;
    }

    private function formatMsg($recipient = '', $type = null, $customer=null, $info=null)
    {
        if ($recipient) {
            $msg = '';
            return $msg;
        }
    }
}
