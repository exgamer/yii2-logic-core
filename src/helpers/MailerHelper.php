<?php

namespace concepture\yii2logic\helpers;

use Swift_Message;
use Yii;

/**
 * Вспомогательный класс для отправки почты
 *
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class MailerHelper
{
    const USERNAME = "username";
    const PASSWORD = "password";
    const HOST = "host";
    const PORT = "port";
    const ENCRYPTION = "encryption";
    const FROM = "from";

    /**
     * отправка
     *
     * @param $to
     * @param $subject
     * @param $body
     */
    public static function send($to, $subject, $body)
    {
        $transport = self::createTransport(
            Yii::$app->params['concepture']['mailer'][self::HOST],
            Yii::$app->params['concepture']['mailer'][self::PORT],
            Yii::$app->params['concepture']['mailer'][self::ENCRYPTION]
        );
        $message = self::createMessage(
            Yii::$app->params['concepture']['mailer'][self::FROM],
            $to,
            $subject,
            $body
        );

        return self::sendMail($transport, $message);
    }

    /**
     * Создание транспорта
     *
     * @param string $host
     * @param int $port
     * @param null $encryption
     * @return \Swift_SmtpTransport
     */
    private static  function  createTransport($host = 'localhost', $port = 25, $encryption = null)
    {
        $transport = new \Swift_SmtpTransport($host, $port, $encryption);
        $transport->setUsername(Yii::$app->params['concepture']['mailer'][self::USERNAME]);
        $transport->setPassword(Yii::$app->params['concepture']['mailer'][self::PASSWORD]);

        return $transport;
    }

    /**
     * Создание сообщения
     *
     * @param $from
     * @param $to
     * @param $subject
     * @param $body
     * @return Swift_Message
     */
    private static  function  createMessage($from, $to, $subject, $body)
    {
        if (!is_array($to)){
            $to= [$to];
        }
        $message = new Swift_Message();
        $message->setTo($to);
        $message->setSubject($subject);
        $message->setBody($body);
        $message->setFrom($from);
        $message->getHeaders()->addTextHeader('MIME-Version', "1.0");
        $message->getHeaders()->addTextHeader('Content-type', "text/html;");

        return $message;
    }

    /**
     * отправка письма
     * @param $transport
     * @param $message
     */
    private static  function  sendMail($transport, $message)
    {
        $mailer = new \Swift_Mailer($transport);
        $mailer->send($message);
    }

}