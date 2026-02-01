@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header">
        <h2>Edit Room</h2>
        <a href="<?= BASE_URL ?>/rooms" class="btn btn-small btn-secondary">
            Back to Rooms
        </a>
    </div>
    
    <form method="POST" action="<?= BASE_URL ?>/rooms/<?= $room['id'] ?>/update">
        @csrf

        <div class="form-group">
            <label for="room_number">Room Number</label>
            <input
                type="text"
                id="room_number"
                name="room_number"
                value="{{ $room['room_number'] }}"
                required
            >
        </div>
        
        <div class="form-group">
            <label for="type">Room Type</label>
            <select id="type" name="type" required>
                <option value="Single" {{ $room['type'] === 'Single' ? 'selected' : '' }}>Single</option>
                <option value="Double" {{ $room['type'] === 'Double' ? 'selected' : '' }}>Double</option>
                <option value="Triple" {{ $room['type'] === 'Triple' ? 'selected' : '' }}>Triple</option>
                <option value="Dorm" {{ $room['type'] === 'Dorm' ? 'selected' : '' }}>Dorm</option>
            </select>
        </div>
        
        <div class="form-group">
            <label for="capacity">Capacity</label>
            <input
                type="number"
                id="capacity"
                name="capacity"
                value="{{ $room['capacity'] }}"
                min="1"
                max="10"
                required
            >
        </div>
        
        <div class="form-group">
            <label for="floor">Floor</label>
            <input
                type="number"
                id="floor"
                name="floor"
                value="{{ $room['floor'] }}"
                min="1"
                max="10"
                required
            >
        </div>
        
        <div class="form-group">
            <label for="price">Price (₹/month)</label>
            <input
                type="number"
                id="price"
                name="price"
                value="{{ $room['price'] }}"
                step="0.01"
                min="0"
                required
            >
        </div>
        
        <div class="form-group">
            <label for="status">Status</label>
            <select id="status" name="status">
                <option value="Available" {{ $room['status'] === 'Available' ? 'selected' : '' }}>Available</option>
                <option value="Booked" {{ $room['status'] === 'Booked' ? 'selected' : '' }}>Booked</option>
                <option value="Maintenance" {{ $room['status'] === 'Maintenance' ? 'selected' : '' }}>Maintenance</option>
            </select>
        </div>
        
        <div class="form-group">
            <label for="description">Description</label>
            <textarea
                id="description"
                name="description"
                rows="3"
            >{{ $room['description'] }}</textarea>
        </div>
        
        <div class="form-buttons">
            <button type="submit" class="btn">Update Room</button>
            <a href="<?= BASE_URL ?>/rooms" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection
