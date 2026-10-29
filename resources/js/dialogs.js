/**
 * Global UI Notification & Confirmation Dialogs System
 * Print Form Standard UI Component
 */

const typeConfigs = {
    success: {
        iconClass: 'bi-check-circle-fill',
        bgClass: 'bg-success',
        subtleBgClass: 'bg-success-subtle text-success',
        btnClass: 'btn-success',
        defaultTitle: 'สำเร็จ'
    },
    danger: {
        iconClass: 'bi-exclamation-triangle-fill',
        bgClass: 'bg-danger',
        subtleBgClass: 'bg-danger-subtle text-danger',
        btnClass: 'btn-danger',
        defaultTitle: 'ข้อผิดพลาด'
    },
    error: {
        iconClass: 'bi-exclamation-triangle-fill',
        bgClass: 'bg-danger',
        subtleBgClass: 'bg-danger-subtle text-danger',
        btnClass: 'btn-danger',
        defaultTitle: 'ข้อผิดพลาด'
    },
    warning: {
        iconClass: 'bi-exclamation-circle-fill',
        bgClass: 'bg-warning text-dark',
        subtleBgClass: 'bg-warning-subtle text-warning',
        btnClass: 'btn-warning text-dark',
        defaultTitle: 'แจ้งเตือน'
    },
    info: {
        iconClass: 'bi-info-circle-fill',
        bgClass: 'bg-info text-white',
        subtleBgClass: 'bg-info-subtle text-info',
        btnClass: 'btn-info text-white',
        defaultTitle: 'ข้อมูล'
    },
    primary: {
        iconClass: 'bi-info-circle-fill',
        bgClass: 'bg-primary',
        subtleBgClass: 'bg-primary-subtle text-primary',
        btnClass: 'btn-primary',
        defaultTitle: 'แจ้งเตือน'
    }
};

/**
 * แสดง Toast Notification
 * @param {'success'|'danger'|'error'|'warning'|'info'|'primary'} type 
 * @param {string} message 
 * @param {number} duration ms (default 3500)
 */
export function showToast(type = 'primary', message = '', duration = 3500) {
    const toastEl = document.getElementById('globalToast');
    const toastIcon = document.getElementById('globalToastIcon');
    const toastText = document.getElementById('globalToastText');

    if (!toastEl || !toastText) {
        // Fallback หากไม่มี Global Component ในหน้า
        console.warn('[Toast]', `[${type}]`, message);
        return;
    }

    const config = typeConfigs[type] || typeConfigs.primary;

    // Reset and apply theme classes
    toastEl.className = `toast align-items-center text-white border-0 shadow-lg rounded-3 ${config.bgClass}`;
    if (toastIcon) {
        toastIcon.className = `bi ${config.iconClass} fs-5`;
    }
    toastText.textContent = message;

    if (window.bootstrap && window.bootstrap.Toast) {
        const toastInstance = window.bootstrap.Toast.getOrCreateInstance(toastEl, {
            delay: duration,
            autohide: true
        });
        toastInstance.show();
    }
}

/**
 * แสดง Confirmation Action Modal แบบ Callback
 * @param {Object} options
 * @param {string} options.title หัวข้อ Dialog
 * @param {string} options.message ข้อความรายละเอียด
 * @param {'danger'|'warning'|'info'|'primary'|'success'} [options.type='primary'] ประเภท dialog
 * @param {string} [options.iconClass] คลาสไอคอนกำหนดเอง
 * @param {string} [options.iconBgClass] คลาสสีพื้นหลังไอคอน
 * @param {string} [options.confirmBtnClass] คลาสปุ่มยืนยัน
 * @param {string} [options.confirmText='ยืนยัน'] ข้อความปุ่มยืนยัน
 * @param {string} [options.cancelText='ยกเลิก'] ข้อความปุ่มยกเลิก
 * @param {Function} [options.onConfirm] ฟังก์ชัน callback เมื่อกดยืนยัน
 * @param {Function} [options.onCancel] ฟังก์ชัน callback เมื่อกดยกเลิก
 */
export function showConfirmModal(options = {}) {
    const {
        title = 'ยืนยันการดำเนินการ',
        message = 'คุณแน่ใจหรือไม่ว่าต้องการดำเนินการนี้?',
        type = 'primary',
        iconClass,
        iconBgClass,
        confirmBtnClass,
        confirmText = 'ยืนยัน',
        cancelText = 'ยกเลิก',
        onConfirm,
        onCancel
    } = options;

    const modalEl = document.getElementById('globalConfirmModal');
    if (!modalEl || !window.bootstrap || !window.bootstrap.Modal) {
        if (confirm(message)) {
            if (typeof onConfirm === 'function') onConfirm();
        } else {
            if (typeof onCancel === 'function') onCancel();
        }
        return;
    }

    const config = typeConfigs[type] || typeConfigs.primary;

    // Set texts
    const titleEl = document.getElementById('globalConfirmTitle');
    const msgEl = document.getElementById('globalConfirmMessage');
    const cancelBtn = document.getElementById('btnGlobalConfirmCancel');
    if (titleEl) titleEl.textContent = title;
    if (msgEl) msgEl.textContent = message;
    if (cancelBtn) cancelBtn.textContent = cancelText;

    // Set Icon & Background
    const iconWrapper = document.getElementById('globalConfirmIconWrapper');
    const icon = document.getElementById('globalConfirmIcon');
    if (iconWrapper) {
        iconWrapper.className = `mx-auto rounded-circle d-flex align-items-center justify-content-center mb-3 ${iconBgClass || config.subtleBgClass}`;
    }
    if (icon) {
        icon.className = `bi fs-2 ${iconClass || config.iconClass}`;
    }

    // Configure Submit Button
    const btnSubmit = document.getElementById('btnGlobalConfirmSubmit');
    const newBtnSubmit = btnSubmit.cloneNode(true);
    newBtnSubmit.className = `btn btn-sm px-4 rounded-pill fw-semibold shadow-xs ${confirmBtnClass || config.btnClass}`;
    newBtnSubmit.textContent = confirmText;
    btnSubmit.parentNode.replaceChild(newBtnSubmit, btnSubmit);

    const modal = window.bootstrap.Modal.getOrCreateInstance(modalEl);

    newBtnSubmit.addEventListener('click', function () {
        modal.hide();
        if (typeof onConfirm === 'function') {
            onConfirm();
        }
    }, { once: true });

    if (cancelBtn) {
        const newCancelBtn = cancelBtn.cloneNode(true);
        cancelBtn.parentNode.replaceChild(newCancelBtn, cancelBtn);
        newCancelBtn.addEventListener('click', function () {
            modal.hide();
            if (typeof onCancel === 'function') {
                onCancel();
            }
        }, { once: true });
    }

    modal.show();
}

/**
 * แสดง Confirmation แบบ Promise (ใช้กับ async / await ได้)
 * @param {Object} options 
 * @returns {Promise<boolean>}
 */
export function showConfirm(options = {}) {
    return new Promise((resolve) => {
        showConfirmModal({
            ...options,
            onConfirm: () => resolve(true),
            onCancel: () => resolve(false)
        });
    });
}

/**
 * แสดง Alert Modal แทน window.alert()
 * @param {string|Object} messageOrOptions 
 * @param {string} [title] 
 * @param {'danger'|'warning'|'info'|'primary'|'success'} [type='info'] 
 */
export function showAlertModal(messageOrOptions, title, type = 'info') {
    let options = {};
    if (typeof messageOrOptions === 'string') {
        options = {
            message: messageOrOptions,
            title: title || 'แจ้งเตือน',
            type: type
        };
    } else {
        options = messageOrOptions || {};
    }

    const {
        title: dialogTitle = 'แจ้งเตือน',
        message = '',
        type: dialogType = 'info',
        okText = 'ตกลง',
        onClose
    } = options;

    const modalEl = document.getElementById('globalAlertModal');
    if (!modalEl || !window.bootstrap || !window.bootstrap.Modal) {
        alert(message);
        if (typeof onClose === 'function') onClose();
        return;
    }

    const config = typeConfigs[dialogType] || typeConfigs.info;

    const titleEl = document.getElementById('globalAlertTitle');
    const msgEl = document.getElementById('globalAlertMessage');
    const iconWrapper = document.getElementById('globalAlertIconWrapper');
    const icon = document.getElementById('globalAlertIcon');
    const btnOk = document.getElementById('btnGlobalAlertOk');

    if (titleEl) titleEl.textContent = dialogTitle;
    if (msgEl) msgEl.textContent = message;
    if (iconWrapper) iconWrapper.className = `mx-auto rounded-circle d-flex align-items-center justify-content-center mb-3 ${config.subtleBgClass}`;
    if (icon) icon.className = `bi fs-2 ${config.iconClass}`;

    if (btnOk) {
        const newBtnOk = btnOk.cloneNode(true);
        newBtnOk.className = `btn btn-sm px-4 rounded-pill fw-semibold shadow-xs ${config.btnClass}`;
        newBtnOk.textContent = okText;
        btnOk.parentNode.replaceChild(newBtnOk, btnOk);

        const modal = window.bootstrap.Modal.getOrCreateInstance(modalEl);
        newBtnOk.addEventListener('click', function () {
            modal.hide();
            if (typeof onClose === 'function') onClose();
        }, { once: true });
    }

    const modal = window.bootstrap.Modal.getOrCreateInstance(modalEl);
    modal.show();
}

// Bind to window object for global availability
window.showToast = showToast;
window.showConfirmModal = showConfirmModal;
window.showConfirm = showConfirm;
window.showAlertModal = showAlertModal;
window.showAlert = (msg, title, type) => showAlertModal(msg, title, type);
