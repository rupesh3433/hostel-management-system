@extends('layouts.app')

@section('content')
<style>
    /* Additional inline styles for enhanced rooms page */
    .rooms-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
        flex-wrap: wrap;
        gap: 20px;
    }
    
    .rooms-title-section h1 {
        color: white;
        font-size: 32px;
        font-weight: 700;
        margin-bottom: 8px;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    }
    
    .rooms-subtitle {
        color: rgba(255, 255, 255, 0.9);
        font-size: 16px;
        font-weight: 400;
    }
    
    .rooms-actions {
        display: flex;
        gap: 15px;
        align-items: center;
    }
    
    .rooms-actions .btn {
        padding: 12px 25px;
        font-size: 15px;
        font-weight: 600;
        border-radius: 10px;
        text-decoration: none;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 8px;
        white-space: nowrap;
    }
    
    .rooms-actions .btn-primary {
        background: linear-gradient(135deg, #10b981 0%, #34d399 100%);
        border: none;
    }
    
    .rooms-actions .btn-secondary {
        background: rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.3);
        color: white;
    }
    
    /* Enhanced search input group */
    .search-input-group:focus-within {
        box-shadow: 0 4px 25px rgba(102, 126, 234, 0.3);
        transform: translateY(-2px);
    }
    
    /* Enhanced search results info */
    .search-results-info {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 20px 30px;
        border-radius: 15px;
        margin-bottom: 30px;
        box-shadow: 0 8px 25px rgba(102, 126, 234, 0.25);
        display: flex;
        justify-content: space-between;
        align-items: center;
        animation: slideDown 0.4s ease;
    }
    
    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .results-count {
        font-weight: 700;
        font-size: 18px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .clear-search {
        color: white;
        text-decoration: none;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s;
        background: rgba(255, 255, 255, 0.15);
        padding: 10px 20px;
        border-radius: 10px;
        border: 1px solid rgba(255, 255, 255, 0.2);
    }
    
    .clear-search:hover {
        background: rgba(255, 255, 255, 0.25);
        text-decoration: none;
        transform: translateX(-3px);
    }
    
    /* Enhanced room cards */
    .room-card {
        animation: fadeInUp 0.5s ease forwards;
        opacity: 0;
    }
    
    .room-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        border-color: #667eea;
    }
    
    /* Enhanced room badge */
    .badge-available {
        background: #10b981;
    }
    
    .badge-booked {
        background: #f59e0b;
    }
    
    .badge-maintenance {
        background: #ef4444;
    }
    
    /* Responsive */
    @media (max-width: 768px) {
        .rooms-header {
            flex-direction: column;
            align-items: stretch;
        }
        
        .rooms-actions {
            flex-direction: column;
            width: 100%;
        }
        
        .rooms-actions .btn {
            width: 100%;
            justify-content: center;
        }
    }
</style>

<div class="rooms">
    <!-- Header Section -->
    <div class="rooms-header">
        <div class="rooms-title-section">
            <h1>Room Management</h1>
            <div class="rooms-subtitle">Browse, search, and manage all hostel rooms</div>
        </div>
        
        <div class="rooms-actions">
            <a href="<?= BASE_URL ?>/rooms/create" class="btn btn-primary">
                <span>➕</span>
                <span>Add New Room</span>
            </a>
            <a href="<?= BASE_URL ?>/dashboard" class="btn btn-secondary">
                <span>📊</span>
                <span>Back to Dashboard</span>
            </a>
        </div>
    </div>

    <!-- Smart Search Bar -->
    <div class="smart-search-container">
        <form method="GET" action="<?= BASE_URL ?>/rooms" class="smart-search">
            <div class="search-input-group">
                <input type="text" 
                       name="q" 
                       class="search-input" 
                       placeholder="Search rooms by number, type, status, floor, price, or description..." 
                       value="<?php echo isset($searchQuery) ? htmlspecialchars($searchQuery) : ''; ?>"
                       autocomplete="off"
                       autofocus>
                <button type="submit" class="search-btn">
                    <span>🔍</span>
                    <span>Search Rooms</span>
                </button>
            </div>
            <div class="search-hint">
                💡 Tip: Search for "101", "Single", "Available", "2nd floor", "5000", "AC room", etc.
            </div>
        </form>
    </div>

    <!-- Quick Stats & Filters -->
    <div class="quick-stats">
        <a href="<?= BASE_URL ?>/rooms" class="stat-pill <?php echo empty($searchQuery ?? '') ? 'active' : ''; ?>">
            <span>All Rooms</span>
            <span class="stat-count"><?php echo isset($stats['total']) ? $stats['total'] : 0; ?></span>
        </a>
        <a href="<?= BASE_URL ?>/rooms?q=Available" class="stat-pill <?php echo (($searchQuery ?? '') == 'Available') ? 'active' : ''; ?>">
            <span>Available</span>
            <span class="stat-count"><?php echo isset($stats['available']) ? $stats['available'] : 0; ?></span>
        </a>
        <a href="<?= BASE_URL ?>/rooms?q=Booked" class="stat-pill <?php echo (($searchQuery ?? '') == 'Booked') ? 'active' : ''; ?>">
            <span>Booked</span>
            <span class="stat-count"><?php echo isset($stats['booked']) ? $stats['booked'] : 0; ?></span>
        </a>
        <a href="<?= BASE_URL ?>/rooms?q=Maintenance" class="stat-pill <?php echo (($searchQuery ?? '') == 'Maintenance') ? 'active' : ''; ?>">
            <span>Maintenance</span>
            <span class="stat-count"><?php echo isset($stats['maintenance']) ? $stats['maintenance'] : 0; ?></span>
        </a>
        <a href="<?= BASE_URL ?>/rooms/create" class="stat-pill" style="background: #10b981; color: white;">
            <span>➕ Add New Room</span>
        </a>
    </div>

    <!-- Search Results Info -->
    <?php if(!empty($searchQuery ?? '')): ?>
    <div class="search-results-info">
        <div class="results-count">
            Found <?php echo isset($rooms) ? count($rooms) : 0; ?> room(s) for "<?php echo htmlspecialchars($searchQuery); ?>"
        </div>
        <a href="<?= BASE_URL ?>/rooms" class="clear-search">
            <span>✕</span>
            <span>Clear Search</span>
        </a>
    </div>
    <?php endif; ?>

    <!-- Rooms Grid -->
    <div class="rooms-grid">
        <?php if(isset($rooms) && is_array($rooms) && count($rooms) > 0): ?>
            <?php $i = 0; ?>
            <?php foreach($rooms as $room): ?>
            <div class="room-card" style="animation-delay: <?= $i * 0.1 ?>s">
                <!-- Status Badge -->
                <div class="room-badge badge-<?php echo strtolower($room['status']); ?>">
                    <?php echo htmlspecialchars($room['status']); ?>
                </div>
                
                <!-- Room Header -->
                <div class="room-card-header">
                    <div>
                        <div class="room-number">Room <?php echo htmlspecialchars($room['room_number']); ?></div>
                        <span class="room-type"><?php echo htmlspecialchars($room['type']); ?> Room</span>
                    </div>
                </div>
                
                <!-- Room Features -->
                <div class="room-features">
                    <div class="room-feature">
                        <span class="feature-icon">👥</span>
                        <span><?php echo htmlspecialchars($room['capacity']); ?> Person(s)</span>
                    </div>
                    <div class="room-feature">
                        <span class="feature-icon">🏢</span>
                        <span>Floor <?php echo htmlspecialchars($room['floor']); ?></span>
                    </div>
                </div>
                
                <!-- Price -->
                <div class="room-price-tag">
                    ₹<?php echo number_format($room['price'], 2); ?> / month
                </div>
                
                <!-- Description -->
                <?php if(!empty($room['description'])): ?>
                <div class="room-description">
                    <?php echo htmlspecialchars($room['description']); ?>
                </div>
                <?php endif; ?>
                
                <!-- Actions -->
                <div class="room-actions">
                    <a href="<?= BASE_URL ?>/rooms/<?= $room['id'] ?>/edit" class="action-icon-btn btn-edit">
                        <span>✏️</span>
                        <span>Edit</span>
                    </a>
                    <form method="POST" action="<?= BASE_URL ?>/rooms/<?= $room['id'] ?>/delete" style="flex: 1;">
                        @csrf
                        <button type="submit" class="action-icon-btn btn-delete" 
                                onclick="return confirm('Are you sure you want to delete room <?php echo addslashes($room['room_number']); ?>?')">
                            <span>🗑️</span>
                            <span>Delete</span>
                        </button>
                    </form>
                </div>
            </div>
            <?php $i++; ?>
            <?php endforeach; ?>
        <?php else: ?>
            <!-- Empty State -->
            <div class="empty-state">
                <div class="empty-icon">🏨</div>
                <h3>No rooms found</h3>
                <p>
                    <?php if(!empty($searchQuery ?? '')): ?>
                    No rooms match your search "<?php echo htmlspecialchars($searchQuery); ?>". Try a different search term.
                    <?php else: ?>
                    No rooms available in the system. Create your first room to get started!
                    <?php endif; ?>
                </p>
                <a href="<?= BASE_URL ?>/rooms/create" class="btn" style="width: auto; display: inline-block; padding: 12px 30px;">
                    Create New Room
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.querySelector('.search-input');
    
    // Keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        // Ctrl+K or Cmd+K to focus search
        if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
            e.preventDefault();
            if (searchInput) {
                searchInput.focus();
                searchInput.select();
            }
        }
        
        // Escape to clear search
        if (e.key === 'Escape') {
            if (searchInput && document.activeElement === searchInput) {
                if (searchInput.value) {
                    window.location.href = "<?= BASE_URL ?>/rooms";
                }
            }
        }
    });
    
    // Add click handler to clear search
    const clearSearchLink = document.querySelector('.clear-search');
    if (clearSearchLink) {
        clearSearchLink.addEventListener('click', function(e) {
            e.preventDefault();
            window.location.href = "<?= BASE_URL ?>/rooms";
        });
    }
    
    // Animate room cards on scroll
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.animationPlayState = 'running';
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);
    
    // Observe all room cards
    document.querySelectorAll('.room-card').forEach(card => {
        observer.observe(card);
    });
});
</script>
@endsection