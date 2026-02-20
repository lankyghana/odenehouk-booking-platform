import './bootstrap';
import Alpine from 'alpinejs';

// Initialize Alpine.js
window.Alpine = Alpine;
Alpine.start();

// CSRF Token Setup for AJAX
const token = document.querySelector('meta[name="csrf-token"]');
if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
}

// Booking Time Slots Component
window.bookingSlots = function() {
    return {
        selectedDate: '',
        selectedTime: '',
        availableSlots: [],
        loading: false,
        error: null,

        async loadSlots() {
            if (!this.selectedDate) {
                this.availableSlots = [];
                return;
            }

            this.loading = true;
            this.error = null;
            this.selectedTime = '';

            try {
                const offerId = this.$el.dataset.offerId;
                const response = await fetch(`/bookings/${offerId}/available-slots?date=${this.selectedDate}`);
                
                if (!response.ok) {
                    throw new Error('Failed to load available slots');
                }

                const data = await response.json();
                this.availableSlots = data.slots;

                if (this.availableSlots.length === 0) {
                    this.error = 'No available slots for this date';
                }
            } catch (error) {
                this.error = 'Failed to load available time slots';
                console.error(error);
            } finally {
                this.loading = false;
            }
        },

        selectTime(time) {
            this.selectedTime = time;
        }
    };
};

// Notification Toast
window.showToast = function(message, type = 'success') {
    const toast = document.createElement('div');
    toast.className = `fixed top-4 right-4 px-6 py-4 rounded-lg shadow-lg z-50 animate-slide-down ${
        type === 'success' ? 'bg-green-500' :
        type === 'error' ? 'bg-red-500' :
        type === 'warning' ? 'bg-yellow-500' : 'bg-blue-500'
    } text-white`;
    toast.textContent = message;
    
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.classList.add('animate-fade-out');
        setTimeout(() => toast.remove(), 300);
    }, 3000);
};

// Confirm Dialog
window.confirmAction = function(message) {
    return confirm(message);
};

// Format Currency
window.formatCurrency = function(amount) {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD'
    }).format(amount);
};

// Format Date
window.formatDate = function(date) {
    return new Intl.DateTimeFormat('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    }).format(new Date(date));
};

// Format Time
window.formatTime = function(time) {
    const [hours, minutes] = time.split(':');
    const date = new Date();
    date.setHours(hours, minutes);
    return date.toLocaleTimeString('en-US', {
        hour: 'numeric',
        minute: '2-digit',
        hour12: true
    });
};

// Auto-hide alerts
document.addEventListener('DOMContentLoaded', function() {
    const alerts = document.querySelectorAll('.alert-auto-dismiss');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.classList.add('opacity-0', 'transition-opacity', 'duration-500');
            setTimeout(() => alert.remove(), 500);
        }, 5000);
    });
});

// Mobile Menu Toggle
window.toggleMobileMenu = function() {
    return {
        open: false,
        toggle() {
            this.open = !this.open;
        }
    };
};

// Image Preview
window.previewImage = function() {
    return {
        imageUrl: null,
        previewFile(event) {
            const file = event.target.files[0];
            if (file && file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.imageUrl = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        }
    };
};

// Form Validation Enhancement
document.addEventListener('DOMContentLoaded', function() {
    const forms = document.querySelectorAll('form[data-validate]');
    
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const requiredFields = form.querySelectorAll('[required]');
            let isValid = true;

            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    field.classList.add('border-red-500');
                    
                    // Show error message
                    let errorMsg = field.nextElementSibling;
                    if (!errorMsg || !errorMsg.classList.contains('error-message')) {
                        errorMsg = document.createElement('p');
                        errorMsg.className = 'error-message text-red-500 text-sm mt-1';
                        errorMsg.textContent = 'This field is required';
                        field.parentNode.insertBefore(errorMsg, field.nextSibling);
                    }
                } else {
                    field.classList.remove('border-red-500');
                    const errorMsg = field.nextElementSibling;
                    if (errorMsg && errorMsg.classList.contains('error-message')) {
                        errorMsg.remove();
                    }
                }
            });

            if (!isValid) {
                e.preventDefault();
                showToast('Please fill in all required fields', 'error');
            }
        });

        // Remove error on input
        form.querySelectorAll('[required]').forEach(field => {
            field.addEventListener('input', function() {
                this.classList.remove('border-red-500');
                const errorMsg = this.nextElementSibling;
                if (errorMsg && errorMsg.classList.contains('error-message')) {
                    errorMsg.remove();
                }
            });
        });
    });
});

// Loading State
window.loadingState = function() {
    return {
        loading: false,
        
        async submit(callback) {
            this.loading = true;
            try {
                await callback();
            } finally {
                this.loading = false;
            }
        }
    };
};

console.log('Booking Platform Initialized');
