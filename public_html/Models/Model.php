<?php
namespace Models;

use Core\DBWrapper;
use Core\PHPMailer\PHPMailer;
class Model
{
    protected $db_file;

    public function __construct()
    {
        $this->db_file = (new DBWrapper('sqlite:databases/testAppDB.sqlite3'))->getDBFile();
    }

    protected function sendEmailCore(string $to, string $name, string $subject, string $message)
    {
        $mail = new PHPMailer;
        $mail->isSMTP();
        $mail->Host = 'smtp.yopmail.com';
        $mail->setFrom('from@example.com', 'First Last');
        $mail->addAddress($to, $name);
        $mail->Subject = $subject;
        $mail->msgHTML($message);

        $send = $mail->send();
        $matchpos = strpos($mail->ErrorInfo, "SMTP Error: The following recipients failed:");

        if (gettype($matchpos) == 'integer') {
            $result = 'not_support';
        } else {
            $result = $send;
        }
        var_dump($send);
        var_dump('$send');
        var_dump($result);

        return $result;
    }
}