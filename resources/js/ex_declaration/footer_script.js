import jQuery from 'jquery';
import 'datatables.net-bs5';

// Assign jQuery to window globally
window.$ = window.jQuery = jQuery;

jQuery(function ($) {
    var tableEl = $('#footer_declaration');

    if (tableEl.length > 0) {
        tableEl.DataTable({
            pageLength: 10,
            lengthMenu: [[10, 50, 100], [10, 50, 100]],
            searching: true,
            language: {
                lengthMenu: "แสดง _MENU_ รายการ",
                search: "ค้นหาข้อมูล:",
                info: "แสดงข้อมูล _START_ ถึง _END_ จากทั้งหมด _TOTAL_ รายการ",
                infoEmpty: "แสดงข้อมูล 0 ถึง 0 จากทั้งหมด 0 รายการ",
                infoFiltered: "(กรองจากทั้งหมด _MAX_ รายการ)",
                zeroRecords: "ไม่พบข้อมูลที่ค้นหา",
                paginate: {
                    first: "หน้าแรก",
                    last: "หน้าสุดท้าย",
                    next: "ถัดไป",
                    previous: "ก่อนหน้า"
                }
            }
        });
    }

    // จัดการ Event เมื่อเปิด Modal Create / Edit
    const createOrEditModal = document.getElementById('createOrEditModal');
    if (createOrEditModal) {
        createOrEditModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const errorAlert = document.getElementById('modalErrorAlert');
            const errorMessageEl = document.getElementById('modalErrorMessage');
            if (errorAlert) {
                errorAlert.classList.add('d-none');
                if (errorMessageEl) errorMessageEl.textContent = '';
            }

            if (button) {
                const id = button.getAttribute('data-id');
                const name = button.getAttribute('data-name');
                const desc = button.getAttribute('data-desc');
                const status = button.getAttribute('data-status');
                const date = button.getAttribute('data-date');

                document.getElementById('create_or_edit_footer_template_id').value = id || '';
                document.getElementById('create_or_edit_footer_template_name').value = name || '';
                document.getElementById('create_or_edit_description').value = desc || '';
                document.getElementById('create_or_edit_status').value = (status !== null && status !== undefined && status !== '') ? status : '1';
                document.getElementById('create_or_edit_created_at').value = date || '';

                const labelEl = document.getElementById('editModalLabel');
                const subtitleEl = document.getElementById('editModalSubtitle');

                if (id) {
                    // โหมด Edit
                    document.getElementById('action_mode').value = 'edit';
                    if (labelEl) labelEl.innerHTML = '<i class="bi bi-pencil-square me-2 text-warning"></i>Edit Footer Template';
                    if (subtitleEl) subtitleEl.textContent = 'แก้ไขข้อมูลและสถานะเทมเพลตส่วนท้ายเอกสารและลายเซ็น';
                    document.getElementById('wrapper_created_at').style.display = 'block';
                } else {
                    // โหมด Create
                    document.getElementById('action_mode').value = 'create';
                    if (labelEl) labelEl.innerHTML = '<i class="bi bi-layout-text-window-reverse me-2 text-primary"></i>Create Footer Template';
                    if (subtitleEl) subtitleEl.textContent = 'กำหนดชื่อและรายละเอียดเทมเพลตส่วนท้ายเอกสารและลายเซ็น (Footer Template) สำหรับใบขนสินค้าขาออก';
                    document.getElementById('wrapper_created_at').style.display = 'none';
                    document.getElementById('createOrEditForm').reset();
                    document.getElementById('create_or_edit_footer_template_id').value = '';
                    document.getElementById('create_or_edit_status').value = '1';
                }
            }
        });

        const btnCancel = document.getElementById('btnCancelcreateOrEdit');
        if (btnCancel) {
            btnCancel.addEventListener('click', function () {
                document.getElementById('createOrEditForm').reset();
                const errorAlert = document.getElementById('modalErrorAlert');
                if (errorAlert) {
                    errorAlert.classList.add('d-none');
                }
            });
        }

        const btnSave = document.getElementById('btnSavecreateOrEdit');
        if (btnSave) {
            btnSave.addEventListener('click', function () {
                const mode = document.getElementById('action_mode').value;
                const id = document.getElementById('create_or_edit_footer_template_id').value;
                const name = document.getElementById('create_or_edit_footer_template_name').value;
                const desc = document.getElementById('create_or_edit_description').value;
                const status = document.getElementById('create_or_edit_status').value;

                const errorAlert = document.getElementById('modalErrorAlert');
                const errorMessageEl = document.getElementById('modalErrorMessage');

                if (!name.trim()) {
                    if (errorAlert && errorMessageEl) {
                        errorMessageEl.textContent = 'กรุณาระบุชื่อ Footer Template Name';
                        errorAlert.classList.remove('d-none');
                    } else {
                        alert('กรุณาระบุชื่อ Footer Template Name');
                    }
                    document.getElementById('create_or_edit_footer_template_name').focus();
                    return;
                }

                let url = '/ex-declaration/footer-template';
                let method = 'POST';

                if (mode === 'edit' && id) {
                    url = `/ex-declaration/footer-template/${id}`;
                    method = 'PUT';
                }

                const originalHtml = btnSave.innerHTML;
                btnSave.disabled = true;
                btnSave.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> กำลังบันทึก...';

                if (errorAlert) {
                    errorAlert.classList.add('d-none');
                }

                $.ajax({
                    url: url,
                    type: "POST",
                    data: {
                        _method: method,
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        footer_template_name: name.trim(),
                        description: desc,
                        status: status
                    },
                    success: function (res) {
                        alert(res.message || 'บันทึกข้อมูลเรียบร้อยแล้ว');
                        location.reload();
                    },
                    error: function (xhr) {
                        let msg = 'เกิดข้อผิดพลาดในการบันทึกข้อมูล กรุณาลองใหม่อีกครั้ง';
                        if (xhr.responseJSON) {
                            if (xhr.responseJSON.errors) {
                                const keys = Object.keys(xhr.responseJSON.errors);
                                if (keys.length > 0) {
                                    msg = xhr.responseJSON.errors[keys[0]][0];
                                }
                            } else if (xhr.responseJSON.message) {
                                msg = xhr.responseJSON.message;
                            }
                        }

                        if (errorAlert && errorMessageEl) {
                            errorMessageEl.textContent = msg;
                            errorAlert.classList.remove('d-none');
                        } else {
                            alert(msg);
                        }

                        console.error(xhr.responseText);
                        btnSave.disabled = false;
                        btnSave.innerHTML = originalHtml;
                    }
                });
            });
        }
    }

    // จัดการ Event เมื่อกดปุ่ม Copy
    $('#footer_declaration tbody').on('click', '.copy-btn', function (e) {
        e.preventDefault();
        const id = $(this).attr('data-id');

        if (!id) return;

        if (confirm('Are you sure you want to duplicate this template?')) {
            const btn = $(this);
            const originalHtml = btn.html();
            btn.html('<span class="spinner-border spinner-border-sm text-success" role="status" aria-hidden="true"></span>');
            btn.css('pointer-events', 'none');

            $.ajax({
                url: `/ex-declaration/footer-template/${id}/copy`,
                type: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function (res) {
                    if (res.success) {
                        alert('Copied successfully!');
                        location.reload();
                    } else {
                        alert('Failed to copy: ' + res.message);
                        btn.html(originalHtml);
                        btn.css('pointer-events', 'auto');
                    }
                },
                error: function (xhr) {
                    const errMsg = (xhr.responseJSON && xhr.responseJSON.message) 
                        ? xhr.responseJSON.message 
                        : (xhr.statusText || 'Unknown error');
                    alert('Failed to copy data: ' + errMsg);
                    console.error(xhr.responseText);
                    btn.html(originalHtml);
                    btn.css('pointer-events', 'auto');
                }
            });
        }
    });

    // จัดการ Event เมื่อกดปุ่ม Delete (Trash)
    $('#footer_declaration tbody').on('click', '.delete-btn', function (e) {
        e.preventDefault();
        const id = $(this).attr('data-id');

        if (!id) return;

        if (confirm('Are you sure you want to delete this template?')) {
            const btn = $(this);
            const originalHtml = btn.html();
            btn.html('<span class="spinner-border spinner-border-sm text-danger" role="status" aria-hidden="true"></span>');
            btn.css('pointer-events', 'none');

            $.ajax({
                url: `/ex-declaration/footer-template/${id}`,
                type: 'POST',
                data: {
                    _method: 'DELETE',
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function (res) {
                    if (res.success) {
                        alert('Deleted successfully!');
                        location.reload();
                    }
                },
                error: function (xhr) {
                    alert('Failed to delete data. Please try again.');
                    console.error(xhr.responseText);
                    btn.html(originalHtml);
                    btn.css('pointer-events', 'auto');
                }
            });
        }
    });
});
