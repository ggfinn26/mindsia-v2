<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>{{ $status }} — Error | MINDSIA</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        .font-jakarta { font-family: 'Plus Jakarta Sans', sans-serif; }
        .font-inter { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="min-h-screen bg-[#F8FAFC] text-[#111827] font-inter antialiased flex items-center justify-center p-6">

@php
    $map = [
        401 => [
            'badge'   => 'Unauthorized',
            'note'    => '"You need to log in before entering this classroom."',
            'body'    => 'You haven\'t identified yourself yet — like a student who shows up to class without their student card. Please log in and try again.',
            'word'    => 'unauthorized', 'ipa' => '/ʌnˈɔː.θər.aɪzd/', 'pos' => 'adjective',
            'def'     => 'Not having official permission. Like speaking in class without raising your hand first.',
            'tagline' => '"Please introduce yourself before entering." ✦',
        ],
        405 => [
            'badge'   => 'Wrong Method',
            'note'    => '"That is the wrong answer. Try a different approach."',
            'body'    => 'The request method isn\'t allowed here. Like answering a fill-in-the-blank question with a full essay — the format is wrong, even if the intent is right.',
            'word'    => 'incorrect', 'ipa' => '/ˌɪn.kəˈrekt/', 'pos' => 'adjective',
            'def'     => 'Not in accordance with fact or particular standards. Your method was incorrect. Please consult the syllabus.',
            'tagline' => '"Wrong format. Read the instructions next time." ✦',
        ],
        408 => [
            'badge'   => 'Timed Out',
            'note'    => '"The connection took too long to respond."',
            'body'    => 'Request timeout — the server waited, like a teacher waiting for a student to answer a question, until the awkward silence became unbearable. Please try again.',
            'word'    => 'procrastinate', 'ipa' => '/prəˈkræs.tɪ.neɪt/', 'pos' => 'verb',
            'def'     => 'To delay or postpone action. What this connection did instead of responding promptly.',
            'tagline' => '"Time management is a C1 skill. Practice it." ✦',
        ],
        413 => [
            'badge'   => 'Too Large',
            'note'    => '"Your submission exceeds the page limit."',
            'body'    => 'The file or data you sent is too large — much like an essay that was supposed to be 500 words but somehow became 5,000. Please reduce the size and resubmit.',
            'word'    => 'concise', 'ipa' => '/kənˈsaɪs/', 'pos' => 'adjective',
            'def'     => 'Giving a lot of information clearly and in a few words. The opposite of what you just sent.',
            'tagline' => '"Quality over quantity. Always." ✦',
        ],
        422 => [
            'badge'   => 'Unprocessable',
            'note'    => '"This answer cannot be marked. Please re-read the question."',
            'body'    => 'The input provided is semantically incorrect — like writing "I is happy" on a grammar test. Grammatically attempted, logically broken. Please check your input and try again.',
            'word'    => 'invalid', 'ipa' => '/ɪnˈvæl.ɪd/', 'pos' => 'adjective',
            'def'     => 'Not valid; not legally or officially acceptable. Also: a sentence without subject-verb agreement.',
            'tagline' => '"Check your work before submitting." ✦',
        ],
        502 => [
            'badge'   => 'Bad Gateway',
            'note'    => '"There was a miscommunication between the servers."',
            'body'    => 'Two servers tried to communicate and failed — like a game of telephone where the message "the cat sat on the mat" became "the rat ate the hat." We\'re looking into it.',
            'word'    => 'miscommunication', 'ipa' => '/ˌmɪs.kəˌmjuː.nɪˈkeɪ.ʃən/', 'pos' => 'noun',
            'def'     => 'Failure to communicate adequately. The root cause of most homework misunderstandings and, apparently, this error.',
            'tagline' => '"Active listening prevents miscommunication. Servers haven\'t learned this yet." ✦',
        ],
        504 => [
            'badge'   => 'Gateway Timeout',
            'note'    => '"The upstream server did not respond in time."',
            'body'    => 'The gateway timed out waiting for a response — like asking a classmate for the answer and they just stare at you for 60 seconds. Uncomfortable. Please try again.',
            'word'    => 'unresponsive', 'ipa' => '/ˌʌn.rɪˈspɒn.sɪv/', 'pos' => 'adjective',
            'def'     => 'Not reacting to attempts at communication. The server\'s current mood. Also a common participation grade.',
            'tagline' => '"Participation matters. Tell that to the gateway." ✦',
        ],
    ];

    $data = $map[$status] ?? [
        'badge'   => 'Unexpected Error',
        'note'    => '"Something went wrong — and it wasn\'t in the curriculum."',
        'body'    => 'An unexpected error occurred. Like an idiom that makes no logical sense — it just is what it is. Our team has been notified.',
        'word'    => 'anomaly', 'ipa' => '/əˈnɒm.ə.li/', 'pos' => 'noun',
        'def'     => 'Something that deviates from what is standard or expected. Today, that\'s this error.',
        'tagline' => '"Even the best students encounter questions they\'ve never seen before." ✦',
    ];
@endphp

    <div class="w-full max-w-xl">

        <div class="bg-white border border-[#E5E7EB] rounded-2xl shadow-sm overflow-hidden">

            <div class="bg-[#5586DB] px-8 py-5 flex items-center justify-between">
                <img src="/img/logo/mindsia-logo.webp" alt="Mindsia Logo" class="h-8 w-auto filter brightness-0 invert opacity-90">
                <span class="font-jakarta font-extrabold text-white/80 text-[13px] uppercase tracking-widest">Error Report</span>
            </div>

            <div class="px-8 py-8">

                <div class="flex items-start justify-between mb-6">
                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-widest text-[#9CA3AF] mb-1">HTTP Status</p>
                        <p class="font-jakarta font-extrabold text-[56px] leading-none text-[#5586DB]">{{ $status }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-[11px] font-bold uppercase tracking-widest text-[#9CA3AF] mb-1">Verdict</p>
                        <p class="font-jakarta font-extrabold text-[18px] leading-none text-amber-400 mt-2 max-w-[140px]">{{ $data['badge'] }}</p>
                    </div>
                </div>

                <div class="border-t border-dashed border-[#E5E7EB] pt-6 mb-6">
                    <p class="text-[11px] font-bold uppercase tracking-widest text-[#9CA3AF] mb-3">Teacher's Note</p>
                    <h1 class="font-jakarta font-extrabold text-[22px] text-[#111827] mb-3 leading-snug">
                        {{ $data['note'] }}
                    </h1>
                    <p class="text-[14px] text-[#4B5563] leading-relaxed">{{ $data['body'] }}</p>
                </div>

                <div class="bg-[#F8FAFC] border border-[#E5E7EB] rounded-xl px-5 py-4 mb-8">
                    <p class="text-[13px] text-[#111827]">
                        <span class="font-bold">{{ $data['word'] }}</span>
                        <span class="text-[#9CA3AF] text-[12px] mx-1">{{ $data['ipa'] }}</span>
                        <span class="italic text-[#9CA3AF] text-[12px]">{{ $data['pos'] }}</span>
                    </p>
                    <p class="text-[13px] text-[#4B5563] mt-1">
                        <span class="font-bold">1.</span> {{ $data['def'] }}
                    </p>
                </div>

                <div class="flex flex-col sm:flex-row gap-3">
                    <a href="javascript:history.back()"
                       class="flex-1 inline-flex items-center justify-center gap-2 px-5 py-3 bg-white border border-[#E5E7EB] rounded-xl text-[14px] font-bold text-[#4B5563] hover:border-[#5586DB] hover:text-[#5586DB] transition-all">
                        ← Kembali
                    </a>
                    <a href="/"
                       class="flex-1 inline-flex items-center justify-center gap-2 px-5 py-3 bg-[#5586DB] rounded-xl text-[14px] font-bold text-white hover:bg-[#00AACC] transition-all shadow-[0_4px_12px_-2px_rgba(85,134,219,0.3)]">
                        Ke Beranda
                    </a>
                </div>
            </div>

        </div>

        <p class="text-center text-[12px] text-[#9CA3AF] mt-5 font-medium">
            {{ $data['tagline'] }}
        </p>

    </div>

</body>
</html>
