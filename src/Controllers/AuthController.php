<?php
namespace Src\Controllers;

use Src\Config\Database;
use Src\Helpers\Jwt;
use Src\Helpers\Response;
use PDO;

class AuthController extends BaseController
{
    public function login()
    {
        $input = json_decode(file_get_contents('php://input'), true) ?? [];
        if (empty($input['email']) || empty($input['password'])) {
            return $this->error(422, 'Email dan password wajib diisi');
        }

        $db = Database::conn();
        $stmt = $db->prepare('SELECT id, name, email, password_hash, role FROM users WHERE email = ?');
        $stmt->execute([$input['email']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user || !password_verify($input['password'], $user['password_hash'])) {
            return $this->error(401, 'Invalid credentials');
        }

        $payload = [
            'sub' => $user['id'],
            'name' => $user['name'],
            'role' => $user['role'],
            'iat' => time(),
            'exp' => time() + 3600
        ];

        $token = Jwt::sign($payload, $_ENV['app']['jwt_secret']);
        Response::json(['token' => $token]);
    }
}