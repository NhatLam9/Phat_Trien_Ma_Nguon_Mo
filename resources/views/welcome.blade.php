@extends('layouts.app')

@section('title', 'DET Practice - Nền tảng luyện thi Duolingo English Test')

@push('styles')
<style>
    /* --- HERO BANNER --- */
    .hero-banner {
        position: relative;
        background: linear-gradient(135deg, #ffffff 0%, #eefcf4 60%, #e1f8eb 100%);
        border: 1px solid #cceee0;
        border-radius: 20px;
        padding: 56px 40px;
        text-align: center;
        box-shadow: 0 10px 30px rgba(0, 184, 107, 0.08);
        margin-bottom: 48px;
        overflow: hidden;
    }

    .hero-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: #e6f9f0;
        color: #00874e;
        border: 1px solid #b2ecd1;
        padding: 6px 16px;
        border-radius: 20px;
        font-size: 13px;
        font-weight: 700;
        margin-bottom: 18px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .hero-title {
        font-size: 38px;
        font-weight: 800;
        color: #1a202c;
        line-height: 1.25;
        margin: 0 auto 16px;
        max-width: 820px;
    }

    .hero-title span {
        color: #00b86b;
    }

    .hero-subtitle {
        font-size: 17px;
        color: #4a5568;
        max-width: 740px;
        margin: 0 auto 28px;
        line-height: 1.65;
    }

    .hero-cta {
        display: flex;
        justify-content: center;
        gap: 16px;
        flex-wrap: wrap;
        margin-bottom: 36px;
    }

    .stats-bar {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 18px 24px;
        max-width: 820px;
        margin: 0 auto;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04);
    }

    .stat-item {
        text-align: center;
    }

    .stat-value {
        font-size: 24px;
        font-weight: 800;
        color: #00b86b;
    }

    .stat-label {
        font-size: 12px;
        color: #64748b;
        margin-top: 2px;
        font-weight: 600;
    }

    /* --- SECTION HEADINGS --- */
    .section-header {
        text-align: center;
        margin-bottom: 32px;
    }

    .section-header h2 {
        font-size: 26px;
        font-weight: 800;
        color: #1e293b;
    }

    .section-header p {
        font-size: 15px;
        color: #64748b;
        margin-top: 6px;
        max-width: 700px;
        margin-left: auto;
        margin-right: auto;
    }

    /* --- ABOUT DET EXAM SECTION --- */
    .det-intro-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 36px 32px;
        margin-bottom: 48px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.03);
    }

    .det-intro-grid {
        display: grid;
        grid-template-columns: 1.2fr 1fr;
        gap: 32px;
        align-items: center;
    }

    .det-intro-text h3 {
        font-size: 22px;
        font-weight: 800;
        color: #1e293b;
        margin-bottom: 12px;
    }

    .det-intro-text p {
        font-size: 14.5px;
        color: #475569;
        line-height: 1.7;
        margin-bottom: 14px;
    }

    .det-highlights {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 14px;
    }

    .det-highlight-box {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 16px;
    }

    .det-highlight-box strong {
        display: block;
        font-size: 15px;
        color: #00b86b;
        margin-bottom: 4px;
    }

    .det-highlight-box span {
        font-size: 13px;
        color: #64748b;
        line-height: 1.5;
    }

    /* --- 4 CORE PLATFORM FEATURES --- */
    .features-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 22px;
        margin-bottom: 48px;
    }

    .feature-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 28px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        transition: border-color 0.2s, box-shadow 0.2s;
    }

    .feature-card:hover {
        border-color: #00b86b;
        box-shadow: 0 8px 20px rgba(0, 184, 107, 0.08);
    }

    .feature-icon {
        width: 52px;
        height: 52px;
        border-radius: 12px;
        background: #e6f9f0;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 26px;
        margin-bottom: 16px;
    }

    .feature-title {
        font-size: 19px;
        font-weight: 800;
        color: #1e293b;
        margin-bottom: 8px;
    }

    .feature-desc {
        font-size: 14px;
        color: #64748b;
        line-height: 1.65;
        margin-bottom: 16px;
    }

    .feature-tags {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .feature-tag {
        background: #f1f5f9;
        color: #475569;
        font-size: 12px;
        font-weight: 600;
        padding: 4px 10px;
        border-radius: 6px;
    }

    /* --- BOTTOM CTA --- */
    .cta-banner {
        background: #ffffff;
        border: 2px solid #00b86b;
        border-radius: 16px;
        padding: 36px 28px;
        text-align: center;
        margin-bottom: 20px;
    }

    .cta-banner h3 {
        font-size: 24px;
        font-weight: 800;
        color: #1e293b;
        margin-bottom: 8px;
    }

    .cta-banner p {
        font-size: 15px;
        color: #64748b;
        margin-bottom: 20px;
    }

    @media (max-width: 868px) {
        .hero-title {
            font-size: 28px;
        }
        .stats-bar {
            grid-template-columns: repeat(2, 1fr);
        }
        .det-intro-grid,
        .features-grid {
            grid-template-columns: 1fr;
        }
        .det-highlights {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')
    <!-- 1. HERO BANNER GIỚI THIỆU & NÚT BẮT ĐẦU NGAY DẪN ĐẾN ĐĂNG KÝ -->
    <section class="hero-banner">
        <div class="hero-badge">
            ✨ Nền tảng luyện thi Duolingo English Test trực tuyến
        </div>

        <h1 class="hero-title">
            Chinh Phục Điểm Số Mục Tiêu Cùng <span>DET Practice</span>
        </h1>

        <p class="hero-subtitle">
            Hệ thống ôn luyện và mô phỏng bài thi <strong>Duolingo English Test (DET)</strong> chuẩn quốc tế. Tích hợp ngân hàng câu hỏi 4 kỹ năng, thi thử thích ứng (Adaptive Mock Test), chấm điểm Writing & Speaking bằng AI và cấp chứng chỉ thi thử trực tuyến.
        </p>

        <div class="hero-cta">
            @auth
                <a href="{{ route('practice.index') }}" class="btn btn-primary" style="padding: 13px 32px; font-size: 16px;">
                    Bắt đầu luyện tập ngay →
                </a>
                <a href="{{ url('/mock-test') }}" class="btn btn-outline" style="padding: 13px 26px; font-size: 15px;">
                    Vào phòng Thi thử (Mock Test)
                </a>
            @else
                <!-- Đúng ngữ cảnh Khách: Nút "Bắt đầu ngay" dẫn đến trang Đăng ký -->
                <a href="{{ route('register') }}" class="btn btn-primary" style="padding: 13px 32px; font-size: 16px;">
                    Bắt đầu ngay →
                </a>
                <a href="{{ route('login') }}" class="btn btn-outline" style="padding: 13px 26px; font-size: 15px;">
                    Đăng nhập tài khoản
                </a>
            @endauth
        </div>

        <div class="stats-bar">
            <div class="stat-item">
                <div class="stat-value">14</div>
                <div class="stat-label">Dạng bài DET chuẩn</div>
            </div>
            <div class="stat-item">
                <div class="stat-value">4</div>
                <div class="stat-label">Kỹ năng Nghe-Nói-Đọc-Viết</div>
            </div>
            <div class="stat-item">
                <div class="stat-value">10–160</div>
                <div class="stat-label">Thang điểm chuẩn DET</div>
            </div>
            <div class="stat-item">
                <div class="stat-value">Auto & AI</div>
                <div class="stat-label">Chấm tự động & Nhận xét AI</div>
            </div>
        </div>
    </section>

    <!-- 2. GIỚI THIỆU VỀ KỲ THI DUOLINGO ENGLISH TEST (DET) -->
    <section class="det-intro-card">
        <div class="det-intro-grid">
            <div class="det-intro-text">
                <h3>Giới thiệu về kỳ thi Duolingo English Test (DET)</h3>
                <p>
                    <strong>Duolingo English Test (DET)</strong> là bài thi đánh giá năng lực tiếng Anh học thuật trực tuyến hiện đại, được hơn 5.000 trường đại học và tổ chức giáo dục trên toàn thế giới công nhận (tương đương chứng chỉ IELTS và TOEFL iBT).
                </p>
                <p>
                    Điểm đặc trưng của kỳ thi DET là cơ chế <strong>Thi thích ứng máy tính (Computer-Adaptive Testing)</strong>: độ khó của câu hỏi kế tiếp sẽ tự động thay đổi từ cấp độ 1 đến 10 dựa trên năng lực trả lời thực tế của thí sinh, giúp đánh giá chính xác trình độ chỉ trong 45–60 phút.
                </p>
                <p style="margin-bottom: 0;">
                    Bài thi chấm điểm trên thang <strong>10 – 160</strong> (quy đổi tương ứng từ CEFR A1 đến C2) cùng 4 điểm thành phần: <strong>Reading (Đọc), Listening (Nghe), Writing (Viết) và Speaking (Nói)</strong>.
                </p>
            </div>

            <div class="det-highlights">
                <div class="det-highlight-box">
                    <strong>📖 Reading (Đọc)</strong>
                    <span>Read and Select, Fill in the Blanks, Read and Complete, Interactive Reading.</span>
                </div>
                <div class="det-highlight-box">
                    <strong>🎧 Listening (Nghe)</strong>
                    <span>Listen and Type, Interactive Listening (Listen & Complete, Respond, Summarise).</span>
                </div>
                <div class="det-highlight-box">
                    <strong>✍️ Writing (Viết)</strong>
                    <span>Write About the Photo, Interactive Writing, Writing Sample (chấm bằng AI).</span>
                </div>
                <div class="det-highlight-box">
                    <strong>🎙️ Speaking (Nói)</strong>
                    <span>Read Aloud, Speak About the Photo, Read/Listen Then Speak, Speaking Sample.</span>
                </div>
            </div>
        </div>
    </section>

    <!-- 3. CÁC TÍNH NĂNG CHÍNH CỦA NỀN TẢNG (Luyện tập, Mock Test, AI chấm bài, Chứng chỉ thi thử) -->
    <section>
        <div class="section-header">
            <h2>Các tính năng chính của nền tảng DET Practice</h2>
            <p>Hệ sinh thái ôn luyện toàn diện giúp người học làm quen từng dạng câu hỏi, thi thử sát thực tế và theo dõi sự tiến bộ mỗi ngày.</p>
        </div>

        <div class="features-grid">
            <!-- Tính năng 1: Luyện tập (Practice) -->
            <div class="feature-card">
                <div>
                    <div class="feature-icon">🎯</div>
                    <h3 class="feature-title">1. Luyện tập theo dạng bài (Practice)</h3>
                    <p class="feature-desc">
                        Luyện tập chuyên sâu từng dạng bài thuộc 4 kỹ năng Đọc, Nghe, Viết, Nói; lọc câu hỏi theo chủ đề và độ khó từ 1–10 (ưu tiên câu chưa làm). Nhận đáp án và giải thích chi tiết ngay sau khi nộp, hỗ trợ đánh dấu câu hỏi yếu (Bookmark) để ôn tập lại.
                    </p>
                </div>
                <div class="feature-tags">
                    <span class="feature-tag">Lọc độ khó 1–10</span>
                    <span class="feature-tag">Chấm tự động tức thì</span>
                    <span class="feature-tag">Đánh dấu câu yếu</span>
                    <span class="feature-tag">Lịch sử luyện tập</span>
                </div>
            </div>

            <!-- Tính năng 2: Mock Test (Thi thử) -->
            <div class="feature-card">
                <div>
                    <div class="feature-icon">⏱️</div>
                    <h3 class="feature-title">2. Thi thử thích ứng (Mock Test)</h3>
                    <p class="feature-desc">
                        Mô phỏng bài thi DET đầy đủ (Full Test) hoặc từng kỹ năng (Section Test). Hệ thống cấp câu hỏi kế tiếp theo thuật toán thích ứng năng lực (Adaptive), kiểm soát chặt chẽ thời gian làm bài và lập báo cáo điểm tổng 10–160 cùng 4 điểm thành phần.
                    </p>
                </div>
                <div class="feature-tags">
                    <span class="feature-tag">Adaptive 1–10</span>
                    <span class="feature-tag">Full / Section Test</span>
                    <span class="feature-tag">Kiểm soát thời gian</span>
                    <span class="feature-tag">Báo cáo chi tiết</span>
                </div>
            </div>

            <!-- Tính năng 3: AI chấm bài & AI Tutor -->
            <div class="feature-card">
                <div>
                    <div class="feature-icon">🤖</div>
                    <h3 class="feature-title">3. AI chấm bài & Trợ lý AI Tutor</h3>
                    <p class="feature-desc">
                        Tích hợp dịch vụ AI chấm điểm tự động cho các dạng bài tự luận Writing và ghi âm Speaking theo bộ tiêu chí ngữ pháp, từ vựng, độ trôi chảy, phát âm và mạch lạc. Hỗ trợ AI Tutor sửa lỗi ngữ pháp, dịch thuật Anh–Việt và gợi ý viết lại câu học thuật.
                    </p>
                </div>
                <div class="feature-tags">
                    <span class="feature-tag">Chấm Writing & Speaking</span>
                    <span class="feature-tag">Nhận xét chi tiết</span>
                    <span class="feature-tag">Sửa ngữ pháp</span>
                    <span class="feature-tag">Dịch & Viết lại bài</span>
                </div>
            </div>

            <!-- Tính năng 4: Chứng chỉ thi thử & Dashboard -->
            <div class="feature-card">
                <div>
                    <div class="feature-icon">🎓</div>
                    <h3 class="feature-title">4. Chứng chỉ thi thử & Dashboard tiến độ</h3>
                    <p class="feature-desc">
                        Sau khi hoàn thành bài thi thử Mock Test, hệ thống cấp Chứng chỉ thi thử DET có mã định danh duy nhất, hiển thị điểm tổng, 4 điểm kỹ năng và cấp độ CEFR. Theo dõi thống kê học tập, biểu đồ lịch sử điểm số và chuỗi ngày học liên tiếp (Streak) trên Dashboard.
                    </p>
                </div>
                <div class="feature-tags">
                    <span class="feature-tag">Chứng chỉ thi thử DET</span>
                    <span class="feature-tag">Tra cứu mã chứng chỉ</span>
                    <span class="feature-tag">Biểu đồ điểm SVG</span>
                    <span class="feature-tag">Chuỗi ngày học (Streak)</span>
                </div>
            </div>
        </div>
    </section>

    <!-- 4. KÊU GỌI HÀNH ĐỘNG (CTA) -->
    <section class="cta-banner">
        @auth
            <h3>Sẵn sàng chinh phục mục tiêu điểm DET của bạn?</h3>
            <p>Hãy chọn dạng bài luyện tập hoặc làm bài thi thử để đánh giá năng lực ngay hôm nay.</p>
            <a href="{{ route('practice.index') }}" class="btn btn-primary" style="padding: 12px 30px; font-size: 15px;">
                Vào khu vực Luyện tập →
            </a>
        @else
            <h3>Bắt đầu lộ trình luyện thi Duolingo English Test ngay hôm nay</h3>
            <p>Tạo tài khoản miễn phí chỉ trong 30 giây để mở khóa toàn bộ tính năng Luyện tập, Mock Test, AI chấm bài và Chứng chỉ thi thử.</p>
            <a href="{{ route('register') }}" class="btn btn-primary" style="padding: 12px 32px; font-size: 15px;">
                Bắt đầu ngay →
            </a>
        @endauth
    </section>
@endsection
