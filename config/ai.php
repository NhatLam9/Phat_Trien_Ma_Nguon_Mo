<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Nhà cung cấp AI (AI Provider)
    |--------------------------------------------------------------------------
    | Hỗ trợ: "gemini", "openai", "mock" (dùng cho testing/local offline)
    */
    'provider' => env('AI_PROVIDER', 'gemini'),

    /*
    |--------------------------------------------------------------------------
    | Cấu hình Google Gemini
    |--------------------------------------------------------------------------
    */
    'gemini' => [
        'api_key' => env('GEMINI_API_KEY', ''),
        'model' => env('GEMINI_MODEL', 'gemini-1.5-flash'),
        'base_url' => env('GEMINI_BASE_URL', 'https://generativelanguage.googleapis.com/v1beta'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Cấu hình OpenAI (Dự phòng / Whisper STT)
    |--------------------------------------------------------------------------
    */
    'openai' => [
        'api_key' => env('OPENAI_API_KEY', ''),
        'model' => env('OPENAI_MODEL', 'gpt-4o-mini'),
        'stt_model' => env('OPENAI_STT_MODEL', 'whisper-1'),
        'base_url' => env('OPENAI_BASE_URL', 'https://api.openai.com/v1'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Hạn mức & Thang điểm DET Practice
    |--------------------------------------------------------------------------
    | daily_limit: Số lượt gọi AI tối đa mỗi người dùng trong ngày (reset vào 00:00)
    | min_score, max_score: Thang điểm chuẩn DET (10 - 160)
    */
    'daily_limit' => (int) env('AI_DAILY_LIMIT', 20),
    'min_score' => (float) env('AI_MIN_SCORE', 10.0),
    'max_score' => (float) env('AI_MAX_SCORE', 160.0),

    /*
    |--------------------------------------------------------------------------
    | Thời gian chờ HTTP (Timeout tính bằng giây)
    |--------------------------------------------------------------------------
    | grade: 30s, STT: 40s, Tutor: 20s
    */
    'timeout_grading' => (int) env('AI_TIMEOUT_GRADING', 30),
    'timeout_stt' => (int) env('AI_TIMEOUT_STT', 40),
    'timeout_tutor' => (int) env('AI_TIMEOUT_TUTOR', 20),

    /*
    |--------------------------------------------------------------------------
    | Giới hạn độ dài văn bản (chống DoS / tràn ngữ cảnh)
    |--------------------------------------------------------------------------
    */
    'max_input_length' => 3000,
    'max_transcript_length' => 3000,

    /*
    |--------------------------------------------------------------------------
    | Thời gian tối đa giữ trạng thái pending (phút) trước khi chốt failed
    |--------------------------------------------------------------------------
    */
    'pending_timeout_minutes' => 5,
];

