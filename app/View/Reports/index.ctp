<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?> - ReckNap Reports</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- SortableJS -->
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    
    <style>
        .drag-handle {
            cursor: grab;
            color: #6c757d;
        }
        .drag-handle:active {
            cursor: grabbing;
        }
        .sortable-ghost {
            opacity: 0.4;
        }
        .field-item {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
            padding: 0.75rem;
            margin-bottom: 0.5rem;
            transition: all 0.2s ease;
        }
        .field-item:hover {
            background: #e9ecef;
            border-color: #adb5bd;
        }
        .field-item.selected {
            background: #d1ecf1;
            border-color: #bee5eb;
        }
        .report-preview {
            max-height: 400px;
            overflow-y: auto;
        }
        .loading {
            display: none;
        }
        .loading.show {
            display: block;
        }
        .stats-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        .btn-export {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            border: none;
            color: white;
        }
        .btn-export:hover {
            background: linear-gradient(135deg, #f5576c 0%, #f093fb 100%);
            color: white;
        }
        .modal-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        .table-responsive {
            border-radius: 0.375rem;
            border: 1px solid #dee2e6;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container-fluid py-4">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h1 class="h3 mb-1">🚀 ReckNap Dynamic Reports</h1>
                                <p class="text-muted mb-0">Create, customize, and export reports with drag-and-drop field selection</p>
                            </div>
                            <div>
                                <button class="btn btn-outline-primary me-2" onclick="loadSavedConfigs()">
                                    <i class="fas fa-folder-open"></i> Load Config
                                </button>
                                <button class="btn btn-outline-success" onclick="saveCurrentConfig()">
                                    <i class="fas fa-save"></i> Save Config
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Left Panel - Field Selection -->
            <div class="col-lg-4 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-primary text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0"><i class="fas fa-list"></i> Available Fields</h5>
                            <button class="btn btn-light btn-sm" onclick="showAddFieldModal()">
                                <i class="fas fa-plus"></i> Add Field
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <input type="text" class="form-control" id="fieldSearch" placeholder="Search fields..." onkeyup="filterFields()">
                        </div>
                        
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="selectAll" onchange="toggleSelectAll()">
                                <label class="form-check-label" for="selectAll">
                                    <strong>Select All</strong>
                                </label>
                            </div>
                        </div>
                        
                        <div id="availableFields" class="sortable-list">
                            <!-- Fields will be loaded here -->
                        </div>
                        
                        <div class="loading text-center" id="fieldsLoading">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-2">Loading fields...</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Middle Panel - Selected Fields -->
            <div class="col-lg-4 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-success text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0"><i class="fas fa-check-square"></i> Selected Fields</h5>
                            <span class="badge bg-light text-dark" id="selectedCount">0</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info" id="dragHint">
                            <i class="fas fa-info-circle"></i>
                            <strong>Drag to reorder</strong> - The order here will be the column order in your export
                        </div>
                        
                        <div id="selectedFields" class="sortable-list">
                            <div class="text-center text-muted py-4" id="emptyState">
                                <i class="fas fa-mouse-pointer fa-2x mb-2"></i>
                                <p>Select fields from the left panel</p>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="d-grid gap-2">
                            <button class="btn btn-primary" onclick="generatePreview()" id="previewBtn" disabled>
                                <i class="fas fa-eye"></i> Generate Preview
                            </button>
                            <button class="btn btn-export" onclick="exportReport()" id="exportBtn" disabled>
                                <i class="fas fa-download"></i> Export to Excel
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Panel - Report Preview -->
            <div class="col-lg-4 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="fas fa-table"></i> Report Preview</h5>
                    </div>
                    <div class="card-body p-0">
                        <div id="reportStats" class="stats-card p-3 d-none">
                            <div class="row text-center">
                                <div class="col-4">
                                    <div class="h4 mb-0" id="totalRecords">0</div>
                                    <small>Records</small>
                                </div>
                                <div class="col-4">
                                    <div class="h4 mb-0" id="totalFields">0</div>
                                    <small>Fields</small>
                                </div>
                                <div class="col-4">
                                    <div class="h4 mb-0" id="generatedAt">-</div>
                                    <small>Generated</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="report-preview p-3">
                            <div class="text-center text-muted py-5" id="previewEmpty">
                                <i class="fas fa-chart-bar fa-3x mb-3"></i>
                                <h5>Report Preview</h5>
                                <p>Select fields and click "Generate Preview" to see your report</p>
                            </div>
                            
                            <div class="loading text-center" id="previewLoading">
                                <div class="spinner-border text-info" role="status">
                                    <span class="visually-hidden">Generating...</span>
                                </div>
                                <p class="mt-2">Generating preview...</p>
                            </div>
                            
                            <div id="previewTable" class="d-none">
                                <div class="table-responsive">
                                    <table class="table table-sm table-striped" id="reportTable">
                                        <thead class="table-dark">
                                            <!-- Headers will be added dynamically -->
                                        </thead>
                                        <tbody>
                                            <!-- Data will be added dynamically -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Field Modal -->
    <div class="modal fade" id="addFieldModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-plus"></i> Add New Field</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="addFieldForm">
                        <div class="mb-3">
                            <label for="fieldLabel" class="form-label">Field Label *</label>
                            <input type="text" class="form-control" id="fieldLabel" required>
                        </div>
                        <div class="mb-3">
                            <label for="fieldKey" class="form-label">Field Key *</label>
                            <input type="text" class="form-control" id="fieldKey" required>
                            <div class="form-text">Auto-generated from label, or enter custom key</div>
                        </div>
                        <div class="mb-3">
                            <label for="tableName" class="form-label">Table Name *</label>
                            <select class="form-select" id="tableName" required onchange="loadTableColumns()">
                                <option value="">Select table...</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="columnName" class="form-label">Column Name *</label>
                            <select class="form-select" id="columnName" required>
                                <option value="">Select column...</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="dataType" class="form-label">Data Type *</label>
                            <select class="form-select" id="dataType" required>
                                <option value="">Select data type...</option>
                                <option value="string">String</option>
                                <option value="integer">Integer</option>
                                <option value="decimal">Decimal</option>
                                <option value="date">Date</option>
                                <option value="datetime">DateTime</option>
                                <option value="boolean">Boolean</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="fieldType" class="form-label">Field Type</label>
                            <select class="form-select" id="fieldType">
                                <option value="simple">Simple</option>
                                <option value="calculated">Calculated</option>
                                <option value="joined">Joined</option>
                                <option value="aggregated">Aggregated</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="addNewField()">
                        <i class="fas fa-plus"></i> Add Field
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Save Config Modal -->
    <div class="modal fade" id="saveConfigModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-save"></i> Save Configuration</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="saveConfigForm">
                        <div class="mb-3">
                            <label for="configName" class="form-label">Configuration Name *</label>
                            <input type="text" class="form-control" id="configName" required>
                        </div>
                        <div class="mb-3">
                            <label for="configDescription" class="form-label">Description</label>
                            <textarea class="form-control" id="configDescription" rows="3"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-success" onclick="saveConfiguration()">
                        <i class="fas fa-save"></i> Save
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Load Config Modal -->
    <div class="modal fade" id="loadConfigModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-folder-open"></i> Load Configuration</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="configsList">
                        <!-- Configurations will be loaded here -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- External JavaScript -->
    <script src="js/report-manager.js"></script>
    
    <!-- Custom JavaScript -->
    <script>
        // Global variables
        let availableFieldsData = [];
        let selectedFieldsData = [];
        let availableSortable, selectedSortable;
        
        // Initialize the application
        document.addEventListener('DOMContentLoaded', function() {
            initializeSortable();
            loadAvailableFields();
            // loadAvailableTables(); // Will be called when needed
            
            // Auto-generate field key from label
            document.getElementById('fieldLabel').addEventListener('input', function() {
                const label = this.value;
                const key = label.toLowerCase()
                    .replace(/[^a-z0-9\s]/g, '')
                    .replace(/\s+/g, '_');
                document.getElementById('fieldKey').value = key;
            });
        });
        
        // Initialize drag and drop
        function initializeSortable() {
            const availableContainer = document.getElementById('availableFields');
            const selectedContainer = document.getElementById('selectedFields');
            
            availableSortable = new Sortable(availableContainer, {
                group: {
                    name: 'fields',
                    pull: 'clone',
                    put: false
                },
                handle: '.drag-handle',
                animation: 150,
                sort: false,
                onEnd: function(evt) {
                    if (evt.to === selectedContainer) {
                        handleFieldSelection(evt);
                    }
                }
            });
            
            selectedSortable = new Sortable(selectedContainer, {
                group: {
                    name: 'fields',
                    pull: true,
                    put: true
                },
                handle: '.drag-handle',
                animation: 150,
                onAdd: function(evt) {
                    handleFieldSelection(evt);
                },
                onRemove: function(evt) {
                    handleFieldRemoval(evt);
                },
                onUpdate: function(evt) {
                    updateFieldOrder();
                }
            });
        }
        
        // Load available fields from API
        function loadAvailableFields() {
            showLoading('fieldsLoading');
            
            fetch('report-fields', {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    availableFieldsData = data.data;
                    renderAvailableFields();
                } else {
                    showAlert('Error loading fields: ' + data.message, 'danger');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('Failed to load fields', 'danger');
            })
            .finally(() => {
                hideLoading('fieldsLoading');
            });
        }
        
        // Render available fields
        function renderAvailableFields() {
            const container = document.getElementById('availableFields');
            container.innerHTML = '';
            
            availableFieldsData.forEach(field => {
                const fieldElement = createFieldElement(field, false);
                container.appendChild(fieldElement);
            });
        }
        
        // Create field element
        function createFieldElement(field, isSelected = false) {
            const div = document.createElement('div');
            div.className = 'field-item';
            div.dataset.fieldKey = field.field_key;
            
            const isCurrentlySelected = selectedFieldsData.some(f => f.field_key === field.field_key);
            
            div.innerHTML = `
                <div class="d-flex align-items-center">
                    <div class="drag-handle me-2">
                        <i class="fas fa-grip-vertical"></i>
                    </div>
                    <div class="form-check me-2">
                        <input class="form-check-input field-checkbox" type="checkbox" 
                               ${isCurrentlySelected ? 'checked' : ''} 
                               onchange="toggleFieldSelection('${field.field_key}', this.checked)">
                    </div>
                    <div class="flex-grow-1">
                        <div class="fw-bold">${field.label}</div>
                        <small class="text-muted">
                            ${field.table_name}.${field.column_name} 
                            <span class="badge bg-secondary">${field.data_type}</span>
                        </small>
                    </div>
                </div>
            `;
            
            if (isCurrentlySelected) {
                div.classList.add('selected');
            }
            
            return div;
        }
        
        // Toggle field selection
        function toggleFieldSelection(fieldKey, isSelected) {
            const field = availableFieldsData.find(f => f.field_key === fieldKey);
            if (!field) return;
            
            if (isSelected) {
                if (!selectedFieldsData.some(f => f.field_key === fieldKey)) {
                    selectedFieldsData.push(field);
                    renderSelectedFields();
                }
            } else {
                selectedFieldsData = selectedFieldsData.filter(f => f.field_key !== fieldKey);
                renderSelectedFields();
            }
            
            updateFieldStates();
            updateButtonStates();
        }
        
        // Render selected fields
        function renderSelectedFields() {
            const container = document.getElementById('selectedFields');
            const emptyState = document.getElementById('emptyState');
            
            if (selectedFieldsData.length === 0) {
                container.innerHTML = '';
                container.appendChild(emptyState);
            } else {
                container.innerHTML = '';
                selectedFieldsData.forEach(field => {
                    const fieldElement = createSelectedFieldElement(field);
                    container.appendChild(fieldElement);
                });
            }
            
            updateSelectedCount();
        }
        
        // Create selected field element
        function createSelectedFieldElement(field) {
            const div = document.createElement('div');
            div.className = 'field-item selected';
            div.dataset.fieldKey = field.field_key;
            
            div.innerHTML = `
                <div class="d-flex align-items-center">
                    <div class="drag-handle me-2">
                        <i class="fas fa-grip-vertical"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="fw-bold">${field.label}</div>
                        <small class="text-muted">
                            ${field.table_name}.${field.column_name}
                            <span class="badge bg-secondary">${field.data_type}</span>
                        </small>
                    </div>
                    <button class="btn btn-sm btn-outline-danger" onclick="removeField('${field.field_key}')">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;
            
            return div;
        }
        
        // Remove field from selection
        function removeField(fieldKey) {
            selectedFieldsData = selectedFieldsData.filter(f => f.field_key !== fieldKey);
            renderSelectedFields();
            updateFieldStates();
            updateButtonStates();
        }
        
        // Update field states in available list
        function updateFieldStates() {
            const availableContainer = document.getElementById('availableFields');
            const fieldElements = availableContainer.querySelectorAll('.field-item');
            
            fieldElements.forEach(element => {
                const fieldKey = element.dataset.fieldKey;
                const checkbox = element.querySelector('.field-checkbox');
                const isSelected = selectedFieldsData.some(f => f.field_key === fieldKey);
                
                checkbox.checked = isSelected;
                element.classList.toggle('selected', isSelected);
            });
        }
        
        // Update selected count
        function updateSelectedCount() {
            document.getElementById('selectedCount').textContent = selectedFieldsData.length;
        }
        
        // Update button states
        function updateButtonStates() {
            const hasSelection = selectedFieldsData.length > 0;
            document.getElementById('previewBtn').disabled = !hasSelection;
            document.getElementById('exportBtn').disabled = !hasSelection;
        }
        
        // Generate report preview
        function generatePreview() {
            if (selectedFieldsData.length === 0) {
                showAlert('Please select at least one field', 'warning');
                return;
            }
            
            showLoading('previewLoading');
            hideElement('previewEmpty');
            hideElement('previewTable');
            
            const selectedFields = selectedFieldsData.map(f => f.field_key);
            const fieldOrder = selectedFields; // Use current order
            
            fetch('reports/generate', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    selected: selectedFields,
                    order: fieldOrder,
                    limit: 50 // Preview limit
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    renderReportPreview(data.data);
                } else {
                    showAlert('Error generating preview: ' + data.message, 'danger');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('Failed to generate preview', 'danger');
            })
            .finally(() => {
                hideLoading('previewLoading');
            });
        }
        
        // Render report preview
        function renderReportPreview(reportData) {
            const { fields, data, stats } = reportData;
            
            // Update stats
            document.getElementById('totalRecords').textContent = stats.total_records;
            document.getElementById('totalFields').textContent = stats.fields_count;
            document.getElementById('generatedAt').textContent = new Date(stats.generated_at).toLocaleTimeString();
            showElement('reportStats');
            
            // Render table
            const table = document.getElementById('reportTable');
            const thead = table.querySelector('thead');
            const tbody = table.querySelector('tbody');
            
            // Clear existing content
            thead.innerHTML = '';
            tbody.innerHTML = '';
            
            // Create header row
            const headerRow = document.createElement('tr');
            fields.forEach(field => {
                const th = document.createElement('th');
                th.textContent = field.ReportField.label;
                headerRow.appendChild(th);
            });
            thead.appendChild(headerRow);
            
            // Create data rows
            data.forEach(record => {
                const row = document.createElement('tr');
                fields.forEach(field => {
                    const td = document.createElement('td');
                    const value = record[0][field.ReportField.field_key] || '';
                    td.textContent = formatCellValue(value, field.ReportField.data_type);
                    row.appendChild(td);
                });
                tbody.appendChild(row);
            });
            
            showElement('previewTable');
        }
        
        // Format cell value based on data type
        function formatCellValue(value, dataType) {
            if (!value) return '';
            
            switch (dataType) {
                case 'decimal':
                    return parseFloat(value).toFixed(2);
                case 'date':
                    return new Date(value).toLocaleDateString();
                case 'datetime':
                    return new Date(value).toLocaleString();
                case 'boolean':
                    return value ? 'Yes' : 'No';
                default:
                    return value;
            }
        }
        
        // Export report to Excel
        function exportReport() {
            if (selectedFieldsData.length === 0) {
                showAlert('Please select at least one field', 'warning');
                return;
            }
            
            const selectedFields = selectedFieldsData.map(f => f.field_key);
            const fieldOrder = selectedFields;
            const reportName = prompt('Enter report name:', 'Dynamic_Report') || 'Dynamic_Report';
            
            // Create form for file download
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = 'reports/export';
            form.style.display = 'none';
            
            // Add form fields
            const fields = {
                selected: JSON.stringify(selectedFields),
                order: JSON.stringify(fieldOrder),
                report_name: reportName
            };
            
            Object.keys(fields).forEach(key => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = key;
                input.value = fields[key];
                form.appendChild(input);
            });
            
            document.body.appendChild(form);
            form.submit();
            document.body.removeChild(form);
            
            showAlert('Excel export started. Download will begin shortly.', 'success');
        }
        
        // Utility functions
        function showLoading(elementId) {
            document.getElementById(elementId).classList.add('show');
        }
        
        function hideLoading(elementId) {
            document.getElementById(elementId).classList.remove('show');
        }
        
        function showElement(elementId) {
            document.getElementById(elementId).classList.remove('d-none');
        }
        
        function hideElement(elementId) {
            document.getElementById(elementId).classList.add('d-none');
        }
        
        function showAlert(message, type = 'info') {
            // Create and show Bootstrap alert
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
            alertDiv.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            
            document.body.insertBefore(alertDiv, document.body.firstChild);
            
            // Auto-remove after 5 seconds
            setTimeout(() => {
                if (alertDiv.parentNode) {
                    alertDiv.remove();
                }
            }, 5000);
        }
        
        // Additional functionality for field management, configuration saving/loading
        
        // Add missing functions
        function loadAvailableTables() {
            // This will be implemented when Add Field modal is opened
            console.log('Loading available tables...');
        }
        
        function showAddFieldModal() {
            alert('Add Field functionality will be implemented with full CakePHP setup');
        }
        
        function saveCurrentConfig() {
            alert('Save Config functionality will be implemented with full CakePHP setup');
        }
        
        function loadSavedConfigs() {
            alert('Load Config functionality will be implemented with full CakePHP setup');
        }
        
    </script>
</body>
</html>
