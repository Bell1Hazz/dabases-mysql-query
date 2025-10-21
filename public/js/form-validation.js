/**
 * Form Validation Handler
 */
class FormValidator {
    constructor(formId) {
        this.form = document.getElementById(formId);
        if (!this.form) return;
        
        this.init();
    }

    init() {
        // Validate on submit
        this.form.addEventListener('submit', (e) => {
            if (!this.validateForm()) {
                e.preventDefault();
            }
        });

        // Real-time validation
        this.form.querySelectorAll('input, textarea, select').forEach(field => {
            field.addEventListener('blur', () => this.validateField(field));
            field.addEventListener('input', () => {
                if (field.classList.contains('error')) {
                    this.validateField(field);
                }
            });
        });

        // Image preview & validation
        const imageInput = this.form.querySelector('input[type="file"][name="image"]');
        if (imageInput) {
            imageInput.addEventListener('change', (e) => this.validateImage(e));
        }

        // Character counter
        this.addCharacterCounters();
    }

    validateForm() {
        let isValid = true;
        const fields = this.form.querySelectorAll('input, textarea, select');
        
        fields.forEach(field => {
            if (!this.validateField(field)) {
                isValid = false;
            }
        });

        return isValid;
    }

    validateField(field) {
        const value = field.value.trim();
        const name = field.getAttribute('name');
        const required = field.hasAttribute('required');
        
        // Clear previous error
        this.clearError(field);

        // Required validation
        if (required && !value) {
            this.showError(field, `${this.getFieldLabel(name)} wajib diisi.`);
            return false;
        }

        // Specific field validations
        switch (name) {
            case 'title':
                if (value.length > 255) {
                    this.showError(field, 'Judul maksimal 255 karakter.');
                    return false;
                }
                break;

            case 'summary':
                if (value.length > 500) {
                    this.showError(field, 'Ringkasan maksimal 500 karakter.');
                    return false;
                }
                break;

            case 'content':
                if (value.length < 50) {
                    this.showError(field, 'Konten artikel minimal 50 karakter.');
                    return false;
                }
                break;

            case 'date':
                if (!this.isValidDate(value)) {
                    this.showError(field, 'Format tanggal tidak valid.');
                    return false;
                }
                break;

            case 'read_time':
                if (!value.match(/^\d+\s*(min|menit)\s*read$/i)) {
                    this.showError(field, 'Format: "5 min read" atau "10 menit read".');
                    return false;
                }
                break;
        }

        // Show success state
        field.classList.add('success');
        return true;
    }

    validateImage(event) {
        const input = event.target;
        const file = input.files[0];
        
        this.clearError(input);

        if (!file) return;

        // File type validation
        const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'];
        if (!allowedTypes.includes(file.type)) {
            this.showError(input, 'Format gambar harus JPG, PNG, JPEG, atau WEBP.');
            input.value = '';
            return false;
        }

        // File size validation (2MB)
        const maxSize = 2 * 1024 * 1024; // 2MB in bytes
        if (file.size > maxSize) {
            this.showError(input, 'Ukuran gambar maksimal 2MB.');
            input.value = '';
            return false;
        }

        // Image dimension validation (optional)
        const reader = new FileReader();
        reader.onload = (e) => {
            const img = new Image();
            img.onload = () => {
                if (img.width < 400 || img.height < 300) {
                    this.showError(input, 'Dimensi gambar minimal 400x300 pixel.');
                    input.value = '';
                }
            };
            img.src = e.target.result;
        };
        reader.readAsDataURL(file);

        return true;
    }

    isValidDate(dateString) {
        const date = new Date(dateString);
        return date instanceof Date && !isNaN(date);
    }

    getFieldLabel(name) {
        const labels = {
            'title': 'Judul artikel',
            'user_id': 'Penulis',
            'category_id': 'Kategori',
            'date': 'Tanggal publikasi',
            'summary': 'Ringkasan',
            'content': 'Konten artikel',
            'image': 'Gambar',
            'read_time': 'Estimasi waktu baca',
            'tags': 'Tags'
        };
        return labels[name] || name;
    }

    showError(field, message) {
        field.classList.remove('success');
        field.classList.add('error');
        
        const errorSpan = field.parentElement.querySelector('.form-error') || 
                         document.createElement('span');
        errorSpan.className = 'form-error';
        errorSpan.textContent = message;
        
        if (!field.parentElement.querySelector('.form-error')) {
            field.parentElement.appendChild(errorSpan);
        }
    }

    clearError(field) {
        field.classList.remove('error', 'success');
        const errorSpan = field.parentElement.querySelector('.form-error');
        if (errorSpan) {
            errorSpan.remove();
        }
    }

    addCharacterCounters() {
        // Title counter
        const titleInput = this.form.querySelector('input[name="title"]');
        if (titleInput) {
            this.addCounter(titleInput, 255);
        }

        // Summary counter
        const summaryInput = this.form.querySelector('textarea[name="summary"]');
        if (summaryInput) {
            this.addCounter(summaryInput, 500);
        }

        // Content counter
        const contentInput = this.form.querySelector('textarea[name="content"]');
        if (contentInput) {
            this.addCounter(contentInput, 5000, 50);
        }
    }

    addCounter(field, max, min = 0) {
        const counter = document.createElement('div');
        counter.className = 'character-counter';
        field.parentElement.appendChild(counter);

        const updateCounter = () => {
            const length = field.value.length;
            const remaining = max - length;
            
            counter.textContent = min > 0 
                ? `${length}/${max} karakter (minimal ${min})`
                : `${length}/${max} karakter`;
            
            if (length > max) {
                counter.style.color = '#ef4444';
            } else if (length < min) {
                counter.style.color = '#f59e0b';
            } else {
                counter.style.color = '#10b981';
            }
        };

        field.addEventListener('input', updateCounter);
        updateCounter();
    }
}

// Initialize form validation
document.addEventListener('DOMContentLoaded', function() {
    new FormValidator('articleForm');
});