<script>
document.addEventListener('DOMContentLoaded', function () {
    // 1. Accordion Header Toggle
    const headers = document.querySelectorAll('.group-accordion-header');
    headers.forEach(header => {
        header.addEventListener('click', function () {
            const targetSelector = this.getAttribute('data-target');
            const targetBody = document.querySelector(targetSelector);
            const toggleId = this.getAttribute('data-toggle-id');
            const toggleIcon = document.getElementById(`toggle_icon_${toggleId}`);

            if (targetBody) {
                const isCurrentlyHidden = targetBody.classList.contains('d-none');
                if (isCurrentlyHidden) {
                    targetBody.classList.remove('d-none');
                    if (toggleIcon) toggleIcon.textContent = '—';
                } else {
                    targetBody.classList.add('d-none');
                    if (toggleIcon) toggleIcon.textContent = '+';
                }
            }
        });
    });

    // 2. Expand All & Collapse All Controls
    const btnExpandAll = document.getElementById('btnExpandAll');
    const btnCollapseAll = document.getElementById('btnCollapseAll');

    if (btnExpandAll) {
        btnExpandAll.addEventListener('click', function () {
            document.querySelectorAll('.group-accordion-body').forEach(body => {
                body.classList.remove('d-none');
            });
            document.querySelectorAll('.group-toggle-symbol').forEach(icon => {
                icon.textContent = '—';
            });
        });
    }

    if (btnCollapseAll) {
        btnCollapseAll.addEventListener('click', function () {
            document.querySelectorAll('.group-accordion-body').forEach(body => {
                body.classList.add('d-none');
            });
            document.querySelectorAll('.group-toggle-symbol').forEach(icon => {
                icon.textContent = '+';
            });
        });
    }

    // 3. Helper to update group checked count
    function updateGroupCount(groupSlug) {
        const groupSwitches = document.querySelectorAll(`.perm-switch[data-group="${groupSlug}"]`);
        let checked = 0;
        groupSwitches.forEach(sw => {
            if (sw.checked) checked++;
        });

        const countEl = document.getElementById(`count_${groupSlug}`);
        if (countEl) {
            countEl.textContent = checked;
        }
    }

    function updateAllGroupCounts() {
        const groups = new Set();
        document.querySelectorAll('.perm-switch').forEach(sw => {
            const g = sw.getAttribute('data-group');
            if (g) groups.add(g);
        });
        groups.forEach(g => updateGroupCount(g));
    }

    // 4. Select All & Deselect All
    const btnSelectAll = document.getElementById('btnSelectAll');
    const btnDeselectAll = document.getElementById('btnDeselectAll');

    if (btnSelectAll) {
        btnSelectAll.addEventListener('click', function () {
            document.querySelectorAll('.perm-switch').forEach(sw => {
                const parentItem = sw.closest('.permission-item');
                const groupCard = sw.closest('.group-accordion-card');
                const isGroupVisible = groupCard && groupCard.style.display !== 'none';
                const isItemVisible = parentItem && parentItem.style.display !== 'none';

                if (isGroupVisible && isItemVisible) {
                    sw.checked = true;
                }
            });
            updateAllGroupCounts();

            if (typeof window.showToast === 'function') {
                window.showToast('success', 'เลือกสิทธิ์การใช้งานทั้งหมดเรียบร้อยแล้ว');
            }
        });
    }

    if (btnDeselectAll) {
        btnDeselectAll.addEventListener('click', function () {
            document.querySelectorAll('.perm-switch').forEach(sw => {
                const parentItem = sw.closest('.permission-item');
                const groupCard = sw.closest('.group-accordion-card');
                const isGroupVisible = groupCard && groupCard.style.display !== 'none';
                const isItemVisible = parentItem && parentItem.style.display !== 'none';

                if (isGroupVisible && isItemVisible) {
                    sw.checked = false;
                }
            });
            updateAllGroupCounts();

            if (typeof window.showToast === 'function') {
                window.showToast('info', 'ยกเลิกการเลือกสิทธิ์ทั้งหมดเรียบร้อยแล้ว');
            }
        });
    }

    // 5. Parent / Child Multi-level Hierarchy Sync using parent_id & DOM hierarchy
    const allSwitches = document.querySelectorAll('.perm-switch');
    allSwitches.forEach(sw => {
        sw.addEventListener('change', function () {
            const groupSlug = this.getAttribute('data-group');

            if (!this.checked) {
                // If unchecked: recursively uncheck all descendant switches in its tree-node-wrapper
                const nodeWrapper = this.closest('.tree-node-wrapper');
                if (nodeWrapper) {
                    const descendants = nodeWrapper.querySelectorAll('.perm-switch');
                    descendants.forEach(dsw => {
                        if (dsw !== this) dsw.checked = false;
                    });
                }
            } else {
                // If checked: walk up parent hierarchy using data-parent-id
                let currentParentId = this.getAttribute('data-parent-id');
                while (currentParentId) {
                    const parentSwitch = document.querySelector(`.perm-switch[data-node-id="${currentParentId}"]`);
                    if (parentSwitch) {
                        parentSwitch.checked = true;
                        currentParentId = parentSwitch.getAttribute('data-parent-id');
                    } else {
                        break;
                    }
                }
            }

            updateGroupCount(groupSlug);
        });
    });

    // 6. Real-time Search Filter
    const searchInput = document.getElementById('permissionSearch');
    if (searchInput) {
        searchInput.addEventListener('input', function () {
            const query = this.value.trim().toLowerCase();
            const groupCards = document.querySelectorAll('.group-accordion-card');

            if (!query) {
                groupCards.forEach(card => {
                    card.style.display = '';
                    const items = card.querySelectorAll('.permission-item');
                    items.forEach(item => {
                        item.style.display = '';
                    });
                    const targetBody = card.querySelector('.group-accordion-body');
                    const toggleIcon = card.querySelector('.group-toggle-symbol');
                    if (targetBody) targetBody.classList.add('d-none');
                    if (toggleIcon) toggleIcon.textContent = '+';
                });
                return;
            }

            groupCards.forEach(card => {
                const groupName = card.getAttribute('data-group-name') || '';
                const items = card.querySelectorAll('.permission-item');
                let groupMatches = groupName.includes(query);
                let hasMatchingItem = false;

                items.forEach(item => {
                    const permName = item.getAttribute('data-perm-name') || '';
                    const permTitle = item.getAttribute('data-perm-title') || '';
                    const itemMatches = groupMatches || permName.includes(query) || permTitle.includes(query);

                    if (itemMatches) {
                        item.style.display = '';
                        hasMatchingItem = true;
                    } else {
                        item.style.display = 'none';
                    }
                });

                if (groupMatches || hasMatchingItem) {
                    card.style.display = '';
                    const targetBody = card.querySelector('.group-accordion-body');
                    const toggleIcon = card.querySelector('.group-toggle-symbol');
                    if (targetBody) targetBody.classList.remove('d-none');
                    if (toggleIcon) toggleIcon.textContent = '—';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    }

    // 7. Inline Role Rename Handlers
    const btnEditRoleName = document.getElementById('btnEditRoleName');
    const inlineRenameBox = document.getElementById('inlineRenameBox');
    const roleNameInlineInput = document.getElementById('roleNameInlineInput');
    const btnSaveRoleName = document.getElementById('btnSaveRoleName');
    const btnCancelRoleName = document.getElementById('btnCancelRoleName');
    const roleNameDisplay = document.getElementById('roleNameDisplay');
    const hiddenNameInput = document.getElementById('hiddenRoleNameInput');

    if (btnEditRoleName && inlineRenameBox) {
        btnEditRoleName.addEventListener('click', function () {
            inlineRenameBox.classList.toggle('d-none');
            if (!inlineRenameBox.classList.contains('d-none')) {
                roleNameInlineInput.focus();
                roleNameInlineInput.select();
            }
        });
    }

    if (btnCancelRoleName && inlineRenameBox) {
        btnCancelRoleName.addEventListener('click', function () {
            inlineRenameBox.classList.add('d-none');
            if (hiddenNameInput && roleNameInlineInput) {
                roleNameInlineInput.value = hiddenNameInput.value;
            }
        });
    }

    if (btnSaveRoleName && inlineRenameBox) {
        btnSaveRoleName.addEventListener('click', function () {
            const newName = roleNameInlineInput.value.trim();
            if (!newName) {
                if (typeof window.showToast === 'function') {
                    window.showToast('danger', 'กรุณาระบุชื่อ Role');
                }
                return;
            }
            if (roleNameDisplay) roleNameDisplay.textContent = newName;
            if (hiddenNameInput) hiddenNameInput.value = newName;
            inlineRenameBox.classList.add('d-none');

            if (typeof window.showToast === 'function') {
                window.showToast('success', `อัปเดตชื่อ Role เป็น "${newName}" เรียบร้อยแล้ว (กดบันทึกเพื่อบันทึกการเปลี่ยนแปลงทั้งหมด)`);
            }
        });
    }
});
</script>
