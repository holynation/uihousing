<?php

namespace App\Models;

use CodeIgniter\Model;

class Mailer extends Model
{

    private $mailer;
    private $ccMailAddress = '';
    private $templateBody='';

    const COMPANY_URL = 'ui.edu.ng';
    const COMPANY_NAME = 'UIHousing';
    const COMPANY_SUPPORT = 'support@equipro.io';
    const COMPANY_EMAIL = "noreply@equipro.io";

    public function __construct()
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
            'verify_account' => 'Verification of account from UIHousing',
            'welcome' => 'Welcome On Board To UIHousing Platform',
            'password_reset' => 'Request to Reset your Password!',
            'password_reset_success' => 'UIHousing Password Recovery Success',
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
    public function sendCustomerMail(string $recipient,string $subject,int $type=null,
        string $customer=null,array $info = null
    ){
        $message = $this->formatMsg($recipient, $type, $customer, $info);
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
            $msg .= $this->mailHeader();
            $msg .= $this->mailBody($recipient, $type, $customer, $info);
            $msg .= $this->mailFooter();
            return $msg;
        }
    }

    public function mailerTest($recipient = '', $type = '', $customer = '', $info = '')
    {
        echo $this->formatMsg($recipient, $type, $customer, $info);
    }

    private function mailHeader()
    {
        $msg = '';
        $imgLink = 'https://';

        $msg .= '<!DOCTYPE html><html lang="en" xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office"><head>
                     <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
                     <meta charset="utf-8">
                     <meta name="viewport" content="width=device-width">
                    <meta http-equiv="X-UA-Compatible" content="IE=edge">
                    <meta name="x-apple-disable-message-reformatting">
                    <link href="https://fonts.googleapis.com/css?family=Roboto:400,600" rel="stylesheet" type="text/css">
                    <style>body,html{margin:0 auto!important;padding:0!important;height:100%!important;width:100%!important;font-family:Roboto,sans-serif!important;font-size:14px;margin-bottom:10px;line-height:24px;color:#8094ae;font-weight:400}*{-ms-text-size-adjust:100%;-webkit-text-size-adjust:100%;margin:0;padding:0}table,td{mso-table-lspace:0!important;mso-table-rspace:0!important}table{border-spacing:0!important;border-collapse:collapse!important;table-layout:fixed!important;margin:0 auto!important}table table table{table-layout:auto}a{text-decoration:none}img{-ms-interpolation-mode:bicubic}</style>
                </head>
            <body width="100%" style="margin: 0; padding: 0 !important; mso-line-height-rule: exactly; background-color: #f5f6fa;">';
        $msg .= '<center style="width: 100%; background-color: #f5f6fa;">';
        $msg .= '<table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#f5f6fa">';
        $msg .= '<tr><td style="padding: 40px 0;">';
        $msg .= '<table style="width:100%;max-width:620px;margin:0 auto;">
                        <tbody>
                            <tr>
                                <td style="text-align: center; padding-bottom:25px">
                                    <a href=""><img style="height: 40px" src="' . $imgLink . '" alt="logo"/></a>
                                </td>
                            </tr>
                        </tbody>
                    </table>';

        $msg .= '<table style="width:100%;max-width:620px;margin:0 auto;background-color:#ffffff;color:#526484;">';
        return $msg;
    }

    private function mailBody($recipient = '', $type = '', $customer = '', $info = '')
    {
        $msg = '';
        $receiverName = $recipient;
        $mailSalt = appConfig('salt');
        $email = str_replace(array('@', '.com'), array('~az~', '~09~'), $recipient);
        $temp = md5($mailSalt . $recipient);
        $expire = rndEncode(time());
        $verifyTask = rndEncode('verify');
        $accountLink = base_url("account/verify/$email/$temp/$type?task=$verifyTask&tk=$expire");
        $mailType = appConfig('type');

        switch ($mailType[$type]) {
            case 'verify_account':
                $msg = $this->loadEmailConfirmText($customer, $accountLink);
                break;
            case 'verify_success':
                $msg = $this->loadWelcomeText($customer);
                break;
            case 'forget':
                $msg = $this->loadPasswordRequestText($customer, $accountLink);
                break;
            case 'forget_success':
                $msg = $this->loadPasswordResetSuccessText($customer);
                break;
            case 'password_forget_token':
                $msg = $this->loadAppPasswordTokenText($customer, $info);
                break;
        }

        return $msg;
    }

    private function mailFooter()
    {
        $msg = '';
        $footerLink = self::COMPANY_URL;
        $reserved = "&copy;Copyright " .date('Y'). " UIHousing. All rights reserved.";

        $msg .= '</table><table style="width:100%;max-width:620px;margin:0 auto;color:#8094ae;">
                        <tbody>
                            <tr>
                                <td style="text-align: center; padding:25px 20px 0;">
                                    <p style="font-size: 13px;">' . $reserved . '</p>
                                    
                                    <p style="padding-top: 15px; font-size: 12px;">This email was sent to you as a registered user of <a style="color: #2d7eef; text-decoration:none;" href="' . $footerLink . '">"'.self::COMPANY_NAME.'"</a>.</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>';
        $msg .= '</td></tr>';
        $msg .= '</table></center></body></html>';
        return $msg;
    }

    private function loadEmailConfirmText($customerName, $confirmLink)
    {
        $msg = '';
        $msg .= '<tbody>';
        $customerName = $customerName['fullname'];
        $content = '
                <p style="margin-bottom: 10px;">Hi ' . $customerName . ',</p>
                <p style="margin-bottom: 10px;">Welcome! <br> You are receiving this email because you have registered on our site.</p>
                <p style="margin-bottom: 10px;">Click the link below to activate your account.</p>
                <p style="margin-bottom: 25px;">This link will expire in 30 minutes and can only be used once.</p>
                <a href="' . $confirmLink . '" style="background-color:#2d7eef;border-radius:4px;color:#ffffff;display:inline-block;font-size:13px;font-weight:600;line-height:44px;text-align:center;text-decoration:none;text-transform: uppercase; padding: 0 30px">Verify Email</a>
            ';
        $content .= '
                <tr>
                <td style="padding: 0 30px">
                    <h4 style="font-size: 15px; color: #000000; font-weight: 600; margin: 0; text-transform: uppercase; margin-bottom: 10px">or</h4>
                    <p style="margin-bottom: 10px;">If the button above does not work, paste this link into your web browser:</p>
                    <a href="#" style="color: #2d7eef; text-decoration:none;word-break: break-all;">' . $confirmLink . '</a>
                </td>
            </tr>
            <tr>
                <td style="padding: 20px 30px 40px">
                    <p>If you did not make this request, please contact us or ignore this message.</p>
                    <p style="margin: 0; font-size: 13px; line-height: 22px; color:#9ea8bb;">This is an automatically generated email, please do not reply to this email.</p>
                </td>
            </tr>
            ';
        $msg .= '<tr><td style="padding: 30px 30px 15px 30px;"><h2 style="font-size: 18px; color: #2d7eef; font-weight: 600; margin: 0;">Confirm Your E-Mail Address</h2></td></tr>';
        $msg .= "<tr><td style='padding:0 30px 20px'>$content</td></tr>";
        $msg .= '</tbody>';
        return $msg;
    }

    private function loadWelcomeText($customerName)
    {
        $msg = '';
        $msg .= '<tbody>';
        $customerName = $customerName;
        $content = '
                    <td style="padding: 30px 30px 20px">
                        <p style="margin-bottom: 10px;">Hi ' . $customerName . ',</p>
                        <p style="margin-bottom: 10px;">We are pleased to have you registered for UIHousing Platform.</p>
                        <p style="margin-bottom: 10px;">Your account is now verified and you can now register your devices for protection.</p>
                        <p style="margin-bottom: 15px;">We hope you will enjoy the experience. We are here for you. </p>
                    </td>
                        ';
        $msg .= '<tr>' . $content . '</tr>';
        $msg .= '</tbody>';
        return $msg;
    }

    private function loadPasswordRequestText($customerName, $resetLink)
    {
        $msg = '';
        $msg .= '<tbody>';
        $content = '<tr>
                        <td style="text-align:center;padding: 30px 30px 15px 30px;">
                            <h2 style="font-size: 18px; color: #2d7eef; font-weight: 600; margin: 0;">Reset Password</h2>
                        </td>
                    </tr>';

        $content .= '<tr>
                        <td style="text-align:center;padding: 0 30px 20px">
                            <p style="margin-bottom: 10px;">Hi ' . $customerName . ',</p>
                            <p style="margin-bottom: 18px;">Click on the link below to reset your password.</p>
                            <p style="margin-bottom: 25px;"><b style="color:red;">NOTE:</b>This link will expire in 30 minutes and can only be used once.</p>
                            <a href="' . $resetLink . '" style="background-color:#2d7eef;border-radius:4px;color:#ffffff;display:inline-block;font-size:13px;font-weight:600;line-height:44px;text-align:center;text-decoration:none;text-transform: uppercase; padding: 0 25px">Reset Password</a>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 20px 40px 40px">
                            <p>If you did not make this request, please contact us or ignore this message.</p>
                            <p style="margin: 0; font-size: 13px; line-height: 22px; color:#9ea8bb;">This is an automatically generated email, please do not reply to this email.</p>
                        </td>
                    </tr>
                    ';

        $msg .= $content;
        $msg .= '</tbody>';
        return $msg;
    }

    private function getFooterText()
    {
        $msg = '';
        $msg .= '<tr style="text-align:center;">
                        <td style="text-align:center;padding: 20px 30px 40px">
                            <p>If you did not make this request, please contact us or ignore this message.</p>
                        </td>
                    </tr>';
        return $msg;
    }

    private function loadPasswordResetSuccessText($customerName)
    {
        $msg = '';
        $msg .= '<tbody>';
        $content = '<tr>
                            <td style="text-align:center;padding: 30px 30px 15px 30px;">
                                <h2 style="font-size: 18px; color: #1ee0ac; font-weight: 600; margin: 0;">Your Password Has Been Reset</h2>
                            </td>
                        </tr>
                        <tr>
                            <td style="text-align:center;padding: 0 30px 20px">
                                <p style="margin-bottom: 10px;">Hi ' . $customerName . ',</p>
                                <p>You have successfully reset your password. Thank you for being with us.</p>
                            </td>
                        </tr>';
        $msg .= $content;
        $msg .= '</tbody>';
        return $msg;
    }


}
