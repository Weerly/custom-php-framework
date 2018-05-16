<?php
namespace Core;
class Session extends Base
{
    protected $user;
    protected $session;

    public function __construct($cookies)
    {
        session_start();
        parent::__construct();

        $auth = $this->checkAuth($cookies);

        if (!$auth) {
            self::processCookies();
        }
    }

    public static function processCookies() :string
    {
        $hashString = md5(date("Y-m-d h:i:sa").''.(new \DateTime())->getTimestamp());
//        var_dump($hashString);
        $passHash = password_hash($hashString, PASSWORD_BCRYPT, ['cost' => 11]);
//        var_dump($passHash);
//        $verify = password_verify($hashString, $passHash);
//        var_dump($verify);

        setcookie ("psid" ,$passHash, 0, '/', '', false, true);

        return $passHash;
    }

    protected  function checkAuth($cookies) :bool
    {
        $psid = $cookies['psid'] ?? "";
        $result = $this->readDBFileForLogedUsers($psid);
        if (!!$result) {
            $userData  = $this->getUserData($result['login']);
            $login = (!!$userData) ? $result['login'] : null;
            $name = (!!$userData) ? $userData['name'] : null;
            $pass = (!!$userData) ? $userData['password'] : null;


        } else {
            $login = null;
            $name  = null;
            $pass  = null;
        }

        if ($login == null) {
            $auth = false;
        } else {
            $auth = true;
            $this->session['user'] = ['login' => $login, 'name' => $name, 'password' => $pass];
        }

        return $auth;

    }

    protected function getUserData(string $login) :array
    {
        $name = null;
        $db_file = (new DBWrapper('sqlite:databases/testAppDB.sqlite3'))->getDBFile();

        $query = "SELECT `name`, `password` FROM `Users` WHERE `login` = :login";
        $statement = $db_file->prepare($query);

        $statement->bindParam('login', $login);
        $temp = $statement->execute();

        $result = $temp->fetchArray(SQLITE3_ASSOC);

        return $result;
    }

    protected function readDBFileForLogedUsers(string $psid)
    {
        $db_file = (new DBWrapper('sqlite:databases/testAppDB.sqlite3'))->getDBFile();

        $query = "SELECT `login` FROM `LoggedUsers` WHERE `psid` = :psid";
        $statement = $db_file->prepare($query);

        $statement->bindParam('psid', $psid);
        $temp = $statement->execute();

        $result = $temp->fetchArray(SQLITE3_ASSOC);

        return $result;
    }

    protected function getUser() {
        return $this->session["user"];
    }

    protected function getSession() {
        return $this->session;
    }

}