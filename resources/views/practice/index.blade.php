<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Danh sách dạng bài Practice - DET</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background: #f9f9f9; }
        .container { max-width: 800px; margin: auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        h1 { color: #2cb67d; }
        ul { list-style: none; padding: 0; }
        li { padding: 12px; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; align-items: center; }
        a { text-decoration: none; background: #2cb67d; color: white; padding: 6px 12px; border-radius: 4px; }
        a:hover { background: #249667; }
    </style>
</head>
<body>
    <div class="container">
        <h1>📚 Danh sách dạng bài luyện tập DET</h1>
        <p>Chọn dạng bài bạn muốn luyện tập:</p>
        <ul>
            @forelse($dsDangBai as $item)
                <li>
                    <span><strong>{{ $item->ten ?? $item->ten_loai ?? 'Dạng bài luyện tập' }}</strong></span>
                    <a href="#">Luyện tập ngay</a>
                </li>
            @empty
                <p>Chưa có dạng bài nào được cập nhật.</p>
            @endforelse
        </ul>
        <br>
        <a href="/" style="background: #6c757d;">Quay lại trang chủ</a>
    </div>
</body>
</html>