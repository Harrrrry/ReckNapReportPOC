/**
 * Additional JavaScript functions for Report Manager
 * Handles field management, configuration saving/loading, and advanced features
 */

// Field management functions
function filterFields() {
    const searchTerm = document.getElementById('fieldSearch').value.toLowerCase();
    const fieldItems = document.querySelectorAll('#availableFields .field-item');
    
    fieldItems.forEach(item => {
        const label = item.querySelector('.fw-bold').textContent.toLowerCase();
        const tableColumn = item.querySelector('.text-muted').textContent.toLowerCase();
        
        if (label.includes(searchTerm) || tableColumn.includes(searchTerm)) {
            item.style.display = '';
        } else {
            item.style.display = 'none';
        }
    });
}

function toggleSelectAll() {
    const selectAllCheckbox = document.getElementById('selectAll');
    const fieldCheckboxes = document.querySelectorAll('#availableFields .field-checkbox');
    
    fieldCheckboxes.forEach(checkbox => {
        if (checkbox.closest('.field-item').style.display !== 'none') {
            checkbox.checked = selectAllCheckbox.checked;
            const fieldKey = checkbox.closest('.field-item').dataset.fieldKey;
            toggleFieldSelection(fieldKey, checkbox.checked);
        }
    });
}

// Add new field functionality
function showAddFieldModal() {
    const modal = new bootstrap.Modal(document.getElementById('addFieldModal'));
    
    // Reset form
    document.getElementById('addFieldForm').reset();
    
    modal.show();
}

function loadAvailableTables() {
    fetch('/report-fields/tables', {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const tableSelect = document.getElementById('tableName');
            tableSelect.innerHTML = '<option value="">Select table...</option>';
            
            Object.keys(data.data).forEach(key => {
                const option = document.createElement('option');
                option.value = key;
                option.textContent = data.data[key];
                tableSelect.appendChild(option);
            });
        }
    })
    .catch(error => {
        console.error('Error loading tables:', error);
    });
}

function loadTableColumns() {
    const tableName = document.getElementById('tableName').value;
    const columnSelect = document.getElementById('columnName');
    
    if (!tableName) {
        columnSelect.innerHTML = '<option value="">Select column...</option>';
        return;
    }
    
    fetch(`/report-fields/columns/${tableName}`, {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            columnSelect.innerHTML = '<option value="">Select column...</option>';
            
            Object.keys(data.data).forEach(key => {
                const option = document.createElement('option');
                option.value = key;
                option.textContent = data.data[key];
                columnSelect.appendChild(option);
            });
        }
    })
    .catch(error => {
        console.error('Error loading columns:', error);
    });
}

function addNewField() {
    const form = document.getElementById('addFieldForm');
    const formData = new FormData(form);
    
    // Validate form
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }
    
    const fieldData = {
        field_key: document.getElementById('fieldKey').value,
        label: document.getElementById('fieldLabel').value,
        table_name: document.getElementById('tableName').value,
        column_name: document.getElementById('columnName').value,
        data_type: document.getElementById('dataType').value,
        field_type: document.getElementById('fieldType').value
    };
    
    fetch('/report-fields/add', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify(fieldData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('Field added successfully!', 'success');
            
            // Close modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('addFieldModal'));
            modal.hide();
            
            // Reload available fields
            loadAvailableFields();
        } else {
            showAlert('Error adding field: ' + data.message, 'danger');
            
            if (data.errors) {
                console.error('Validation errors:', data.errors);
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('Failed to add field', 'danger');
    });
}

// Configuration management
function showSaveConfigModal() {
    if (selectedFieldsData.length === 0) {
        showAlert('Please select at least one field before saving configuration', 'warning');
        return;
    }
    
    const modal = new bootstrap.Modal(document.getElementById('saveConfigModal'));
    
    // Reset form
    document.getElementById('saveConfigForm').reset();
    
    modal.show();
}

function saveCurrentConfig() {
    showSaveConfigModal();
}

function saveConfiguration() {
    const form = document.getElementById('saveConfigForm');
    
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }
    
    const configData = {
        name: document.getElementById('configName').value,
        description: document.getElementById('configDescription').value,
        selected_fields: selectedFieldsData.map(f => f.field_key),
        field_order: selectedFieldsData.map(f => f.field_key)
    };
    
    fetch('/reports/save-config', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify(configData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('Configuration saved successfully!', 'success');
            
            // Close modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('saveConfigModal'));
            modal.hide();
        } else {
            showAlert('Error saving configuration: ' + data.message, 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('Failed to save configuration', 'danger');
    });
}

function loadSavedConfigs() {
    fetch('/reports/configs', {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            renderConfigsList(data.data);
            
            const modal = new bootstrap.Modal(document.getElementById('loadConfigModal'));
            modal.show();
        } else {
            showAlert('Error loading configurations: ' + data.message, 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('Failed to load configurations', 'danger');
    });
}

function renderConfigsList(configs) {
    const container = document.getElementById('configsList');
    container.innerHTML = '';
    
    if (configs.length === 0) {
        container.innerHTML = `
            <div class="text-center text-muted py-4">
                <i class="fas fa-folder-open fa-2x mb-2"></i>
                <p>No saved configurations found</p>
            </div>
        `;
        return;
    }
    
    configs.forEach(config => {
        const configElement = document.createElement('div');
        configElement.className = 'card mb-2';
        configElement.innerHTML = `
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h6 class="card-title mb-1">${config.name}</h6>
                        <p class="card-text text-muted small mb-1">${config.description || 'No description'}</p>
                        <small class="text-muted">
                            Created: ${new Date(config.created).toLocaleDateString()}
                            ${config.is_default ? '<span class="badge bg-primary ms-1">Default</span>' : ''}
                        </small>
                    </div>
                    <button class="btn btn-sm btn-primary" onclick="loadConfiguration(${config.id})">
                        <i class="fas fa-download"></i> Load
                    </button>
                </div>
            </div>
        `;
        container.appendChild(configElement);
    });
}

function loadConfiguration(configId) {
    fetch(`/reports/load-config/${configId}`, {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const config = data.data;
            
            // Clear current selection
            selectedFieldsData = [];
            
            // Load selected fields
            config.selected_fields.forEach(fieldKey => {
                const field = availableFieldsData.find(f => f.field_key === fieldKey);
                if (field) {
                    selectedFieldsData.push(field);
                }
            });
            
            // Reorder fields according to saved order
            if (config.field_order) {
                const orderedFields = [];
                config.field_order.forEach(fieldKey => {
                    const field = selectedFieldsData.find(f => f.field_key === fieldKey);
                    if (field) {
                        orderedFields.push(field);
                    }
                });
                selectedFieldsData = orderedFields;
            }
            
            // Update UI
            renderSelectedFields();
            updateFieldStates();
            updateButtonStates();
            
            showAlert(`Configuration "${config.name}" loaded successfully!`, 'success');
            
            // Close modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('loadConfigModal'));
            modal.hide();
        } else {
            showAlert('Error loading configuration: ' + data.message, 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('Failed to load configuration', 'danger');
    });
}

// Advanced features
function updateFieldOrder() {
    const selectedContainer = document.getElementById('selectedFields');
    const fieldElements = selectedContainer.querySelectorAll('.field-item');
    
    const newOrder = [];
    fieldElements.forEach(element => {
        const fieldKey = element.dataset.fieldKey;
        const field = selectedFieldsData.find(f => f.field_key === fieldKey);
        if (field) {
            newOrder.push(field);
        }
    });
    
    selectedFieldsData = newOrder;
}

function handleFieldSelection(evt) {
    const fieldKey = evt.item.dataset.fieldKey;
    const field = availableFieldsData.find(f => f.field_key === fieldKey);
    
    if (field && !selectedFieldsData.some(f => f.field_key === fieldKey)) {
        selectedFieldsData.push(field);
        renderSelectedFields();
        updateFieldStates();
        updateButtonStates();
    }
    
    // Remove the cloned element if it was dropped in the wrong place
    if (evt.item.parentNode === document.getElementById('availableFields')) {
        evt.item.remove();
    }
}

function handleFieldRemoval(evt) {
    const fieldKey = evt.item.dataset.fieldKey;
    selectedFieldsData = selectedFieldsData.filter(f => f.field_key !== fieldKey);
    renderSelectedFields();
    updateFieldStates();
    updateButtonStates();
}

// Export functions for different formats (future enhancement)
function exportToCSV() {
    // Implementation for CSV export
    showAlert('CSV export feature coming soon!', 'info');
}

function exportToPDF() {
    // Implementation for PDF export
    showAlert('PDF export feature coming soon!', 'info');
}

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    // Ctrl+S to save configuration
    if (e.ctrlKey && e.key === 's') {
        e.preventDefault();
        saveCurrentConfig();
    }
    
    // Ctrl+O to load configuration
    if (e.ctrlKey && e.key === 'o') {
        e.preventDefault();
        loadSavedConfigs();
    }
    
    // Ctrl+Enter to generate preview
    if (e.ctrlKey && e.key === 'Enter') {
        e.preventDefault();
        generatePreview();
    }
    
    // Ctrl+E to export
    if (e.ctrlKey && e.key === 'e') {
        e.preventDefault();
        exportReport();
    }
});

// Auto-save functionality (optional)
let autoSaveTimer;
function enableAutoSave() {
    clearTimeout(autoSaveTimer);
    autoSaveTimer = setTimeout(() => {
        if (selectedFieldsData.length > 0) {
            // Auto-save current selection to localStorage
            localStorage.setItem('reportAutoSave', JSON.stringify({
                selectedFields: selectedFieldsData.map(f => f.field_key),
                timestamp: new Date().toISOString()
            }));
        }
    }, 5000); // Auto-save after 5 seconds of inactivity
}

// Load auto-saved data on page load
function loadAutoSave() {
    const autoSave = localStorage.getItem('reportAutoSave');
    if (autoSave) {
        try {
            const data = JSON.parse(autoSave);
            const timeDiff = new Date() - new Date(data.timestamp);
            
            // Only restore if less than 1 hour old
            if (timeDiff < 3600000) {
                data.selectedFields.forEach(fieldKey => {
                    const field = availableFieldsData.find(f => f.field_key === fieldKey);
                    if (field && !selectedFieldsData.some(f => f.field_key === fieldKey)) {
                        selectedFieldsData.push(field);
                    }
                });
                
                if (selectedFieldsData.length > 0) {
                    renderSelectedFields();
                    updateFieldStates();
                    updateButtonStates();
                    showAlert('Previous session restored', 'info');
                }
            }
        } catch (error) {
            console.error('Error loading auto-save:', error);
        }
    }
}

// Initialize auto-save when fields are loaded
document.addEventListener('DOMContentLoaded', function() {
    // Load auto-save after a short delay to ensure fields are loaded
    setTimeout(loadAutoSave, 1000);
});

// Enable auto-save on field selection changes
function enableAutoSaveOnChange() {
    const observer = new MutationObserver(enableAutoSave);
    observer.observe(document.getElementById('selectedFields'), {
        childList: true,
        subtree: true
    });
}
