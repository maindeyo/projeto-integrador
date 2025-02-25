<?php

class User
{
    protected $conn; //conexão

    public function __construct(SQLite3 $connection) {
        $this->conn = $connection;
    }

    public function save(string $name, string $email, string $password) : SQLite3Result | bool {
        $query = "INSERT INTO tb_usuarios ('usu_name', 'usu_email', 'usu_password') "
            . "values(:usu_name,:usu_email,:usu_password)";

        $sttm = $this->conn->prepare($query);

        $sttm->bindValue(":usu_name", $name);
        $sttm->bindValue(":usu_email", $email);
        $sttm->bindValue(":usu_password", password_hash($password, PASSWORD_ARGON2I));
        $result = $sttm->execute();
        return $result;
    }

    public function find (string $email) : Array | bool {
        $query = "SELECT * FROM tb_usuarios WHERE usu_email=:usu_email";
        $sttm = $this->conn->prepare($query);
        $sttm->bindValue(":usu_email", $email);
        $result = $sttm->execute();
        return $result->fetchArray();
    }
    
    public function getID (string $email) {
        $model = $this->find($email);
        return $model["usu_id"];
    }
    
    public function getName (string $email) {
        $model = $this->find($email);
        return $model["usu_name"];
    }

    public function updateName(string $newname, string $email) {
        $query = "UPDATE tb_usuarios SET usu_nome=:new_name WHERE usu_nome=:old_name";
        $sttm = $this->conn->prepare($query);
        $sttm->bindValue(":new_name", $newname);
        $sttm->bindValue(":old_name", $this->getName($email));
        $result = $sttm->execute();
        return $result->fetchArray();
    }

    public function updatePassword(string $newpassword, string $email) {
        $query = "UPDATE tb_usuarios SET usu_password=:new_name WHERE usu_name=:username";
        $sttm = $this->conn->prepare($query);
        $sttm->bindValue(":new_password", password_hash($newpassword, PASSWORD_ARGON2I));
        $sttm->bindValue(":username", $this->getName($email));
        $result = $sttm->execute();
        return $result->fetchArray();
    }

    public function all() {

        $db_conn = $this->conn;

        $result = $db_conn->query('SELECT * FROM tb_usuarios');

        $user_list = array();
        while ($user = $result->fetchArray()) {
            array_push($user_list, [
                'title' => $user['title'],
                'user' => $user['user'],
            ]);
        }
        return $user_list;
    }
}
?>