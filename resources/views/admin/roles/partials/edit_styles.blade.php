<style>
/* Edit Role Theme & Tree Styles */
:root {
    --tree-line-color: #cbd5e1;
    --tree-accent-color: #00a67e;
    --tree-accent-hover: #008f6c;
    --card-border-color: #e5e7eb;
}

/* Page Topbar & Breadcrumb */
.role-page-header {
    margin-bottom: 1.25rem;
}

.role-breadcrumb {
    font-size: 13px;
    color: #64748b;
}

.role-breadcrumb a {
    color: #64748b;
    text-decoration: none;
    transition: color 0.15s;
}

.role-breadcrumb a:hover {
    color: #0284c7;
}

/* Main Role Card Wrapper */
.role-edit-card {
    background: #ffffff;
    border: 1px solid var(--card-border-color);
    border-radius: 12px;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
    overflow: hidden;
    margin-bottom: 1.5rem;
}

/* Header & Action Toolbar */
.role-card-header {
    padding: 1.25rem 1.5rem;
    border-bottom: 1px solid #f1f5f9;
    background: #ffffff;
}

.role-title-icon {
    font-size: 22px;
    color: #0284c7;
}

.role-title-text {
    font-size: 16px;
    font-weight: 700;
    color: #1e293b;
    margin-bottom: 2px;
}

.role-subtitle-text {
    font-size: 13px;
    color: #64748b;
}

/* Toolbar Buttons */
.role-toolbar {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
}

.search-input-box {
    position: relative;
    width: 210px;
}

.search-input-box .form-control {
    font-size: 13px;
    padding-left: 32px;
    border-radius: 6px;
    border: 1px solid #cbd5e1;
    height: 33px;
    transition: all 0.15s;
}

.search-input-box .form-control:focus {
    border-color: #0284c7;
    box-shadow: 0 0 0 2px rgba(2, 132, 199, 0.15);
}

.search-input-box .search-icon {
    position: absolute;
    left: 10px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 13px;
    color: #94a3b8;
    pointer-events: none;
}

.btn-toolbar-select-all {
    background-color: #e0f2fe;
    color: #0284c7;
    border: 1px solid transparent;
    font-size: 12.5px;
    font-weight: 500;
    padding: 5px 12px;
    border-radius: 6px;
    height: 33px;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    transition: all 0.15s;
}

.btn-toolbar-select-all:hover {
    background-color: #bae6fd;
    color: #0369a1;
}

.btn-toolbar-deselect-all {
    background-color: #fee2e2;
    color: #ef4444;
    border: 1px solid transparent;
    font-size: 12.5px;
    font-weight: 500;
    padding: 5px 12px;
    border-radius: 6px;
    height: 33px;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    transition: all 0.15s;
}

.btn-toolbar-deselect-all:hover {
    background-color: #fecaca;
    color: #dc2626;
}

.btn-toolbar-icon {
    background-color: #ffffff;
    border: 1px solid #e2e8f0;
    color: #64748b;
    border-radius: 6px;
    width: 33px;
    height: 33px;
    padding: 0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    transition: all 0.15s;
}

.btn-toolbar-icon:hover {
    background-color: #f8fafc;
    color: #334155;
    border-color: #cbd5e1;
}

/* Accordion Group Cards */
.groups-container {
    padding: 1.25rem 1.5rem;
}

.group-accordion-card {
    border: 1px solid var(--card-border-color);
    border-radius: 8px;
    margin-bottom: 10px;
    background-color: #ffffff;
    transition: border-color 0.15s, box-shadow 0.15s;
}

.group-accordion-card:hover {
    border-color: #cbd5e1;
}

.group-accordion-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 18px;
    cursor: pointer;
    user-select: none;
    border-radius: 8px;
    background-color: #ffffff;
    transition: background-color 0.15s;
}

.group-accordion-header:hover {
    background-color: #f8fafc;
}

.group-folder-icon {
    font-size: 18px;
    color: #0284c7;
    margin-right: 10px;
}

.group-name-title {
    font-size: 14.5px;
    font-weight: 600;
    color: #1e293b;
}

.group-badge {
    background-color: var(--tree-accent-color);
    color: #ffffff;
    font-size: 12px;
    font-weight: 500;
    padding: 3px 10px;
    border-radius: 50rem;
    letter-spacing: 0.2px;
    display: inline-flex;
    align-items: center;
}

.group-toggle-symbol {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 20px;
    height: 20px;
    font-size: 16px;
    font-weight: 600;
    color: #0284c7;
    margin-left: 10px;
}

/* Group Inner Tree Box */
.group-accordion-body {
    padding: 14px 18px 18px 18px;
    border-top: 1px solid #f1f5f9;
    background-color: #fafbfc;
    border-bottom-left-radius: 8px;
    border-bottom-right-radius: 8px;
}

.group-tree-inner-box {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 16px 20px;
}

/* Tree Structure & Connectors */
.tree-node-wrapper {
    position: relative;
    padding: 2px 0;
}

.tree-item-row {
    position: relative;
    padding: 4px 0;
    display: flex;
    align-items: center;
}

.tree-children-container {
    position: relative;
    margin-left: 14px;
    padding-left: 24px;
    padding-top: 4px;
}

/* Vertical line connecting children down to last child */
.tree-children-container::before {
    content: '';
    position: absolute;
    left: 0;
    top: -4px;
    bottom: 14px;
    width: 1.5px;
    background-color: var(--tree-line-color);
}

/* Horizontal branch line for each child inside container */
.tree-children-container > .tree-node-wrapper > .tree-item-row::before {
    content: '';
    position: absolute;
    left: -24px;
    top: 50%;
    width: 22px;
    height: 1.5px;
    background-color: var(--tree-line-color);
}

/* Switch Styles */
.tree-switch .form-check-input {
    cursor: pointer;
    width: 2.3em;
    height: 1.2em;
    margin-top: 0;
    background-color: #e2e8f0;
    border-color: #cbd5e1;
    transition: background-color 0.15s ease-in-out, border-color 0.15s ease-in-out;
}

.tree-switch .form-check-input:checked {
    background-color: var(--tree-accent-color) !important;
    border-color: var(--tree-accent-color) !important;
}

.tree-switch .form-check-input:focus {
    box-shadow: 0 0 0 2px rgba(0, 166, 126, 0.2);
    border-color: var(--tree-accent-color);
}

.tree-switch .form-check-label {
    cursor: pointer;
    user-select: none;
    margin-left: 8px;
    display: inline-flex;
    align-items: baseline;
    gap: 6px;
}

.tree-title-bold {
    font-size: 13.5px;
    font-weight: 700;
    color: #1e293b;
}

.tree-title-semibold {
    font-size: 13px;
    font-weight: 600;
    color: #1e293b;
}

.tree-title-normal {
    font-size: 13px;
    font-weight: 500;
    color: #334155;
}

.tree-code-muted {
    font-size: 12px;
    color: #94a3b8;
}

/* Highlight matching search terms */
.perm-highlight {
    background-color: #fef08a;
    padding: 1px 4px;
    border-radius: 3px;
}

/* Footer Action Buttons */
.role-footer-actions {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 1.25rem;
    padding: 0.5rem 0;
}

.btn-cancel-custom {
    background-color: #f1f5f9;
    color: #475569;
    border: 1px solid #e2e8f0;
    font-weight: 500;
    font-size: 13px;
    border-radius: 6px;
    padding: 7px 16px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    text-decoration: none;
    transition: all 0.15s;
}

.btn-cancel-custom:hover {
    background-color: #e2e8f0;
    color: #1e293b;
}

.btn-save-changes {
    background-color: var(--tree-accent-color);
    color: #ffffff;
    border: none;
    font-weight: 500;
    font-size: 13.5px;
    border-radius: 6px;
    padding: 8px 22px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    box-shadow: 0 1px 2px rgba(0, 166, 126, 0.2);
    transition: all 0.15s;
}

.btn-save-changes:hover {
    background-color: var(--tree-accent-hover);
    color: #ffffff;
    box-shadow: 0 2px 4px rgba(0, 166, 126, 0.3);
}

.btn-save-changes:disabled {
    background-color: #6ee7b7;
    cursor: not-allowed;
}
</style>
