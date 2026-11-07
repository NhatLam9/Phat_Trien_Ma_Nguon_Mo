@extends('layouts.app')

@section('title', 'Chuẩn bị thi | DET Practice')
@section('main_class', 'app-main--mock-test')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/mock-test.css') }}">
@endpush

@section('content')
    <div class="container page">
        <div class="page-intro">
            <span class="eyebrow">THI THỬ DET</span>
            <h1>{{ $deThi->ten }}</h1>
            <p>{{ $deThi->mota }}</p>
        </div>

        @if ($errors->any())
            <div class="alert" role="alert">
                {{ $errors->first() }}
            </div>
        @endif

        <section class="exam-card">
            <h2>Chuẩn bị làm bài</h2>

            <p>
                Thời gian:
                <strong>{{ ceil($deThi->thoigian / 60) }} phút</strong>.
            </p>

            <p>
                Đồng hồ chỉ bắt đầu khi bạn bấm
                “Bắt đầu làm bài”.
                Sau khi bắt đầu, thời gian vẫn tiếp tục
                nếu bạn tải lại hoặc rời trang.
            </p>

            <div class="exam-prepare-actions">
                <form method="POST" action="{{ route('mock-test.start') }}">
                    @csrf
                    <input type="hidden" name="dethi_id" value="{{ $deThi->id }}">
                    <input type="hidden" name="xac_nhan_bat_dau" value="1">

                    <button type="submit" class="btn btn-primary">Bắt đầu làm bài</button>
                </form>

                <a href="{{ route('mock-test.index') }}" class="btn btn-outline">
                    Quay lại danh sách đề
                </a>
            </div>
        </section>
    </div>
@endsection