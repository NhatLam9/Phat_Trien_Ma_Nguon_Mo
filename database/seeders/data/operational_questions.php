<?php

/**
 * Ngân hàng câu hỏi vận hành tối thiểu.
 *
 * Dữ liệu này được dùng bởi OperationalQuestionBankSeeder.
 * Mục tiêu: 120 câu cha, 60 câu con, đủ dựng 5 đề full.
 *
 * Đây là dữ liệu seed để chạy/demo, không thay thế nội dung
 * do giáo viên biên soạn và duyệt trước khi dùng chính thức.
 */
return [
    'read_and_select' => [
        [
            'noidung' => 'The library ___ at nine every morning.',
            'options' => [
                ['opens', true],
                ['open', false],
                ['opening', false],
                ['opened', false],
            ],
            'giaithich' => 'Chủ ngữ "The library" số ít nên động từ thêm -s.',
            'dokho' => 2,
        ],
        [
            'noidung' => 'If it rains tomorrow, we ___ the picnic.',
            'options' => [
                ['will cancel', true],
                ['cancel', false],
                ['cancelled', false],
                ['have cancelled', false],
            ],
            'giaithich' => 'Câu điều kiện loại 1 dùng will + động từ nguyên mẫu.',
            'dokho' => 3,
        ],
        [
            'noidung' => 'She has lived in Da Nang ___ 2019.',
            'options' => [
                ['since', true],
                ['for', false],
                ['from', false],
                ['during', false],
            ],
            'giaithich' => 'Since đi với mốc thời gian cụ thể.',
            'dokho' => 3,
        ],
        [
            'noidung' => 'The new bridge ___ next year.',
            'options' => [
                ['will be built', true],
                ['builds', false],
                ['is building', false],
                ['built', false],
            ],
            'giaithich' => 'Câu bị động tương lai: will be + V3.',
            'dokho' => 4,
        ],
        [
            'noidung' => 'He is interested ___ learning Japanese.',
            'options' => [
                ['in', true],
                ['on', false],
                ['at', false],
                ['for', false],
            ],
            'giaithich' => 'Cấu trúc interested in + V-ing.',
            'dokho' => 2,
        ],
        [
            'noidung' => 'The teacher asked us to ___ our homework before Friday.',
            'options' => [
                ['submit', true],
                ['submits', false],
                ['submitting', false],
                ['submission', false],
            ],
            'giaithich' => 'Sau to dùng động từ nguyên mẫu.',
            'dokho' => 3,
        ],
        [
            'noidung' => 'This is the cafe ___ we first met.',
            'options' => [
                ['where', true],
                ['which', false],
                ['who', false],
                ['when', false],
            ],
            'giaithich' => 'Where thay cho cụm chỉ địa điểm.',
            'dokho' => 4,
        ],
        [
            'noidung' => 'The company is looking for someone with strong ___ skills.',
            'options' => [
                ['communication', true],
                ['communicate', false],
                ['communicative', false],
                ['communicator', false],
            ],
            'giaithich' => 'Cụm danh từ communication skills.',
            'dokho' => 4,
        ],
        [
            'noidung' => 'You should ___ the instructions carefully before using the machine.',
            'options' => [
                ['read', true],
                ['reads', false],
                ['reading', false],
                ['reader', false],
            ],
            'giaithich' => 'Sau should dùng động từ nguyên mẫu.',
            'dokho' => 2,
        ],
        [
            'noidung' => 'The weather was ___ cold that we stayed indoors.',
            'options' => [
                ['so', true],
                ['such', false],
                ['too', false],
                ['enough', false],
            ],
            'giaithich' => 'Cấu trúc so + tính từ + that.',
            'dokho' => 4,
        ],
        [
            'noidung' => 'I wish I ___ more time to travel.',
            'options' => [
                ['had', true],
                ['have', false],
                ['will have', false],
                ['am having', false],
            ],
            'giaithich' => 'Wish ở hiện tại dùng quá khứ đơn.',
            'dokho' => 5,
        ],
        [
            'noidung' => 'The report needs to be finished ___ Friday.',
            'options' => [
                ['by', true],
                ['until', false],
                ['since', false],
                ['during', false],
            ],
            'giaithich' => 'By + mốc thời gian nghĩa là trước hoặc đúng lúc đó.',
            'dokho' => 3,
        ],
        [
            'noidung' => 'Many species are at risk ___ habitat loss.',
            'options' => [
                ['because of', true],
                ['although', false],
                ['despite of', false],
                ['so that', false],
            ],
            'giaithich' => 'Because of + cụm danh từ.',
            'dokho' => 5,
        ],
        [
            'noidung' => 'The film was boring, ___ I left early.',
            'options' => [
                ['so', true],
                ['because', false],
                ['although', false],
                ['unless', false],
            ],
            'giaithich' => 'So diễn tả kết quả.',
            'dokho' => 2,
        ],
        [
            'noidung' => 'She apologized ___ being late.',
            'options' => [
                ['for', true],
                ['of', false],
                ['about', false],
                ['with', false],
            ],
            'giaithich' => 'Apologize for + V-ing.',
            'dokho' => 4,
        ],
        [
            'noidung' => 'The more you practice, the ___ you become.',
            'options' => [
                ['better', true],
                ['good', false],
                ['best', false],
                ['well', false],
            ],
            'giaithich' => 'Cấu trúc so sánh kép: the more..., the better...',
            'dokho' => 4,
        ],
        [
            'noidung' => 'Please turn down the music. It is ___ loud.',
            'options' => [
                ['too', true],
                ['enough', false],
                ['so', false],
                ['such', false],
            ],
            'giaithich' => 'Too + tính từ nghĩa là quá mức.',
            'dokho' => 2,
        ],
        [
            'noidung' => 'The students were divided ___ four groups.',
            'options' => [
                ['into', true],
                ['in', false],
                ['for', false],
                ['with', false],
            ],
            'giaithich' => 'Divide into = chia thành.',
            'dokho' => 3,
        ],
        [
            'noidung' => 'He denied ___ the window.',
            'options' => [
                ['breaking', true],
                ['break', false],
                ['to break', false],
                ['broke', false],
            ],
            'giaithich' => 'Deny + V-ing.',
            'dokho' => 5,
        ],
        [
            'noidung' => 'The meeting has been postponed ___ further notice.',
            'options' => [
                ['until', true],
                ['since', false],
                ['for', false],
                ['by', false],
            ],
            'giaithich' => 'Until further notice = cho đến khi có thông báo mới.',
            'dokho' => 4,
        ],
        [
            'noidung' => 'A number of volunteers ___ helping at the event.',
            'options' => [
                ['are', true],
                ['is', false],
                ['was', false],
                ['has', false],
            ],
            'giaithich' => 'A number of + danh từ số nhiều dùng động từ số nhiều.',
            'dokho' => 5,
        ],
        [
            'noidung' => 'The book, ___ was published last year, won an award.',
            'options' => [
                ['which', true],
                ['who', false],
                ['where', false],
                ['when', false],
            ],
            'giaithich' => 'Which thay cho vật trong mệnh đề quan hệ.',
            'dokho' => 4,
        ],
        [
            'noidung' => 'We need to find a solution ___ this problem.',
            'options' => [
                ['to', true],
                ['for', false],
                ['of', false],
                ['with', false],
            ],
            'giaithich' => 'A solution to a problem.',
            'dokho' => 3,
        ],
        [
            'noidung' => 'She was tired, but she ___ working.',
            'options' => [
                ['kept', true],
                ['keeps', false],
                ['keep', false],
                ['has kept', false],
            ],
            'giaithich' => 'Keep + V-ing = tiếp tục làm gì.',
            'dokho' => 4,
        ],
        [
            'noidung' => 'The museum is worth ___.',
            'options' => [
                ['visiting', true],
                ['to visit', false],
                ['visit', false],
                ['visited', false],
            ],
            'giaithich' => 'Worth + V-ing.',
            'dokho' => 4,
        ],
        [
            'noidung' => 'He speaks English ___ than his brother.',
            'options' => [
                ['more fluently', true],
                ['fluent', false],
                ['most fluent', false],
                ['fluency', false],
            ],
            'giaithich' => 'Trạng từ so sánh hơn: more fluently.',
            'dokho' => 3,
        ],
        [
            'noidung' => 'The project will fail ___ we work together.',
            'options' => [
                ['unless', true],
                ['if', false],
                ['because', false],
                ['so', false],
            ],
            'giaithich' => 'Unless = nếu không.',
            'dokho' => 4,
        ],
        [
            'noidung' => 'I am looking forward to ___ from you.',
            'options' => [
                ['hearing', true],
                ['hear', false],
                ['heard', false],
                ['hears', false],
            ],
            'giaithich' => 'Look forward to + V-ing.',
            'dokho' => 3,
        ],
        [
            'noidung' => 'The price of electricity has gone ___ recently.',
            'options' => [
                ['up', true],
                ['on', false],
                ['off', false],
                ['over', false],
            ],
            'giaithich' => 'Go up = tăng lên.',
            'dokho' => 2,
        ],
        [
            'noidung' => 'It is important ___ the environment.',
            'options' => [
                ['to protect', true],
                ['protect', false],
                ['protecting', false],
                ['protected', false],
            ],
            'giaithich' => 'It is important + to + động từ nguyên mẫu.',
            'dokho' => 3,
        ],
    ],

    'fill_in_the_blanks' => [
        [
            'noidung' => 'The opposite of "expensive" is ___.',
            'options' => [
                ['cheap', true],
                ['rich', false],
                ['high', false],
                ['valuable', false],
            ],
            'giaithich' => 'Expensive (đắt) >< cheap (rẻ).',
            'dokho' => 2,
        ],
        [
            'noidung' => 'Please ___ your seatbelt before the car starts.',
            'options' => [
                ['fasten', true],
                ['tie', false],
                ['lock', false],
                ['catch', false],
            ],
            'giaithich' => 'Fasten a seatbelt = thắt dây an toàn.',
            'dokho' => 3,
        ],
        [
            'noidung' => 'The company wants to ___ its carbon footprint.',
            'options' => [
                ['reduce', true],
                ['refuse', false],
                ['repeat', false],
                ['remove', false],
            ],
            'giaithich' => 'Reduce carbon footprint = giảm dấu chân carbon.',
            'dokho' => 4,
        ],
        [
            'noidung' => 'She has a talent ___ music.',
            'options' => [
                ['for', true],
                ['at', false],
                ['on', false],
                ['with', false],
            ],
            'giaithich' => 'A talent for something.',
            'dokho' => 3,
        ],
        [
            'noidung' => 'The medicine had a positive ___ on her health.',
            'options' => [
                ['effect', true],
                ['affect', false],
                ['effective', false],
                ['effectively', false],
            ],
            'giaithich' => 'Effect là danh từ; affect là động từ.',
            'dokho' => 4,
        ],
        [
            'noidung' => 'We need to ___ a decision before noon.',
            'options' => [
                ['make', true],
                ['do', false],
                ['take', false],
                ['have', false],
            ],
            'giaithich' => 'Make a decision = đưa ra quyết định.',
            'dokho' => 2,
        ],
        [
            'noidung' => 'The road was ___ because of the flood.',
            'options' => [
                ['closed', true],
                ['close', false],
                ['closing', false],
                ['closure', false],
            ],
            'giaithich' => 'Câu bị động cần tính từ/dạng V3: was closed.',
            'dokho' => 3,
        ],
        [
            'noidung' => 'He is responsible ___ training new staff.',
            'options' => [
                ['for', true],
                ['of', false],
                ['to', false],
                ['with', false],
            ],
            'giaithich' => 'Responsible for + V-ing.',
            'dokho' => 3,
        ],
        [
            'noidung' => 'The lecture was so ___ that I fell asleep.',
            'options' => [
                ['boring', true],
                ['bored', false],
                ['bore', false],
                ['boredom', false],
            ],
            'giaithich' => 'Boring mô tả thứ gây buồn chán.',
            'dokho' => 2,
        ],
        [
            'noidung' => 'The team celebrated its ___ after the final match.',
            'options' => [
                ['victory', true],
                ['victorious', false],
                ['victor', false],
                ['victoriously', false],
            ],
            'giaithich' => 'Sau tính từ sở hữu cần danh từ: its victory.',
            'dokho' => 3,
        ],
        [
            'noidung' => 'You can ___ the form online.',
            'options' => [
                ['complete', true],
                ['completion', false],
                ['completely', false],
                ['completing', false],
            ],
            'giaithich' => 'Sau can dùng động từ nguyên mẫu.',
            'dokho' => 2,
        ],
        [
            'noidung' => 'The hotel offers a wide ___ of services.',
            'options' => [
                ['range', true],
                ['amount', false],
                ['number', false],
                ['piece', false],
            ],
            'giaithich' => 'A wide range of services = nhiều dịch vụ đa dạng.',
            'dokho' => 4,
        ],
        [
            'noidung' => 'The scientist made an important ___.',
            'options' => [
                ['discovery', true],
                ['discover', false],
                ['discovered', false],
                ['discovering', false],
            ],
            'giaithich' => 'Make a discovery = tạo ra phát hiện.',
            'dokho' => 3,
        ],
        [
            'noidung' => 'The children were excited ___ the trip.',
            'options' => [
                ['about', true],
                ['of', false],
                ['on', false],
                ['with', false],
            ],
            'giaithich' => 'Excited about something.',
            'dokho' => 2,
        ],
        [
            'noidung' => 'Please ___ sure the door is locked.',
            'options' => [
                ['make', true],
                ['do', false],
                ['take', false],
                ['get', false],
            ],
            'giaithich' => 'Make sure = đảm bảo.',
            'dokho' => 2,
        ],
        [
            'noidung' => 'The company suffered a serious ___ last year.',
            'options' => [
                ['loss', true],
                ['lose', false],
                ['lost', false],
                ['losing', false],
            ],
            'giaithich' => 'Sau tính từ cần danh từ: a serious loss.',
            'dokho' => 4,
        ],
        [
            'noidung' => 'Her speech was ___ received by the audience.',
            'options' => [
                ['well', true],
                ['good', false],
                ['better', false],
                ['best', false],
            ],
            'giaithich' => 'Well received = được đón nhận tốt.',
            'dokho' => 4,
        ],
        [
            'noidung' => 'The train ___ at 7:30 every morning.',
            'options' => [
                ['leaves', true],
                ['leave', false],
                ['leaving', false],
                ['left', false],
            ],
            'giaithich' => 'Lịch trình cố định dùng hiện tại đơn; chủ ngữ số ít.',
            'dokho' => 2,
        ],
        [
            'noidung' => 'We should ___ advantage of this opportunity.',
            'options' => [
                ['take', true],
                ['make', false],
                ['do', false],
                ['get', false],
            ],
            'giaithich' => 'Take advantage of = tận dụng.',
            'dokho' => 3,
        ],
        [
            'noidung' => 'The building is ___ construction.',
            'options' => [
                ['under', true],
                ['in', false],
                ['on', false],
                ['at', false],
            ],
            'giaithich' => 'Under construction = đang được xây dựng.',
            'dokho' => 3,
        ],
        [
            'noidung' => 'He was ___ for the accident.',
            'options' => [
                ['responsible', true],
                ['responsibility', false],
                ['responsibly', false],
                ['respond', false],
            ],
            'giaithich' => 'Sau was cần tính từ: was responsible.',
            'dokho' => 4,
        ],
        [
            'noidung' => 'The instructions were not ___ enough.',
            'options' => [
                ['clear', true],
                ['clearly', false],
                ['clarity', false],
                ['clarify', false],
            ],
            'giaithich' => 'Sau was not cần tính từ.',
            'dokho' => 3,
        ],
        [
            'noidung' => 'The festival attracts ___ from all over the world.',
            'options' => [
                ['visitors', true],
                ['visits', false],
                ['visiting', false],
                ['visited', false],
            ],
            'giaithich' => 'Cần danh từ chỉ người: visitors.',
            'dokho' => 2,
        ],
        [
            'noidung' => 'She has been working here ___ three years.',
            'options' => [
                ['for', true],
                ['since', false],
                ['during', false],
                ['from', false],
            ],
            'giaithich' => 'For + khoảng thời gian.',
            'dokho' => 2,
        ],
        [
            'noidung' => 'The company is committed ___ quality.',
            'options' => [
                ['to', true],
                ['for', false],
                ['with', false],
                ['at', false],
            ],
            'giaithich' => 'Committed to + danh từ/V-ing.',
            'dokho' => 4,
        ],
        [
            'noidung' => 'The report provides a detailed ___ of the results.',
            'options' => [
                ['analysis', true],
                ['analyse', false],
                ['analytical', false],
                ['analytically', false],
            ],
            'giaithich' => 'Sau tính từ cần danh từ: a detailed analysis.',
            'dokho' => 5,
        ],
        [
            'noidung' => 'The noise made it difficult to ___.',
            'options' => [
                ['concentrate', true],
                ['concentration', false],
                ['concentrated', false],
                ['concentrating', false],
            ],
            'giaithich' => 'Sau to dùng động từ nguyên mẫu.',
            'dokho' => 4,
        ],
        [
            'noidung' => 'The new policy will come ___ effect next month.',
            'options' => [
                ['into', true],
                ['in', false],
                ['on', false],
                ['at', false],
            ],
            'giaithich' => 'Come into effect = có hiệu lực.',
            'dokho' => 4,
        ],
        [
            'noidung' => 'He is used to ___ up early.',
            'options' => [
                ['getting', true],
                ['get', false],
                ['got', false],
                ['gets', false],
            ],
            'giaithich' => 'Be used to + V-ing.',
            'dokho' => 4,
        ],
        [
            'noidung' => 'The project was completed ___ schedule.',
            'options' => [
                ['ahead of', true],
                ['in front of', false],
                ['before of', false],
                ['on front of', false],
            ],
            'giaithich' => 'Ahead of schedule = trước kế hoạch.',
            'dokho' => 4,
        ],
    ],

    'read_and_complete' => [
        [
            'noidung' => 'Many cities are adding more trees along streets. Trees ___ shade and help lower temperatures during hot summers.',
            'options' => [
                ['provide', true],
                ['provides', false],
                ['providing', false],
                ['provided', false],
            ],
            'giaithich' => 'Chủ ngữ "Trees" số nhiều nên dùng provide.',
            'dokho' => 4,
        ],
        [
            'noidung' => 'Sleep plays an important role in memory. During sleep, the brain ___ information from the day and stores what matters.',
            'options' => [
                ['organizes', true],
                ['organize', false],
                ['organized', false],
                ['organizing', false],
            ],
            'giaithich' => 'Chủ ngữ "the brain" số ít nên dùng organizes.',
            'dokho' => 5,
        ],
        [
            'noidung' => 'Plastic waste is a serious problem. Every year, millions of tons of plastic ___ in the ocean.',
            'options' => [
                ['end up', true],
                ['ends up', false],
                ['ending up', false],
                ['ended up', false],
            ],
            'giaithich' => 'Chủ ngữ "millions of tons" số nhiều nên dùng end up.',
            'dokho' => 5,
        ],
        [
            'noidung' => 'More people are working from home. This change ___ employees save time on commuting.',
            'options' => [
                ['helps', true],
                ['help', false],
                ['helping', false],
                ['helped', false],
            ],
            'giaithich' => 'Chủ ngữ "This change" số ít nên dùng helps.',
            'dokho' => 4,
        ],
        [
            'noidung' => 'Regular exercise is good for both the body and the mind. Even a short walk ___ stress.',
            'options' => [
                ['reduces', true],
                ['reduce', false],
                ['reducing', false],
                ['reduced', false],
            ],
            'giaithich' => 'Chủ ngữ "a short walk" số ít nên dùng reduces.',
            'dokho' => 4,
        ],
        [
            'noidung' => 'Bees are essential for agriculture. They ___ pollen from one flower to another.',
            'options' => [
                ['carry', true],
                ['carries', false],
                ['carrying', false],
                ['carried', false],
            ],
            'giaithich' => 'Chủ ngữ "They" số nhiều nên dùng carry.',
            'dokho' => 3,
        ],
        [
            'noidung' => 'Public libraries are changing. Many now ___ free classes and digital services.',
            'options' => [
                ['offer', true],
                ['offers', false],
                ['offering', false],
                ['offered', false],
            ],
            'giaithich' => 'Chủ ngữ "Many" số nhiều nên dùng offer.',
            'dokho' => 4,
        ],
        [
            'noidung' => 'Electric cars are becoming more popular. They ___ less noise and produce no exhaust fumes.',
            'options' => [
                ['make', true],
                ['makes', false],
                ['making', false],
                ['made', false],
            ],
            'giaithich' => 'Chủ ngữ "They" số nhiều nên dùng make.',
            'dokho' => 4,
        ],
        [
            'noidung' => 'Cooking at home can be healthier and cheaper. People who cook often ___ more control over what they eat.',
            'options' => [
                ['have', true],
                ['has', false],
                ['having', false],
                ['had', false],
            ],
            'giaithich' => 'Chủ ngữ "People" số nhiều nên dùng have.',
            'dokho' => 4,
        ],
        [
            'noidung' => 'Volunteering benefits both the community and the volunteer. It ___ people learn new skills and meet others.',
            'options' => [
                ['helps', true],
                ['help', false],
                ['helping', false],
                ['helped', false],
            ],
            'giaithich' => 'Chủ ngữ "It" số ít nên dùng helps.',
            'dokho' => 4,
        ],
    ],

    'interactive_reading' => [
        [
            'noidung' => 'Read the passage: The Amazon rainforest covers much of northwestern Brazil and extends into several other South American countries. It produces a large share of the oxygen and freshwater that support life in the region, and it is home to millions of species. However, deforestation for farming and logging threatens the forest. Scientists warn that losing too much of the Amazon could change rainfall patterns and accelerate climate change.',
            'dokho' => 5,
            'children' => [
                [
                    'loai' => 'complete_sentence',
                    'noidung' => 'The Amazon rainforest is important because it is home to ___.',
                    'options' => [
                        ['millions of species', true],
                        ['only one species', false],
                        ['no rivers', false],
                        ['few plants', false],
                    ],
                    'giaithich' => 'Thông tin có trong câu thứ hai của đoạn văn.',
                ],
                [
                    'loai' => 'identify_idea',
                    'noidung' => 'What is the main idea of the passage?',
                    'options' => [
                        ['The Amazon is vital but faces serious threats.', true],
                        ['The Amazon has no environmental problems.', false],
                        ['Farming has no effect on the Amazon.', false],
                        ['Scientists have stopped deforestation completely.', false],
                    ],
                    'giaithich' => 'Đoạn văn nêu vai trò của rừng và nguy cơ từ phá rừng.',
                ],
                [
                    'loai' => 'title_passage',
                    'noidung' => 'Choose the best title for the passage.',
                    'dapandung' => 'The Amazon: A Forest Under Threat',
                    'giaithich' => 'Tiêu đề khái quát vai trò và mối đe dọa.',
                ],
            ],
        ],
        [
            'noidung' => 'Read the passage: Urban farming is growing in popularity. People are using rooftops, balconies, and empty lots to grow vegetables and herbs. These gardens can reduce food costs, improve air quality, and give city residents a chance to connect with nature. However, urban farmers must deal with limited space, polluted soil, and the need for regular watering.',
            'dokho' => 5,
            'children' => [
                [
                    'loai' => 'complete_sentence',
                    'noidung' => 'Urban farmers often grow food on ___.',
                    'options' => [
                        ['rooftops and balconies', true],
                        ['highways', false],
                        ['airplanes', false],
                        ['underground trains', false],
                    ],
                    'giaithich' => 'Đoạn văn nêu rooftops, balconies và empty lots.',
                ],
                [
                    'loai' => 'identify_idea',
                    'noidung' => 'What is the main idea of the passage?',
                    'options' => [
                        ['Urban farming has benefits but also challenges.', true],
                        ['Urban farming is impossible in cities.', false],
                        ['Urban farming only produces flowers.', false],
                        ['Urban farmers never need water.', false],
                    ],
                    'giaithich' => 'Đoạn văn nêu lợi ích và khó khăn của nông nghiệp đô thị.',
                ],
                [
                    'loai' => 'title_passage',
                    'noidung' => 'Choose the best title for the passage.',
                    'dapandung' => 'Growing Food in the City',
                    'giaithich' => 'Tiêu đề phù hợp với nội dung nông nghiệp đô thị.',
                ],
            ],
        ],
        [
            'noidung' => 'Read the passage: Sleep is essential for learning. While we sleep, the brain reviews information from the day and strengthens memories. Students who get enough sleep tend to remember more and perform better on tests. Unfortunately, many teenagers stay up late using phones and computers, which can make it harder to concentrate the next day.',
            'dokho' => 5,
            'children' => [
                [
                    'loai' => 'complete_sentence',
                    'noidung' => 'During sleep, the brain ___.',
                    'options' => [
                        ['strengthens memories', true],
                        ['stops working', false],
                        ['forgets everything', false],
                        ['increases screen time', false],
                    ],
                    'giaithich' => 'Đoạn văn nói não củng cố ký ức khi ngủ.',
                ],
                [
                    'loai' => 'identify_idea',
                    'noidung' => 'What is the main idea of the passage?',
                    'options' => [
                        ['Sleep supports learning and memory.', true],
                        ['Teenagers should use phones at night.', false],
                        ['Tests are not affected by sleep.', false],
                        ['The brain rests completely during sleep.', false],
                    ],
                    'giaithich' => 'Đoạn văn tập trung vào vai trò của giấc ngủ với học tập.',
                ],
                [
                    'loai' => 'title_passage',
                    'noidung' => 'Choose the best title for the passage.',
                    'dapandung' => 'Why Sleep Matters for Learning',
                    'giaithich' => 'Tiêu đề khái quát mối liên hệ giữa ngủ và học tập.',
                ],
            ],
        ],
        [
            'noidung' => 'Read the passage: Plastic pollution affects oceans, rivers, and wildlife. Much of the plastic that people throw away ends up in the sea, where it breaks into tiny pieces called microplastics. Fish and birds often mistake these pieces for food. Reducing single-use plastic and improving recycling are two important ways to address the problem.',
            'dokho' => 6,
            'children' => [
                [
                    'loai' => 'complete_sentence',
                    'noidung' => 'Plastic in the ocean breaks into ___.',
                    'options' => [
                        ['microplastics', true],
                        ['large rocks', false],
                        ['fresh water', false],
                        ['seaweed', false],
                    ],
                    'giaithich' => 'Đoạn văn gọi các mảnh nhỏ là microplastics.',
                ],
                [
                    'loai' => 'identify_idea',
                    'noidung' => 'What is the main idea of the passage?',
                    'options' => [
                        ['Plastic pollution harms wildlife and needs action.', true],
                        ['Plastic is good for fish and birds.', false],
                        ['Recycling has no benefits.', false],
                        ['Only rivers contain plastic.', false],
                    ],
                    'giaithich' => 'Đoạn văn nêu tác hại và giải pháp giảm nhựa.',
                ],
                [
                    'loai' => 'title_passage',
                    'noidung' => 'Choose the best title for the passage.',
                    'dapandung' => 'The Problem of Plastic Waste',
                    'giaithich' => 'Tiêu đề sát với chủ đề rác thải nhựa.',
                ],
            ],
        ],
        [
            'noidung' => 'Read the passage: Tea has been drunk for thousands of years. According to legend, it was discovered in China when leaves fell into a pot of boiling water. Today, tea is one of the most popular drinks in the world. It is grown in many countries, and each region produces tea with a different flavor.',
            'dokho' => 4,
            'children' => [
                [
                    'loai' => 'complete_sentence',
                    'noidung' => 'According to legend, tea was discovered in ___.',
                    'options' => [
                        ['China', true],
                        ['Brazil', false],
                        ['Egypt', false],
                        ['Australia', false],
                    ],
                    'giaithich' => 'Đoạn văn nêu truyền thuyết về Trung Quốc.',
                ],
                [
                    'loai' => 'identify_idea',
                    'noidung' => 'What is the main idea of the passage?',
                    'options' => [
                        ['Tea has a long history and is popular worldwide.', true],
                        ['Tea is only grown in one country.', false],
                        ['Tea was invented in the twentieth century.', false],
                        ['All tea has the same flavor.', false],
                    ],
                    'giaithich' => 'Đoạn văn nói về lịch sử và sự phổ biến của trà.',
                ],
                [
                    'loai' => 'title_passage',
                    'noidung' => 'Choose the best title for the passage.',
                    'dapandung' => 'A Short History of Tea',
                    'giaithich' => 'Tiêu đề phù hợp với nội dung lịch sử của trà.',
                ],
            ],
        ],
        [
            'noidung' => 'Read the passage: Remote work has become more common since the internet made it possible to communicate from anywhere. Employees can save time on commuting and often have more flexibility. However, working from home can also be lonely, and some people find it difficult to separate work from personal life.',
            'dokho' => 6,
            'children' => [
                [
                    'loai' => 'complete_sentence',
                    'noidung' => 'One benefit of remote work is ___.',
                    'options' => [
                        ['saving time on commuting', true],
                        ['working longer hours', false],
                        ['having no colleagues', false],
                        ['never using the internet', false],
                    ],
                    'giaithich' => 'Đoạn văn nêu lợi ích tiết kiệm thời gian đi lại.',
                ],
                [
                    'loai' => 'identify_idea',
                    'noidung' => 'What is the main idea of the passage?',
                    'options' => [
                        ['Remote work has advantages and disadvantages.', true],
                        ['Remote work is impossible for everyone.', false],
                        ['All employees prefer working from home.', false],
                        ['Remote work removes all stress.', false],
                    ],
                    'giaithich' => 'Đoạn văn nêu cả lợi ích và khó khăn.',
                ],
                [
                    'loai' => 'title_passage',
                    'noidung' => 'Choose the best title for the passage.',
                    'dapandung' => 'The Rise of Remote Work',
                    'giaithich' => 'Tiêu đề khái quát xu hướng làm việc từ xa.',
                ],
            ],
        ],
        [
            'noidung' => 'Read the passage: Bees play a vital role in food production. As they move from flower to flower, they carry pollen and help plants produce fruit and seeds. Without bees, many crops would be much harder to grow. Pesticides, disease, and loss of habitat are among the biggest threats to bee populations.',
            'dokho' => 5,
            'children' => [
                [
                    'loai' => 'complete_sentence',
                    'noidung' => 'Bees help plants by carrying ___.',
                    'options' => [
                        ['pollen', true],
                        ['water', false],
                        ['soil', false],
                        ['seeds', false],
                    ],
                    'giaithich' => 'Đoạn văn nêu ong mang phấn hoa.',
                ],
                [
                    'loai' => 'identify_idea',
                    'noidung' => 'What is the main idea of the passage?',
                    'options' => [
                        ['Bees are important for food production but are threatened.', true],
                        ['Bees are not important for agriculture.', false],
                        ['Pesticides help bees survive.', false],
                        ['All crops can grow without bees.', false],
                    ],
                    'giaithich' => 'Đoạn văn nêu vai trò và mối đe dọa với ong.',
                ],
                [
                    'loai' => 'title_passage',
                    'noidung' => 'Choose the best title for the passage.',
                    'dapandung' => 'Why Bees Matter',
                    'giaithich' => 'Tiêu đề ngắn gọn về vai trò của ong.',
                ],
            ],
        ],
        [
            'noidung' => 'Read the passage: Space tourism is no longer only a dream. Several private companies are developing spacecraft that can carry paying passengers to the edge of space. The experience lasts only a few minutes, but it is extremely expensive. Critics argue that the money could be better spent on solving problems on Earth.',
            'dokho' => 7,
            'children' => [
                [
                    'loai' => 'complete_sentence',
                    'noidung' => 'Space tourism trips are currently ___.',
                    'options' => [
                        ['extremely expensive', true],
                        ['free for everyone', false],
                        ['longer than a month', false],
                        ['limited to students', false],
                    ],
                    'giaithich' => 'Đoạn văn nêu chi phí rất cao.',
                ],
                [
                    'loai' => 'identify_idea',
                    'noidung' => 'What is the main idea of the passage?',
                    'options' => [
                        ['Space tourism is possible but costly and controversial.', true],
                        ['Space tourism is already common and cheap.', false],
                        ['Private companies are not interested in space.', false],
                        ['Everyone supports space tourism.', false],
                    ],
                    'giaithich' => 'Đoạn văn nêu khả năng, chi phí và tranh cãi.',
                ],
                [
                    'loai' => 'title_passage',
                    'noidung' => 'Choose the best title for the passage.',
                    'dapandung' => 'The Business of Space Tourism',
                    'giaithich' => 'Tiêu đề khái quát hoạt động du lịch vũ trụ.',
                ],
            ],
        ],
        [
            'noidung' => 'Read the passage: Public libraries are no longer just places to borrow books. Many offer free internet access, job-search workshops, and classes for children and adults. They also provide quiet spaces for study and research. For many communities, the library is an important public resource.',
            'dokho' => 5,
            'children' => [
                [
                    'loai' => 'complete_sentence',
                    'noidung' => 'Public libraries now offer more than ___.',
                    'options' => [
                        ['books', true],
                        ['buildings', false],
                        ['computers only', false],
                        ['coffee shops only', false],
                    ],
                    'giaithich' => 'Đoạn văn nêu thư viện cung cấp nhiều dịch vụ hơn sách.',
                ],
                [
                    'loai' => 'identify_idea',
                    'noidung' => 'What is the main idea of the passage?',
                    'options' => [
                        ['Libraries provide many services beyond books.', true],
                        ['Libraries are closing everywhere.', false],
                        ['Libraries only serve children.', false],
                        ['Libraries no longer offer internet.', false],
                    ],
                    'giaithich' => 'Đoạn văn nêu vai trò mở rộng của thư viện.',
                ],
                [
                    'loai' => 'title_passage',
                    'noidung' => 'Choose the best title for the passage.',
                    'dapandung' => 'The Changing Role of Libraries',
                    'giaithich' => 'Tiêu đề phù hợp với sự thay đổi của thư viện.',
                ],
            ],
        ],
        [
            'noidung' => 'Read the passage: Electric vehicles are becoming more common on roads around the world. They produce no exhaust fumes and are quieter than petrol cars. However, they still face challenges, including limited charging stations and the environmental cost of producing batteries. Governments are investing in charging networks to encourage more people to switch.',
            'dokho' => 6,
            'children' => [
                [
                    'loai' => 'complete_sentence',
                    'noidung' => 'One advantage of electric vehicles is that they ___.',
                    'options' => [
                        ['produce no exhaust fumes', true],
                        ['need no electricity', false],
                        ['are always cheaper', false],
                        ['use more petrol', false],
                    ],
                    'giaithich' => 'Đoạn văn nêu xe điện không thải khí thải.',
                ],
                [
                    'loai' => 'identify_idea',
                    'noidung' => 'What is the main idea of the passage?',
                    'options' => [
                        ['Electric vehicles have benefits and challenges.', true],
                        ['Electric vehicles are impossible to build.', false],
                        ['Charging stations are everywhere.', false],
                        ['Petrol cars are cleaner than electric cars.', false],
                    ],
                    'giaithich' => 'Đoạn văn nêu cả ưu điểm và thách thức.',
                ],
                [
                    'loai' => 'title_passage',
                    'noidung' => 'Choose the best title for the passage.',
                    'dapandung' => 'The Future of Electric Cars',
                    'giaithich' => 'Tiêu đề khái quát xu hướng xe điện.',
                ],
            ],
        ],
    ],

    'interactive_listening' => [
        [
            'noidung' => 'Listen to a restaurant reservation and answer the questions.',
            'audio' => 'audio/questions/listening-bank-01.wav',
            'script' => 'Good evening, Bella Restaurant. How can I help you? I would like to reserve a table for four people on Saturday at seven. Certainly. Would you like a table inside or outside? Outside, please. And could we have a high chair for a child? Of course. We will see you on Saturday.',
            'dokho' => 5,
            'children' => [
                [
                    'loai' => 'listen_and_complete',
                    'noidung' => 'The customer wants a table for ___.',
                    'options' => [
                        ['four', true],
                        ['two', false],
                        ['six', false],
                        ['eight', false],
                    ],
                    'giaithich' => 'Người khách nói "a table for four people".',
                ],
                [
                    'loai' => 'listen_and_respond',
                    'noidung' => 'What does the customer ask for?',
                    'options' => [
                        ['A high chair', true],
                        ['A birthday cake', false],
                        ['A menu', false],
                        ['A taxi', false],
                    ],
                    'giaithich' => 'Người khách hỏi "could we have a high chair".',
                ],
                [
                    'loai' => 'summarise_conversation',
                    'noidung' => 'Summarise the conversation in one sentence.',
                    'dapandung' => 'The customer reserves an outdoor table for four on Saturday and asks for a high chair.',
                    'giaithich' => 'Tóm tắt cần nêu số người, thời gian, vị trí bàn và ghế trẻ em.',
                ],
            ],
        ],
        [
            'noidung' => 'Listen to a job interview and answer the questions.',
            'audio' => 'audio/questions/listening-bank-02.wav',
            'script' => 'Thanks for coming in today. Can you tell me about your experience? I worked at a bookstore for two years, where I helped customers and managed the cash register. That sounds useful. When could you start? I can start next Monday. Great. We will call you by Friday.',
            'dokho' => 6,
            'children' => [
                [
                    'loai' => 'listen_and_complete',
                    'noidung' => 'The applicant worked at a ___.',
                    'options' => [
                        ['bookstore', true],
                        ['bank', false],
                        ['school', false],
                        ['hotel', false],
                    ],
                    'giaithich' => 'Ứng viên nói đã làm ở bookstore hai năm.',
                ],
                [
                    'loai' => 'listen_and_respond',
                    'noidung' => 'When can the applicant start?',
                    'options' => [
                        ['Next Monday', true],
                        ['Next Sunday', false],
                        ['Next month', false],
                        ['Tomorrow', false],
                    ],
                    'giaithich' => 'Ứng viên nói "I can start next Monday".',
                ],
                [
                    'loai' => 'summarise_conversation',
                    'noidung' => 'Summarise the conversation in one sentence.',
                    'dapandung' => 'The applicant has bookstore experience and can start next Monday.',
                    'giaithich' => 'Tóm tắt cần nêu kinh nghiệm và thời gian bắt đầu.',
                ],
            ],
        ],
        [
            'noidung' => 'Listen to a conversation about a library card and answer the questions.',
            'audio' => 'audio/questions/listening-bank-03.wav',
            'script' => 'Hello, I would like to get a library card. Are you a resident of this city? Yes, I moved here last month. Please fill out this form and show me your ID. How many books can I borrow? You can borrow up to ten books for three weeks. Perfect, thank you.',
            'dokho' => 5,
            'children' => [
                [
                    'loai' => 'listen_and_complete',
                    'noidung' => 'The customer can borrow up to ___ books.',
                    'options' => [
                        ['ten', true],
                        ['five', false],
                        ['three', false],
                        ['twenty', false],
                    ],
                    'giaithich' => 'Thủ thư nói "up to ten books".',
                ],
                [
                    'loai' => 'listen_and_respond',
                    'noidung' => 'How long can the customer keep the books?',
                    'options' => [
                        ['Three weeks', true],
                        ['Three days', false],
                        ['Ten weeks', false],
                        ['One month', false],
                    ],
                    'giaithich' => 'Thời hạn mượn là ba tuần.',
                ],
                [
                    'loai' => 'summarise_conversation',
                    'noidung' => 'Summarise the conversation in one sentence.',
                    'dapandung' => 'The customer gets a library card and can borrow ten books for three weeks.',
                    'giaithich' => 'Tóm tắt cần nêu số sách và thời hạn mượn.',
                ],
            ],
        ],
        [
            'noidung' => 'Listen to a phone call to a clinic and answer the questions.',
            'audio' => 'audio/questions/listening-bank-04.wav',
            'script' => 'Good morning, City Clinic. I would like to make an appointment. What seems to be the problem? I have a sore throat and a cough. Can you come in tomorrow at ten? I have a meeting then. How about two in the afternoon? That works. Thank you.',
            'dokho' => 5,
            'children' => [
                [
                    'loai' => 'listen_and_complete',
                    'noidung' => 'The patient has a ___.',
                    'options' => [
                        ['sore throat and a cough', true],
                        ['headache', false],
                        ['back pain', false],
                        ['broken arm', false],
                    ],
                    'giaithich' => 'Bệnh nhân nói bị đau họng và ho.',
                ],
                [
                    'loai' => 'listen_and_respond',
                    'noidung' => 'When is the appointment?',
                    'options' => [
                        ['Tomorrow at two', true],
                        ['Tomorrow at ten', false],
                        ['Today at two', false],
                        ['Next week', false],
                    ],
                    'giaithich' => 'Cuộc hẹn được chốt lúc hai giờ chiều mai.',
                ],
                [
                    'loai' => 'summarise_conversation',
                    'noidung' => 'Summarise the conversation in one sentence.',
                    'dapandung' => 'The patient has a sore throat and a cough and books an appointment for tomorrow at two.',
                    'giaithich' => 'Tóm tắt cần nêu triệu chứng và thời gian hẹn.',
                ],
            ],
        ],
        [
            'noidung' => 'Listen to an airport check-in conversation and answer the questions.',
            'audio' => 'audio/questions/listening-bank-05.wav',
            'script' => 'Good afternoon. May I see your passport and ticket? Here you are. Are you checking any bags? Just one. Please place it on the scale. It is within the limit. Your gate is B12, and boarding starts at three fifteen. Thank you.',
            'dokho' => 5,
            'children' => [
                [
                    'loai' => 'listen_and_complete',
                    'noidung' => 'The passenger is checking ___ bag.',
                    'options' => [
                        ['one', true],
                        ['two', false],
                        ['three', false],
                        ['no', false],
                    ],
                    'giaithich' => 'Hành khách nói "Just one".',
                ],
                [
                    'loai' => 'listen_and_respond',
                    'noidung' => 'Where is the gate?',
                    'options' => [
                        ['B12', true],
                        ['A12', false],
                        ['B20', false],
                        ['C12', false],
                    ],
                    'giaithich' => 'Nhân viên nói gate là B12.',
                ],
                [
                    'loai' => 'summarise_conversation',
                    'noidung' => 'Summarise the conversation in one sentence.',
                    'dapandung' => 'The passenger checks one bag and boards at gate B12.',
                    'giaithich' => 'Tóm tắt cần nêu số hành lý và cổng.',
                ],
            ],
        ],
        [
            'noidung' => 'Listen to a gym membership conversation and answer the questions.',
            'audio' => 'audio/questions/listening-bank-06.wav',
            'script' => 'Welcome to FitLife Gym. Are you interested in a membership? Yes, I would like the monthly plan. The monthly plan is thirty dollars and includes access to all classes. Is there a joining fee? No, there is not. Great, I will take it.',
            'dokho' => 5,
            'children' => [
                [
                    'loai' => 'listen_and_complete',
                    'noidung' => 'The customer chooses the ___ plan.',
                    'options' => [
                        ['monthly', true],
                        ['yearly', false],
                        ['weekly', false],
                        ['student', false],
                    ],
                    'giaithich' => 'Khách chọn gói monthly.',
                ],
                [
                    'loai' => 'listen_and_respond',
                    'noidung' => 'How much is the plan?',
                    'options' => [
                        ['Thirty dollars', true],
                        ['Thirteen dollars', false],
                        ['Three dollars', false],
                        ['Fifty dollars', false],
                    ],
                    'giaithich' => 'Gói tập giá ba mươi đô la.',
                ],
                [
                    'loai' => 'summarise_conversation',
                    'noidung' => 'Summarise the conversation in one sentence.',
                    'dapandung' => 'The customer joins the gym on the monthly plan for thirty dollars.',
                    'giaithich' => 'Tóm tắt cần nêu loại gói và giá.',
                ],
            ],
        ],
        [
            'noidung' => 'Listen to a course registration conversation and answer the questions.',
            'audio' => 'audio/questions/listening-bank-07.wav',
            'script' => 'I would like to register for the evening English course. The course runs for twelve weeks, on Tuesdays and Thursdays. What time does it start? At six thirty. Is there a placement test? Yes, it is online and takes about thirty minutes. I will send you the link.',
            'dokho' => 6,
            'children' => [
                [
                    'loai' => 'listen_and_complete',
                    'noidung' => 'The course is on ___.',
                    'options' => [
                        ['Tuesdays and Thursdays', true],
                        ['Mondays and Wednesdays', false],
                        ['weekends', false],
                        ['Fridays', false],
                    ],
                    'giaithich' => 'Khóa học diễn ra thứ Ba và thứ Năm.',
                ],
                [
                    'loai' => 'listen_and_respond',
                    'noidung' => 'How long is the placement test?',
                    'options' => [
                        ['About thirty minutes', true],
                        ['About three hours', false],
                        ['Twelve weeks', false],
                        ['Six minutes', false],
                    ],
                    'giaithich' => 'Bài kiểm tra đầu vào kéo dài khoảng ba mươi phút.',
                ],
                [
                    'loai' => 'summarise_conversation',
                    'noidung' => 'Summarise the conversation in one sentence.',
                    'dapandung' => 'The student registers for a twelve-week evening English course with an online placement test.',
                    'giaithich' => 'Tóm tắt cần nêu thời lượng khóa học và bài kiểm tra.',
                ],
            ],
        ],
        [
            'noidung' => 'Listen to an apartment viewing and answer the questions.',
            'audio' => 'audio/questions/listening-bank-08.wav',
            'script' => 'Thanks for showing me the apartment. It is bright and quite spacious. How much is the rent? It is nine hundred dollars a month, including water. Is electricity included? No, that is separate. When can I move in? The apartment is available from the first of next month.',
            'dokho' => 6,
            'children' => [
                [
                    'loai' => 'listen_and_complete',
                    'noidung' => 'The rent is ___ a month.',
                    'options' => [
                        ['nine hundred dollars', true],
                        ['seven hundred dollars', false],
                        ['one thousand dollars', false],
                        ['five hundred dollars', false],
                    ],
                    'giaithich' => 'Giá thuê là chín trăm đô la một tháng.',
                ],
                [
                    'loai' => 'listen_and_respond',
                    'noidung' => 'When is the apartment available?',
                    'options' => [
                        ['The first of next month', true],
                        ['Today', false],
                        ['Next year', false],
                        ['The end of this week', false],
                    ],
                    'giaithich' => 'Căn hộ có thể vào ở từ ngày đầu tháng sau.',
                ],
                [
                    'loai' => 'summarise_conversation',
                    'noidung' => 'Summarise the conversation in one sentence.',
                    'dapandung' => 'The apartment costs nine hundred dollars a month and is available next month.',
                    'giaithich' => 'Tóm tắt cần nêu giá thuê và thời gian có thể vào ở.',
                ],
            ],
        ],
        [
            'noidung' => 'Listen to a train ticket conversation and answer the questions.',
            'audio' => 'audio/questions/listening-bank-09.wav',
            'script' => 'I would like a return ticket to Boston, please. When would you like to travel? Tomorrow morning, around eight. The eight fifteen train is available. How long does the journey take? About three hours. That is fine. I will take it.',
            'dokho' => 5,
            'children' => [
                [
                    'loai' => 'listen_and_complete',
                    'noidung' => 'The customer wants a ticket to ___.',
                    'options' => [
                        ['Boston', true],
                        ['Chicago', false],
                        ['Seattle', false],
                        ['Denver', false],
                    ],
                    'giaithich' => 'Khách mua vé đến Boston.',
                ],
                [
                    'loai' => 'listen_and_respond',
                    'noidung' => 'How long is the journey?',
                    'options' => [
                        ['About three hours', true],
                        ['About thirty minutes', false],
                        ['About eight hours', false],
                        ['About three days', false],
                    ],
                    'giaithich' => 'Hành trình kéo dài khoảng ba giờ.',
                ],
                [
                    'loai' => 'summarise_conversation',
                    'noidung' => 'Summarise the conversation in one sentence.',
                    'dapandung' => 'The customer buys a return ticket to Boston on the eight fifteen train.',
                    'giaithich' => 'Tóm tắt cần nêu điểm đến và chuyến tàu.',
                ],
            ],
        ],
        [
            'noidung' => 'Listen to a customer service call and answer the questions.',
            'audio' => 'audio/questions/listening-bank-10.wav',
            'script' => 'Hello, I ordered a lamp last week, but it arrived broken. I am sorry to hear that. Would you like a replacement or a refund? I would prefer a replacement. Certainly. We will send a new one today, and you can return the broken lamp for free. Thank you.',
            'dokho' => 6,
            'children' => [
                [
                    'loai' => 'listen_and_complete',
                    'noidung' => 'The customer received a ___ lamp.',
                    'options' => [
                        ['broken', true],
                        ['wrong', false],
                        ['missing', false],
                        ['used', false],
                    ],
                    'giaithich' => 'Khách nói đèn đến bị hỏng.',
                ],
                [
                    'loai' => 'listen_and_respond',
                    'noidung' => 'What does the customer choose?',
                    'options' => [
                        ['A replacement', true],
                        ['A refund', false],
                        ['A discount', false],
                        ['A repair', false],
                    ],
                    'giaithich' => 'Khách chọn đổi hàng mới.',
                ],
                [
                    'loai' => 'summarise_conversation',
                    'noidung' => 'Summarise the conversation in one sentence.',
                    'dapandung' => 'The customer receives a broken lamp and asks for a replacement.',
                    'giaithich' => 'Tóm tắt cần nêu sản phẩm lỗi và lựa chọn đổi hàng.',
                ],
            ],
        ],
    ],

    'write_about_photo' => [
        [
            'noidung' => 'Describe the scene in the photo. What are the people doing, and what is the mood?',
            'anh' => 'images/questions/photo-4.svg',
            'dokho' => 6,
        ],
        [
            'noidung' => 'Write a short description of the photo. Include the setting, the people, and their activities.',
            'anh' => 'images/questions/photo-5.svg',
            'dokho' => 6,
        ],
        [
            'noidung' => 'Look at the photo and describe what is happening. What might happen next?',
            'anh' => 'images/questions/photo-6.svg',
            'dokho' => 7,
        ],
        [
            'noidung' => 'Describe the photo in 3-5 sentences. Focus on details such as objects, colors, and people.',
            'anh' => 'images/questions/photo-7.svg',
            'dokho' => 7,
        ],
        [
            'noidung' => 'Write about the scene in the photo. Where are the people, and how do they seem to feel?',
            'anh' => 'images/questions/photo-8.svg',
            'dokho' => 7,
        ],
        [
            'noidung' => 'Describe the photo. What is the main subject, and what details support your description?',
            'anh' => 'images/questions/photo-9.svg',
            'dokho' => 7,
        ],
        [
            'noidung' => 'Write a short paragraph about the photo. Include what you see and what you think is happening.',
            'anh' => 'images/questions/photo-10.svg',
            'dokho' => 8,
        ],
        [
            'noidung' => 'Describe the scene. What are the people doing, and what might they be talking about?',
            'anh' => 'images/questions/photo-11.svg',
            'dokho' => 8,
        ],
        [
            'noidung' => 'Look at the photo and describe it in detail. Mention the setting and the people.',
            'anh' => 'images/questions/photo-12.svg',
            'dokho' => 8,
        ],
        [
            'noidung' => 'Write a description of the photo. What is the story behind this scene?',
            'anh' => 'images/questions/photo-13.svg',
            'dokho' => 9,
        ],
    ],

    'read_aloud' => [
        [
            'noidung' => 'Read the following sentence aloud: "Clear goals and steady effort are often more valuable than short bursts of motivation."',
            'dokho' => 5,
        ],
        [
            'noidung' => 'Read the following paragraph aloud: "Learning a new skill requires patience and regular practice. Progress matters more than perfection, and every mistake is a chance to improve."',
            'dokho' => 6,
        ],
        [
            'noidung' => 'Read the following sentence aloud: "The city library offers free internet access, quiet study spaces, and workshops for people of all ages."',
            'dokho' => 5,
        ],
        [
            'noidung' => 'Read the following paragraph aloud: "Climate change affects weather patterns, food production, and the availability of clean water. Governments and communities must work together to reduce its impact."',
            'dokho' => 7,
        ],
        [
            'noidung' => 'Read the following sentence aloud: "Good communication is not only about speaking clearly; it is also about listening carefully."',
            'dokho' => 6,
        ],
        [
            'noidung' => 'Read the following paragraph aloud: "Many scientific discoveries begin with a simple question and a willingness to observe the world. Curiosity and patience are just as important as intelligence."',
            'dokho' => 7,
        ],
        [
            'noidung' => 'Read the following sentence aloud: "Volunteering can help you develop new skills, meet new people, and make a positive difference."',
            'dokho' => 5,
        ],
        [
            'noidung' => 'Read the following paragraph aloud: "A balanced diet, regular exercise, and enough sleep are essential for good health. Small daily habits often have a greater effect than short-term changes."',
            'dokho' => 6,
        ],
        [
            'noidung' => 'Read the following sentence aloud: "The internet has changed the way people work, learn, and communicate with one another."',
            'dokho' => 5,
        ],
        [
            'noidung' => 'Read the following paragraph aloud: "Travel can broaden your perspective and help you understand different cultures and ways of life. It also teaches patience and flexibility."',
            'dokho' => 6,
        ],
    ],

    'read_then_speak' => [
        [
            'noidung' => 'Read the short text, then speak about whether you agree with it: "High school students should complete a community project before graduation."',
            'dokho' => 7,
        ],
        [
            'noidung' => 'Read the short text, then speak about whether you agree with it: "Working from home is better than working in an office."',
            'dokho' => 7,
        ],
        [
            'noidung' => 'Read the short text, then speak about whether you agree with it: "Governments should invest more in public transport than in new roads."',
            'dokho' => 8,
        ],
        [
            'noidung' => 'Read the short text, then speak about whether you agree with it: "Social media does more harm than good for young people."',
            'dokho' => 8,
        ],
        [
            'noidung' => 'Read the short text, then speak about whether you agree with it: "Everyone should learn how to cook."',
            'dokho' => 7,
        ],
        [
            'noidung' => 'Read the short text, then speak about whether you agree with it: "Museums should be free for all visitors."',
            'dokho' => 7,
        ],
        [
            'noidung' => 'Read the short text, then speak about whether you agree with it: "It is better to live in a city than in the countryside."',
            'dokho' => 8,
        ],
        [
            'noidung' => 'Read the short text, then speak about whether you agree with it: "Schools should require students to do volunteer work."',
            'dokho' => 8,
        ],
        [
            'noidung' => 'Read the short text, then speak about whether you agree with it: "Artificial intelligence will create more jobs than it replaces."',
            'dokho' => 9,
        ],
        [
            'noidung' => 'Read the short text, then speak about whether you agree with it: "People should spend less time on their phones."',
            'dokho' => 8,
        ],
    ],
];
