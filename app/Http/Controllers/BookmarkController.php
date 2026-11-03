<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\Danhdau;

class BookmarkController extends Controller
{
    /**
     * LT07: Hiển thị danh sách các câu hỏi yếu đã được đánh dấu
     */
    public function index(Request $request)
    {
        $userId = Auth::id();

        $bookmarks = Danhdau::where('nguoidung_id', $userId)
            ->orderByDesc('id')
            ->paginate(15);

        // Nạp thông tin chi tiết câu hỏi nếu bảng cauhoi đã tồn tại
        if (Schema::hasTable('cauhoi')) {
            $questionIds = $bookmarks->pluck('cauhoi_id')->toArray();
            $questions = DB::table('cauhoi')
                ->whereIn('id', $questionIds)
                ->get()
                ->keyBy('id');

            foreach ($bookmarks as $bm) {
                $bm->cauhoi = $questions->get($bm->cauhoi_id) ?? null;
            }
        }

        return view('practice.danhdau', compact('bookmarks'));
    }

    /**
     * Thêm hoặc bỏ đánh dấu (toggle bookmark) một câu hỏi
     */
    public function toggle(Request $request, $cauhoiId)
    {
        $userId = Auth::id();

        $bookmark = Danhdau::where('nguoidung_id', $userId)
            ->where('cauhoi_id', $cauhoiId)
            ->first();

        if ($bookmark) {
            $bookmark->delete();
            $isBookmarked = false;
            $message = 'Đã bỏ đánh dấu câu hỏi khỏi danh sách ôn tập.';
        } else {
            Danhdau::create([
                'nguoidung_id' => $userId,
                'cauhoi_id' => $cauhoiId,
                'ngaytao' => now(),
            ]);
            $isBookmarked = true;
            $message = 'Đã đánh dấu câu hỏi để ôn tập lại!';
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'is_bookmarked' => $isBookmarked,
                'message' => $message,
            ]);
        }

        return back()->with('status', $message);
    }
}

