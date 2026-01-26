@extends('layouts.app')

@section('title', 'Đăng ký')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow border-0 rounded-4">
                <div class="card-body p-4">
                    <h3 class="text-center mb-4 fw-bold">Đăng ký tài khoản</h3>

                    <form method="POST" action="{{ url('/register') }}">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">Họ và tên</label>
                            <input type="text" name="name"
                                   class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name') }}"
                                   placeholder="Nhập họ và tên">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email"
                                   class="form-control @error('email') is-invalid @enderror"
                                   value="{{ old('email') }}"
                                   placeholder="example@gmail.com">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Mật khẩu</label>
                            <input type="password" name="password"
                                   class="form-control @error('password') is-invalid @enderror"
                                   placeholder="Ít nhất 6 ký tự">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Nhập lại mật khẩu</label>
                            <input type="password" name="password_confirmation"
                                   class="form-control"
                                   placeholder="Nhập lại mật khẩu">
                        </div>

                        <button type="submit" class="btn btn-success w-100 py-2">
                            Đăng ký
                        </button>
                    </form>

                    <p class="text-center mt-4 mb-0">
                        Đã có tài khoản?
                        <a href="{{ url('/login') }}" class="fw-semibold">Đăng nhập</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
