<?php

namespace App\Controllers;

require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../Models/Room.php';

use App\Models\Room;

class DashboardController extends Controller {
    
    public function index() {
        $roomModel = new Room($this->pdo);
        
        $stats = [
            'total' => $roomModel->count(),
            'available' => $roomModel->countByStatus('Available'),
            'booked' => $roomModel->countByStatus('Booked'),
            'maintenance' => $roomModel->countByStatus('Maintenance')
        ];
        
        // Pass data to view
        $this->view('dashboard.index', [
            'stats' => $stats
        ]);
    }
}