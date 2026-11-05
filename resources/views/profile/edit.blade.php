@extends('layouts.app')

@section('title', 'Hồ sơ cá nhân - DET Practice')

@section('content')
<div class="profile-wrapper">

    <!-- THÔNG BÁO FLASH STATUS -->
    @if (session('status'))
        <div class="alert alert-success" style="margin-bottom: 20px;">
            <span>{{ session('status') }}</span>
        </div>
    @endif

    @if (session('status_password'))
        <div class="alert alert-success" style="margin-bottom: 20px;">
            <span>{{ session('status_password') }}</span>
        </div>
    @endif

    <!-- HERO PROFILE HEADER -->
    <div class="profile-hero">
        <div class="profile-hero-banner"></div>
        <div class="profile-hero-content">
            <div class="profile-avatar-container">
                <img id="hero-avatar-img" src="{{ $user->avatar_url }}" alt="{{ $user->hoten }}" class="profile-avatar-large">
            </div>

            <div class="profile-header-info">
                <h1 class="profile-header-title">
                    <span>{{ $user->hoten }}</span>
                    @if($user->isAdmin())
                        <span class="badge badge-admin">Quản trị viên</span>
                    @else
                        <span class="badge badge-speaking">Học viên DET</span>
                    @endif

                    @if($user->trangthai === 'active')
                        <span class="badge badge-reading" style="font-size: 11px;">● Hoạt động</span>
                    @else
                        <span class="badge badge-listening" style="font-size: 11px;">● Bị khóa</span>
                    @endif
                </h1>

                <div class="profile-meta-tags">
                    <span class="profile-meta-tag">
                         {{ $user->email }}
                        @if($user->hasVerifiedEmail())
                            <span class="badge badge-speaking" style="padding: 2px 8px; font-size: 11px;">✓ Đã xác minh</span>
                        @else
                            <span class="badge badge-listening" style="padding: 2px 8px; font-size: 11px;">Chưa xác minh</span>
                        @endif
                    </span>
                    <span class="profile-meta-tag">
                         Tham gia: {{ $user->ngaytao ? \Carbon\Carbon::parse($user->ngaytao)->format('d/m/Y') : 'Mới tham gia' }}
                    </span>
                </div>
            </div>

            <div class="profile-hero-actions">
                <button type="button" id="btn-switch-edit" class="btn btn-primary" onclick="toggleEditMode(true)">
                    ✏️ Sửa đổi thông tin
                </button>
                <button type="button" class="btn btn-outline" onclick="toggleEditMode(true, true)">
                    🔒 Đổi mật khẩu
                </button>
            </div>
        </div>
    </div>

    <!-- HÀNG THỐNG KÊ NHANH -->
    <div class="profile-stats-row">
        <!-- Mục tiêu điểm DET -->
        <div class="profile-stat-box">
            <div class="profile-stat-info" style="flex: 1;">
                <div class="stat-title">Mục tiêu điểm DET</div>
                <div class="stat-value">
                    {{ $user->muctieu ? $user->muctieu . ' / 160' : 'Chưa đặt mục tiêu' }}
                </div>
                @if($user->muctieu)
                    @php
                        $percent = min(100, max(5, round(($user->muctieu / 160) * 100)));
                    @endphp
                    <div class="det-progress-bar" title="Mục tiêu {{ $user->muctieu }}/160 điểm ({{ $percent }}%)">
                        <div class="det-progress-fill" style="width: {{ $percent }}%;"></div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Trạng thái tài khoản -->
        <div class="profile-stat-box">
            <div class="profile-stat-info">
                <div class="stat-title">Trạng thái bảo mật</div>
                <div class="stat-value" style="font-size: 16px;">
                    {{ $user->hasVerifiedEmail() ? 'Đã xác thực Email' : 'Cần xác minh Email' }}
                </div>
            </div>
        </div>

        <!-- Phương thức xác thực -->
        <div class="profile-stat-box">
            <div class="profile-stat-info">
                <div class="stat-title">Đăng nhập qua</div>
                <div class="stat-value" style="font-size: 16px;">
                    @if($user->google_id)
                        Google Account
                    @else
                        Email & Mật khẩu
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- ===================================================================== -->
    <!-- 1. CHẾ ĐỘ XEM THÔNG TIN (VIEW MODE - MẶC ĐỊNH HIỂN THỊ)               -->
    <!-- ===================================================================== -->
    <div id="profile-view-section" class="profile-card">
        <div class="profile-card-header">
            <div>
                <h2 class="card-title" style="margin-bottom: 2px;">Thông tin hồ sơ cá nhân</h2>
                <p style="color: #64748b; font-size: 13px; margin: 0;">Xem thông tin tài khoản của bạn trên nền tảng DET Practice.</p>
            </div>
        </div>

        <div class="profile-grid">
            <div class="profile-field-box">
                <div class="profile-field-label">Họ và tên</div>
                <div class="profile-field-val">{{ $user->hoten }}</div>
            </div>

            <div class="profile-field-box">
                <div class="profile-field-label">Địa chỉ Email</div>
                <div class="profile-field-val">
                    <span>{{ $user->email }}</span>
                    @if($user->hasVerifiedEmail())
                        <span class="badge badge-speaking" style="font-size: 11px;">✓ Đã xác minh</span>
                    @else
                        <a href="{{ route('verification.notice') }}" class="badge badge-listening" style="font-size: 11px; text-decoration: none;">
                            Xác minh ngay →
                        </a>
                    @endif
                </div>
            </div>

            <div class="profile-field-box">
                <div class="profile-field-label">Mục tiêu điểm Duolingo English Test (DET)</div>
                <div class="profile-field-val">
                    @if($user->muctieu)
                        <span style="color: #00b86b; font-weight: 800; font-size: 17px;">{{ $user->muctieu }}</span>
                        <span style="color: #64748b; font-weight: 600; font-size: 13px;">/ 160 điểm</span>
                    @else
                        <span style="color: #94a3b8; font-style: italic;">Chưa thiết lập mục tiêu</span>
                    @endif
                </div>
            </div>

            <div class="profile-field-box">
                <div class="profile-field-label">Vai trò trong hệ thống</div>
                <div class="profile-field-val">
                    @if($user->isAdmin())
                        <span class="badge badge-admin">Quản trị viên (Admin)</span>
                    @else
                        <span class="badge badge-speaking">Học viên luyện thi (User)</span>
                    @endif
                </div>
            </div>

            <div class="profile-field-box">
                <div class="profile-field-label">Mật khẩu tài khoản</div>
                <div class="profile-field-val" style="display: flex; align-items: center; justify-content: space-between;">
                    <span>••••••••••••</span>
                    <button type="button" class="btn btn-outline btn-sm" style="padding: 4px 10px; font-size: 12px;" onclick="toggleEditMode(true, true)">
                        Đổi mật khẩu
                    </button>
                </div>
            </div>

            <div class="profile-field-box">
                <div class="profile-field-label">Thời gian tạo tài khoản</div>
                <div class="profile-field-val">
                    {{ $user->ngaytao ? \Carbon\Carbon::parse($user->ngaytao)->format('d/m/Y - H:i') : 'Mới tham gia' }}
                </div>
            </div>
        </div>
    </div>

    <!-- ===================================================================== -->
    <!-- 2. CHẾ ĐỘ SỬA THÔNG TIN & CÀI LẠI MẬT KHẨU (EDIT MODE)              -->
    <!-- ===================================================================== -->
    <div id="profile-edit-section" class="profile-card" style="display: none;">
        <div class="profile-card-header">
            <div>
                <h2 class="card-title" style="margin-bottom: 2px;">Chỉnh sửa thông tin hồ sơ & Đổi mật khẩu</h2>
                <p style="color: #64748b; font-size: 13px; margin: 0;">Cập nhật ảnh đại diện, họ tên, mục tiêu điểm thi và đổi mật khẩu tài khoản.</p>
            </div>
            <button type="button" class="btn btn-outline btn-sm" onclick="toggleEditMode(false)">
                ✕ Hủy bỏ
            </button>
        </div>

        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" id="profile-edit-form">
            @csrf
            @method('PUT')

            <!-- 1. TẢI LÊN ẢNH ĐẠI DIỆN & GỠ ẢNH TRỰC TIẾP -->
            <div class="avatar-upload-zone">
                <div style="position: relative; flex-shrink: 0;">
                    <img id="avatar-preview-img" src="{{ $user->avatar_url }}" alt="{{ $user->hoten }}" class="avatar-upload-preview">
                    <input type="hidden" id="remove_avatar_input" name="remove_avatar" value="0">
                </div>
                
                <div style="flex: 1;">
                    <label for="anh" style="font-weight: 700; color: #1e293b; margin-bottom: 6px; display: block;">
                        Ảnh đại diện
                    </label>

                    <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
                        <input type="file" id="anh" name="anh" accept="image/jpeg,image/png,image/jpg,image/webp,image/svg+xml" class="form-control" style="font-size: 13px; max-width: 340px;" onchange="previewSelectedAvatar(this)">
                        
                        <button type="button" id="btn-remove-avatar" class="btn btn-sm" style="background: #fee2e2; color: #b91c1c; border: 1px solid #fca5a5; padding: 7px 14px; font-weight: 700;" onclick="triggerRemoveAvatar()">
                            🗑️ Gỡ ảnh đại diện
                        </button>
                    </div>

                    <!-- Thông báo khi bấm Gỡ ảnh -->
                    <div id="remove-avatar-notice" style="display: none; margin-top: 8px; color: #b91c1c; font-size: 12px; font-weight: 600; background: #fef2f2; padding: 6px 12px; border-radius: 6px; border: 1px solid #fecaca;">
                        Đã chọn gỡ ảnh đại diện về mặc định. Nhấn nút <strong>"Lưu thay đổi hồ sơ"</strong> bên dưới để hoàn tất.
                        <button type="button" onclick="cancelRemoveAvatar()" style="background: none; border: none; color: #0284c7; text-decoration: underline; cursor: pointer; margin-left: 6px; font-size: 12px; font-weight: 700;">Hoàn tác</button>
                    </div>

                    <small style="color: #64748b; font-size: 12px; margin-top: 6px; display: block;">Định dạng: JPG, PNG, WEBP. Tối đa 2MB.</small>

                    @error('anh')
                        <div class="invalid-feedback" style="display: block; margin-top: 6px;">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- 2. HỌ VÀ TÊN -->
            <div class="form-group">
                <label for="hoten">Họ và tên <span style="color: #ef4444;">*</span></label>
                <input type="text" id="hoten" name="hoten" value="{{ old('hoten', $user->hoten) }}" class="form-control" required placeholder="Nhập họ và tên đầy đủ">
                @error('hoten')
                    <div class="invalid-feedback" style="display: block;">{{ $message }}</div>
                @enderror
            </div>

            <!-- 3. EMAIL (CHỈ ĐỌC) -->
            <div class="form-group">
                <label for="email">Địa chỉ Email (Cố định theo tài khoản)</label>
                <div style="display: flex; gap: 12px; align-items: center;">
                    <input type="email" id="email" value="{{ $user->email }}" class="form-control" disabled style="background: #f1f5f9; cursor: not-allowed; color: #64748b;">
                    @if($user->hasVerifiedEmail())
                        <span class="badge badge-speaking" style="white-space: nowrap;">✓ Đã xác minh</span>
                    @else
                        <span class="badge badge-listening" style="white-space: nowrap;">Chưa xác minh</span>
                    @endif
                </div>
                <small style="color: #64748b; font-size: 12px; margin-top: 4px; display: block;">Email dùng để nhận liên kết khôi phục mật khẩu và thông báo bài tập.</small>
            </div>

            <!-- 4. MỤC TIÊU ĐIỂM DET -->
            <div class="form-group">
                <label for="muctieu">Mục tiêu điểm DET (Thang điểm từ 10 đến 160)</label>
                <input type="number" id="muctieu" name="muctieu" value="{{ old('muctieu', $user->muctieu) }}" min="10" max="160" step="5" class="form-control" placeholder="Ví dụ: 120">
                <small style="color: #64748b; font-size: 12px; margin-top: 4px; display: block;">Mục tiêu điểm số giúp hệ thống đề xuất bài tập thích ứng phù hợp cho bạn.</small>
                @error('muctieu')
                    <div class="invalid-feedback" style="display: block;">{{ $message }}</div>
                @enderror
            </div>

            <!-- ===================================================================== -->
            <!-- 5. PHẦN CÀI LẠI MẬT KHẨU (ĐÃ ĐƯỢC ĐƯA VÀO BÊN TRONG PHẦN CHỈNH SỬA)   -->
            <!-- ===================================================================== -->
            <div id="inner-password-box" style="margin-top: 28px; padding: 22px; background: #f8fafc; border-radius: 12px; border: 1px solid #e2e8f0;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 14px; border-bottom: 1px solid #e2e8f0; padding-bottom: 10px;">
                    <div>
                        <h3 style="font-size: 16px; font-weight: 800; color: #1e293b; margin: 0;">
                            🔑 Cài lại / Đổi mật khẩu tài khoản
                        </h3>
                        <p style="color: #64748b; font-size: 12px; margin: 3px 0 0;">
                            Để trống các ô bên dưới nếu bạn không có nhu cầu đổi mật khẩu.
                        </p>
                    </div>
                    <span class="badge badge-admin" style="font-size: 11px;">Tùy chọn</span>
                </div>

                @if(!empty($user->matkhau) && empty($user->google_id))
                    <!-- Mật khẩu hiện tại (Chỉ yêu cầu khi tài khoản dùng mật khẩu thường và muốn đổi) -->
                    <div class="form-group">
                        <label for="current_password">Mật khẩu hiện tại</label>
                        <div class="password-field-wrapper">
                            <input type="password" id="current_password" name="current_password" class="form-control" placeholder="Nhập mật khẩu hiện tại của bạn">
                            <button type="button" class="btn-toggle-eye" onclick="togglePasswordVisibility('current_password', this)" title="Hiện mật khẩu" tabindex="-1">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg>
                            </button>
                        </div>
                        <small style="color: #64748b; font-size: 12px;">Chỉ cần nhập khi bạn muốn thiết lập mật khẩu mới.</small>
                        @error('current_password')
                            <div class="invalid-feedback" style="display: block;">{{ $message }}</div>
                        @enderror
                    </div>
                @elseif(!empty($user->google_id))
                    <div style="font-size: 13px; color: #0369a1; background: #e0f2fe; padding: 10px 14px; border-radius: 8px; margin-bottom: 16px; border: 1px solid #bae6fd;">
                        Tài khoản của bạn liên kết qua <strong>Google</strong>. Bạn có thể nhập mật khẩu mới bên dưới để tạo mật khẩu đăng nhập trực tiếp (không cần nhập mật khẩu cũ).
                    </div>
                @endif

                <!-- Mật khẩu mới -->
                <div class="form-group">
                    <label for="matkhau">Mật khẩu mới</label>
                    <div class="password-field-wrapper">
                        <input type="password" id="matkhau" name="matkhau" class="form-control" placeholder="Tối thiểu 8 ký tự (hoa, thường, số, ký tự đặc biệt)">
                        <button type="button" class="btn-toggle-eye" onclick="togglePasswordVisibility('matkhau', this)" title="Hiện mật khẩu" tabindex="-1">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                        </button>
                    </div>
                    @error('matkhau')
                        <div class="invalid-feedback" style="display: block;">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Xác nhận mật khẩu mới -->
                <div class="form-group" style="margin-bottom: 0;">
                    <label for="matkhau_confirmation">Xác nhận lại mật khẩu mới</label>
                    <div class="password-field-wrapper">
                        <input type="password" id="matkhau_confirmation" name="matkhau_confirmation" class="form-control" placeholder="Nhập lại mật khẩu mới để xác nhận">
                        <button type="button" class="btn-toggle-eye" onclick="togglePasswordVisibility('matkhau_confirmation', this)" title="Hiện mật khẩu" tabindex="-1">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- HÀNG NÚT HÀNH ĐỘNG -->
            <div style="margin-top: 24px; padding-top: 18px; border-top: 1px solid #e2e8f0; display: flex; gap: 12px; align-items: center;">
                <button type="submit" class="btn btn-primary">
                    Lưu thay đổi hồ sơ
                </button>
                <button type="button" class="btn btn-outline" onclick="toggleEditMode(false)">
                    ✕ Hủy bỏ
                </button>
            </div>
        </form>
    </div>

</div>

<!-- JAVASCRIPT ĐIỀU KHIỂN CHUYỂN ĐỔI CHẾ ĐỘ XEM / SỬA VÀ XEM TRƯỚC ẢNH -->
<script>
    const defaultAvatarUrl = 'https://ui-avatars.com/api/?name=' + encodeURIComponent(@json($user->hoten)) + '&background=00b86b&color=ffffff';
    const originalAvatarUrl = @json($user->avatar_url);

    // Chuyển đổi giữa chế độ Xem và Sửa thông tin
    function toggleEditMode(showEdit, scrollToPassword = false) {
        const viewSection = document.getElementById('profile-view-section');
        const editSection = document.getElementById('profile-edit-section');
        const btnSwitch = document.getElementById('btn-switch-edit');

        if (showEdit) {
            viewSection.style.display = 'none';
            editSection.style.display = 'block';
            if (btnSwitch) btnSwitch.style.display = 'none';

            if (scrollToPassword) {
                const passBox = document.getElementById('inner-password-box');
                if (passBox) {
                    passBox.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    const targetInput = document.getElementById('current_password') || document.getElementById('matkhau');
                    if (targetInput) setTimeout(() => targetInput.focus(), 400);
                }
            } else {
                editSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        } else {
            editSection.style.display = 'none';
            viewSection.style.display = 'block';
            if (btnSwitch) btnSwitch.style.display = 'inline-flex';
            viewSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    }

    // Xem trước ảnh đại diện khi chọn file mới
    function previewSelectedAvatar(input) {
        if (input.files && input.files[0]) {
            const file = input.files[0];
            
            if (file.size > 2 * 1024 * 1024) {
                alert('Tệp ảnh quá lớn! Vui lòng chọn ảnh có dung lượng dưới 2MB.');
                input.value = '';
                return;
            }

            // Hủy trạng thái gỡ ảnh nếu vừa chọn file mới
            document.getElementById('remove_avatar_input').value = '0';
            document.getElementById('remove-avatar-notice').style.display = 'none';

            const reader = new FileReader();
            reader.onload = function(e) {
                const previewImg = document.getElementById('avatar-preview-img');
                const heroImg = document.getElementById('hero-avatar-img');
                if (previewImg) previewImg.src = e.target.result;
                if (heroImg) heroImg.src = e.target.result;
            };
            reader.readAsDataURL(file);
        }
    }

    // Xử lý khi nhấn nút Gỡ ảnh đại diện
    function triggerRemoveAvatar() {
        // Đặt giá trị hidden input thành 1 để controller nhận diện
        document.getElementById('remove_avatar_input').value = '1';

        // Xóa trắng input file nếu đã chọn tệp
        const fileInput = document.getElementById('anh');
        if (fileInput) fileInput.value = '';

        // Đổi ảnh preview sang avatar chữ cái mặc định ngay lập tức
        const previewImg = document.getElementById('avatar-preview-img');
        const heroImg = document.getElementById('hero-avatar-img');
        if (previewImg) previewImg.src = defaultAvatarUrl;
        if (heroImg) heroImg.src = defaultAvatarUrl;

        // Hiển thị thông báo hướng dẫn bấm Lưu
        document.getElementById('remove-avatar-notice').style.display = 'block';
    }

    // Hoàn tác gỡ ảnh
    function cancelRemoveAvatar() {
        document.getElementById('remove_avatar_input').value = '0';
        const previewImg = document.getElementById('avatar-preview-img');
        const heroImg = document.getElementById('hero-avatar-img');
        if (previewImg) previewImg.src = originalAvatarUrl;
        if (heroImg) heroImg.src = originalAvatarUrl;
        document.getElementById('remove-avatar-notice').style.display = 'none';
    }

    // Tự động mở form sửa thông tin nếu có lỗi từ server trả về
    @if ($errors->has('hoten') || $errors->has('muctieu') || $errors->has('anh') || $errors->has('current_password') || $errors->has('matkhau'))
        document.addEventListener('DOMContentLoaded', function() {
            const hasPassError = @json($errors->has('current_password') || $errors->has('matkhau'));
            toggleEditMode(true, hasPassError);
        });
    @endif
</script>
@endsection
