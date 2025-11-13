<?php
namespace Src\Repositories;

use PDO;
use Src\Config\Database;

class UserRepository {
    private PDO $db;

    public function __construct(array $cfg) {
        $this->db = Database::conn($cfg);
    }

    public function paginate($page, $per) {
        $offset = ($page - 1) * $per;
        $total = (int)$this->db->query('SELECT COUNT(*) FROM users')->fetchColumn();
        
        $stmt = $this->db->prepare('SELECT id, name, email, role, created_at, updated_at FROM users ORDER BY id DESC LIMIT :per OFFSET :off');
        $stmt->bindValue(':per', (int)$per, PDO::PARAM_INT);
        $stmt->bindValue(':off', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        
        return [
            'data' => $stmt->fetchAll(),
            'meta' => [
                'total' => $total,
                'page' => $page,
                'per_page' => $per,
                'last_page' => max(1, (int)ceil($total / $per))
            ]
        ];
    }

    public function find($id) {
        $stmt = $this->db->prepare('SELECT id, name, email, role, created_at, updated_at FROM users WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function create($name, $email, $hash, $role = 'user') {
        $this->db->beginTransaction();
        try {
            $stmt = $this->db->prepare('INSERT INTO users (name, email, password_hash, role) VALUES (?, ?, ?, ?)');
            $stmt->execute([$name, $email, $hash, $role]);
            $id = (int)$this->db->lastInsertId();
            $this->db->commit();
            return $this->find($id);
        } catch (\Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function update($id, $name, $email, $role) {
        $stmt = $this->db->prepare('UPDATE users SET name = ?, email = ?, role = ? WHERE id = ?');
        $stmt->execute([$name, $email, $role, $id]);
        return $this->find($id);
    }

    public function delete($id) {
        $stmt = $this->db->prepare('DELETE FROM users WHERE id = ?');
        return $stmt->execute([$id]);
    }
}