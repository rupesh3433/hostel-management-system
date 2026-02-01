// public/js/main.js

// Global utility functions
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    initializeTooltips();
    
    // Handle form submissions with confirmation
    initializeFormConfirmations();
    
    // Add active class to current page in nav
    highlightCurrentPage();
    
    // Initialize animations
    initializeAnimations();
});

function initializeTooltips() {
    const elementsWithTooltip = document.querySelectorAll('[data-tooltip]');
    
    elementsWithTooltip.forEach(element => {
        element.addEventListener('mouseenter', function() {
            const tooltipText = this.getAttribute('data-tooltip');
            showTooltip(this, tooltipText);
        });
        
        element.addEventListener('mouseleave', hideTooltip);
    });
}

function showTooltip(element, text) {
    const tooltip = document.createElement('div');
    tooltip.className = 'tooltip';
    tooltip.textContent = text;
    tooltip.style.cssText = `
        position: absolute;
        background: #333;
        color: white;
        padding: 5px 10px;
        border-radius: 4px;
        font-size: 0.8rem;
        z-index: 1000;
        white-space: nowrap;
    `;
    
    const rect = element.getBoundingClientRect();
    tooltip.style.top = (rect.top - 30) + 'px';
    tooltip.style.left = (rect.left + (rect.width / 2)) + 'px';
    tooltip.style.transform = 'translateX(-50%)';
    
    tooltip.setAttribute('id', 'current-tooltip');
    document.body.appendChild(tooltip);
}

function hideTooltip() {
    const tooltip = document.getElementById('current-tooltip');
    if (tooltip) {
        tooltip.remove();
    }
}

function initializeFormConfirmations() {
    const deleteForms = document.querySelectorAll('form[data-confirm]');
    
    deleteForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const message = this.getAttribute('data-confirm') || 'Are you sure?';
            if (!confirm(message)) {
                e.preventDefault();
            }
        });
    });
}

function highlightCurrentPage() {
    const currentPath = window.location.pathname;
    const navLinks = document.querySelectorAll('.nav-link');
    
    navLinks.forEach(link => {
        const linkPath = link.getAttribute('href');
        if (currentPath === linkPath || currentPath.startsWith(linkPath + '/')) {
            link.classList.add('active');
        } else {
            link.classList.remove('active');
        }
    });
}

function initializeAnimations() {
    // Add animation to stat cards on load
    const statCards = document.querySelectorAll('.stat-card');
    statCards.forEach((card, index) => {
        card.style.animationDelay = (index * 0.1) + 's';
        card.classList.add('animate-fade-in');
    });
    
    // Add CSS for animations if not present
    if (!document.querySelector('#animation-styles')) {
        const style = document.createElement('style');
        style.id = 'animation-styles';
        style.textContent = `
            .animate-fade-in {
                animation: fadeIn 0.5s ease forwards;
                opacity: 0;
            }
            
            @keyframes fadeIn {
                to {
                    opacity: 1;
                }
            }
        `;
        document.head.appendChild(style);
    }
}

// AJAX helper function
function ajaxRequest(url, options = {}) {
    const defaultOptions = {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        credentials: 'same-origin'
    };
    
    const finalOptions = { ...defaultOptions, ...options };
    
    return fetch(url, finalOptions)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        });
}

// Format currency helper
function formatCurrency(amount) {
    return new Intl.NumberFormat('en-IN', {
        style: 'currency',
        currency: 'INR',
        minimumFractionDigits: 2
    }).format(amount);
}

// Export for global use
window.HostelUtils = {
    ajaxRequest,
    formatCurrency,
    showNotification: function(message, type) {
        // You can implement this or use the one from search.js
        alert(message);
    }
};