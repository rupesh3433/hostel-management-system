// public/js/search.js

document.addEventListener('DOMContentLoaded', function() {
    // Clear search filters
    const clearSearchBtn = document.querySelector('.btn-clear');
    if (clearSearchBtn) {
        clearSearchBtn.addEventListener('click', function(e) {
            e.preventDefault();
            clearSearchFilters();
        });
    }
    
    // Price range validation
    const minPriceInput = document.getElementById('min_price');
    const maxPriceInput = document.getElementById('max_price');
    
    if (minPriceInput && maxPriceInput) {
        minPriceInput.addEventListener('change', validatePriceRange);
        maxPriceInput.addEventListener('change', validatePriceRange);
    }
    
    // Initialize search form
    initializeSearchForm();
    
    // Add keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        // Ctrl/Cmd + F to focus search
        if ((e.ctrlKey || e.metaKey) && e.key === 'f') {
            e.preventDefault();
            const searchInput = document.getElementById('room_number');
            if (searchInput) {
                searchInput.focus();
            }
        }
        
        // Escape to clear search
        if (e.key === 'Escape') {
            const searchInput = document.getElementById('room_number');
            if (document.activeElement === searchInput && searchInput.value) {
                searchInput.value = '';
            }
        }
    });
});

function clearSearchFilters() {
    // Clear all form inputs
    const form = document.querySelector('.search-form');
    if (form) {
        const inputs = form.querySelectorAll('input[type="text"], input[type="number"], select');
        inputs.forEach(input => {
            if (input.type === 'select-one') {
                input.selectedIndex = 0;
            } else {
                input.value = '';
            }
        });
        
        // Submit form to show all results
        form.submit();
    }
}

function validatePriceRange() {
    const minPriceInput = document.getElementById('min_price');
    const maxPriceInput = document.getElementById('max_price');
    
    if (!minPriceInput || !maxPriceInput) return;
    
    const minPrice = parseFloat(minPriceInput.value) || 0;
    const maxPrice = parseFloat(maxPriceInput.value) || 0;
    
    if (maxPrice > 0 && minPrice > maxPrice) {
        showNotification('Minimum price cannot be greater than maximum price', 'error');
        minPriceInput.value = '';
        maxPriceInput.value = '';
        minPriceInput.focus();
    }
}

function initializeSearchForm() {
    const searchForm = document.querySelector('.search-form');
    if (!searchForm) return;
    
    // Add debounced search for real-time suggestions
    const roomNumberInput = document.getElementById('room_number');
    if (roomNumberInput) {
        let searchTimeout;
        
        roomNumberInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            
            searchTimeout = setTimeout(() => {
                if (this.value.length >= 2) {
                    suggestRoomNumbers(this.value);
                }
            }, 300);
        });
    }
    
    // Auto-submit when certain filters change
    const autoSubmitSelects = searchForm.querySelectorAll('select[name="type"], select[name="status"]');
    autoSubmitSelects.forEach(select => {
        select.addEventListener('change', function() {
            if (this.value) {
                searchForm.submit();
            }
        });
    });
}

function suggestRoomNumbers(query) {
    // This would typically make an AJAX request to get suggestions
    // For now, we'll just log it
    console.log('Looking for room numbers matching:', query);
    
    // Example AJAX implementation:
    /*
    fetch(`/hostel-management-system/public/rooms/suggest?q=${encodeURIComponent(query)}`)
        .then(response => response.json())
        .then(data => {
            showSuggestions(data);
        })
        .catch(error => {
            console.error('Error fetching suggestions:', error);
        });
    */
}

function showNotification(message, type = 'info') {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.textContent = message;
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 12px 20px;
        border-radius: 4px;
        color: white;
        font-weight: 500;
        z-index: 9999;
        animation: slideIn 0.3s ease;
    `;
    
    // Set color based on type
    const colors = {
        success: '#28a745',
        error: '#dc3545',
        warning: '#ffc107',
        info: '#17a2b8'
    };
    
    notification.style.backgroundColor = colors[type] || colors.info;
    
    // Add to document
    document.body.appendChild(notification);
    
    // Remove after 3 seconds
    setTimeout(() => {
        notification.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 300);
    }, 3000);
    
    // Add CSS animations if not already present
    if (!document.querySelector('#notification-styles')) {
        const style = document.createElement('style');
        style.id = 'notification-styles';
        style.textContent = `
            @keyframes slideIn {
                from {
                    transform: translateX(100%);
                    opacity: 0;
                }
                to {
                    transform: translateX(0);
                    opacity: 1;
                }
            }
            
            @keyframes slideOut {
                from {
                    transform: translateX(0);
                    opacity: 1;
                }
                to {
                    transform: translateX(100%);
                    opacity: 0;
                }
            }
        `;
        document.head.appendChild(style);
    }
}

// Export function for global use
window.clearSearch = clearSearchFilters;