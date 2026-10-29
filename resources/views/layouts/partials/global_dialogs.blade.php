{{-- Global Confirmation Modal --}}
<div class="modal fade" id="globalConfirmModal" tabindex="-1" aria-labelledby="globalConfirmTitle" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 380px;">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-body p-4 text-center">
                <div id="globalConfirmIconWrapper" class="mx-auto rounded-circle d-flex align-items-center justify-content-center mb-3 bg-primary-subtle text-primary" style="width: 56px; height: 56px;">
                    <i id="globalConfirmIcon" class="bi bi-question-circle fs-2"></i>
                </div>
                <h5 class="fw-bold text-dark mb-2" id="globalConfirmTitle">ยืนยันการดำเนินการ</h5>
                <p class="text-secondary small mb-4" id="globalConfirmMessage">คุณแน่ใจหรือไม่ว่าต้องการดำเนินการนี้?</p>
                <div class="d-flex justify-content-center gap-2">
                    <button type="button" class="btn btn-outline-secondary btn-sm px-3 rounded-pill" data-bs-dismiss="modal" id="btnGlobalConfirmCancel">
                        ยกเลิก
                    </button>
                    <button type="button" class="btn btn-primary btn-sm px-4 rounded-pill fw-semibold shadow-xs" id="btnGlobalConfirmSubmit">
                        ยืนยัน
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Global Alert Modal (แทนที่ browser alert() แบบมีปุ่มตกลง) --}}
<div class="modal fade" id="globalAlertModal" tabindex="-1" aria-labelledby="globalAlertTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 380px;">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-body p-4 text-center">
                <div id="globalAlertIconWrapper" class="mx-auto rounded-circle d-flex align-items-center justify-content-center mb-3 bg-info-subtle text-info" style="width: 56px; height: 56px;">
                    <i id="globalAlertIcon" class="bi bi-info-circle fs-2"></i>
                </div>
                <h5 class="fw-bold text-dark mb-2" id="globalAlertTitle">แจ้งเตือน</h5>
                <p class="text-secondary small mb-4" id="globalAlertMessage"></p>
                <div class="d-flex justify-content-center">
                    <button type="button" class="btn btn-primary btn-sm px-4 rounded-pill fw-semibold shadow-xs" data-bs-dismiss="modal" id="btnGlobalAlertOk">
                        ตกลง
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Global Toast Notification Container --}}
<div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 9999;" id="globalToastContainer">
    <div id="globalToast" class="toast align-items-center text-white border-0 shadow-lg rounded-3" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body d-flex align-items-center gap-2 fs-6" id="globalToastBody">
                <i id="globalToastIcon" class="bi bi-info-circle-fill fs-5"></i>
                <span id="globalToastText"></span>
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto shadow-none" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
</div>
