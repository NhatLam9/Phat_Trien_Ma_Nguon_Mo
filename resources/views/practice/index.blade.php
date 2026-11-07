@extends('layouts.app')

@section('title', 'Luyện tập - DET Practice')

@section('content')
    <div class="page-heading">
        <h1>Danh sách dạng bài luyện tập DET</h1>
        <p>Chọn dạng bài bạn muốn luyện tập.</p>
    </div>

    <ul class="practice-list">
        @forelse ($dsDangBai as $item)
            <li class="practice-list__item">
                <strong>{{ $item->ten ?? $item->ten_loai ?? 'Dạng bài luyện tập' }}</strong>
                <a class="btn btn-primary btn-sm" href="{{ route('practice.session', ['loai_id' => $item->id ?? 1]) }}">Luyện tập ngay</a>
            </li>
        @empty
            <li class="practice-list__empty">Chưa có dạng bài nào được cập nhật.</li>
        @endforelse
    </ul>
@endsection
