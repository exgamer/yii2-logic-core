<?php
namespace concepture\yii2logic\helper;

use Yii;

/**
 * Вспомогательный для отрправки почты
 *
 * @author CitizenZet <exgamer@live.ru>
 */
class MailerHelper
{
    const USERNAME = "cMailerUsername";
    const PASSWORD = "cMailerPassword";
    const HOST = "cMailerHost";
    const PORT = "cMailerPort";
    const ENCRYPTION = "cMailerEncryption";
    const FROM = "cMailerFrom";

    public static function send($to, $subject, $body)
    {
        $transport = self::createTransport(
            Yii::$app->params[self::HOST],
            Yii::$app->params[self::PORT],
            Yii::$app->params[self::ENCRYPTION]
        );
        $message = self::createMessage(
            Yii::$app->params[self::FROM],
            $to,
            $subject,
            $body
        );

        return self::sendMail($transport, $message);
    }


    private static  function  createTransport($host = 'localhost', $port = 25, $encryption = null)
    {
        $transport = new \Swift_SmtpTransport($host, $port, $encryption);
        $transport->setUsername(Yii::$app->params[self::USERNAME]);
        $transport->setPassword(Yii::$app->params[self::PASSWORD]);

        return $transport;
    }

    private static  function  createMessage($from, $to, $subject, $body)
    {
        if (!is_array($to)){
            $to= [$to];
        }
        $message = new \Swift_Message();
        $message->setTo($to);
        $message->setSubject($subject);
        $message->setBody($body);
        $message->setFrom($from);
        $message->getHeaders()->addTextHeader('MIME-Version', "1.0");
        $message->getHeaders()->addTextHeader('Content-type', "text/html;");

        return $message;
    }

    private static  function  sendMail($transport, $message)
    {
        $mailer = new \Swift_Mailer($transport);
        $mailer->send($message);
    }

}