<?php
namespace Src\Controllers;

use Src\Repositories\UserRepository;
use Src\Helpers\Response;
use Src\Validation\Validator;

class UserController {
    private array $cfg;

    public function __construct(array $cfg) {
        $this->cfg = $cfg;
    }

    private function ok($data, $code = 200) {
        Response::json($data, $code);
    }

    private function error($code, $message, $errors = []) {
        Response::jsonError($code, $message, $errors);
    }

    public function index() {
        $page = (int)($_GET['page'] ?? 1);
        $perPage = (int)($_GET['per_page'] ?? 10);
        $repo = new UserRepository($this->cfg);
        $this->ok($repo->paginate(max(1, $page), min(100, max(1, $perPage))));
    }

    public function show($id) {
        $repo = new UserRepository($this->cfg);
        $user = $repo->find((int)$id);
        $user ? $this->ok($user) : $this->error(404, 'User not found');
    }

    public function store() {
        $input = json_decode(file_get_contents('php://input'), true) ?? [];
        
        $validator = Validator::make($input, [
            'name' => 'required|min:3|max:100',
            'email' => 'required|email|max:150',
            'password' => 'required|min:6|max:72',
            'role' => 'enum:user,admin'
        ]);

        if ($validator->fails()) {
            return $this->error(422, 'Validation error', $validator->errors());
        }

        $hash = password_hash($input['password'], PASSWORD_DEFAULT);
        $repo = new UserRepository($this->cfg);
        
        try {
            $user = $repo->create(
                $input['name'],
                $input['email'],
                $hash,
                $input['role'] ?? 'user'
            );
            $this->ok($user, 201);
        } catch (\Throwable $e) {
            $this->error(400, 'Create failed', ['details' => $e->getMessage()]);
        }
    }

    public function update($id) {
        $input = json_decode(file_get_contents('php://input'), true) ?? [];
        
        $validator = Validator::make($input, [
            'name' => 'required|min:3|max:100',
            'email' => 'required|email|max:150',
            'role' => 'enum:user,admin'
        ]);

        if ($validator->fails()) {
            return $this->error(422, 'Validation error', $validator->errors());
        }

        $repo = new UserRepository($this->cfg);
        $user = $repo->update(
            (int)$id,
            $input['name'],
            $input['email'],
            $input['role']
        );
        
        $user ? $this->ok($user) : $this->error(404, 'User not found');
    }

    public function destroy($id) {
        $repo = new UserRepository($this->cfg);
        $ok = $repo->delete((int)$id);
        $ok ? $this->ok(['deleted' => true]) : $this->error(400, 'Delete failed');
    }
}