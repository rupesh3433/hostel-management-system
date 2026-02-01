<?php

namespace App\Controllers;

require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../Models/Room.php';

use App\Models\Room;

class RoomController extends Controller {
    
    public function index() {
        $roomModel = new Room($this->pdo);
        
        $searchQuery = $_GET['q'] ?? '';
        $rooms = [];
        
        if (!empty($searchQuery)) {
            // Smart search across all fields with better matching
            $sql = "SELECT * FROM rooms WHERE 
                    room_number LIKE ? OR 
                    type LIKE ? OR 
                    status LIKE ? OR 
                    description LIKE ? OR 
                    CAST(floor AS CHAR) LIKE ? OR
                    CAST(price AS CHAR) LIKE ? OR
                    CAST(capacity AS CHAR) LIKE ?
                    ORDER BY 
                    CASE 
                        WHEN room_number = ? THEN 1
                        WHEN room_number LIKE ? THEN 2
                        WHEN type = ? THEN 3
                        WHEN status = ? THEN 4
                        ELSE 5
                    END,
                    id DESC";
            
            $exactSearch = $searchQuery;
            $startsWith = $searchQuery . '%';
            $contains = '%' . $searchQuery . '%';
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                $contains, $contains, $contains, $contains,
                $contains, $contains, $contains,
                $exactSearch, $startsWith, $exactSearch, $exactSearch
            ]);
            $rooms = $stmt->fetchAll();
        } else {
            // Get all rooms
            $rooms = $roomModel->all();
        }
        
        // Get room statistics
        $stats = [
            'total' => $roomModel->count(),
            'available' => $roomModel->countByStatus('Available'),
            'booked' => $roomModel->countByStatus('Booked'),
            'maintenance' => $roomModel->countByStatus('Maintenance')
        ];
        
        $this->view('rooms.index', [
            'rooms' => $rooms,
            'stats' => $stats,
            'searchQuery' => $searchQuery
        ]);
    }
    
    public function create() {
        $roomModel = new Room($this->pdo);
        $roomTypes = $roomModel->getDistinctTypes();
        
        $this->view('rooms.create', [
            'roomTypes' => $roomTypes,
            'roomTypesList' => ['Single', 'Double', 'Triple', 'Dorm'],
            'statusList' => ['Available', 'Booked', 'Maintenance']
        ]);
    }
    
    public function store() {
        $roomModel = new Room($this->pdo);
        
        $data = [
            'room_number' => trim($_POST['room_number'] ?? ''),
            'type' => $_POST['type'] ?? 'Single',
            'capacity' => (int)($_POST['capacity'] ?? 1),
            'floor' => (int)($_POST['floor'] ?? 1),
            'price' => (float)($_POST['price'] ?? 0),
            'description' => trim($_POST['description'] ?? ''),
            'status' => $_POST['status'] ?? 'Available'
        ];
        
        // Validation
        $errors = [];
        
        if (empty($data['room_number'])) {
            $errors[] = 'Room number is required';
        } elseif (!preg_match('/^[A-Za-z0-9-]+$/', $data['room_number'])) {
            $errors[] = 'Room number can only contain letters, numbers, and hyphens';
        }
        
        if ($data['capacity'] < 1 || $data['capacity'] > 10) {
            $errors[] = 'Capacity must be between 1 and 10';
        }
        
        if ($data['floor'] < 1 || $data['floor'] > 20) {
            $errors[] = 'Floor must be between 1 and 20';
        }
        
        if ($data['price'] < 0) {
            $errors[] = 'Price cannot be negative';
        } elseif ($data['price'] > 100000) {
            $errors[] = 'Price cannot exceed ₹100,000';
        }
        
        // Check if room number already exists
        $existingRoom = $roomModel->findByRoomNumber($data['room_number']);
        if ($existingRoom) {
            $errors[] = 'Room number already exists';
        }
        
        if (!empty($errors)) {
            $_SESSION['error'] = implode('. ', $errors);
            $this->redirect('/rooms/create');
        }
        
        if ($roomModel->create($data)) {
            $_SESSION['success'] = 'Room created successfully!';
        } else {
            $_SESSION['error'] = 'Failed to create room. Please try again.';
        }
        
        $this->redirect('/rooms');
    }
    
    public function edit($id) {
        $roomModel = new Room($this->pdo);
        $room = $roomModel->find($id);
        
        if (!$room) {
            $_SESSION['error'] = 'Room not found';
            $this->redirect('/rooms');
        }
        
        $roomTypes = $roomModel->getDistinctTypes();
        
        $this->view('rooms.edit', [
            'room' => $room,
            'roomTypes' => $roomTypes,
            'roomTypesList' => ['Single', 'Double', 'Triple', 'Dorm'],
            'statusList' => ['Available', 'Booked', 'Maintenance']
        ]);
    }
    
    public function update($id) {
        $roomModel = new Room($this->pdo);
        $existingRoom = $roomModel->find($id);
        
        if (!$existingRoom) {
            $_SESSION['error'] = 'Room not found';
            $this->redirect('/rooms');
        }
        
        $data = [
            'room_number' => trim($_POST['room_number'] ?? ''),
            'type' => $_POST['type'] ?? 'Single',
            'capacity' => (int)($_POST['capacity'] ?? 1),
            'floor' => (int)($_POST['floor'] ?? 1),
            'price' => (float)($_POST['price'] ?? 0),
            'description' => trim($_POST['description'] ?? ''),
            'status' => $_POST['status'] ?? 'Available'
        ];
        
        // Validation
        $errors = [];
        
        if (empty($data['room_number'])) {
            $errors[] = 'Room number is required';
        } elseif (!preg_match('/^[A-Za-z0-9-]+$/', $data['room_number'])) {
            $errors[] = 'Room number can only contain letters, numbers, and hyphens';
        }
        
        if ($data['capacity'] < 1 || $data['capacity'] > 10) {
            $errors[] = 'Capacity must be between 1 and 10';
        }
        
        if ($data['floor'] < 1 || $data['floor'] > 20) {
            $errors[] = 'Floor must be between 1 and 20';
        }
        
        if ($data['price'] < 0) {
            $errors[] = 'Price cannot be negative';
        } elseif ($data['price'] > 100000) {
            $errors[] = 'Price cannot exceed ₹100,000';
        }
        
        // Check if room number already exists (excluding current room)
        if ($data['room_number'] !== $existingRoom['room_number']) {
            $roomWithSameNumber = $roomModel->findByRoomNumber($data['room_number']);
            if ($roomWithSameNumber) {
                $errors[] = 'Room number already exists';
            }
        }
        
        if (!empty($errors)) {
            $_SESSION['error'] = implode('. ', $errors);
            $this->redirect("/rooms/{$id}/edit");
        }
        
        if ($roomModel->update($id, $data)) {
            $_SESSION['success'] = 'Room updated successfully!';
        } else {
            $_SESSION['error'] = 'Failed to update room. Please try again.';
        }
        
        $this->redirect('/rooms');
    }
    
    public function delete($id) {
        $roomModel = new Room($this->pdo);
        $room = $roomModel->find($id);
        
        if (!$room) {
            $_SESSION['error'] = 'Room not found';
            $this->redirect('/rooms');
        }
        
        if ($roomModel->delete($id)) {
            $_SESSION['success'] = 'Room deleted successfully!';
        } else {
            $_SESSION['error'] = 'Failed to delete room. Please try again.';
        }
        
        $this->redirect('/rooms');
    }
    
    // API endpoint for AJAX search
    public function search() {
        $roomModel = new Room($this->pdo);
        
        $query = $_GET['query'] ?? '';
        $type = $_GET['type'] ?? '';
        $status = $_GET['status'] ?? '';
        
        $conditions = [];
        $params = [];
        
        if (!empty($query)) {
            $conditions[] = "(room_number LIKE ? OR description LIKE ?)";
            $params[] = '%' . $query . '%';
            $params[] = '%' . $query . '%';
        }
        
        if (!empty($type)) {
            $conditions[] = "type = ?";
            $params[] = $type;
        }
        
        if (!empty($status)) {
            $conditions[] = "status = ?";
            $params[] = $status;
        }
        
        $sql = "SELECT * FROM rooms";
        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }
        $sql .= " LIMIT 10";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $rooms = $stmt->fetchAll();
        
        header('Content-Type: application/json');
        echo json_encode($rooms);
        exit;
    }
    
    // Quick filters - these are optional since we have search
    public function available() {
        return $this->indexWithFilter('Available');
    }
    
    public function booked() {
        return $this->indexWithFilter('Booked');
    }
    
    public function maintenance() {
        return $this->indexWithFilter('Maintenance');
    }
    
    public function single() {
        return $this->indexWithFilter('Single', 'type');
    }
    
    public function double() {
        return $this->indexWithFilter('Double', 'type');
    }
    
    public function triple() {
        return $this->indexWithFilter('Triple', 'type');
    }
    
    public function dorm() {
        return $this->indexWithFilter('Dorm', 'type');
    }
    
    // Helper method for filtered views
    private function indexWithFilter($value, $field = 'status') {
        $roomModel = new Room($this->pdo);
        $rooms = $roomModel->search([$field => $value]);
        
        $stats = [
            'total' => $roomModel->count(),
            'available' => $roomModel->countByStatus('Available'),
            'booked' => $roomModel->countByStatus('Booked'),
            'maintenance' => $roomModel->countByStatus('Maintenance')
        ];
        
        $this->view('rooms.index', [
            'rooms' => $rooms,
            'stats' => $stats,
            'searchQuery' => $value
        ]);
    }
    
    // Get room suggestions for autocomplete
    public function suggest() {
        $query = $_GET['q'] ?? '';
        
        if (strlen($query) < 2) {
            echo json_encode([]);
            exit;
        }
        
        $sql = "SELECT DISTINCT room_number FROM rooms WHERE room_number LIKE ? LIMIT 5";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['%' . $query . '%']);
        $suggestions = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
        
        header('Content-Type: application/json');
        echo json_encode($suggestions);
        exit;
    }
    
    // Get room statistics for dashboard
    public function stats() {
        $roomModel = new Room($this->pdo);
        
        $stats = [
            'total' => $roomModel->count(),
            'available' => $roomModel->countByStatus('Available'),
            'booked' => $roomModel->countByStatus('Booked'),
            'maintenance' => $roomModel->countByStatus('Maintenance'),
            'by_type' => $this->getRoomsByType(),
            'recent' => $roomModel->getRecent(5)
        ];
        
        header('Content-Type: application/json');
        echo json_encode($stats);
        exit;
    }
    
    private function getRoomsByType() {
        $sql = "SELECT type, COUNT(*) as count FROM rooms GROUP BY type";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll();
    }
    
    // Export rooms to CSV
    public function export() {
        $roomModel = new Room($this->pdo);
        $rooms = $roomModel->all();
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="rooms_' . date('Y-m-d') . '.csv"');
        
        $output = fopen('php://output', 'w');
        
        // Add CSV headers
        fputcsv($output, ['ID', 'Room Number', 'Type', 'Capacity', 'Floor', 'Price', 'Status', 'Description', 'Created At']);
        
        // Add data rows
        foreach ($rooms as $room) {
            fputcsv($output, [
                $room['id'],
                $room['room_number'],
                $room['type'],
                $room['capacity'],
                $room['floor'],
                $room['price'],
                $room['status'],
                $room['description'],
                $room['created_at']
            ]);
        }
        
        fclose($output);
        exit;
    }
}