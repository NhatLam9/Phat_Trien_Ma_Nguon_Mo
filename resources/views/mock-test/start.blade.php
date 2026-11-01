
@extends('layouts.app')

@section('title', 'Thi thử DET - DET Practice')
@section('main_class', 'app-main--mock-test')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/mock-test.css') }}">
@endpush

@section('content')
    <div class="container page">

        <!-- Gioi thieu -->
        <div class="page-intro">
            <span class="eyebrow">MOCK TEST · MT01</span>

            <h1>Danh sách đề thi thử</h1>

            <p>
                Lựa chọn một đề thi phù hợp để bắt đầu
                hành trình luyện tập Duolingo English Test.
            </p>
        </div>

        <!-- Thong bao loi -->
        @if ($errors->any())
            <div class="alert" role="alert">
                {{ $errors->first() }}
            </div>
        @endif

        <!-- Tiep tuc bai thi dang mo -->
        @if ($lanThiDangMo)
            <section class="resume-box">
                <div>
                    <strong>Bạn có một bài thi đang diễn ra</strong>

                    <p>
                        Phiên thi #{{ $lanThiDangMo->id }}.
                        Bạn có thể quay lại tiếp tục bài thi.
                    </p>
                </div>

                <a class="btn btn-dark"
                   href="{{ route('mock-test.show', $lanThiDangMo->id) }}">
                    Tiếp tục thi →
                </a>
            </section>
        @endif

        <!-- Danh sach de thi -->
        <div class="section-title">
            <h2>Đề thi hiện có</h2>
            <span>{{ $deThi->count() }} đề thi</span>
        </div>

        <div class="exam-grid">
            @forelse ($deThi as $de)
                <article class="exam-card">

                    <div class="card-top">
                        <span class="exam-label">
                            {{ strtoupper($de->chedo) }}
                        </span>

                        <span class="exam-type">
                            {{ $de->kieu === 'adaptive'
                                ? 'Adaptive'
                                : 'Fixed' }}
                        </span>
                    </div>

                    <h3>{{ $de->ten }}</h3>

                    <p class="description">
                        {{ $de->mota ?: 'Đề thi luyện tập DET Practice.' }}
                    </p>

                    <div class="exam-meta">
                        <span>
                            ⏱ {{ ceil($de->thoigian / 60) }} phút
                        </span>

                        <span>
                            {{ $de->chedo === 'full'
                                ? 'Toàn bài'
                                : 'Theo kỹ năng' }}
                        </span>
                    </div>

                    @auth
                        @if ($lanThiDangMo &&
                             $lanThiDangMo->dethi_id == $de->id)

                            <a class="btn btn-dark full-width"
                               href="{{ route('mock-test.show', $lanThiDangMo->id) }}">
                                Tiếp tục bài thi
                            </a>

                        @elseif ($lanThiDangMo)

                            <button class="btn btn-disabled full-width"
                                    disabled>
                                Có bài thi chưa hoàn thành
                            </button>

                        @else

                            <form action="{{ route('mock-test.start') }}"
                                  method="POST">
                                @csrf

                                <input type="hidden"
                                       name="dethi_id"
                                       value="{{ $de->id }}">

                                <button type="submit"
                                        class="btn btn-primary full-width">
                                    Bắt đầu thi →
                                </button>
                            </form>

                        @endif
                    @else
                        <div class="auth-notice">
                            Đăng nhập để bắt đầu thi.

                            <a href="{{ route('login') }}">
                                Đăng nhập →
                            </a>
                        </div>
                    @endauth

                </article>
            @empty
                <div class="empty-state">
                    <div class="empty-symbol">✎</div>

                    <h3>Chưa có đề thi được xuất bản</h3>

                    <p>
                        Đề thi sẽ xuất hiện tại đây khi
                        quản trị viên xuất bản nội dung.
                    </p>
                </div>
            @endforelse
        </div>

    </div>
@endsection