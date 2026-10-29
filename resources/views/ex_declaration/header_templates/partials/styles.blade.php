<style>
    .design-workspace {
        display: flex;
        gap: 24px;
        margin-top: 15px;
    }

    .designer-container {
        flex: 1;
        background: #ffffff;
        padding: 20px;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        border: 1px solid #e2e8f0;
    }

    .design-sheet-wrapper {
        width: 100%;
        overflow-x: auto;
    }

    #design-table-layout {
        display: grid;
        grid-template-columns: repeat(6, calc(54.2% / 6)) repeat(6, calc(45.8% / 6));
        grid-template-rows: repeat(18, 40px) 30px;
        background: #ffffff;
        border: 2px solid #000000 !important;
        border-radius: 4px;
        overflow: hidden;
        width: 100%;
        min-width: 900px;
        transition: all 0.2s ease;
    }

    .grid-cell {
        box-sizing: border-box;
        border-right: 1px solid #000000 !important;
        border-bottom: 1px solid #000000 !important;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 13px;
        color: #000000;
        cursor: pointer;
        transition: all 0.15s;
        user-select: none;
        position: relative;
        background-color: #ffffff;
        padding: 0 6px;
        text-align: center;
    }

    .grid-cell:hover {
        background-color: rgba(85, 110, 230, 0.08);
    }

    .grid-cell.active {
        background-color: rgba(85, 110, 230, 0.15);
        box-shadow: inset 0 0 0 2px #556ee6;
        font-weight: bold;
    }

    .cell-last {
        background-color: #f8f9fa;
        cursor: not-allowed;
    }

    .cell-filled {
        background-color: rgba(52, 195, 143, 0.08);
        color: #000000;
        box-shadow: inset 0 0 0 1.5px #34c38f;
    }

    .cell-info-popover {
        position: absolute;
        font-size: 9px;
        top: 2px;
        left: 6px;
        color: #495057;
        white-space: pre-wrap;
        line-height: 1.1;
        pointer-events: none;
    }

    .target-plus {
        position: absolute;
        cursor: pointer;
        z-index: 10;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 16px;
        height: 16px;
        border-radius: 50%;
        background-color: #198754;
        color: #ffffff;
        border: 1px solid #146c43;
        box-shadow: 0 2px 4px rgba(0,0,0,0.15);
        transition: all 0.2s ease-in-out;
    }

    .target-plus::before {
        content: "+";
        font-size: 13px;
        font-weight: bold;
        line-height: 1;
        margin-top: -1px;
    }

    .target-plus:hover {
        background-color: #157347;
        transform: scale(1.2);
        box-shadow: 0 4px 8px rgba(0,0,0,0.25);
    }

    .target-plus.pos-bottom-left {
        top: calc(50% - 8px);
        left: 8px;
    }

    .target-plus.pos-bottom-right {
        top: calc(50% - 8px);
        right: 8px;
    }

    .target-plus.pos-top-right {
        top: calc(50% - 8px);
        right: 8px;
    }

    .target-plus.pos-inline {
        position: relative;
        display: inline-flex;
        vertical-align: middle;
        margin-left: 8px;
        top: -1px;
    }

    .target-plus.pos-center {
        left: calc(50% - 8px);
        top: calc(50% - 8px);
    }

    /* Sidebar panel styling */
    .sidebar-panel {
        width: 320px;
        background: white;
        padding: 20px;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        border: 1px solid #e2e8f0;
        display: flex;
        flex-direction: column;
    }

    .field-list {
        margin-top: 10px;
        flex: 1;
        overflow-y: auto;
        max-height: 520px;
    }

    .field-item {
        padding: 6px 10px;
        background: #f8f9fa;
        border: 1px solid #e9ecef;
        border-radius: 6px;
        margin-bottom: 5px;
        cursor: pointer;
        font-size: 11.5px;
        font-weight: 500;
        display: flex;
        align-items: center;
        justify-content: space-between;
        transition: all 0.2s;
    }

    .field-item:hover {
        background: #556ee6;
        color: white;
        border-color: #556ee6;
        transform: translateY(-1px);
    }

    .field-item i {
        font-size: 14px;
    }

    .dimension-info-badge {
        background: #e1e6fc;
        color: #556ee6;
        padding: 6px 12px;
        border-radius: 6px;
        font-weight: bold;
        font-size: 12px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .position-center {
        left: 0; right: 0; text-align: center;
    }

    .tp-badge {
        background-color: #34c38f;
        border: 1px solid #2ca579;
        border-radius: 4px;
        padding: 2px 6px;
        font-size: 10.5px;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        color: #ffffff;
        font-weight: bold;
        white-space: nowrap;
        vertical-align: middle;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }

    .tp-badge-label {
        color: #ffffff;
        font-weight: bold;
    }

    .tp-badge-delete {
        color: #ff3d60;
        cursor: pointer;
        font-size: 12px;
        font-weight: bold;
        display: inline-flex;
        align-items: center;
        transition: color 0.15s;
    }

    .tp-badge-delete:hover {
        color: #ff1f48;
    }

    .grid-cell.drag-over {
        background-color: rgba(85, 110, 230, 0.15) !important;
        border: 2px dashed #556ee6 !important;
    }

    .target-plus.drag-over {
        transform: scale(1.3);
        background-color: #157347 !important;
        box-shadow: 0 4px 10px rgba(0,0,0,0.3) !important;
    }

    /* Preview mode styles */
    .preview-mode .target-plus {
        display: none !important;
    }
    .preview-mode .cell-info-popover {
        opacity: 0.35;
    }
    .preview-mode .grid-cell {
        cursor: default;
    }
    .preview-mode .grid-cell:hover {
        background-color: #ffffff !important;
    }
    .preview-mode .cell-filled {
        background-color: #ffffff !important;
        box-shadow: none !important;
    }
    .preview-value {
        font-family: 'Courier New', Courier, monospace;
        font-weight: 700;
        font-size: 12px;
        color: #0b1a30;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        display: inline-block;
        max-width: 100%;
        letter-spacing: 0.3px;
    }

    .mode-btn-active {
        background-color: #4f46e5 !important;
        color: #ffffff !important;
        border-color: #4f46e5 !important;
    }
</style>
