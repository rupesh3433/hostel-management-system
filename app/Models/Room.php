<?php

namespace App\Models;

class Room {
    protected $pdo;
    protected $table = 'rooms';
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    public function all() {
        $stmt = $this->pdo->query("SELECT * FROM {$this->table} ORDER BY id DESC");
        return $stmt->fetchAll();
    }
    
    public function find($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM {$this->table} WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    public function findByRoomNumber($roomNumber) {
        $stmt = $this->pdo->prepare("SELECT * FROM {$this->table} WHERE room_number = ?");
        $stmt->execute([$roomNumber]);
        return $stmt->fetch();
    }
    
    public function create($data) {
        $stmt = $this->pdo->prepare(
            "INSERT INTO {$this->table} (room_number, type, capacity, floor, price, description, status, created_at) 
             VALUES (?, ?, ?, ?, ?, ?, ?, NOW())"
        );
        
        return $stmt->execute([
            $data['room_number'],
            $data['type'],
            $data['capacity'],
            $data['floor'],
            $data['price'],
            $data['description'],
            $data['status']
        ]);
    }
    
    public function update($id, $data) {
        $stmt = $this->pdo->prepare(
            "UPDATE {$this->table} 
             SET room_number = ?, type = ?, capacity = ?, floor = ?, price = ?, description = ?, status = ?, updated_at = NOW()
             WHERE id = ?"
        );
        
        return $stmt->execute([
            $data['room_number'],
            $data['type'],
            $data['capacity'],
            $data['floor'],
            $data['price'],
            $data['description'],
            $data['status'],
            $id
        ]);
    }
    
    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM {$this->table} WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    public function count() {
        $stmt = $this->pdo->query("SELECT COUNT(*) as total FROM {$this->table}");
        $result = $stmt->fetch();
        return $result['total'];
    }
    
    public function countByStatus($status) {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) as total FROM {$this->table} WHERE status = ?");
        $stmt->execute([$status]);
        $result = $stmt->fetch();
        return $result['total'];
    }
    
    public function getDistinctTypes() {
        $stmt = $this->pdo->query("SELECT DISTINCT type FROM {$this->table} ORDER BY type");
        $results = $stmt->fetchAll();
        return array_column($results, 'type');
    }
    
    public function getRecent($limit = 5) {
        $stmt = $this->pdo->prepare("SELECT * FROM {$this->table} ORDER BY created_at DESC LIMIT ?");
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }
    
    public function search($filters) {
        $conditions = [];
        $params = [];
        
        if (!empty($filters['room_number'])) {
            $conditions[] = "room_number LIKE ?";
            $params[] = '%' . $filters['room_number'] . '%';
        }
        
        if (!empty($filters['type'])) {
            $conditions[] = "type = ?";
            $params[] = $filters['type'];
        }
        
        if (!empty($filters['status'])) {
            $conditions[] = "status = ?";
            $params[] = $filters['status'];
        }
        
        if (!empty($filters['floor'])) {
            $conditions[] = "floor = ?";
            $params[] = $filters['floor'];
        }
        
        if (!empty($filters['min_price'])) {
            $conditions[] = "price >= ?";
            $params[] = $filters['min_price'];
        }
        
        if (!empty($filters['max_price'])) {
            $conditions[] = "price <= ?";
            $params[] = $filters['max_price'];
        }
        
        $sql = "SELECT * FROM {$this->table}";
        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }
        $sql .= " ORDER BY id DESC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}