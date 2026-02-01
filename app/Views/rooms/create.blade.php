@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header">
        <h2>Add New Room</h2>
        <a href="<?= BASE_URL ?>/rooms" class="btn btn-small btn-secondary">
            Back to Rooms
        </a>
    </div>
    
    <form method="POST" action="<?= BASE_URL ?>/rooms/store">
        @csrf

        <div class="form-group">
            <label for="room_number">Room Number</label>
            <input type="text" id="room_number" name="room_number" required>
        </div>
        
        <div class="form-group">
            <label for="type">Room Type</label>
            <select id="type" name="type" required>
                <option value="Single">Single</option>
                <option value="Double">Double</option>
                <option value="Triple">Triple</option>
                <option value="Dorm">Dorm</option>
            </select>
        </div>
        
        <div class="form-group">
            <label for="capacity">Capacity</label>
            <input type="number" id="capacity" name="capacity" min="1" max="10" required>
        </div>
        
        <div class="form-group">
            <label for="floor">Floor</label>
            <input type="number" id="floor" name="floor" min="1" max="10" required>
        </div>
        
        <div class="form-group">
            <label for="price">Price (₹/month)</label>
            <input type="number" id="price" name="price" step="0.01" min="0" required>
        </div>
        
        <div class="form-group">
            <label for="status">Status</label>
            <select id="status" name="status">
                <option value="Available">Available</option>
                <option value="Booked">Booked</option>
                <option value="Maintenance">Maintenance</option>
            </select>
        </div>
        
        <div class="form-group">
            <label for="description">Description</label>
            <textarea id="description" name="description" rows="3"></textarea>
        </div>
        
        <div class="form-buttons">
            <button type="submit" class="btn">Create Room</button>
            <a href="<?= BASE_URL ?>/rooms" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection
