<?php

namespace Tests\Feature;

use App\Models\Ailog;
use App\Models\Aisudung;
use App\Models\Nguoidung;
use App\Services\AIService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AITutorTest extends TestCase
{
    use RefreshDatabase;

    protected Nguoidung $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = Nguoidung::create([
            'hoten' => 'Tutor Student',
            'email' => 'tutor@example.com',
            'matkhau' => Hash::make('password123'),
            'trangthai' => 'active',
            'vaitro' => 'user',
        ]);

        Config::set('ai.provider', 'gemini');
        Config::set('ai.gemini.api_key', 'test-key');
    }

    public function test_grammar_check_success_with_prg_and_logs_ailog(): void
    {
        Http::fake([
            '*generateContent*' => Http::response([
                'candidates' => [
                    [
                        'content' => [
                            'parts' => [
                                [
                                    'text' => json_encode([
                                        'corrected' => 'She goes to school every day.',
                                        'error_count' => 1,
                                        'errors' => [
                                            ['original' => 'go', 'replacement' => 'goes', 'explanation' => 'Chia thì hiện tại đơn với ngôi thứ 3'],
                                        ],
                                        'overall_comment' => 'Khá tốt.',
                                    ]),
                                ],
                            ],
                        ],
                    ],
                ],
            ], 200),
        ]);

        $response = $this->actingAs($this->user)
            ->from('/tutor/grammar')
            ->post('/tutor/grammar', [
                'content' => 'She go to school every day.',
            ]);

        // PRG: redirect back to GET
        $response->assertRedirect('/tutor/grammar');
        $response->assertSessionHas('ai_result');
        $response->assertSessionHas('success');

        // Kiểm tra trừ 1 lượt
        $this->assertEquals(1, Aisudung::where('nguoidung_id', $this->user->id)->value('solan'));

        // Kiểm tra ghi ailog
        $this->assertDatabaseHas('ailog', [
            'nguoidung_id' => $this->user->id,
            'loai' => 'grammar',
        ]);
    }

    public function test_translate_text_success_with_prg(): void
    {
        Http::fake([
            '*generateContent*' => Http::response([
                'candidates' => [
                    [
                        'content' => [
                            'parts' => [
                                [
                                    'text' => json_encode([
                                        'translation' => 'Cô ấy đọc sách mỗi buổi tối.',
                                        'key_vocabulary' => [],
                                        'grammar_notes' => 'Thì hiện tại đơn.',
                                    ]),
                                ],
                            ],
                        ],
                    ],
                ],
            ], 200),
        ]);

        $response = $this->actingAs($this->user)
            ->from('/tutor/translate')
            ->post('/tutor/translate', [
                'content' => 'She reads books every evening.',
                'direction' => 'en_vi',
            ]);

        $response->assertRedirect('/tutor/translate');
        $response->assertSessionHas('ai_result');
        $this->assertEquals(1, Aisudung::where('nguoidung_id', $this->user->id)->value('solan'));
    }

    public function test_rewrite_text_success_with_prg(): void
    {
        Http::fake([
            '*generateContent*' => Http::response([
                'candidates' => [
                    [
                        'content' => [
                            'parts' => [
                                [
                                    'text' => json_encode([
                                        'rewritten' => 'Engaging in regular exercise significantly enhances psychological well-being.',
                                        'improvements' => [],
                                        'explanation' => 'Nâng cấp từ vựng học thuật C1.',
                                    ]),
                                ],
                            ],
                        ],
                    ],
                ],
            ], 200),
        ]);

        $response = $this->actingAs($this->user)
            ->from('/tutor/rewrite')
            ->post('/tutor/rewrite', [
                'content' => 'Exercise is good for mental health.',
                'style' => 'academic',
            ]);

        $response->assertRedirect('/tutor/rewrite');
        $response->assertSessionHas('ai_result');
        $this->assertEquals(1, Aisudung::where('nguoidung_id', $this->user->id)->value('solan'));
    }

    public function test_tutor_api_error_refunds_quota_and_keeps_input(): void
    {
        // Giả lập API lỗi 500
        Http::fake([
            '*generateContent*' => Http::response('Server Error', 500),
        ]);

        $rawText = 'This is my test text that should not be lost.';

        $response = $this->actingAs($this->user)
            ->from('/tutor/grammar')
            ->post('/tutor/grammar', [
                'content' => $rawText,
            ]);

        $response->assertRedirect('/tutor/grammar');
        $response->assertSessionHasErrors('api_error');

        // Form input được giữ nguyên
        $this->assertEquals($rawText, session('_old_input.content'));

        // Hạn mức phải được hoàn lại = 0
        $this->assertEquals(0, Aisudung::where('nguoidung_id', $this->user->id)->value('solan'));
    }

    public function test_tutor_limit_exceeded_blocks_and_preserves_input(): void
    {
        Config::set('ai.daily_limit', 1);
        app(AIService::class)->checkAndDeductLimit($this->user->id); // Dùng hết 1 lượt

        $rawText = 'My draft text that needs to stay.';

        $response = $this->actingAs($this->user)
            ->from('/tutor/grammar')
            ->post('/tutor/grammar', [
                'content' => $rawText,
            ]);

        $response->assertRedirect('/tutor/grammar');
        $response->assertSessionHasErrors('ai_limit');
        $this->assertEquals($rawText, session('_old_input.content'));
    }

    public function test_tutor_history_displays_log_created_by_real_tutor_flow(): void
    {
        Config::set('ai.provider', 'mock');

        $this->actingAs($this->user)
            ->from('/tutor/grammar')
            ->post('/tutor/grammar', [
                'content' => 'She go to school every day.',
            ])
            ->assertRedirect('/tutor/grammar');

        $this->assertDatabaseHas('ailog', [
            'nguoidung_id' => $this->user->id,
            'loai' => 'grammar',
            'trangthai' => 'success',
        ]);

        $this->actingAs($this->user)
            ->get('/tutor/history')
            ->assertOk()
            ->assertSee('She go to school every day.')
            ->assertSee('grammar');
    }

    public function test_tutor_history_displays_interactions(): void
    {
        Ailog::create([
            'nguoidung_id' => $this->user->id,
            'loai' => 'grammar',
            'dauvao' => 'I has a question.',
            'daura' => 'I have a question.',
            'model' => 'gemini-1.5-flash',
        ]);

        $response = $this->actingAs($this->user)->get('/tutor/history');

        $response->assertOk();
        $response->assertSee('I has a question.');
        $response->assertSee('I have a question.');
    }

    public function test_tutor_history_hien_thi_ket_qua_json_than_thien(): void
    {
        Ailog::create([
            'nguoidung_id' => $this->user->id,
            'loai' => 'grammar',
            'dauvao' => 'She go to school every day.',
            'daura' => json_encode([
                'corrected' => 'She goes to school every day.',
                'error_count' => 1,
                'errors' => [
                    [
                        'original' => 'She go',
                        'replacement' => 'She goes',
                        'explanation' => 'Chủ ngữ ngôi thứ ba số ít cần động từ thêm -es.',
                    ],
                ],
                'overall_comment' => 'Câu đã được sửa và giải thích rõ ràng.',
            ], JSON_UNESCAPED_UNICODE),
            'model' => 'gemini-1.5-flash',
        ]);

        $this->actingAs($this->user)
            ->get('/tutor/history')
            ->assertOk()
            ->assertSee('Đoạn văn đã được sửa')
            ->assertSee('She goes to school every day.')
            ->assertSee('Số lỗi phát hiện: 1')
            ->assertSee('Chủ ngữ ngôi thứ ba số ít cần động từ thêm -es.')
            ->assertSee('Nhận xét tổng quan')
            ->assertDontSee('&quot;corrected&quot;');
    }
}
