<?php
namespace Models;

use Core\Session;
class UserModel extends Model
{
    public function saveNewUser(array $userData)
    {
        $query = "INSERT INTO `Users` (`login`, `name`, `password`, `active`) VALUES (:login, :name, :password, 'FALSE' )";
        $statement = $this->db_file->prepare($query);

        $statement->bindParam('login', $userData['login']);
        $statement->bindParam('name', $userData['name']);
        $statement->bindParam('password', $userData['password']);

        $result = $statement->execute();

        return !!$result;
    }

    public function activateUser(string $code, string $login)
    {
        $query = "INSERT INTO `ActivateUsers` (`login`, `code`) VALUES (:login, :code)";
        $statement = $this->db_file->prepare($query);

        $statement->bindParam('login', $login);
        $statement->bindParam('code', $code);

        $result = $statement->execute();

        return !!$result;
    }

    public function deleteActivateUser(string $code, string $login)
    {
        $query = "DELETE FROM `ActivateUsers` WHERE `login` = :login AND `code` = :code";
        $statement = $this->db_file->prepare($query);

        $statement->bindParam('login', $login);
        $statement->bindParam('code', $code);

        $result = $statement->execute();

        return !!$result;
    }

    public function makeUserActive(string $login)
    {
        $query = "UPDATE `Users` SET `active` = 'TRUE' WHERE `login` = :login";
        $statement = $this->db_file->prepare($query);

        $statement->bindParam('login', $login);

        $result = $statement->execute();

        return !!$result;
    }

    public function loginUser(string $login, string $psid) :bool
    {
        $query = "INSERT INTO `LoggedUsers` (`login`, `psid`) VALUES (:login, :psid)";
        $statement = $this->db_file->prepare($query);

        $statement->bindParam('login', $login);
        $statement->bindParam('psid', $psid);

        $result = $statement->execute();

        return !!$result;
    }

    public function logOutUser(string $login) :bool
    {
        $query = "DELETE FROM `LoggedUsers` WHERE login = :login";
        $statement = $this->db_file->prepare($query);

        $statement->bindParam('login', $login);

        $result = $statement->execute();

        return !!$result;
    }

    public function getUser(array $userData)
    {
        $login = "";

        $query = "SELECT login, password FROM Users WHERE login = :login";
        $statement = $this->db_file->prepare($query);

        $statement->bindParam('login', $userData['login']);
        $temp = $statement->execute();

        $result = $temp->fetchArray(SQLITE3_ASSOC);

        if (!$temp) {
            $result = "error";
        }

        return $result;
    }

    public function updateUserData(array $userData) :bool
    {
        $query = "UPDATE Users SET name = :name, password = :new_password WHERE login = :login";
        $statement = $this->db_file->prepare($query);

        $statement->bindParam('name', $userData['name']);
        $statement->bindParam('login', $userData['login']);
        $statement->bindParam('new_password', $userData['new_password']);
        $result = $statement->execute();

        return !!$result;
    }

    public function isUserActivated(string $login) :bool
    {
        $query = "SELECT active FROM Users WHERE login = :login AND active = 'TRUE'";
        $statement = $this->db_file->prepare($query);

        $statement->bindParam('login', $login);
        $temp = $statement->execute();

        $result = $temp->fetchArray(SQLITE3_NUM);

        return (!!$result);
    }

    public function getNotActivatedUser(string $code) :string
    {
        $login = "";

        $query = "SELECT `login` FROM `ActivateUsers` WHERE `code` = :code";
        $statement = $this->db_file->prepare($query);

        $statement->bindParam('code', $code);
        $temp = $statement->execute();

        $result = $temp->fetchArray(SQLITE3_ASSOC);

        if (!!$result) {
            $login = $result["login"];
        }

        return $login;
    }

    public function sendEmail(string $email, string $name, string $code)
    {
        $message  = "<p style='margin-left: 10%;font-weight: 600'>Hi, " . $name . "</p><br/>";
        $message .= "<p style='margin-left: 10%'>if your did not signed up on our service, </p><br/>";
        $message .= "<p style='margin-left: 10%'>please just ignore this email </p><br/>";
        $message .= "<p style='margin-left: 10%'>if you did it, to activate your account </p><br/>";
        $message .= "<p style='margin-left: 10%'>please follow next link </p><br/>";
        $message .= "<p style='margin-left: 10%'><a href='http://192.168.10.10:1981/activate?code=" . $code . "'>activate</a></p>";

        $result = $this->sendEmailCore($email, $name, "activate your account", $message);

        return $result;
    }

    public function logInSession() :string
    {
        $psid = Session::processCookies();

        return $psid;
    }

    public function isUserLogged(array $session) :bool
    {
        $isUserLogged = $session["user"] ?? false;

        return !!$isUserLogged;
    }

    public function createHashString($md5String) :string
    {
        $hashString = password_hash($md5String, PASSWORD_BCRYPT, ['cost' => 11]);

        return $hashString;
    }

    public function isInputDataNotEmpty($post) :bool
    {
        foreach ($post as $input) {
            if (empty($input)) {
                return false;
            }
        }

        return true;
    }

    public function isInputDataFullyEmpty($post) :bool
    {
        $empty = 0;
        foreach ($post as $input) {
            if (empty($input)) {
                $empty++;
            }
        }

        return ($empty == count($post));
    }
}