@include('client.layout.header')

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-11 col-xl-10">

            @include('client.Order.components.order-header', ['donHang' => $donHang])

            <div class="row g-4">

                <div class="col-lg-8">

                    @include('client.Order.components.order-status', ['donHang' => $donHang])

                    @include('client.Order.components.order-products', ['donHang' => $donHang])

                </div>

                <div class="col-lg-4">
                    @include('client.Order.components.order-sidebar', ['donHang' => $donHang])
                </div>

            </div>
        </div>
    </div>
</div>

@include('client.Order.components.refund-request-modal', ['donHang' => $donHang])

<style>
    .bg-gradient {
        background: linear-gradient(135deg, #0d6efd 0%, #6610f2 100%);
    }

    .timeline-compact {
        padding: 0 15px;
    }

    .timeline-step {
        text-align: center;
        flex: 1;
        position: relative;
    }

    .step-icon {
        width: 48px;
        height: 48px;
        background: #e9ecef;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 6px;
        font-size: 1.4rem;
        color: #adb5bd;
        transition: all 0.3s;
    }

    .timeline-step.active .step-icon {
        background: #0d6efd;
        color: white;
        box-shadow: 0 0 0 4px rgba(13, 110, 253, .25);
    }

    .timeline-step small {
        font-size: 0.8rem;
        font-weight: 500;
        color: #495057;
    }

    .timeline-step:not(:last-child)::after {
        content: '';
        position: absolute;
        top: 24px;
        left: 50%;
        width: 100%;
        height: 2px;
        background: #dee2e6;
        z-index: -1;
    }

    .card {
        border-radius: 12px;
    }

    table th,
    table td {
        vertical-align: middle;
    }

    .sticky-top {
        top: 20px;
        z-index: 100;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const checkboxes = document.querySelectorAll('input[name="chi_tiet_ids[]"]');
        checkboxes.forEach(checkbox => {
            const inputSoLuong = checkbox.closest('.list-group-item')?.querySelector('.so-luong-input');
            if (!inputSoLuong) return;
            checkbox.addEventListener('change', function() {
                inputSoLuong.disabled = !this.checked;
                inputSoLuong.value = this.checked ? 1 : '';
            });
            inputSoLuong.addEventListener('input', function() {
                let val = parseInt(this.value) || 1;
                const max = parseInt(this.max) || 999;
                if (val < 1) val = 1;
                if (val > max) val = max;
                this.value = val;
            });
        });

        setupImagePreview('hinhAnhInput', 'previewContainer', 5,
            'Bạn chỉ được tải lên tối đa 5 ảnh minh chứng.');
        setupImagePreview('hinhTaiKhoanInput', 'previewTaiKhoanContainer', 5,
            'Bạn chỉ được tải lên tối đa 5 ảnh thông tin tài khoản.');

        const form = document.getElementById('formYeuCauHoanTra');
        if (form) {
            form.addEventListener('submit', function(e) {
                const hasReturnItemSelection = document.querySelectorAll('input[name="chi_tiet_ids[]"]').length > 0;
                const checkedCount = document.querySelectorAll('input[name="chi_tiet_ids[]"]:checked').length;
                if (hasReturnItemSelection && checkedCount === 0) {
                    e.preventDefault();
                    alert('Vui lòng chọn ít nhất một sản phẩm để hoàn trả.');
                    return;
                }
            });
        }

        function setupImagePreview(inputId, containerId, maxFiles, alertMessage) {
            const input = document.getElementById(inputId);
            const container = document.getElementById(containerId);
            if (!input || !container) return;

            input.addEventListener('change', function() {
                container.innerHTML = '';
                const files = this.files;
                if (files.length > maxFiles) {
                    alert(alertMessage);
                    this.value = '';
                    return;
                }
                Array.from(files).forEach((file, index) => {
                    if (!file.type.startsWith('image/')) {
                        alert('File không phải ảnh: ' + file.name);
                        return;
                    }
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const col = document.createElement('div');
                        col.className = 'col-6 col-md-4 col-lg-3';
                        col.innerHTML = `
                            <div class="position-relative">
                                <img src="${e.target.result}" class="img-fluid rounded shadow-sm" alt="Preview" 
                                     style="height: 120px; object-fit: cover; width: 100%;">
                                <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1 remove-preview" 
                                        data-index="${index}">
                                    <i class="bi bi-x"></i>
                                </button>
                                <small class="d-block text-center mt-1 text-muted text-truncate" style="max-width: 100%;">
                                    ${file.name}
                                </small>
                            </div>
                        `;
                        container.appendChild(col);
                    };
                    reader.readAsDataURL(file);
                });
            });

            container.addEventListener('click', function(e) {
                const btn = e.target.closest('.remove-preview');
                if (btn) btn.closest('.col-6')?.remove();
            });
        }

        const methodUpload = document.getElementById('method_upload');
        const methodManual = document.getElementById('method_manual');
        const uploadSection = document.getElementById('upload_section');
        const manualSection = document.getElementById('manual_section');
        const uploadInput = document.getElementById('hinhTaiKhoanInput');
        const manualInputs = manualSection ? manualSection.querySelectorAll('input[name="ngan_hang"], input[name="so_tai_khoan"], input[name="chi_nhanh"], input[name="ten_chu_tk"]') : [];

        if (methodUpload && methodManual) {
            function toggleRefundMethod() {
                if (methodUpload.checked) {
                    uploadSection.classList.remove('d-none');
                    manualSection.classList.add('d-none');
                    if (uploadInput) {
                        uploadInput.disabled = false;
                        uploadInput.required = true;
                    }
                    manualInputs.forEach((input) => {
                        input.disabled = true;
                        input.required = false;
                    });
                } else {
                    uploadSection.classList.add('d-none');
                    manualSection.classList.remove('d-none');
                    if (uploadInput) {
                        uploadInput.disabled = true;
                        uploadInput.required = false;
                        uploadInput.value = '';
                    }
                    manualInputs.forEach((input) => {
                        input.disabled = false;
                        if (input.name !== 'chi_nhanh') {
                            input.required = true;
                        }
                    });
                }
            }

            methodUpload.addEventListener('change', toggleRefundMethod);
            methodManual.addEventListener('change', toggleRefundMethod);
            toggleRefundMethod();
        }
    });
</script>

@include('client.layout.footer')
@include('client.layout.scripts')
