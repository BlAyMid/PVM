<?php
namespace App\controllers;

use App\models\User;

// контроллер rest api для управления пользователями (crud)
class UserApiController
{
    // проверяет корректность api-ключа из заголовка запроса
    // @return bool true, если ключ валиден
    private function checkApiKey()
    {
        $key = isset($_ENV['API_KEY']) ? $_ENV['API_KEY'] : '';
        $provided = isset($_SERVER['HTTP_X_API_KEY']) ? $_SERVER['HTTP_X_API_KEY'] : '';
        return $key !== '' && hash_equals($key, $provided);
    }

    // принудительно требует авторизацию api: при ошибке отдаёт 401 и сообщение
    // @return bool true, если авторизован
    private function requireAuth()
    {
        if (!$this->checkApiKey()) {
            http_response_code(401);
            header('Content-Type: application/json');
            echo json_encode(array('error' => 'Unauthorized'));
            return false;
        }
        return true;
    }

    // читает json-тело запроса как массив
    // @return array данные запроса
    private function readJson()
    {
        $raw = file_get_contents('php://input');
        $data = json_decode($raw, true);
        return is_array($data) ? $data : array();
    }

    // отправляет json-ответ с указанным http-кодом
    private function json($data, $code = 200)
    {
        http_response_code((int)$code);
        header('Content-Type: application/json');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    // возвращает список пользователей
    public function index()
    {
        if (!$this->requireAuth()) return;
        $users = array();
        foreach (User::all() as $u) {
            $users[] = $u->toPublicArray();
        }
        $this->json($users);
    }

    // возвращает пользователя по id
    public function show($id)
    {
        if (!$this->requireAuth()) return;
        $user = User::findById((int)$id);
        if (!$user) { $this->json(array('error' => 'Not found'), 404); return; }
        $this->json($user->toPublicArray());
    }

    // создаёт нового пользователя
    public function create()
    {
        if (!$this->requireAuth()) return;
        $data = $this->readJson();
        $name = trim(isset($data['name']) ? $data['name'] : '');
        $email = trim(isset($data['email']) ? $data['email'] : '');
        $password = isset($data['password']) ? (string)$data['password'] : '';
        if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 9) {
            $this->json(array('error' => 'Invalid payload'), 422);
            return;
        }
        if (User::findByEmail($email)) {
            $this->json(array('error' => 'Email already exists'), 409);
            return;
        }
        $user = User::create($name, $email, $password);
        $this->json($user->toPublicArray(), 201);
    }

    // обновляет данные пользователя (полное или частичное обновление)
    public function update($id)
    {
        if (!$this->requireAuth()) return;
        $user = User::findById((int)$id);
        if (!$user) { $this->json(array('error' => 'Not found'), 404); return; }
        $data = $this->readJson();
        $fields = array();
        if (array_key_exists('name', $data)) $fields['name'] = trim((string)$data['name']);
        if (array_key_exists('email', $data)) {
            $email = trim((string)$data['email']);
            if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->json(array('error' => 'Invalid email'), 422);
                return;
            }
            $fields['email'] = $email;
        }
        if (array_key_exists('password', $data)) {
            $pwd = (string)$data['password'];
            if ($pwd !== '' && strlen($pwd) < 9) {
                $this->json(array('error' => 'Password too short (min 9)'), 422);
                return;
            }
            $fields['password'] = $pwd;
        }
        $user = $user->update($fields);
        $this->json($user->toPublicArray());
    }

    // удаляет пользователя по id
    public function delete($id)
    {
        if (!$this->requireAuth()) return;
        $user = User::findById((int)$id);
        if (!$user) { $this->json(array('error' => 'Not found'), 404); return; }
        $user->delete();
        $this->json(array('status' => 'deleted'));
    }
}
