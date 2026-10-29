<script>
document.addEventListener('DOMContentLoaded', function () {
    // 1. Raw Permissions Database JSON from PHP
    const permissionsData = @json($permissions ?? []);
    const existingMenusData = @json($existingMenus ?? []);

    // Helper: Convert PascalCase/camelCase string to spaced readable title
    function formatPascalCase(name) {
        if (!name || typeof name !== 'string') return '';
        let spaced = name.replace(/([a-z])([A-Z])/g, '$1 $2');
        spaced = spaced.replace(/([A-Z])([A-Z][a-z])/g, '$1 $2');
        spaced = spaced.replace(/[_\-\.]/g, ' ');
        return spaced.replace(/\s+/g, ' ').trim();
    }

    // Helper: Find permission by ID
    function getPermissionById(id) {
        if (!id) return null;
        return permissionsData.find(p => String(p.id) === String(id)) || null;
    }

    // Helper: Calculate next available order number
    function calculateNextOrder(menuId, parentId, excludeId = null) {
        if (!menuId) return 1;
        const siblings = permissionsData.filter(p => {
            if (excludeId && String(p.id) === String(excludeId)) return false;
            if (String(p.menu_id) !== String(menuId)) return false;
            if (parentId) {
                return String(p.parent_id) === String(parentId);
            } else {
                return !p.parent_id || p.parent_id === 0 || p.parent_id === '';
            }
        });

        if (siblings.length === 0) return 1;
        const maxOrder = Math.max(...siblings.map(s => parseInt(s.order) || 0));
        return maxOrder + 1;
    }

    // -------------------------------------------------------------
    // CREATE MODAL LOGIC
    // -------------------------------------------------------------
    const createModalEl = document.getElementById('createPermissionModal');
    const createModal = createModalEl ? new bootstrap.Modal(createModalEl) : null;
    const createForm = document.getElementById('createPermissionForm');
    const createMenuSelect = document.getElementById('create_perm_menu_id');
    const createNewMenuWrapper = document.getElementById('newMenuGroupInputWrapper');
    const createCustomMenuInput = document.getElementById('create_custom_menu_id');
    const createParentSelect = document.getElementById('create_perm_parent_id');
    const createOrderInput = document.getElementById('create_perm_order');
    const createNameInput = document.getElementById('create_perm_name');
    const createGuardInput = document.getElementById('create_perm_guard');

    // Live Preview Elements for Create
    const previewGroupName = document.getElementById('previewGroupName');
    const previewMenuIdBadge = document.getElementById('previewMenuIdBadge');
    const previewParentWrapper = document.getElementById('previewParentWrapper');
    const previewParentName = document.getElementById('previewParentName');
    const previewFriendlyTitle = document.getElementById('previewFriendlyTitle');
    const previewTechnicalCode = document.getElementById('previewTechnicalCode');
    const previewOrderBadge = document.getElementById('previewOrderBadge');
    const previewIndentWrapper = document.getElementById('previewIndentWrapper');

    // Populate Parent Select options for Create
    function updateCreateParentOptions(selectedParentId = null) {
        if (!createMenuSelect || !createParentSelect) return;
        const menuVal = createMenuSelect.value;
        createParentSelect.innerHTML = '<option value="">(ไม่มี - เป็นสิทธิ์ระดับบนสุด / Root Menu ของกลุ่มนี้)</option>';

        if (!menuVal || menuVal === 'NEW_GROUP') {
            return;
        }

        const menuPerms = permissionsData.filter(p => String(p.menu_id) === String(menuVal));

        // Group into hierarchy
        menuPerms.forEach(p => {
            const opt = document.createElement('option');
            opt.value = p.id;
            const friendly = formatPascalCase(p.name);
            const isRoot = !p.parent_id;
            opt.textContent = isRoot ? `📁 ${friendly} (${p.name})` : `  └─ 📂 ${friendly} (${p.name})`;
            if (selectedParentId && String(p.id) === String(selectedParentId)) {
                opt.selected = true;
            }
            createParentSelect.appendChild(opt);
        });
    }

    // Live Preview Updater for Create
    function updateCreateLivePreview() {
        if (!createNameInput) return;

        // 1. Group info
        let menuId = createMenuSelect ? createMenuSelect.value : '';
        let groupTitle = 'กลุ่มเมนูใหม่';
        if (menuId === 'NEW_GROUP') {
            menuId = createCustomMenuInput.value || '?';
            groupTitle = `กลุ่มเมนู #${menuId}`;
        } else if (menuId && existingMenusData[menuId]) {
            groupTitle = existingMenusData[menuId].name;
        }
        if (previewGroupName) previewGroupName.textContent = groupTitle;
        if (previewMenuIdBadge) previewMenuIdBadge.textContent = menuId || '-';

        // 2. Parent info
        const parentId = createParentSelect ? createParentSelect.value : '';
        if (parentId) {
            const parentPerm = getPermissionById(parentId);
            if (parentPerm && previewParentWrapper && previewParentName) {
                previewParentName.textContent = `${formatPascalCase(parentPerm.name)} (${parentPerm.name})`;
                previewParentWrapper.classList.remove('d-none');
                if (previewIndentWrapper) previewIndentWrapper.style.paddingLeft = '24px';
            }
        } else {
            if (previewParentWrapper) previewParentWrapper.classList.add('d-none');
            if (previewIndentWrapper) previewIndentWrapper.style.paddingLeft = '0px';
        }

        // 3. Name & Order
        const rawName = createNameInput.value.trim();
        const friendlyName = rawName ? formatPascalCase(rawName) : 'ชื่อสิทธิ์ที่แปลงให้อัตโนมัติ';
        if (previewFriendlyTitle) previewFriendlyTitle.textContent = friendlyName;
        if (previewTechnicalCode) previewTechnicalCode.textContent = rawName ? `(${rawName})` : '(PermissionTechnicalName)';

        const orderVal = createOrderInput ? (createOrderInput.value || '1') : '1';
        if (previewOrderBadge) previewOrderBadge.textContent = `Order #${orderVal}`;
    }

    // Menu Change Event in Create
    if (createMenuSelect) {
        createMenuSelect.addEventListener('change', function () {
            if (this.value === 'NEW_GROUP') {
                createNewMenuWrapper.classList.remove('d-none');
                createCustomMenuInput.focus();
            } else {
                createNewMenuWrapper.classList.add('d-none');
            }
            updateCreateParentOptions();
            const nextOrder = calculateNextOrder(this.value === 'NEW_GROUP' ? createCustomMenuInput.value : this.value, createParentSelect.value);
            if (createOrderInput) createOrderInput.value = nextOrder;
            updateCreateLivePreview();
        });
    }

    if (createCustomMenuInput) {
        createCustomMenuInput.addEventListener('input', function () {
            updateCreateLivePreview();
        });
    }

    if (createParentSelect) {
        createParentSelect.addEventListener('change', function () {
            const menuVal = createMenuSelect.value === 'NEW_GROUP' ? createCustomMenuInput.value : createMenuSelect.value;
            const nextOrder = calculateNextOrder(menuVal, this.value);
            if (createOrderInput) createOrderInput.value = nextOrder;
            updateCreateLivePreview();
        });
    }

    if (createOrderInput) {
        createOrderInput.addEventListener('input', updateCreateLivePreview);
    }

    if (createNameInput) {
        createNameInput.addEventListener('input', updateCreateLivePreview);
    }

    // Quick Prefix Helper Buttons (Create)
    document.querySelectorAll('.quick-prefix-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const prefix = this.getAttribute('data-prefix');
            let current = createNameInput.value.trim();
            // Remove existing prefix if matched
            const prefixes = ['Create', 'Edit', 'Delete', 'View', 'Manage', 'Export', 'Print', 'Copy'];
            for (let p of prefixes) {
                if (current.startsWith(p)) {
                    current = current.substring(p.length);
                    break;
                }
            }
            createNameInput.value = prefix + current;
            createNameInput.focus();
            updateCreateLivePreview();
        });
    });

    // Form submit verification (Handle NEW_GROUP)
    if (createForm) {
        createForm.addEventListener('submit', function (e) {
            if (createMenuSelect.value === 'NEW_GROUP') {
                const customId = createCustomMenuInput.value.trim();
                if (!customId || isNaN(customId) || parseInt(customId) <= 0) {
                    e.preventDefault();
                    if (window.showAlertModal) {
                        window.showAlertModal('กรุณากรอกรหัสกลุ่มเมนูใหม่เป็นตัวเลขจำนวนเต็มที่ถูกต้อง (เช่น 3, 4, ...)', 'ข้อผิดพลาดการกรอกข้อมูล');
                    }
                    return false;
                }
                // Append or replace menu_id value
                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = 'menu_id';
                hiddenInput.value = customId;
                createMenuSelect.removeAttribute('name');
                createForm.appendChild(hiddenInput);
            }
        });
    }

    // -------------------------------------------------------------
    // EDIT MODAL LOGIC
    // -------------------------------------------------------------
    const editModalEl = document.getElementById('editPermissionModal');
    const editModal = editModalEl ? new bootstrap.Modal(editModalEl) : null;
    const editForm = document.getElementById('editPermissionForm');
    const editMenuSelect = document.getElementById('edit_perm_menu_id');
    const editNewMenuWrapper = document.getElementById('editNewMenuGroupInputWrapper');
    const editCustomMenuInput = document.getElementById('edit_custom_menu_id');
    const editParentSelect = document.getElementById('edit_perm_parent_id');
    const editOrderInput = document.getElementById('edit_perm_order');
    const editNameInput = document.getElementById('edit_perm_name');
    const editGuardInput = document.getElementById('edit_perm_guard');

    // Live Preview Elements for Edit
    const editPreviewGroupName = document.getElementById('editPreviewGroupName');
    const editPreviewMenuIdBadge = document.getElementById('editPreviewMenuIdBadge');
    const editPreviewParentWrapper = document.getElementById('editPreviewParentWrapper');
    const editPreviewParentName = document.getElementById('editPreviewParentName');
    const editPreviewFriendlyTitle = document.getElementById('editPreviewFriendlyTitle');
    const editPreviewTechnicalCode = document.getElementById('editPreviewTechnicalCode');
    const editPreviewOrderBadge = document.getElementById('editPreviewOrderBadge');
    const editPreviewIndentWrapper = document.getElementById('editPreviewIndentWrapper');

    let currentEditingPermId = null;

    function updateEditParentOptions(selectedParentId = null) {
        if (!editMenuSelect || !editParentSelect) return;
        const menuVal = editMenuSelect.value;
        editParentSelect.innerHTML = '<option value="">(ไม่มี - เป็นสิทธิ์ระดับบนสุด / Root Menu ของกลุ่มนี้)</option>';

        if (!menuVal || menuVal === 'NEW_GROUP') return;

        const menuPerms = permissionsData.filter(p => String(p.menu_id) === String(menuVal));

        menuPerms.forEach(p => {
            // Cannot select self as parent
            if (currentEditingPermId && String(p.id) === String(currentEditingPermId)) {
                return;
            }
            const opt = document.createElement('option');
            opt.value = p.id;
            const friendly = formatPascalCase(p.name);
            const isRoot = !p.parent_id;
            opt.textContent = isRoot ? `📁 ${friendly} (${p.name})` : `  └─ 📂 ${friendly} (${p.name})`;
            if (selectedParentId && String(p.id) === String(selectedParentId)) {
                opt.selected = true;
            }
            editParentSelect.appendChild(opt);
        });
    }

    function updateEditLivePreview() {
        if (!editNameInput) return;

        // Group
        let menuId = editMenuSelect ? editMenuSelect.value : '';
        let groupTitle = 'กลุ่มเมนู';
        if (menuId === 'NEW_GROUP') {
            menuId = editCustomMenuInput.value || '?';
            groupTitle = `กลุ่มเมนู #${menuId}`;
        } else if (menuId && existingMenusData[menuId]) {
            groupTitle = existingMenusData[menuId].name;
        }
        if (editPreviewGroupName) editPreviewGroupName.textContent = groupTitle;
        if (editPreviewMenuIdBadge) editPreviewMenuIdBadge.textContent = menuId || '-';

        // Parent
        const parentId = editParentSelect ? editParentSelect.value : '';
        if (parentId) {
            const parentPerm = getPermissionById(parentId);
            if (parentPerm && editPreviewParentWrapper && editPreviewParentName) {
                editPreviewParentName.textContent = `${formatPascalCase(parentPerm.name)} (${parentPerm.name})`;
                editPreviewParentWrapper.classList.remove('d-none');
                if (editPreviewIndentWrapper) editPreviewIndentWrapper.style.paddingLeft = '24px';
            }
        } else {
            if (editPreviewParentWrapper) editPreviewParentWrapper.classList.add('d-none');
            if (editPreviewIndentWrapper) editPreviewIndentWrapper.style.paddingLeft = '0px';
        }

        // Name & Order
        const rawName = editNameInput.value.trim();
        const friendlyName = rawName ? formatPascalCase(rawName) : 'ชื่อสิทธิ์';
        if (editPreviewFriendlyTitle) editPreviewFriendlyTitle.textContent = friendlyName;
        if (editPreviewTechnicalCode) editPreviewTechnicalCode.textContent = rawName ? `(${rawName})` : '';

        const orderVal = editOrderInput ? (editOrderInput.value || '1') : '1';
        if (editPreviewOrderBadge) editPreviewOrderBadge.textContent = `Order #${orderVal}`;
    }

    if (editMenuSelect) {
        editMenuSelect.addEventListener('change', function () {
            if (this.value === 'NEW_GROUP') {
                editNewMenuWrapper.classList.remove('d-none');
                editCustomMenuInput.focus();
            } else {
                editNewMenuWrapper.classList.add('d-none');
            }
            updateEditParentOptions();
            updateEditLivePreview();
        });
    }

    if (editCustomMenuInput) editCustomMenuInput.addEventListener('input', updateEditLivePreview);
    if (editParentSelect) editParentSelect.addEventListener('change', updateEditLivePreview);
    if (editOrderInput) editOrderInput.addEventListener('input', updateEditLivePreview);
    if (editNameInput) editNameInput.addEventListener('input', updateEditLivePreview);

    // Quick Prefix Helper Buttons (Edit)
    document.querySelectorAll('.edit-quick-prefix-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const prefix = this.getAttribute('data-prefix');
            let current = editNameInput.value.trim();
            const prefixes = ['Create', 'Edit', 'Delete', 'View', 'Manage', 'Export', 'Print', 'Copy'];
            for (let p of prefixes) {
                if (current.startsWith(p)) {
                    current = current.substring(p.length);
                    break;
                }
            }
            editNameInput.value = prefix + current;
            editNameInput.focus();
            updateEditLivePreview();
        });
    });

    if (editForm) {
        editForm.addEventListener('submit', function (e) {
            if (editMenuSelect.value === 'NEW_GROUP') {
                const customId = editCustomMenuInput.value.trim();
                if (!customId || isNaN(customId) || parseInt(customId) <= 0) {
                    e.preventDefault();
                    if (window.showAlertModal) {
                        window.showAlertModal('กรุณากรอกรหัสกลุ่มเมนูใหม่เป็นตัวเลขจำนวนเต็มที่ถูกต้อง', 'ข้อผิดพลาดการกรอกข้อมูล');
                    }
                    return false;
                }
                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = 'menu_id';
                hiddenInput.value = customId;
                editMenuSelect.removeAttribute('name');
                editForm.appendChild(hiddenInput);
            }
        });
    }

    // -------------------------------------------------------------
    // BUTTON ACTIONS: QUICK ADD, EDIT, DELETE
    // -------------------------------------------------------------

    // 1. Quick Add to specific menu group
    document.querySelectorAll('.quick-add-to-group-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const menuId = this.getAttribute('data-menu-id');
            if (createMenuSelect) {
                createMenuSelect.value = menuId;
                createNewMenuWrapper.classList.add('d-none');
                updateCreateParentOptions();
                if (createParentSelect) createParentSelect.value = '';
                const nextOrder = calculateNextOrder(menuId, null);
                if (createOrderInput) createOrderInput.value = nextOrder;
                if (createNameInput) createNameInput.value = '';
                updateCreateLivePreview();
            }
            if (createModal) createModal.show();
        });
    });

    // 2. Quick Add sub-permission under parent node
    document.querySelectorAll('.quick-add-sub-perm-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const menuId = this.getAttribute('data-menu-id');
            const parentId = this.getAttribute('data-parent-id');
            if (createMenuSelect) {
                createMenuSelect.value = menuId;
                createNewMenuWrapper.classList.add('d-none');
                updateCreateParentOptions(parentId);
                const nextOrder = calculateNextOrder(menuId, parentId);
                if (createOrderInput) createOrderInput.value = nextOrder;
                if (createNameInput) createNameInput.value = '';
                updateCreateLivePreview();
            }
            if (createModal) createModal.show();
        });
    });

    // 3. Edit Permission Button Click
    document.querySelectorAll('.edit-perm-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const id = this.getAttribute('data-id');
            const name = this.getAttribute('data-name');
            const guard = this.getAttribute('data-guard');
            const menuId = this.getAttribute('data-menu-id');
            const parentId = this.getAttribute('data-parent-id');
            const order = this.getAttribute('data-order');

            currentEditingPermId = id;
            if (editForm) editForm.action = `/permissions/${id}`;
            if (editNameInput) editNameInput.value = name;
            if (editGuardInput) editGuardInput.value = guard || 'web';
            if (editOrderInput) editOrderInput.value = order || 1;

            if (editMenuSelect) {
                if (menuId) {
                    editMenuSelect.value = menuId;
                    editNewMenuWrapper.classList.add('d-none');
                } else {
                    editMenuSelect.value = '';
                }
                updateEditParentOptions(parentId);
            }

            updateEditLivePreview();
            if (editModal) editModal.show();
        });
    });

    // 4. Delete Permission Button with Global Confirm Modal Standard
    document.querySelectorAll('.delete-perm-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const id = this.getAttribute('data-id');
            const name = this.getAttribute('data-name');
            const title = this.getAttribute('data-title');
            const hasChildren = this.getAttribute('data-has-children') === '1';

            let message = `คุณแน่ใจหรือไม่ว่าต้องการลบสิทธิ์ <strong>"${title}"</strong> (<code>${name}</code>)?`;
            if (hasChildren) {
                message += `<br><br><span class="text-warning"><i class="bi bi-exclamation-triangle me-1"></i><strong>หมายเหตุ:</strong> สิทธิ์นี้มีสิทธิ์ย่อยอยู่ภายใต้ การลบจะทำการปรับย้ายสิทธิ์ย่อยให้ขึ้นตรงกับสิทธิ์แม่ระดับถัดไปโดยอัตโนมัติ</span>`;
            }

            if (window.showConfirmModal) {
                window.showConfirmModal({
                    title: 'ยืนยันการลบสิทธิ์ (Delete Permission)',
                    message: message,
                    confirmText: 'ยืนยันลบสิทธิ์',
                    confirmBtnClass: 'btn-danger px-4 rounded-pill',
                    onConfirm: function () {
                        // Submit Delete form dynamically
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = `/permissions/${id}`;

                        const csrfInput = document.createElement('input');
                        csrfInput.type = 'hidden';
                        csrfInput.name = '_token';
                        csrfInput.value = '{{ csrf_token() }}';
                        form.appendChild(csrfInput);

                        const methodInput = document.createElement('input');
                        methodInput.type = 'hidden';
                        methodInput.name = '_method';
                        methodInput.value = 'DELETE';
                        form.appendChild(methodInput);

                        document.body.appendChild(form);
                        form.submit();
                    }
                });
            } else {
                // Fallback (though global modal is defined)
                const deleteModalHtml = `
                    <div class="modal fade" id="tempDeleteModal" tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered modal-sm">
                            <div class="modal-content border-0 shadow">
                                <div class="modal-body text-center p-4">
                                    <div class="text-danger mb-3"><i class="bi bi-exclamation-circle fs-1"></i></div>
                                    <h6 class="fw-bold">ยืนยันลบสิทธิ์?</h6>
                                    <p class="small text-muted mb-4">${name}</p>
                                    <form action="/permissions/${id}" method="POST">
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        <input type="hidden" name="_method" value="DELETE">
                                        <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">ยกเลิก</button>
                                        <button type="submit" class="btn btn-sm btn-danger px-3">ลบ</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>`;
                document.body.insertAdjacentHTML('beforeend', deleteModalHtml);
                const tempModal = new bootstrap.Modal(document.getElementById('tempDeleteModal'));
                tempModal.show();
            }
        });
    });

    // -------------------------------------------------------------
    // EXPAND / COLLAPSE ALL MENU GROUPS
    // -------------------------------------------------------------
    const toggleAllBtn = document.getElementById('btnToggleAllMenuGroups');
    const iconToggleAll = document.getElementById('iconToggleAll');
    const textToggleAll = document.getElementById('textToggleAll');
    let isAllExpanded = false;

    if (toggleAllBtn) {
        toggleAllBtn.addEventListener('click', function () {
            isAllExpanded = !isAllExpanded;
            const collapseElements = document.querySelectorAll('#permissionGroupsContainer .collapse');

            collapseElements.forEach(el => {
                const bsCollapse = bootstrap.Collapse.getOrCreateInstance(el, { toggle: false });
                if (isAllExpanded) {
                    bsCollapse.show();
                } else {
                    bsCollapse.hide();
                }
            });

            // Update Toggle Button Visuals
            if (isAllExpanded) {
                if (iconToggleAll) iconToggleAll.className = 'bi bi-arrows-collapse me-1';
                if (textToggleAll) textToggleAll.textContent = 'ยุบทั้งหมด';
            } else {
                if (iconToggleAll) iconToggleAll.className = 'bi bi-arrows-expand me-1';
                if (textToggleAll) textToggleAll.textContent = 'ขยายทั้งหมด';
            }
        });
    }

    // -------------------------------------------------------------
    // SEARCH / FILTER PERMISSIONS REAL-TIME
    // -------------------------------------------------------------
    const filterInput = document.getElementById('filterPermissionInput');
    if (filterInput) {
        filterInput.addEventListener('input', function () {
            const query = this.value.toLowerCase().trim();
            const groupCards = document.querySelectorAll('.perm-group-card-wrapper');

            groupCards.forEach(groupCard => {
                let anyVisibleInGroup = false;
                const rows = groupCard.querySelectorAll('.perm-table-row');

                rows.forEach(row => {
                    const name = row.getAttribute('data-perm-name') || '';
                    const title = row.getAttribute('data-perm-title') || '';
                    if (!query || name.includes(query) || title.includes(query)) {
                        row.classList.remove('d-none');
                        anyVisibleInGroup = true;
                    } else {
                        row.classList.add('d-none');
                    }
                });

                const collapseEl = groupCard.querySelector('.collapse');
                if (anyVisibleInGroup) {
                    groupCard.classList.remove('d-none');
                    // ถ้ามีการค้นหา ให้ auto-expand กลุ่มที่พบผลลัพธ์ทันที
                    if (query && collapseEl) {
                        const bsCollapse = bootstrap.Collapse.getOrCreateInstance(collapseEl, { toggle: false });
                        bsCollapse.show();
                    }
                } else {
                    groupCard.classList.add('d-none');
                }

                // ถ้าล้างช่องค้นหา และไม่ได้กดขยายทั้งหมดไว้ ให้พับกลุ่มกลับตามค่าเริ่มต้น
                if (!query && !isAllExpanded && collapseEl) {
                    const bsCollapse = bootstrap.Collapse.getOrCreateInstance(collapseEl, { toggle: false });
                    bsCollapse.hide();
                }
            });
        });
    }

    // Default trigger preview on open
    const openCreateBtn = document.getElementById('btnOpenCreatePermModal');
    if (openCreateBtn) {
        openCreateBtn.addEventListener('click', function () {
            updateCreateParentOptions();
            updateCreateLivePreview();
        });
    }
});
</script>
