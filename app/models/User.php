<?php
namespace App\models;

use App\core\DB;

// класс модели пользователя с методами crud и вспомогательными преобразованиями
class User
{
    // @var int идентификатор пользователя
    public $id;
    // @var string имя пользователя
    public $name;
    // @var string email пользователя
    public $email;
    // @var string хеш пароля
    public $password_hash;
    // @var string дата создания записи
    public $created_at;

    // создаёт объект пользователя из массива данных строки
    public static function fromArray($row)
    {
        $u = new self();
        $u->id = (int)$row['id'];
        $u->name = $row['name'];
        $u->email = $row['email'];
        $u->password_hash = $row['password_hash'];
        $u->created_at = $row['created_at'];
        return $u;
    }

    // создаёт нового пользователя в базе
    // @return self созданный пользователь
    public static function create($name, $email, $password)
    {
        $pdo = DB::conn();
        $stmt = $pdo->prepare('INSERT INTO users (name, email, password_hash) VALUES (:name, :email, :pass)');
        $stmt->execute(array(
            ':name' => $name,
            ':email' => $email,
            ':pass' => password_hash($password, PASSWORD_DEFAULT)
        ));
        $id = (int)$pdo->lastInsertId();
        return self::findById($id);
    }

    // находит пользователя по идентификатору
    public static function findById($id)
    {
        $pdo = DB::conn();
        $stmt = $pdo->prepare('SELECT * FROM users WHERE id = :id');
        $stmt->execute(array(':id' => $id));
        $row = $stmt->fetch();
        return $row ? self::fromArray($row) : null;
    }

    // находит пользователя по email
    public static function findByEmail($email)
    {
        $pdo = DB::conn();
        $stmt = $pdo->prepare('SELECT * FROM users WHERE email = :email');
        $stmt->execute(array(':email' => $email));
        $row = $stmt->fetch();
        return $row ? self::fromArray($row) : null;
    }

    // возвращает список всех пользователей (от новых к старым)
    // @return массив пользователей
    public static function all()
    {
        $pdo = DB::conn();
        $stmt = $pdo->query('SELECT id, name, email, created_at, password_hash FROM users ORDER BY id DESC');
        $rows = $stmt->fetchAll();
        $list = array();
        foreach ($rows as $r) { $list[] = self::fromArray($r); }
        return $list;
    }

    // обновляет выбранные поля пользователя
    // @return обновлённый пользователь
    public function update($fields)
    {
        $pdo = DB::conn();
        $sets = array();
        $params = array(':id' => $this->id);
        if (isset($fields['name'])) { $sets[] = 'name = :name'; $params[':name'] = $fields['name']; }
        if (isset($fields['email'])) { $sets[] = 'email = :email'; $params[':email'] = $fields['email']; }
        if (isset($fields['password'])) { $sets[] = 'password_hash = :pass'; $params[':pass'] = password_hash($fields['password'], PASSWORD_DEFAULT); }
        if (!$sets) return $this;
        $sql = 'UPDATE users SET ' . implode(', ', $sets) . ' WHERE id = :id';
        $pdo->prepare($sql)->execute($params);
        return self::findById($this->id);
    }

    // удаляет пользователя из базы
    public function delete()
    {
        $pdo = DB::conn();
        $pdo->prepare('DELETE FROM users WHERE id = :id')->execute(array(':id' => $this->id));
    }

    // возвращает публичное представление пользователя (без чувствительных данных)
    // @return ассоциативный массив полей
    public function toPublicArray()
    {
        return array(
            'id' => (int)$this->id,
            'name' => $this->name,
            'email' => $this->email,
            'created_at' => $this->created_at
        );
    }
}
