    @extends('layouts.app')

    @section('title', 'Đăng nhập')

    @section('content')
    <div class="container py-5">
        <h2>Đăng nhập</h2>
        <form method="POST" action="{{ url('/login') }}">
            @csrf
            <div class="mb-3">
                <label>Email</label>
                <input type="email" name="email" class="form-control">
                @error('email') <span class="text-danger">{{ $message }}</span> @enderror
            </div>
            <div class="mb-3">
                <label>Mật khẩu</label>
                <input type="password" name="password" class="form-control">
                @error('password') <span class="text-danger">{{ $message }}</span> @enderror
            </div>
            <button type="submit" class="btn btn-success">Đăng nhập</button>
        </form>
    </div>
    @endsection
