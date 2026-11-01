@extends('layouts.app')

@section('title', 'Chọn câu hỏi luyện tập - DET Practice')

@section('content')
<div style="margin-bottom: 24px;">
    <a href="{{ route('practice.index') }}" style="color: #64748b; text-decoration: none; font-size: 14px;">← Quay lại danh sách dạng bài</a>
    <h1 style="font-size: 26px; font-weight: 800; color: #1e293b; margin-top: 8px;">
        🎯 {{ $selectedType->ten ?? 'Chọn câu hỏi luyện tập' }}
    </h1>
    <p style="color: #64748b; font-size: 14px;">
        {{ $selectedType->mota ?? 'Lọc câu hỏi theo mức độ khó hoặc từ khóa phù hợp với khả năng hiện tại.' }}
    </p>
</div>

<!-- THANH TÌM KIẾM VÀ BỘ LỌC (LT02 & LT09) -->
<div class="card" style="padding: 18px 24px;">
    <form method="GET" action="{{ route('practice.select') }}" style="display: flex; gap: 14px; flex-wrap: wrap; align-items: flex-end;">
        <div style="flex: 2; min-width: 220px;">
            <label style="font-size: 13px; font-weight: 600; color: #475569; display: block; margin-bottom: 4px;">Dạng bài</label>
            <select name="loai_id" class="form-control" style="font-size: 13px;">
                <option value="">-- Tất cả dạng bài --</option>
                @foreach($types as $t)
                    <option value="{{ $t->id }}" {{ (string)$loaiId === (string)$t->id ? 'selected' : '' }}>
                        [{{ strtoupper($t->kynang) }}] {{ $t->ten }}
                    </option>
                @endforeach
            </select>
        </div>

        <div style="flex: 1; min-width: 140px;">
            <label style="font-size: 13px; font-weight: 600; color: #475569; display: block; margin-bottom: 4px;">Độ khó</label>
            <select name="dokho" class="form-control" style="font-size: 13px;">
                <option value="">-- Tất cả độ khó --</option>
                @for($i = 1; $i <= 10; $i++)
                    <option value="{{ $i }}" {{ (string)$dokho === (string)$i ? 'selected' : '' }}>
                        Cấp độ {{ $i }} {{ $i <= 3 ? '(Dễ)' : ($i <= 7 ? '(Trung bình)' : '(Khó)') }}
                    </option>
                @endfor
            </select>
        </div>

        <div style="flex: 2; min-width: 200px;">
            <label style="font-size: 13px; font-weight: 600; color: #475569; display: block; margin-bottom: 4px;">Từ khóa tìm kiếm (LT09)</label>
            <input type="text" name="q" value="{{ $keyword }}" placeholder="Nhập từ khóa nội dung..." class="form-control" style="font-size: 13px;">
        </div>

        <div style="display: flex; gap: 8px;">
            <button type="submit" class="btn btn-primary" style="height: 40px; padding: 0 18px;">Lọc câu</button>
            <a href="{{ route('practice.select') }}" class="btn btn-outline" style="height: 40px; padding: 0 14px;">Đặt lại</a>
        </div>
    </form>
</div>

<!-- DANH SÁCH CÂU HỎI -->
@if($questions->isEmpty())
    <div class="card" style="text-align: center; padding: 48px 20px;">
        <div style="font-size: 40px; margin-bottom: 12px;">🔍</div>
        <h3 style="font-size: 18px; color: #1e293b; margin-bottom: 6px;">Không tìm thấy câu hỏi phù hợp</h3>
        <p style="color: #64748b; font-size: 14px; margin-bottom: 18px;">Hãy thử điều chỉnh lại độ khó hoặc xóa từ khóa tìm kiếm.</p>
        <a href="{{ route('practice.select') }}" class="btn btn-outline btn-sm">Xem tất cả câu hỏi</a>
    </div>
@else
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(340px, 1fr)); gap: 18px;">
        @foreach($questions as $q)
            <div class="card" style="display: flex; flex-direction: column; justify-content: space-between; margin-bottom: 0;">
                <div>
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                        <span style="font-size: 12px; font-weight: 700; color: #00b86b; background: #e6f9f0; padding: 3px 8px; border-radius: 4px;">
                            Câu #{{ $q->id }}
                        </span>
                        <span style="font-size: 12px; font-weight: 600; color: {{ ($q->dokho ?? 5) <= 3 ? '#166534' : (($q->dokho ?? 5) <= 7 ? '#854d0e' : '#991b1b') }};">
                            Độ khó: {{ $q->dokho ?? 5 }}/10
                        </span>
                    </div>

                    <p style="font-size: 14px; font-weight: 600; color: #1e293b; line-height: 1.5; margin-bottom: 14px;">
                        {{ Str::limit($q->noidung, 120) }}
                    </p>

                    <div style="display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 12px; font-size: 12px; color: #64748b;">
                        @if(!empty($q->thoigian))
                            <span>⏳ {{ $q->thoigian }}s</span>
                        @endif
                        @if(!empty($q->anh))
                            <span>🖼️ Có hình ảnh</span>
                        @endif
                        @if(!empty($q->audio))
                            <span>🔊 Có âm thanh</span>
                        @endif
                    </div>
                </div>

                <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid #f1f5f9; padding-top: 12px; margin-top: 12px;">
                    <span style="font-size: 12px; color: #94a3b8;">Điểm: {{ $q->diem ?? 1 }}</span>
                    <a href="{{ route('practice.show', $q->id) }}" class="btn btn-primary btn-sm">
                        Bắt đầu làm bài →
                    </a>
                </div>
            </div>
        @endforeach
    </div>

    @if(method_exists($questions, 'links'))
        <div style="margin-top: 24px;">
            {{ $questions->links() }}
        </div>
    @endif
@endif
@endsection

