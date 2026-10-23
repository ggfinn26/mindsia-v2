{{-- Listening directions card per part. Usage: @include('toefl.partials.listening-directions', ['part' => 'A']) --}}
@if($part === 'A')
    <div class="bg-white rounded-xl border border-indigo-200 shadow-sm p-6 mb-4">
        <p class="text-xs text-indigo-500 uppercase font-bold tracking-wider mb-1">Section 1 — Listening Comprehension</p>
        <p class="text-xs text-gray-500 mb-4">Time: approximately 35 minutes (including the reading of the directions for each part)</p>
        <p class="text-sm text-gray-700 leading-relaxed mb-4">
            In this section of the test, you will have an opportunity to demonstrate your ability to understand conversations and talks in English. There are three parts to this section. Answer all the questions on the basis of what is <strong>stated</strong> or <strong>implied</strong> by the speakers you hear. Do not take notes or write in your test book at any time. <strong>Do not</strong> turn the pages until you are told to do so.
        </p>
        <div class="border-t border-gray-100 pt-4">
            <p class="text-xs text-indigo-600 uppercase font-bold tracking-wider mb-2">Part A</p>
            <p class="text-sm text-gray-700 leading-relaxed">
                <span class="font-semibold underline">Directions:</span> In Part A you will hear short conversations between two people. After each conversation, you will hear a question about the conversation. The conversations and questions will not be repeated. After you hear a question, read the four possible answers in your test book and choose the best answer. Then, on your answer sheet, find the number of the question and fill in the space that corresponds to the letter of the answer you have chosen.
            </p>
            <p class="text-sm text-gray-700 font-semibold mt-4 mb-2">Listen to an example.</p>
            <div class="text-sm text-gray-700 leading-relaxed rounded-lg p-4 space-y-3" style="background-color: #f9fafb; border: 1px solid #e5e7eb;">
                <div>
                    <p class="font-medium">On the recording, you hear:</p>
                    <p>(man) That exam was just awful.</p>
                    <p>(woman) Oh, it could have been worse.</p>
                    <p>(narrator) What does the woman mean?</p>
                </div>
                <div>
                    <p class="font-medium">In your test book, you read:</p>
                    <p>A. The exam was really awful.</p>
                    <p>B. It was the worst exam she had ever seen.</p>
                    <p>C. It couldn't have been more difficult.</p>
                    <p>D. It wasn't that hard.</p>
                </div>
            </div>
            <p class="text-sm text-gray-700 leading-relaxed mt-3">
                You learn from the conversation that the man thought the exam was very difficult and that the woman disagreed with the man. The best answer to the question, "What does the woman mean?" is (D), "It wasn't that hard." Therefore, the correct choice is (D).
            </p>
        </div>
    </div>
@elseif($part === 'B')
    <div class="bg-white rounded-xl border border-amber-200 shadow-sm p-6 mb-4">
        <p class="text-xs text-amber-600 uppercase font-bold tracking-wider mb-3">Part B</p>
        <p class="text-sm text-gray-700 leading-relaxed mb-3">
            <span class="font-semibold">Directions:</span> In this part of the test, you will hear longer conversations. After each conversation, you will hear several questions. The conversations and questions will not be repeated.
        </p>
        <p class="text-sm text-gray-700 leading-relaxed mb-3">
            After you hear a question, read the four possible answers in your test book and choose the best answer. Then, on your answer sheet, find the number of the question and fill in the space that corresponds to the letter of the answer you have chosen.
        </p>
        <p class="text-sm text-gray-700 font-medium">
            Remember, you are not allowed to take notes or write in your test book.
        </p>
    </div>
@elseif($part === 'C')
    <div class="bg-white rounded-xl border border-teal-200 shadow-sm p-6 mb-4">
        <p class="text-xs text-teal-600 uppercase font-bold tracking-wider mb-3">Part C</p>
        <p class="text-sm text-gray-700 leading-relaxed mb-3">
            <span class="font-semibold underline">Directions:</span> In this part of the test, you will hear several talks. After each talk, you will hear some questions. The talks and questions will not be repeated.
        </p>
        <p class="text-sm text-gray-700 leading-relaxed">
            After you hear a question, read the four possible answers in your test book and choose the best answer. Then, on your answer sheet, find the number of the question and fill in the space that corresponds to the letter of the answer you have chosen.
        </p>
        <p class="text-sm text-gray-700 font-semibold mt-4 mb-2">Here is an example.</p>
        <div class="text-sm text-gray-700 leading-relaxed rounded-lg p-4 space-y-3" style="background-color: #f9fafb; border: 1px solid #e5e7eb;">
            <p class="font-medium">On the recording, you hear:</p>
            <p>(narrator) Listen to an instructor talk to his class about painting.</p>
            <p>(man) Artist Grant Wood was a guiding force in the school of painting known as American regionalist, a style reflecting the distinctive characteristics of art from rural areas of the United States. Wood began drawing animals on the family farm at the age of three, and when he was thirty-eight one of his paintings received a remarkable amount of public notice and acclaim. This painting, called "American Gothic," is a starkly simple depiction of a serious couple staring directly out at the viewer.</p>
        </div>
        <p class="text-sm text-gray-700 font-semibold mt-4 mb-2">Now listen to a sample question.</p>
        <div class="text-sm text-gray-700 leading-relaxed rounded-lg p-4 space-y-1" style="background-color: #f9fafb; border: 1px solid #e5e7eb;">
            <p>(narrator) What style of painting is known as American regionalist?</p>
            <p class="font-medium pt-2">In your test book, you read:</p>
            <p>A. Art from America's inner cities</p>
            <p>B. Art from the central region of the United States</p>
            <p>C. Art from various urban areas in the United States</p>
            <p>D. Art from rural sections of America</p>
        </div>
        <p class="text-sm text-gray-700 leading-relaxed mt-3">
            The best answer to the question, "What style of painting is known as American regionalist?" is (D), "Art from rural sections of America." Therefore, the correct choice is (D).
        </p>
        <p class="text-sm text-gray-700 font-semibold mt-4 mb-2">Now listen to another sample question.</p>
        <div class="text-sm text-gray-700 leading-relaxed rounded-lg p-4 space-y-1" style="background-color: #f9fafb; border: 1px solid #e5e7eb;">
            <p>(narrator) What is the name of Wood's most successful painting?</p>
            <p class="font-medium pt-2">In your test book, you read:</p>
            <p>A. "American Regionalist"</p>
            <p>B. "The Family Farm in Iowa"</p>
            <p>C. "American Gothic"</p>
            <p>D. "A Serious Couple"</p>
        </div>
        <p class="text-sm text-gray-700 leading-relaxed mt-3">
            The best answer to the question, "What is the name of Wood's most successful painting?" is (C), "American Gothic." Therefore, the correct choice is (C).
        </p>
        <p class="text-sm text-gray-700 font-medium mt-4">
            Remember, you are not allowed to take notes or write in your test book.
        </p>
    </div>
@endif
