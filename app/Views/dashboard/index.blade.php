@extends('layouts.app')

@section('content')
<style>
    /* Additional inline styles for dashboard layout */
    .dashboard-welcome {
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
        border-radius: 20px;
        padding: 30px;
        margin-bottom: 30px;
        border: 1px solid rgba(255, 255, 255, 0.2);
    }
    
    .dashboard-welcome h1 {
        color: white;
        font-size: 32px;
        margin-bottom: 10px;
        font-weight: 700;
    }
    
    .dashboard-subtitle {
        color: rgba(255, 255, 255, 0.9);
        font-size: 16px;
        margin-bottom: 5px;
    }
    
    .dashboard-date {
        color: rgba(255, 255, 255, 0.7);
        font-size: 14px;
    }
    
    .dashboard-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 30px;
        margin-bottom: 30px;
    }
    
    @media (max-width: 1024px) {
        .dashboard-grid {
            grid-template-columns: 1fr;
        }
    }
    
    /* Recent Activity Styles */
    .recent-activity-container {
        background: white;
        border-radius: 12px;
        padding: 25px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        height: 100%;
    }
    
    .recent-activity-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 2px solid #f3f4f6;
    }
    
    .recent-activity-header h2 {
        color: #333;
        font-size: 20px;
        font-weight: 600;
        margin: 0;
    }
    
    .activity-count {
        background: #667eea;
        color: white;
        padding: 4px 12px;
        border-radius: 15px;
        font-size: 12px;
        font-weight: 600;
    }
    
    .activity-list {
        list-style: none;
        padding: 0;
        margin: 0;
        max-height: 300px;
        overflow-y: auto;
    }
    
    .activity-item {
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 12px;
        background: #f9fafb;
        border-left: 4px solid #667eea;
        transition: all 0.3s ease;
    }
    
    .activity-item:hover {
        background: #f0f4ff;
        transform: translateX(5px);
    }
    
    .activity-item.success {
        border-left-color: #10b981;
        background: #f0f9f5;
    }
    
    .activity-item.warning {
        border-left-color: #f59e0b;
        background: #fefce8;
    }
    
    .activity-item.error {
        border-left-color: #ef4444;
        background: #fef2f2;
    }
    
    .activity-icon {
        font-size: 18px;
        margin-right: 10px;
        display: inline-block;
        vertical-align: middle;
    }
    
    .activity-content {
        display: inline-block;
        vertical-align: middle;
    }
    
    .activity-title {
        font-weight: 600;
        color: #1f2937;
        font-size: 14px;
        margin-bottom: 2px;
    }
    
    .activity-desc {
        color: #6b7280;
        font-size: 12px;
        line-height: 1.4;
        margin-bottom: 3px;
    }
    
    .activity-time {
        color: #9ca3af;
        font-size: 11px;
        font-weight: 500;
    }
    
    .empty-activity {
        text-align: center;
        padding: 40px 20px;
        color: #9ca3af;
    }
    
    .empty-activity-icon {
        font-size: 48px;
        margin-bottom: 15px;
        opacity: 0.3;
    }
    
    .empty-activity h4 {
        color: #6b7280;
        margin-bottom: 8px;
        font-size: 16px;
    }
    
    .empty-activity p {
        font-size: 13px;
        margin-bottom: 20px;
    }
    
    /* Custom scrollbar for activity list */
    .activity-list::-webkit-scrollbar {
        width: 6px;
    }
    
    .activity-list::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 3px;
    }
    
    .activity-list::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 3px;
    }
    
    .activity-list::-webkit-scrollbar-thumb:hover {
        background: #a1a1a1;
    }
</style>

<div class="dashboard">
    <!-- Welcome Section -->
    <div class="dashboard-welcome">
        <h1>Welcome back, {{ isset($_SESSION['user_name']) ? htmlspecialchars($_SESSION['user_name']) : 'User' }}! 👋</h1>
        <div class="dashboard-subtitle">Manage your hostel efficiently with our dashboard</div>
        <div class="dashboard-date">{{ date('l, F j, Y') }} • <span id="currentTime"></span></div>
    </div>

    <!-- Stats Section -->
    <div class="dashboard-stats">
        <div class="stat-card total">
            <div class="stat-card-icon">🏨</div>
            <h3>Total Rooms</h3>
            <p>{{ isset($stats['total']) ? $stats['total'] : 0 }}</p>
            <div class="stat-card-footer">
                <span>All Rooms</span>
                <span class="trend-up">↗</span>
            </div>
        </div>
        
        <div class="stat-card available">
            <div class="stat-card-icon">✅</div>
            <h3>Available Rooms</h3>
            <p>{{ isset($stats['available']) ? $stats['available'] : 0 }}</p>
            <div class="stat-card-footer">
                <span>Ready to book</span>
                <span class="trend-up">↗</span>
            </div>
        </div>
        
        <div class="stat-card booked">
            <div class="stat-card-icon">🔒</div>
            <h3>Booked Rooms</h3>
            <p>{{ isset($stats['booked']) ? $stats['booked'] : 0 }}</p>
            <div class="stat-card-footer">
                <span>Occupied</span>
                <span class="trend-up">↗</span>
            </div>
        </div>
        
        <div class="stat-card maintenance">
            <div class="stat-card-icon">🔧</div>
            <h3>Under Maintenance</h3>
            <p>{{ isset($stats['maintenance']) ? $stats['maintenance'] : 0 }}</p>
            <div class="stat-card-footer">
                <span>Needs attention</span>
                <span class="trend-down">↘</span>
            </div>
        </div>
    </div>

   <!-- Main Content Grid -->
   <div class="dashboard-grid">
   <!-- Left Column: Quick Actions -->
    <div>
        <div class="card">
            <div class="card-header">
                <h2>Quick Actions</h2>
                <span class="btn-small" style="background: #667eea; color: white; padding: 8px 16px; border-radius: 20px;">
                    6 Actions
                </span>
            </div>
            
            <div class="quick-actions-grid">
                <a href="<?= $_SESSION['base_url'] ?>/rooms" class="action-btn">
                    <span class="action-icon">📋</span>
                    <span>View All Rooms</span>
                    <small style="font-size: 12px; color: #6b7280; margin-top: 5px;">Browse all rooms</small>
                </a>
                
                <a href="<?= $_SESSION['base_url'] ?>/rooms/create" class="action-btn">
                    <span class="action-icon">➕</span>
                    <span>Add New Room</span>
                    <small style="font-size: 12px; color: #6b7280; margin-top: 5px;">Create new room</small>
                </a>
                
                <a href="<?= $_SESSION['base_url'] ?>/rooms?q=Available" class="action-btn btn-success">
                    <span class="action-icon">✅</span>
                    <span>Available Rooms</span>
                    <small style="font-size: 12px; color: rgba(255,255,255,0.9); margin-top: 5px;">Ready for booking</small>
                </a>
                
                <a href="<?= $_SESSION['base_url'] ?>/rooms?q=Maintenance" class="action-btn btn-warning">
                    <span class="action-icon">🔧</span>
                    <span>Maintenance Rooms</span>
                    <small style="font-size: 12px; color: rgba(0,0,0,0.7); margin-top: 5px;">Under repair</small>
                </a>
                
                <a href="<?= $_SESSION['base_url'] ?>/rooms?q=Booked" class="action-btn btn-danger">
                    <span class="action-icon">🔒</span>
                    <span>Booked Rooms</span>
                    <small style="font-size: 12px; color: rgba(255,255,255,0.9); margin-top: 5px;">Currently occupied</small>
                </a>
                
                <a href="<?= $_SESSION['base_url'] ?>/rooms?q=Single" class="action-btn btn-info">
                    <span class="action-icon">🛏️</span>
                    <span>Single Rooms</span>
                    <small style="font-size: 12px; color: rgba(255,255,255,0.9); margin-top: 5px;">Single occupancy</small>
                </a>
            </div>
        </div>
    </div>


        <!-- Right Column: Recent Activity -->
        <div class="recent-activity-container">
            <div class="recent-activity-header">
                <h2>Recent Activity</h2>
                <span class="activity-count" id="activityCount">3</span>
            </div>
            
            <div class="activity-list" id="activityList">
                @if(isset($_SESSION['success']) || isset($_SESSION['error']))
                    @if(isset($_SESSION['success']))
                    <div class="activity-item success">
                        <span class="activity-icon">✓</span>
                        <div class="activity-content">
                            <div class="activity-title">Success</div>
                            <div class="activity-desc">{{ $_SESSION['success'] }}</div>
                            <div class="activity-time">Just now</div>
                        </div>
                    </div>
                    @php unset($_SESSION['success']); @endphp
                    @endif
                    
                    @if(isset($_SESSION['error']))
                    <div class="activity-item error">
                        <span class="activity-icon">✗</span>
                        <div class="activity-content">
                            <div class="activity-title">Error</div>
                            <div class="activity-desc">{{ $_SESSION['error'] }}</div>
                            <div class="activity-time">Just now</div>
                        </div>
                    </div>
                    @php unset($_SESSION['error']); @endphp
                    @endif
                @else
                    <!-- Default activities when no session messages -->
                    <div class="activity-item">
                        <span class="activity-icon">👋</span>
                        <div class="activity-content">
                            <div class="activity-title">Welcome to Hostel Management</div>
                            <div class="activity-desc">You have successfully logged in</div>
                            <div class="activity-time">Today, {{ date('g:i A') }}</div>
                        </div>
                    </div>
                    
                    <div class="activity-item success">
                        <span class="activity-icon">📊</span>
                        <div class="activity-content">
                            <div class="activity-title">Dashboard Loaded</div>
                            <div class="activity-desc">All statistics are up to date</div>
                            <div class="activity-time">Today, {{ date('g:i A', strtotime('-5 minutes')) }}</div>
                        </div>
                    </div>
                    
                    <div class="activity-item">
                        <span class="activity-icon">⚡</span>
                        <div class="activity-content">
                            <div class="activity-title">Quick Actions Ready</div>
                            <div class="activity-desc">Use quick actions for faster navigation</div>
                            <div class="activity-time">Today, {{ date('g:i A', strtotime('-10 minutes')) }}</div>
                        </div>
                    </div>
                @endif
            </div>
            
            <!-- View All Activities Button (only show if there are activities) -->
            @if(isset($_SESSION['success']) || isset($_SESSION['error']) || true)
            <div style="text-align: center; margin-top: 20px; padding-top: 15px; border-top: 1px solid #f3f4f6;">
            <a href="<?= $_SESSION['base_url'] ?>/rooms" 
   class="btn btn-small" 
   style="width: auto; padding: 8px 20px;">
    View All Activities
</a>

            </div>
            @endif
        </div>
    </div>

<!-- Quick Stats Section -->
<div class="card" style="margin-top: 20px;">
    <div class="card-header">
        <h2>Quick Room Stats</h2>
    </div>
    
    <div class="quick-stats">
        <a href="<?= $_SESSION['base_url'] ?>/rooms" 
           class="stat-pill <?= empty($searchQuery ?? '') ? 'active' : '' ?>">
            <span>All Rooms</span>
            <span class="stat-count"><?= $stats['total'] ?? 0 ?></span>
        </a>

        <a href="<?= $_SESSION['base_url'] ?>/rooms?q=Available" 
           class="stat-pill <?= ($searchQuery ?? '') == 'Available' ? 'active' : '' ?>">
            <span>Available</span>
            <span class="stat-count"><?= $stats['available'] ?? 0 ?></span>
        </a>

        <a href="<?= $_SESSION['base_url'] ?>/rooms?q=Booked" 
           class="stat-pill <?= ($searchQuery ?? '') == 'Booked' ? 'active' : '' ?>">
            <span>Booked</span>
            <span class="stat-count"><?= $stats['booked'] ?? 0 ?></span>
        </a>

        <a href="<?= $_SESSION['base_url'] ?>/rooms?q=Maintenance" 
           class="stat-pill <?= ($searchQuery ?? '') == 'Maintenance' ? 'active' : '' ?>">
            <span>Maintenance</span>
            <span class="stat-count"><?= $stats['maintenance'] ?? 0 ?></span>
        </a>

        <a href="<?= $_SESSION['base_url'] ?>/rooms?q=Single" class="stat-pill">
            <span>Single Rooms</span>
            <span class="stat-count">--</span>
        </a>

        <a href="<?= $_SESSION['base_url'] ?>/rooms/create" 
           class="stat-pill" style="background: #10b981; color: white;">
            <span>➕ Add New</span>
        </a>
    </div>
</div>


<script>
document.addEventListener('DOMContentLoaded', function() {
    // Update current time
    function updateTime() {
        const now = new Date();
        const timeString = now.toLocaleTimeString('en-US', { 
            hour: '2-digit', 
            minute: '2-digit',
            hour12: true 
        });
        const timeElement = document.getElementById('currentTime');
        if (timeElement) {
            timeElement.textContent = timeString;
        }
    }
    
    // Initial update
    updateTime();
    
    // Update time every minute
    setInterval(updateTime, 60000);
    
    // Update activity count
    function updateActivityCount() {
        const activityItems = document.querySelectorAll('.activity-item');
        const activityCount = document.getElementById('activityCount');
        if (activityCount && activityItems.length > 0) {
            activityCount.textContent = activityItems.length;
        }
    }
    
    // Initial activity count update
    updateActivityCount();
    
    // Add hover effects to stat cards
    const statCards = document.querySelectorAll('.stat-card');
    statCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-10px)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(-5px)';
        });
    });
    
    // Add animation to elements
    const elements = document.querySelectorAll('.stat-card, .action-btn, .activity-item');
    elements.forEach((element, index) => {
        element.style.animationDelay = `${index * 0.1}s`;
        element.classList.add('animate-fade-in');
    });
    
    // Add click animation to activity items
    const activityItems = document.querySelectorAll('.activity-item');
    activityItems.forEach(item => {
        item.addEventListener('click', function() {
            this.style.transform = 'translateX(5px) scale(0.98)';
            setTimeout(() => {
                this.style.transform = 'translateX(5px) scale(1)';
            }, 150);
        });
    });
});
</script>
@endsection