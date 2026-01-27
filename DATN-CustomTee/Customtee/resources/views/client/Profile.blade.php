@extends('layouts.app')

@section('content')
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-9">

            <h4 class="text-center text-success mb-4">
                <i class="fa fa-user-circle me-2"></i> Thông tin cá nhân
            </h4>

            {{-- ALERT --}}
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            {{-- FORM UPDATE PROFILE --}}
            <form method="POST"
                  action="{{ route('profile.update') }}"
                  enctype="multipart/form-data">
                @csrf

                {{-- AVATAR --}}
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body text-center">

                        <label for="avatarInput" style="cursor:pointer;">
                            <img
                                id="avatarPreview"
                                src="{{ $user->avatar
                                    ? asset('storage/'.$user->avatar)
                                    : asset('img/default-avatar.png') }}"
                                class="rounded-circle shadow border mb-2"
                                width="200"
                                height="200"
                                style="object-fit: cover;"
                            >
                            <div class="text-success fw-semibold">
                                <i class="fa fa-camera me-1"></i> Đổi ảnh đại diện
                            </div>
                        </label>

                        <input
                            type="file"
                            name="avatar"
                            id="avatarInput"
                            class="d-none"
                            accept="image/*"
                            onchange="previewAvatar(event)"
                        >
                    </div>
                </div>

                {{-- 2 CỘT --}}
                <div class="row g-4">

                    {{-- THÔNG TIN CÁ NHÂN --}}
                    <div class="col-md-6">
                        <div class="card shadow-sm border-0 h-100">
                            <div class="card-body">
                                <h6 class="text-success mb-3">
                                    <i class="fa fa-id-card me-2"></i> Thông tin cá nhân
                                </h6>

                                <div class="mb-3">
                                    <label class="form-label">Họ tên</label>
                                    <input type="text"
                                           name="name"
                                           value="{{ $user->name }}"
                                           class="form-control">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Email</label>
                                    <input type="email"
                                           value="{{ $user->email }}"
                                           class="form-control bg-light"
                                           disabled>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label">Số điện thoại</label>
                                    <input type="text"
                                           name="phone"
                                           value="{{ $user->phone }}"
                                           class="form-control">
                                </div>

                                <button class="btn btn-success w-100">
                                    <i class="fa fa-save me-1"></i> Lưu thông tin
                                </button>
                            </div>
                        </div>
                    </div>
            </form>
            {{-- END FORM PROFILE --}}

                    {{-- ĐỔI MẬT KHẨU --}}
                    <div class="col-md-6">
                        <div class="card shadow-sm border-0 h-100">
                            <div class="card-body">
                                <h6 class="text-warning mb-3">
                                    <i class="fa fa-lock me-2"></i> Đổi mật khẩu
                                </h6>

                                <form method="POST" action="{{ route('profile.password') }}">
                                    @csrf

                                    <div class="mb-3">
                                        <label class="form-label">Mật khẩu hiện tại</label>
                                        <input type="password"
                                               name="current_password"
                                               class="form-control">
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Mật khẩu mới</label>
                                        <input type="password"
                                               name="password"
                                               class="form-control">
                                    </div>

                                    <div class="mb-4">
                                        <label class="form-label">Nhập lại mật khẩu mới</label>
                                        <input type="password"
                                               name="password_confirmation"
                                               class="form-control">
                                    </div>

                                    <button class="btn btn-warning text-white w-100">
                                        <i class="fa fa-key me-1"></i> Đổi mật khẩu
                                    </button>
                                </form>

                            </div>
                        </div>
                    </div>

                </div>

        </div>
    </div>
</div>

{{-- PREVIEW AVATAR --}}
<script>
function previewAvatar(event) {
    const reader = new FileReader();
    reader.onload = function () {
        document.getElementById('avatarPreview').src = reader.result;
    };
    reader.readAsDataURL(event.target.files[0]);
}
</script>
@endsection
