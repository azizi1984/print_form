import jQuery from 'jquery';
import 'datatables.net-bs5';

// Assign jQuery to window globally
window.$ = window.jQuery = jQuery;

jQuery(function ($) {
    var tableEl = $('#ex_declaration');

    if (tableEl.length > 0) {
        tableEl.DataTable({
            pageLength: 10,
            lengthMenu: [[10, 50, 100], [10, 50, 100]],
            searching: true,
            order: [], // keep default order
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
            if (errorAlert) {
                errorAlert.classList.add('d-none');
                document.getElementById('modalErrorMessage').textContent = '';
            }

            if (button) {
                const id = button.getAttribute('data-id');
                const name = button.getAttribute('data-name');
                const desc = button.getAttribute('data-desc');
                const status = button.getAttribute('data-status');
                const headerId = button.getAttribute('data-header-id');
                const detailId = button.getAttribute('data-detail-id');
                const footerId = button.getAttribute('data-footer-id');
                const date = button.getAttribute('data-date');

                document.getElementById('create_or_edit_profile_template_id').value = id || '';
                document.getElementById('create_or_edit_profile_template_name').value = name || '';
                document.getElementById('create_or_edit_description').value = desc || '';
                document.getElementById('create_or_edit_status').value = (status !== null && status !== undefined && status !== '') ? status : '1';
                document.getElementById('create_or_edit_header_template_id').value = headerId || '';
                document.getElementById('create_or_edit_detail_template_id').value = detailId || '';
                document.getElementById('create_or_edit_footer_template_id').value = footerId || '';
                document.getElementById('create_or_edit_created_at').value = date || '';

                const labelEl = document.getElementById('editModalLabel');
                const subtitleEl = document.getElementById('editModalSubtitle');

                if (id) {
                    // โหมด Edit
                    document.getElementById('action_mode').value = 'edit';
                    if (labelEl) labelEl.innerHTML = '<i class="bi bi-pencil-square me-2 text-warning"></i>Edit Profile Template';
                    if (subtitleEl) subtitleEl.textContent = 'แก้ไขข้อมูล การเชื่อมโยงเทมเพลต และสถานะโปรไฟล์';
                    document.getElementById('wrapper_created_at').style.display = 'block';
                } else {
                    // โหมด Create
                    document.getElementById('action_mode').value = 'create';
                    if (labelEl) labelEl.innerHTML = '<i class="bi bi-file-earmark-person me-2 text-primary"></i>Create Profile Template';
                    if (subtitleEl) subtitleEl.textContent = 'กำหนดชื่อและเชื่อมโยงเทมเพลตส่วนหัว รายละเอียด และส่วนท้ายสำหรับใบขนสินค้าขาออก';
                    document.getElementById('wrapper_created_at').style.display = 'none';
                    document.getElementById('createOrEditForm').reset();
                    document.getElementById('create_or_edit_profile_template_id').value = '';
                    document.getElementById('create_or_edit_status').value = '1';
                    document.getElementById('create_or_edit_header_template_id').value = '';
                    document.getElementById('create_or_edit_detail_template_id').value = '';
                    document.getElementById('create_or_edit_footer_template_id').value = '';
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
                const id = document.getElementById('create_or_edit_profile_template_id').value;
                const name = document.getElementById('create_or_edit_profile_template_name').value;
                const desc = document.getElementById('create_or_edit_description').value;
                const status = document.getElementById('create_or_edit_status').value;
                const headerId = document.getElementById('create_or_edit_header_template_id').value;
                const detailId = document.getElementById('create_or_edit_detail_template_id').value;
                const footerId = document.getElementById('create_or_edit_footer_template_id').value;

                const errorAlert = document.getElementById('modalErrorAlert');
                const errorMessageEl = document.getElementById('modalErrorMessage');

                if (!name.trim()) {
                    if (errorAlert && errorMessageEl) {
                        errorMessageEl.textContent = 'กรุณากรอกชื่อ Profile Template Name';
                        errorAlert.classList.remove('d-none');
                    } else {
                        alert('กรุณากรอกชื่อ Profile Template Name');
                    }
                    document.getElementById('create_or_edit_profile_template_name').focus();
                    return;
                }

                let url = '/ex-declaration/profile-template';
                let method = 'POST';

                if (mode === 'edit' && id) {
                    url = `/ex-declaration/profile-template/${id}`;
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
                        profile_template_name: name.trim(),
                        description: desc,
                        status: status,
                        header_template_id: headerId || null,
                        detail_template_id: detailId || null,
                        footer_template_id: footerId || null
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

    // จัดการ Event เมื่อกดปุ่ม Delete (Trash)
    $('#ex_declaration tbody').on('click', '.delete-btn', function (e) {
        e.preventDefault();
        const id = $(this).attr('data-id');

        if (!id) return;

        if (confirm('คุณแน่ใจหรือไม่ว่าต้องการลบ Profile Template นี้?')) {
            const btn = $(this);
            const originalHtml = btn.html();
            btn.html('<span class="spinner-border spinner-border-sm text-danger" role="status" aria-hidden="true"></span>');
            btn.css('pointer-events', 'none');

            $.ajax({
                url: `/ex-declaration/profile-template/${id}`,
                type: 'POST',
                data: {
                    _method: 'DELETE',
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function (res) {
                    if (res.success) {
                        alert(res.message || 'ลบข้อมูลเรียบร้อยแล้ว');
                        location.reload();
                    }
                },
                error: function (xhr) {
                    alert('เกิดข้อผิดพลาดในการลบข้อมูล กรุณาลองใหม่อีกครั้ง');
                    console.error(xhr.responseText);
                    btn.html(originalHtml);
                    btn.css('pointer-events', 'auto');
                }
            });
        }
    });
});
