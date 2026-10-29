<script>
    document.addEventListener('DOMContentLoaded', function() {
        let activeCell = null;
        let activeTargetPlus = null;
        let pendingCustomTextCell = null;
        let isDirty = false;
        let currentMode = 'design'; // 'design' or 'preview'

        const cells = document.querySelectorAll('.grid-cell:not(.cell-last)');
        const fields = document.querySelectorAll('.field-item');
        const targetPluses = document.querySelectorAll('.target-plus');
        const btnClear = document.getElementById('btnClearCell');
        const btnReset = document.getElementById('btnResetAll');
        const btnSave = document.getElementById('btnSaveLayout');
        const btnBack = document.getElementById('btnBack');
        const btnModeDesign = document.getElementById('btnModeDesign');
        const btnModePreview = document.getElementById('btnModePreview');
        const dirtyIndicator = document.getElementById('dirtyIndicator');
        const designTableLayout = document.getElementById('design-table-layout');

        // Load saved layout from Database or fallback to localStorage
        const layoutKey = 'header_layout_config_' + '{{ $headerTemplate->header_template_id }}';
        const dbLayout = {!! json_encode($layoutConfig ?? []) !!};
        
        let savedLayout = (dbLayout && Object.keys(dbLayout).length > 0)
            ? dbLayout
            : (JSON.parse(localStorage.getItem(layoutKey)) || {});

        // Map of all fields for lookup
        const fieldsMap = {
            @foreach($fields as $field)
                {!! json_encode($field->field_name) !!}: {
                    field_name: {!! json_encode($field->field_name) !!},
                    app_showe: {!! json_encode($field->app_showe) !!},
                    app_showt: {!! json_encode($field->app_showt ? $field->app_showt : ($field->app_showe ? $field->app_showe : $field->field_name)) !!}
                },
            @endforeach
        };

        // Helper to update dirty state
        function setDirty(state) {
            isDirty = state;
            if (dirtyIndicator) {
                if (isDirty) {
                    dirtyIndicator.classList.remove('d-none');
                } else {
                    dirtyIndicator.classList.add('d-none');
                }
            }
        }

        // Helper for Toast notification
        function showToast(type, message) {
            const toastEl = document.getElementById('actionToast');
            const toastBody = document.getElementById('actionToastBody');
            if (!toastEl || !toastBody) return;

            toastEl.className = 'toast align-items-center text-white border-0 shadow-lg rounded-3';
            if (type === 'success') {
                toastEl.classList.add('bg-success');
                toastBody.innerHTML = '<i class="bi bi-check-circle-fill fs-5"></i> ' + message;
            } else if (type === 'danger') {
                toastEl.classList.add('bg-danger');
                toastBody.innerHTML = '<i class="bi bi-exclamation-triangle-fill fs-5"></i> ' + message;
            } else {
                toastEl.classList.add('bg-primary');
                toastBody.innerHTML = '<i class="bi bi-info-circle-fill fs-5"></i> ' + message;
            }

            const toast = bootstrap.Toast.getOrCreateInstance(toastEl, { delay: 3500 });
            toast.show();
        }

        // Helper to get display label (app_showt) for a field
        function getFieldDisplayLabel(fieldId, fallbackLabel, customText) {
            if (fieldId === 'custom_text') {
                return `Custom Text: ${customText || ''}`;
            }
            if (fieldsMap[fieldId]) {
                return fieldsMap[fieldId].app_showt;
            }
            if (fallbackLabel && fallbackLabel.includes('(')) {
                const startIdx = fallbackLabel.indexOf('(');
                const endIdx = fallbackLabel.lastIndexOf(')');
                if (startIdx !== -1 && endIdx !== -1 && endIdx > startIdx) {
                    return fallbackLabel.substring(startIdx + 1, endIdx).trim();
                }
            }
            return fallbackLabel || fieldId;
        }

        // Helper to get simulated realistic mock data for preview mode
        function getSampleValue(fieldId, customText) {
            if (fieldId === 'custom_text') {
                return customText || '';
            }
            const lower = (fieldId || '').toLowerCase();
            if (lower.includes('tax') || lower.includes('id_tax')) return '0105558012345';
            if (lower.includes('branch')) return '00000';
            if (lower.includes('decl_no') || lower.includes('declaration_no')) return 'A001-0-2567-00123';
            if (lower.includes('decl_type') || lower.includes('declaration_type')) return '0 (ใบขนฯ สินค้าทั่วไป)';
            if (lower.includes('control') || lower.includes('order')) return '01 (ตรวจสอบการปฏิบัติการ)';
            if (lower.includes('exporter') || lower.includes('company')) return 'บริษัท สยามเอ็กซ์พอร์ต จำกัด (มหาชน)';
            if (lower.includes('broker') || lower.includes('agent')) return 'บริษัท ชิปปิ้งไทย เซอร์วิส จำกัด';
            if (lower.includes('vessel') || lower.includes('transport_name')) return 'EVER GIVEN V.0123E';
            if (lower.includes('by') || lower.includes('mode')) return '1 (ทางน้ำ)';
            if (lower.includes('date')) return '15/09/2026';
            if (lower.includes('port_load') || lower.includes('loading')) return 'BANGKOK (THBKK)';
            if (lower.includes('port_discharge') || lower.includes('export_port')) return 'SINGAPORE (SGSIN)';
            if (lower.includes('country')) return 'SG (SINGAPORE)';
            if (lower.includes('qty_text') || lower.includes('amount_text')) return 'ONE THOUSAND FIVE HUNDRED PACKAGES ONLY';
            if (lower.includes('qty') || lower.includes('package')) return '1,500';
            if (lower.includes('rate') || lower.includes('exchange')) return '35.5000';
            if (lower.includes('duty') || lower.includes('tax_amt')) return '0.00';
            if (lower.includes('deposit') || lower.includes('guarantee')) return '0.00';
            
            return getFieldDisplayLabel(fieldId, '');
        }

        // Normalize savedLayout entries for backward compatibility
        Object.keys(savedLayout).forEach(key => {
            const data = savedLayout[key];
            if (data && !Array.isArray(data)) {
                if (data.fieldId) {
                    savedLayout[key] = [data];
                } else {
                    savedLayout[key] = [];
                }
            }
        });

        // Merge any legacy target-plus configurations (tp-*) into parent cell
        Object.keys(savedLayout).forEach(key => {
            if (key.startsWith('tp-')) {
                const tpEl = document.getElementById(key);
                if (tpEl) {
                    const cellEl = tpEl.closest('.grid-cell');
                    if (cellEl) {
                        const cellId = cellEl.id;
                        if (!savedLayout[cellId]) {
                            savedLayout[cellId] = [];
                        }
                        const tpDataList = savedLayout[key];
                        if (Array.isArray(tpDataList)) {
                            tpDataList.forEach(item => {
                                if (!savedLayout[cellId].some(x => x.fieldId === item.fieldId)) {
                                    savedLayout[cellId].push(item);
                                }
                            });
                        }
                    }
                }
                delete savedLayout[key];
            }
        });

        // Custom Text Modal trigger
        function requestCustomText(cellEl) {
            pendingCustomTextCell = cellEl;
            const input = document.getElementById('customTextInput');
            const error = document.getElementById('customTextError');
            if (input) input.value = '';
            if (error) error.classList.add('d-none');

            const modalEl = document.getElementById('customTextModal');
            const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
            modal.show();

            setTimeout(() => {
                if (input) input.focus();
            }, 300);
        }

        // Confirm Custom Text button handler
        const btnConfirmCustom = document.getElementById('btnConfirmCustomText');
        if (btnConfirmCustom) {
            btnConfirmCustom.addEventListener('click', function() {
                const input = document.getElementById('customTextInput');
                const error = document.getElementById('customTextError');
                const val = input ? input.value.trim() : '';

                if (!val) {
                    if (error) error.classList.remove('d-none');
                    if (input) input.focus();
                    return;
                }

                if (pendingCustomTextCell) {
                    const cellId = pendingCustomTextCell.id;
                    if (!savedLayout[cellId] || !Array.isArray(savedLayout[cellId])) {
                        savedLayout[cellId] = [];
                    }

                    savedLayout[cellId].push({
                        fieldId: 'custom_text',
                        fieldName: `Custom Text: ${val}`,
                        customText: val
                    });

                    renderCellFields(pendingCustomTextCell);
                    setDirty(true);
                }

                const modalEl = document.getElementById('customTextModal');
                const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
                modal.hide();
                pendingCustomTextCell = null;
            });
        }

        // Helper to bind field to grid-cell
        function bindFieldToCell(cellEl, fieldId, fieldLabel) {
            if (fieldId === 'custom_text') {
                requestCustomText(cellEl);
                return;
            }

            const cellId = cellEl.id;
            if (!savedLayout[cellId] || !Array.isArray(savedLayout[cellId])) {
                savedLayout[cellId] = [];
            }

            // Check if already contains this field to prevent duplicates
            const exists = savedLayout[cellId].some(item => item.fieldId === fieldId);
            if (exists) {
                showToast('danger', 'ฟิลด์นี้ถูกเลือกในช่องนี้แล้ว');
                return;
            }

            savedLayout[cellId].push({
                fieldId: fieldId,
                fieldName: fieldLabel,
                customText: null
            });

            renderCellFields(cellEl);
            setDirty(true);
        }

        // Helper to render grid-cell fields (Design vs Preview mode)
        function renderCellFields(cellEl) {
            const cellId = cellEl.id;
            const fieldsList = savedLayout[cellId] || [];
            const contentEl = cellEl.querySelector('.cell-content');
            if (!contentEl) return;

            if (fieldsList.length === 0) {
                cellEl.classList.remove('cell-filled');
                contentEl.innerHTML = '';
                return;
            }

            cellEl.classList.add('cell-filled');

            if (currentMode === 'preview') {
                // Render realistic mock data for preview
                let previewHtml = '<div class="d-flex flex-wrap align-items-center justify-content-center gap-1 w-100">';
                fieldsList.forEach(field => {
                    const val = getSampleValue(field.fieldId, field.customText);
                    previewHtml += `<span class="preview-value" title="${val}">${val}</span>`;
                });
                previewHtml += '</div>';
                contentEl.innerHTML = previewHtml;
            } else {
                // Render design badges with delete button
                let html = '<div class="cell-fields-list d-flex flex-wrap align-items-center justify-content-center gap-1" style="pointer-events: auto;">';
                fieldsList.forEach((field, index) => {
                    const displayLabel = getFieldDisplayLabel(field.fieldId, field.fieldName, field.customText);
                    const badgeClass = field.fieldId === 'custom_text' ? 'tp-badge bg-warning border-warning text-dark' : 'tp-badge';
                    const badgeStyle = field.fieldId === 'custom_text' ? 'color: #000 !important;' : '';
                    html += `
                        <span class="${badgeClass}" data-field-id="${field.fieldId}" style="${badgeStyle}">
                            <span class="tp-badge-label" title="${displayLabel}" style="max-width: 95px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; display: inline-block; vertical-align: middle; ${badgeStyle}">${displayLabel}</span>
                            <i class="bi bi-x-circle-fill tp-badge-delete" data-index="${index}" title="Remove field"></i>
                        </span>
                    `;
                });
                html += '</div>';

                contentEl.innerHTML = html;

                // Bind delete button listeners
                const deleteBtns = contentEl.querySelectorAll('.tp-badge-delete');
                deleteBtns.forEach(btn => {
                    btn.addEventListener('click', function(e) {
                        e.stopPropagation();
                        const index = parseInt(this.getAttribute('data-index'), 10);
                        removeFieldFromCell(cellEl, index);
                    });
                });
            }
        }

        // Helper to remove single field from grid-cell
        function removeFieldFromCell(cellEl, index) {
            const cellId = cellEl.id;
            if (savedLayout[cellId] && Array.isArray(savedLayout[cellId])) {
                savedLayout[cellId].splice(index, 1);
                if (savedLayout[cellId].length === 0) {
                    delete savedLayout[cellId];
                }
                setDirty(true);
            }
            renderCellFields(cellEl);
        }

        // Helper to unbind field from grid-cell
        function unbindFieldFromCell(cellEl) {
            const cellId = cellEl.id;
            delete savedLayout[cellId];
            setDirty(true);
            renderCellFields(cellEl);
        }

        // Re-render all cells
        function renderAllCells() {
            cells.forEach(cell => {
                renderCellFields(cell);
            });
        }

        // Initialize Grid with saved config
        renderAllCells();

        @if(request()->has('refresh'))
            showToast('success', 'รีเฟรชข้อมูลฟิลด์จากฐานข้อมูลสดเรียบร้อยแล้ว (อัปเดต Cache ใหม่)');
        @endif

        // Mode Switching Handlers
        if (btnModeDesign && btnModePreview) {
            btnModeDesign.addEventListener('click', function() {
                currentMode = 'design';
                btnModeDesign.classList.add('mode-btn-active', 'text-white');
                btnModeDesign.classList.remove('text-secondary');
                btnModePreview.classList.remove('mode-btn-active', 'text-white');
                btnModePreview.classList.add('text-secondary');
                designTableLayout.classList.remove('preview-mode');
                renderAllCells();
            });

            btnModePreview.addEventListener('click', function() {
                currentMode = 'preview';
                btnModePreview.classList.add('mode-btn-active', 'text-white');
                btnModePreview.classList.remove('text-secondary');
                btnModeDesign.classList.remove('mode-btn-active', 'text-white');
                btnModeDesign.classList.add('text-secondary');
                designTableLayout.classList.add('preview-mode');
                if (activeCell) {
                    activeCell.classList.remove('active');
                    activeCell = null;
                }
                renderAllCells();
            });
        }

        // Cell click handler
        cells.forEach(cell => {
            cell.addEventListener('click', function() {
                if (currentMode === 'preview') return;
                if (activeCell) {
                    activeCell.classList.remove('active');
                }
                activeCell = this;
                activeCell.classList.add('active');
            });
        });

        // Field item click handler (right sidebar)
        fields.forEach(field => {
            field.addEventListener('click', function() {
                if (currentMode === 'preview') {
                    showToast('primary', 'กรุณาสลับกลับมาที่ Design Mode ก่อนทำการเลือกฟิลด์');
                    return;
                }
                if (!activeCell) {
                    showToast('danger', 'กรุณาคลิกเลือกช่องตาราง (Grid Cell) บนฟอร์มก่อน');
                    return;
                }
                const contentEl = activeCell.querySelector('.cell-content');
                if (!contentEl) {
                    showToast('danger', 'ช่องนี้ไม่สามารถผูกฟิลด์ข้อมูลได้');
                    activeCell.classList.remove('active');
                    activeCell = null;
                    return;
                }
                const fieldId = this.getAttribute('data-field');
                const fieldName = this.querySelector('span').innerText.trim();

                bindFieldToCell(activeCell, fieldId, fieldName);

                activeCell.classList.remove('active');
                activeCell = null;
            });
        });

        // Target-plus click handler (opens modal)
        targetPluses.forEach(tp => {
            tp.addEventListener('click', function(e) {
                e.stopPropagation();
                if (currentMode === 'preview') return;
                
                activeTargetPlus = this;

                const modalSearch = document.getElementById('modalFieldSearchInput');
                if (modalSearch) {
                    modalSearch.value = '';
                    modalSearch.dispatchEvent(new Event('input'));
                }

                const modalEl = document.getElementById('fieldSelectorModal');
                const modalInstance = bootstrap.Modal.getOrCreateInstance(modalEl);
                modalInstance.show();
            });
        });

        // Modal field selection handler
        const modalFieldItems = document.querySelectorAll('.modal-field-item');
        modalFieldItems.forEach(item => {
            const addBtn = item.querySelector('.select-field-btn');
            
            const selectFn = function(e) {
                e.stopPropagation();
                if (!activeTargetPlus) return;

                const fieldId = item.getAttribute('data-field');
                const fieldLabel = item.getAttribute('data-label');

                const cellEl = activeTargetPlus.closest('.grid-cell');
                if (cellEl) {
                    bindFieldToCell(cellEl, fieldId, fieldLabel);
                }

                const modalEl = document.getElementById('fieldSelectorModal');
                const modalInstance = bootstrap.Modal.getOrCreateInstance(modalEl);
                modalInstance.hide();

                activeTargetPlus = null;
            };

            item.addEventListener('click', selectFn);
            if (addBtn) {
                addBtn.addEventListener('click', selectFn);
            }
        });

        // Modal search filtering
        const modalSearchInput = document.getElementById('modalFieldSearchInput');
        if (modalSearchInput) {
            modalSearchInput.addEventListener('input', function() {
                const query = this.value.toLowerCase().trim();
                modalFieldItems.forEach(item => {
                    const text = item.innerText.toLowerCase();
                    const fieldId = item.getAttribute('data-field').toLowerCase();
                    if (text.includes(query) || fieldId.includes(query)) {
                        item.style.setProperty('display', 'flex', 'important');
                    } else {
                        item.style.setProperty('display', 'none', 'important');
                    }
                });
            });
        }

        // Clear cell
        btnClear.addEventListener('click', function() {
            if (!activeCell) {
                window.showToast('danger', 'กรุณาคลิกเลือกช่องที่ต้องการล้างข้อมูลก่อน');
                return;
            }
            unbindFieldFromCell(activeCell);
            activeCell.classList.remove('active');
            activeCell = null;
            window.showToast('primary', 'ล้างข้อมูลในช่องที่เลือกแล้ว');
        });

        // Reset grid using Global Confirmation Modal
        function doResetAll() {
            cells.forEach(cell => {
                unbindFieldFromCell(cell);
                cell.classList.remove('active');
            });

            savedLayout = {};
            activeCell = null;
            setDirty(true);
            window.showToast('primary', 'รีเซ็ตแบบฟอร์มเรียบร้อยแล้ว');
        }

        if (btnReset) {
            btnReset.addEventListener('click', function() {
                window.showConfirmModal({
                    title: 'รีเซ็ตแบบฟอร์ม',
                    message: 'คุณแน่ใจหรือไม่ว่าต้องการล้างการจัดวางฟิลด์ทั้งหมดของแบบฟอร์มนี้?',
                    type: 'danger',
                    iconClass: 'bi-arrow-counterclockwise text-danger',
                    confirmText: 'ล้างฟอร์ม',
                    onConfirm: function() {
                        doResetAll();
                    }
                });
            });
        }

        // AJAX Save Layout (Using dedicated save-layout endpoint)
        btnSave.addEventListener('click', function() {
            const originalHtml = this.innerHTML;
            this.disabled = true;
            this.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Saving...';

            // Cache in LocalStorage
            localStorage.setItem(layoutKey, JSON.stringify(savedLayout));

            const templateId = '{{ $headerTemplate->header_template_id }}';
            
            $.ajax({
                url: `/ex-declaration/header-template/${templateId}/save-layout`,
                type: "POST",
                data: {
                    _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    layout: savedLayout
                },
                success: (res) => {
                    setDirty(false);
                    window.showToast('success', res.message || 'บันทึกตำแหน่งเลย์เอาต์เรียบร้อยแล้ว!');
                    this.disabled = false;
                    this.innerHTML = originalHtml;
                },
                error: (xhr) => {
                    const errMsg = (xhr.responseJSON && xhr.responseJSON.message) 
                        ? xhr.responseJSON.message 
                        : (xhr.statusText || 'เกิดข้อผิดพลาดในการบันทึกข้อมูล');
                    window.showToast('danger', errMsg);
                    console.error(xhr.responseText);
                    this.disabled = false;
                    this.innerHTML = originalHtml;
                }
            });
        });

        // Back button navigation protection (Dirty check)
        if (btnBack) {
            btnBack.addEventListener('click', function(e) {
                if (isDirty) {
                    e.preventDefault();
                    const modalEl = document.getElementById('unsavedWarningModal');
                    const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
                    modal.show();
                }
            });
        }

        // Browser beforeunload protection
        window.addEventListener('beforeunload', function(e) {
            if (isDirty) {
                e.preventDefault();
                e.returnValue = '';
            }
        });

        // --- HTML5 Drag and Drop Handlers ---
        fields.forEach(field => {
            field.addEventListener('dragstart', function(e) {
                if (currentMode === 'preview') {
                    e.preventDefault();
                    return;
                }
                e.dataTransfer.setData('field-id', this.getAttribute('data-field'));
                e.dataTransfer.setData('field-name', this.querySelector('span').innerText.trim());
                this.style.opacity = '0.5';
            });

            field.addEventListener('dragend', function() {
                this.style.opacity = '1.0';
            });
        });

        // Make cells drop targets
        cells.forEach(cell => {
            cell.addEventListener('dragover', function(e) {
                if (currentMode === 'preview') return;
                e.preventDefault();
                const contentEl = this.querySelector('.cell-content');
                if (contentEl) {
                    this.classList.add('drag-over');
                }
            });

            cell.addEventListener('dragleave', function() {
                this.classList.remove('drag-over');
            });

            cell.addEventListener('drop', function(e) {
                if (currentMode === 'preview') return;
                e.preventDefault();
                this.classList.remove('drag-over');

                const contentEl = this.querySelector('.cell-content');
                if (!contentEl) return;

                const fieldId = e.dataTransfer.getData('field-id');
                const fieldName = e.dataTransfer.getData('field-name');

                if (!fieldId || !fieldName) return;

                bindFieldToCell(this, fieldId, fieldName);
            });
        });

        // Make target-plus elements drop targets
        targetPluses.forEach(tp => {
            tp.addEventListener('dragover', function(e) {
                if (currentMode === 'preview') return;
                e.preventDefault();
                e.stopPropagation();
                this.classList.add('drag-over');
            });

            tp.addEventListener('dragleave', function(e) {
                e.stopPropagation();
                this.classList.remove('drag-over');
            });

            tp.addEventListener('drop', function(e) {
                if (currentMode === 'preview') return;
                e.preventDefault();
                e.stopPropagation();
                this.classList.remove('drag-over');

                const fieldId = e.dataTransfer.getData('field-id');
                const fieldName = e.dataTransfer.getData('field-name');

                if (!fieldId || !fieldName) return;

                const cellEl = this.closest('.grid-cell');
                if (cellEl) {
                    bindFieldToCell(cellEl, fieldId, fieldName);
                }
            });
        });

        // Search filter for fields (right sidebar)
        const searchInput = document.getElementById('fieldSearchInput');
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                const query = this.value.toLowerCase().trim();
                fields.forEach(field => {
                    const text = field.innerText.toLowerCase();
                    const fieldId = field.getAttribute('data-field').toLowerCase();
                    if (text.includes(query) || fieldId.includes(query)) {
                        field.style.setProperty('display', 'flex', 'important');
                    } else {
                        field.style.setProperty('display', 'none', 'important');
                    }
                });
            });
        }
    });
</script>
