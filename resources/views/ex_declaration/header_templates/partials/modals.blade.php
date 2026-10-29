<!-- Bootstrap Modal for Data Fields -->
<div class="modal fade" id="fieldSelectorModal" tabindex="-1" aria-labelledby="fieldSelectorModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content" style="border-radius: 12px; box-shadow: 0 8px 24px rgba(0,0,0,0.15);">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold" id="fieldSelectorModalLabel"><i class="bi bi-tags me-1 text-primary"></i> Select Data Field</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-2">
                <div class="text-muted mb-3" style="font-size: 13px;">
                    Select a field to bind to this position:
                </div>
                <!-- Modal Search Box -->
                <div class="mb-3 position-relative">
                    <input type="text" id="modalFieldSearchInput" class="form-control form-control-sm" placeholder="Search fields..." style="padding-left: 30px; font-size: 13px; border-radius: 6px;">
                    <i class="bi bi-search position-absolute text-muted" style="left: 10px; top: 50%; transform: translateY(-50%); font-size: 12px;"></i>
                </div>
                
                <!-- Modal Fields List -->
                <div class="modal-field-list" style="max-height: 380px; overflow-y: auto;">
                    <!-- Special Custom Text Field -->
                    <div class="modal-field-item p-2 mb-2 border rounded d-flex justify-content-between align-items-center" 
                         data-field="custom_text" 
                         data-label="Custom Text (ข้อความทั่วไป)"
                         style="cursor: pointer; font-size: 12.5px; transition: all 0.2s; background-color: #fff3cd; border-color: #ffe69c;">
                        <span class="field-label-text">
                            <i class="bi bi-fonts me-2 text-warning"></i>
                            <strong>Custom Text (ข้อความทั่วไป)</strong>
                        </span>
                        <button type="button" class="btn btn-warning btn-sm px-2 py-1 select-field-btn" style="font-size: 11px; border-radius: 4px;">
                            <i class="bi bi-plus-circle me-1"></i> Add
                        </button>
                    </div>

                    @foreach($fields as $field)
                        @php
                            $displayLabel = (!empty($field->app_showe) && !empty($field->app_showt)) 
                                ? $field->app_showe . ' (' . $field->app_showt . ')' 
                                : ($field->app_showe ?? $field->app_showt ?? $field->field_name);
                        @endphp
                        <div class="modal-field-item p-2 mb-2 border rounded d-flex justify-content-between align-items-center" 
                             data-field="{{ $field->field_name }}" 
                             data-label="{{ $displayLabel }}"
                             style="cursor: pointer; font-size: 12.5px; transition: all 0.2s;">
                            <span class="field-label-text">
                                <i class="bi bi-tag me-2 text-muted"></i>
                                {{ $displayLabel }}
                            </span>
                            <button type="button" class="btn btn-primary btn-sm px-2 py-1 select-field-btn" style="font-size: 11px; border-radius: 4px;">
                                <i class="bi bi-plus-circle me-1"></i> Add
                            </button>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .modal-field-item:hover {
        background-color: rgba(85, 110, 230, 0.05);
        border-color: #556ee6 !important;
    }
    .modal-field-item:hover .field-label-text {
        color: #556ee6;
        font-weight: 500;
    }
    .modal-field-item:hover .bi-tag {
        color: #556ee6 !important;
    }
</style>

<!-- Custom Text Modal (Replaces window.prompt) -->
<div class="modal fade" id="customTextModal" tabindex="-1" aria-labelledby="customTextModalLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header border-bottom py-3 px-4 bg-white">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-3 bg-warning-subtle text-warning d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                        <i class="bi bi-fonts fs-5"></i>
                    </div>
                    <div>
                        <h6 class="modal-title fw-bold text-dark mb-0" id="customTextModalLabel">ระบุข้อความทั่วไป (Custom Text)</h6>
                        <small class="text-muted">ข้อความคงที่ที่จะพิมพ์ลงในช่องนี้</small>
                    </div>
                </div>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <label for="customTextInput" class="form-label fw-semibold text-dark mb-1">ข้อความ:</label>
                <input type="text" class="form-control" id="customTextInput" placeholder="เช่น สาขาที่ 0001, โทร. 02-xxx-xxxx, หมายเหตุ..." autocomplete="off">
                <div class="text-danger small mt-2 d-none" id="customTextError">
                    <i class="bi bi-exclamation-circle me-1"></i> กรุณาระบุข้อความก่อนยืนยัน
                </div>
            </div>
            <div class="modal-footer border-top py-2 px-4 bg-light">
                <button type="button" class="btn btn-outline-secondary btn-sm px-3 rounded-pill" data-bs-dismiss="modal">ยกเลิก</button>
                <button type="button" class="btn btn-warning btn-sm px-4 rounded-pill fw-semibold text-dark" id="btnConfirmCustomText">
                    <i class="bi bi-check-lg me-1"></i> ยืนยัน
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Unsaved Changes Warning Modal -->
<div class="modal fade" id="unsavedWarningModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header border-bottom py-3 px-4 bg-white">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-3 bg-danger-subtle text-danger d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                        <i class="bi bi-exclamation-triangle fs-5"></i>
                    </div>
                    <h6 class="modal-title fw-bold text-dark mb-0">มีการเปลี่ยนแปลงที่ยังไม่ได้บันทึก</h6>
                </div>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 text-secondary">
                คุณมีการแก้ไขเลย์เอาต์ที่ยังไม่ได้กด <strong>Save Layout</strong> หากออกจากหน้านี้ การเปลี่ยนแปลงจะไม่ถูกบันทึก คุณแน่ใจหรือไม่ว่าต้องการออกจากหน้านี้?
            </div>
            <div class="modal-footer border-top py-2 px-4 bg-light">
                <button type="button" class="btn btn-outline-secondary btn-sm px-3 rounded-pill" data-bs-dismiss="modal">อยู่หน้านี้ต่อ</button>
                <a href="{{ route('header-template') }}" class="btn btn-danger btn-sm px-4 rounded-pill fw-semibold" id="btnConfirmLeave">
                    ออกจากหน้านี้
                </a>
            </div>
        </div>
    </div>
</div>
