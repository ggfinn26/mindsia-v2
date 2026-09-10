<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Telegram File Gallery</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" crossorigin="anonymous">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js" crossorigin="anonymous"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 40px 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        h1 {
            color: white;
            text-align: center;
            margin-bottom: 40px;
            font-size: 2.5rem;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
        }

        .gallery {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
        }

        .file-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            cursor: pointer;
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .file-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 16px 48px rgba(0, 0, 0, 0.2);
        }

        .file-preview {
            position: relative;
            width: 100%;
            padding-bottom: 100%;
            overflow: hidden;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 60px;
        }

        .file-preview img {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .file-preview.pdf {
            background: linear-gradient(135deg, #f93b1d 0%, #ea1e63 100%);
        }

        .file-preview.word {
            background: linear-gradient(135deg, #2b579a 0%, #5b9bd5 100%);
        }

        .file-preview.text {
            background: linear-gradient(135deg, #36c5f0 0%, #34a853 100%);
        }

        .file-info {
            padding: 16px;
            background: white;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .file-name {
            font-weight: 600;
            color: #333;
            font-size: 14px;
            word-break: break-word;
            margin-bottom: 8px;
        }

        .file-meta {
            display: flex;
            justify-content: space-between;
            font-size: 12px;
            color: #999;
        }

        .file-size {
            margin-top: 8px;
        }

        .download-btn {
            display: inline-block;
            margin-top: 12px;
            padding: 8px 16px;
            background: #667eea;
            color: white;
            border-radius: 6px;
            text-decoration: none;
            font-size: 12px;
            font-weight: 600;
            transition: background 0.3s ease;
            text-align: center;
        }

        .download-btn:hover {
            background: #764ba2;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.7);
        }

        .modal.active {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .modal-content {
            background: white;
            border-radius: 12px;
            width: 90%;
            max-width: 900px;
            max-height: 90vh;
            overflow: auto;
            padding: 20px;
            position: relative;
        }

        .modal-close {
            position: absolute;
            top: 20px;
            right: 20px;
            font-size: 28px;
            cursor: pointer;
            color: #999;
        }

        .modal-close:hover {
            color: #333;
        }

        #pdf-viewer {
            width: 100%;
            height: 600px;
            border: 1px solid #ddd;
            border-radius: 8px;
        }

        .pdf-controls {
            margin-top: 15px;
            display: flex;
            gap: 10px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .pdf-controls button {
            padding: 8px 16px;
            background: #667eea;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
        }

        .pdf-controls button:hover {
            background: #764ba2;
        }

        .pdf-page-info {
            text-align: center;
            margin-top: 10px;
            color: #666;
            font-size: 14px;
        }

        .error {
            text-align: center;
            color: #ff6b6b;
            padding: 40px;
            background: white;
            border-radius: 12px;
            max-width: 500px;
            margin: 0 auto;
        }

        @media (max-width: 768px) {
            h1 {
                font-size: 1.8rem;
            }

            .gallery {
                grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
                gap: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>📁 Telegram File Gallery</h1>

        @if(empty($items))
            <div class="error">
                <p>No files found</p>
            </div>
        @else
            <div class="gallery">
                @foreach($items as $item)
                    <div class="file-card" @if($item['type'] === 'pdf' || $item['type'] === 'word') onclick="openFileModal('{{ $item['url'] }}', '{{ $item['filename'] }}', '{{ $item['proxyUrl'] }}', '{{ $item['type'] }}')" style="cursor: pointer;" @endif>
                        <div class="file-preview {{ $item['type'] }}">
                            @if($item['type'] === 'image')
                                <img src="{{ $item['url'] }}" alt="{{ $item['filename'] }}" loading="lazy">
                            @elseif($item['type'] === 'pdf')
                                <i class="fas fa-file-pdf"></i>
                            @elseif($item['type'] === 'word')
                                <i class="fas fa-file-word"></i>
                            @else
                                <i class="fas fa-file"></i>
                            @endif
                        </div>
                        <div class="file-info">
                            <div>
                                <div class="file-name">{{ $item['filename'] }}</div>
                                <div class="file-meta">
                                    <span>{{ $item['type'] }}</span>
                                    <span class="file-size">{{ $item['file_size_formatted'] }}</span>
                                </div>
                            </div>
                            <a href="{{ $item['url'] }}" download="{{ $item['filename'] }}" class="download-btn" target="_blank">
                                <i class="fas fa-download"></i> Download
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <div id="pdfModal" class="modal">
        <div class="modal-content">
            <span class="modal-close" onclick="closePdfModal()">&times;</span>
            <div style="display: flex; flex-direction: column; height: 100%; gap: 15px;">
                <h3 id="pdf-filename" style="color: #333; margin: 0;"></h3>
                <div id="preview-container" style="flex: 1; overflow: auto; background: #f5f5f5; border-radius: 8px;">
                    <canvas id="pdf-viewer" style="display: none; width: 100%;"></canvas>
                    <iframe id="word-viewer" style="display: none; width: 100%; height: 100%; border: none; border-radius: 8px;"></iframe>
                </div>
                <div style="display: flex; gap: 10px; justify-content: center;">
                    <button id="prev-btn" onclick="previousPage()" style="padding: 8px 16px; background: #667eea; color: white; border: none; border-radius: 6px; cursor: pointer;">← Prev</button>
                    <span id="pdf-page-info" style="align-self: center; color: #666;">Page 1 of 1</span>
                    <button id="next-btn" onclick="nextPage()" style="padding: 8px 16px; background: #667eea; color: white; border: none; border-radius: 6px; cursor: pointer;">Next →</button>
                    <a id="download-link" href="#" download style="padding: 8px 16px; background: #34a853; color: white; border-radius: 6px; text-decoration: none; font-weight: 600; display: flex; align-items: center; gap: 8px;">
                        <i class="fas fa-download"></i> Download
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        let currentPdfDoc = null;
        let currentPageNum = 1;

        if (typeof pdfjsLib !== 'undefined') {
            pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
        }

        function openFileModal(url, filename, proxyUrl, type) {
            document.getElementById('pdf-filename').textContent = filename;
            document.getElementById('download-link').href = url;
            document.getElementById('download-link').download = filename;

            const canvas = document.getElementById('pdf-viewer');
            const iframe = document.getElementById('word-viewer');
            const pageInfo = document.getElementById('pdf-page-info');
            const prevBtn = document.getElementById('prev-btn');
            const nextBtn = document.getElementById('next-btn');

            canvas.style.display = 'none';
            iframe.style.display = 'none';
            prevBtn.style.display = 'none';
            nextBtn.style.display = 'none';
            pageInfo.style.display = 'none';

            if (type === 'pdf') {
                canvas.style.display = 'block';
                pageInfo.style.display = 'inline-block';
                prevBtn.style.display = 'inline-block';
                nextBtn.style.display = 'inline-block';
                loadPdf(proxyUrl);
            } else if (type === 'word') {
                iframe.style.display = 'block';
                loadWord(proxyUrl);
            }

            document.getElementById('pdfModal').classList.add('active');
        }

        function closePdfModal() {
            document.getElementById('pdfModal').classList.remove('active');
            currentPdfDoc = null;
            currentPageNum = 1;
        }

        function loadPdf(proxyUrl) {
            if (typeof pdfjsLib === 'undefined') {
                alert('PDF library not loaded');
                return;
            }

            pdfjsLib.getDocument(proxyUrl).promise.then(function(pdf) {
                currentPdfDoc = pdf;
                document.getElementById('pdf-page-info').textContent = 'Page 1 of ' + pdf.numPages;
                currentPageNum = 1;
                renderPage(1);
            }).catch(function(error) {
                console.error('PDF Error:', error);
                alert('Error loading PDF: ' + error.message);
            });
        }

        function loadWord(proxyUrl) {
            // Word preview via Office Online (requires public URL)
            // For now, show message and provide download
            const iframe = document.getElementById('word-viewer');
            iframe.innerHTML = '';
            iframe.srcdoc = `
                <html>
                    <head>
                        <style>
                            body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI'; padding: 40px; text-align: center; background: #f5f5f5; }
                            .container { background: white; padding: 40px; border-radius: 8px; max-width: 500px; margin: 0 auto; }
                            i { font-size: 48px; color: #2b579a; margin-bottom: 20px; display: block; }
                            h3 { color: #333; margin-bottom: 20px; }
                            p { color: #666; margin-bottom: 30px; }
                        </style>
                    </head>
                    <body>
                        <div class="container">
                            <i class="fas fa-file-word">📄</i>
                            <h3>Word Document</h3>
                            <p>Preview tidak tersedia untuk dokumen Word. Silakan download file untuk membuka.</p>
                        </div>
                    </body>
                </html>
            `;
        }

        function renderPage(pageNum) {
            if (!currentPdfDoc) return;

            currentPdfDoc.getPage(pageNum).then(function(page) {
                const scale = 1.5;
                const viewport = page.getViewport({scale: scale});
                const canvas = document.getElementById('pdf-viewer');
                const context = canvas.getContext('2d');

                canvas.height = viewport.height;
                canvas.width = viewport.width;

                page.render({
                    canvasContext: context,
                    viewport: viewport
                }).promise.then(function() {
                    document.getElementById('pdf-page-info').textContent = 'Page ' + pageNum + ' of ' + currentPdfDoc.numPages;
                });
            });
        }

        function nextPage() {
            if (!currentPdfDoc || currentPageNum >= currentPdfDoc.numPages) return;
            currentPageNum++;
            renderPage(currentPageNum);
        }

        function previousPage() {
            if (currentPageNum <= 1) return;
            currentPageNum--;
            renderPage(currentPageNum);
        }

        document.getElementById('pdfModal').addEventListener('click', function(e) {
            if (e.target === this) closePdfModal();
        });

        document.addEventListener('keydown', function(e) {
            if (document.getElementById('pdfModal').classList.contains('active')) {
                if (e.key === 'Escape') closePdfModal();
            }
        });
    </script>
</body>
</html>
