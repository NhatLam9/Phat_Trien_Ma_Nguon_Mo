<nav class="admin-tabs" aria-label="Ngân hàng câu hỏi">
    <a class="admin-tab {{ request()->routeIs('admin.questions.index') || request()->routeIs('admin.questions.show') ? 'is-active' : '' }}"
       href="{{ route('admin.questions.index') }}">
        Danh sách câu hỏi
    </a>
    <a class="admin-tab {{ request()->routeIs('admin.questions.import.*') ? 'is-active' : '' }}"
       href="{{ route('admin.questions.import.create') }}">
        Nhập câu hỏi
        <span class="admin-tab__hint">JSON</span>
    </a>
</nav>
