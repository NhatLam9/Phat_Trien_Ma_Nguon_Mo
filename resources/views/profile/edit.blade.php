@extends('layouts.app')

@section('title', 'Hồ sơ cá nhân - DET Practice')

@section('content')
<div style="max-width: 800px; margin: 0 auto;">
    <h1 style="font-size: 26px; font-weight: 800; color: #1e293b; margin-bottom: 24px;">👤 Quản lý hồ sơ cá nhân</h1>

    <!-- FORM THÔNG TIN CÁ NHÂN -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Thông tin cơ bản</h2>
            <p style="color: #64748b; font-size: 13px; margin-top: 4px;">Cập nhật thông tin nhận diện và mục tiêu điểm thi Duolingo English Test của bạn.</p>
        </div>

        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <!-- Ảnh đại diện hiện tại & Tải ảnh mới -->
            <div class="form-group" style="display: flex; align-items: center; gap: 20px; margin-bottom: 24px;">
                <img src="{{ $user->avatar_url }}" alt="{{ $user->hoten }}" style="width: 80px; height: 80px; border-radius: 50%; object-fit: cover; border: 3px solid #00b86b;">
                <div>
                    <label for="anh" style="margin-bottom: 4px;">Ảnh đại diện mới</label>
                    <input type="file" id="anh" name="anh" accept="image/*" class="form-control" style="font-size: 13px;">
                    <small style="color: #64748b; font-size: 12px;">Định dạng: JPG, PNG, WEBP. Tối đa 2MB.</small>
                    @error('anh')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Họ và tên -->
            <div class="form-group">
                <label for="hoten">Họ và tên <span style="color:red;">*</span></label>
                <input type="text" id="hoten" name="hoten" value="{{ old('hoten', $user->hoten) }}" class="form-control" required>
                @error('hoten')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Email & Trạng thái xác thực -->
            <div class="form-group">
                <label for="email">Địa chỉ Email</label>
                <div style="display: flex; gap: 12px; align-items: center;">
                    <input type="email" id="email" value="{{ $user->email }}" class="form-control" disabled style="background: #f1f5f9; cursor: not-allowed;">
                    @if($user->hasVerifiedEmail())
                        <span class="badge badge-speaking" style="white-space: nowrap;">✓ Đã xác minh</span>
                    @else
                        <span class="badge badge-listening" style="white-space: nowrap;">Chưa xác minh</span>
                    @endif
                </div>
                @if(!$user->hasVerifiedEmail())
                    <small style="display: block; margin-top: 6px;">
                        <a href="{{ route('verification.notice') }}" style="color: #00b86b; font-weight: 600; text-decoration: none;">Nhấn vào đây để xác minh email</a>
                    </small>
                @endif
            </div>

            <!-- Điểm mục tiêu DET -->
            <div class="form-group">
                <label for="muctieu">Mục tiêu điểm DET (Thang điểm từ 10 đến 160)</label>
                <input type="number" id="muctieu" name="muctieu" value="{{ old('muctieu', $user->muctieu) }}" min="10" max="160" step="5" class="form-control" placeholder="Ví dụ: 120">
                <small style="color: #64748b; font-size: 12px;">Điểm mục tiêu sẽ giúp hệ thống đề xuất các bài tập thích ứng phù hợp cho bạn.</small>
                @error('muctieu')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary" style="margin-top: 8px;">Lưu thay đổi hồ sơ</button>
        </form>
    </div>

    <!-- FORM ĐỔI MẬT KHẨU -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Bảo mật & Mật khẩu</h2>
            <p style="color: #64748b; font-size: 13px; margin-top: 4px;">Đảm bảo tài khoản của bạn sử dụng mật khẩu mạnh tối thiểu 8 ký tự, gồm chữ hoa, chữ thường, số và ký tự đặc biệt.</p>
        </div>

        @if (session('status_password'))
            <div class="alert alert-success">
                <span>{{ session('status_password') }}</span>
            </div>
        @endif

        <form method="POST" action="{{ route('profile.password') }}">
            @csrf
            @method('PUT')

            @if(!empty($user->matkhau))
                <div class="form-group">
                    <label for="current_password">Mật khẩu hiện tại <span style="color:red;">*</span></label>
                    <input type="password" id="current_password" name="current_password" class="form-control" placeholder="••••••••" required>
                    @error('current_password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            @else
                <p style="font-size: 13px; color: #0284c7; background: #e0f2fe; padding: 10px; border-radius: 6px; margin-bottom: 16px;">
                    ℹ️ Bạn đã đăng nhập qua Google và chưa thiết lập mật khẩu riêng. Bạn có thể đặt mật khẩu mới ngay dưới đây để có thể đăng nhập bằng email + mật khẩu.
                </p>
            @endif

            <div class="form-group">
                <label for="matkhau">Mật khẩu mới <span style="color:red;">*</span></label>
                <input type="password" id="matkhau" name="matkhau" class="form-control" placeholder="Tối thiểu 8 ký tự (hoa, thường, số, ký tự đặc biệt)" required>
                <small style="display: block; font-size: 12px; color: #64748b; margin-top: 5px; line-height: 1.4;">
                    🔒 Yêu cầu: tối thiểu 8 ký tự, gồm chữ hoa, chữ thường, số và ký tự đặc biệt (VD: Abc@1234).
                </small>
                @error('matkhau')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="matkhau_confirmation">Xác nhận mật khẩu mới <span style="color:red;">*</span></label>
                <input type="password" id="matkhau_confirmation" name="matkhau_confirmation" class="form-control" placeholder="Nhập lại mật khẩu mới" required>
            </div>

            <button type="submit" class="btn btn-outline" style="margin-top: 8px;">Cập nhật mật khẩu</button>
        </form>
    </div>
</div>
@endsection

