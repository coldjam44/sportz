<?php
include 'routes.php'; // فيه الاتصال $conn والتصميم الأساسي

// تحديد المسار المحتمل للملف api.php
$currentDir = getcwd();
$apiFile = dirname(dirname($currentDir)) . DIRECTORY_SEPARATOR . 'routes' . DIRECTORY_SEPARATOR . 'api.php';

$routes = [];
$apiCode = '';
$fileError = false;

// تحقق من وجود الملف وقابليته للقراءة
if (file_exists($apiFile) && is_readable($apiFile)) {
    $apiCode = file_get_contents($apiFile);
} else {
    $fileError = true; // علم بوجود مشكلة في الملف
}

if ($apiCode) {
    preg_match_all(
        "/['\"]([\w\/\-]+)['\"]/",
        $apiCode,
        $matches
    );

    if (!empty($matches[1])) {
        $routes = array_unique($matches[1]);
        sort($routes); // ترتيب أبجدي
    }
}
?>

<div class="row">
    <div class="col-md-6">
        <form id="apiForm" class="bg-white p-4 rounded shadow-sm">
            <div class="mb-3">
                <label for="endpoint" class="form-label">المسار (Endpoint)</label>
                <input type="text" class="form-control" id="endpoint" name="endpoint" placeholder="مثلاً: register أو provider/login" required />
            </div>

            <div class="mb-3">
                <label for="jsonData" class="form-label">بيانات JSON للإرسال (يمكن تركها فارغة)</label>
                <textarea class="form-control" id="jsonData" name="jsonData" rows="8" placeholder='{"email":"test@example.com","password":"123456"}'></textarea>
            </div>

            <button type="submit" class="btn btn-primary">إرسال</button>
        </form>

        <h4 class="mt-4">رد السيرفر:</h4>
        <pre id="responseOutput" class="bg-white p-3 border rounded" style="min-height: 200px; white-space: pre-wrap;">لم يتم الإرسال بعد.</pre>
        <iframe id="responseIframe" style="width:100%; height:400px; border:1px solid #ccc; display:none;"></iframe>
    </div>

    <div class="col-md-6">
        <h4>قائمة المسارات المتاحة في api.php</h4>

        <!-- حقل البحث -->
        <input type="text" id="searchRoutes" class="form-control mb-3" placeholder="ابحث في المسارات..." />

        <?php if ($fileError): ?>
            <p class="text-danger">تعذر العثور على ملف <code>routes/api.php</code> أو أنه غير قابل للقراءة.</p>
        <?php elseif (count($routes) > 0): ?>
            <ul class="list-group" id="routesList">
                <?php foreach ($routes as $route): ?>
                    <li class="list-group-item route-item" style="cursor:pointer; user-select:none;">
                        <?= htmlspecialchars($route) ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>لم يتم العثور على مسارات في ملف api.php</p>
        <?php endif; ?>
    </div>
</div>

<script>
    const form = document.getElementById('apiForm');
    const output = document.getElementById('responseOutput');
    const iframe = document.getElementById('responseIframe');
    const baseUrl = 'https://sportz.azsystems.tech/api/';

    // أحداث الضغط على المسارات في القائمة لوضعها في حقل المسار
    function attachClickEvents() {
        document.querySelectorAll('.route-item').forEach(item => {
            item.addEventListener('click', () => {
                document.getElementById('endpoint').value = item.textContent.trim();
            });
        });
    }

    attachClickEvents();

    // فلترة القائمة حسب النص في البحث
    const searchInput = document.getElementById('searchRoutes');
    const routesList = document.getElementById('routesList');

    searchInput.addEventListener('input', () => {
        const filter = searchInput.value.toLowerCase();
        const items = routesList ? routesList.getElementsByTagName('li') : [];

        for (let i = 0; i < items.length; i++) {
            const item = items[i];
            const text = item.textContent.toLowerCase();

            if (text.includes(filter)) {
                item.style.display = "";
            } else {
                item.style.display = "none";
            }
        }
    });

    form.addEventListener('submit', async (e) => {
        e.preventDefault();

        const endpoint = form.endpoint.value.trim();
        const jsonData = form.jsonData.value.trim();

        if (!endpoint) {
            output.style.display = 'block';
            iframe.style.display = 'none';
            output.textContent = "يرجى تعبئة المسار (Endpoint).";
            return;
        }

        let parsedData = {};

        if (jsonData) {
            try {
                parsedData = JSON.parse(jsonData);
            } catch (err) {
                output.style.display = 'block';
                iframe.style.display = 'none';
                output.textContent = "خطأ في صيغة JSON: " + err.message;
                return;
            }
        }

        output.style.display = 'block';
        iframe.style.display = 'none';
        output.textContent = "جاري الإرسال...";

        try {
            const response = await fetch(baseUrl + endpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(parsedData)
            });

            const text = await response.text();

            // محاولة تحليل JSON
            try {
                const data = JSON.parse(text);
                // عرض JSON في <pre>
                output.style.display = 'block';
                iframe.style.display = 'none';
                output.textContent = JSON.stringify(data, null, 2);
            } catch {
                // إذا النص يحتوي تاغات html (html, body, div, span ... ) نعرضه في iframe
                const isHTML = /<\s*(html|body|div|span|!DOCTYPE|p|h1|h2|h3|table|script|style)[\s>]/i.test(text);

                if (isHTML) {
                    output.style.display = 'none';
                    iframe.style.display = 'block';

                    // تعيين محتوى iframe (باستخدام srcdoc يدعم معظم المتصفحات)
                    iframe.srcdoc = text;
                } else {
                    // غير html، نعرض كنص عادي في <pre>
                    output.style.display = 'block';
                    iframe.style.display = 'none';
                    output.textContent = text;
                }
            }

        } catch (err) {
            output.style.display = 'block';
            iframe.style.display = 'none';
            output.textContent = "خطأ في الاتصال: " + err.message;
        }
    });
</script>
