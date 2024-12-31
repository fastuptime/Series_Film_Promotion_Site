<?php include '../layout/header.php'; ?>

<div class="bg-white dark:bg-dashboard-card p-6 rounded-xl shadow-md">
<?php

    $categories_query = $conn->query("SELECT * FROM category ORDER BY name");
    $categories_result = $categories_query->fetchAll(PDO::FETCH_ASSOC);

    if (isset($_GET['id'])) {
        $id = intval($_GET['id']);
        $stmt = $conn->prepare("SELECT * FROM movie_and_series WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $movie = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$movie) {
            echo "<div class='text-red-500'>Film bulunamadı.</div>";
            exit;
        }
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        $name = trim($_POST['name'] ?? '');
        $original_name = trim($_POST['original_name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $duration = trim($_POST['duration'] ?? '');
        $trailer = trim($_POST['trailer'] ?? '');
        $release_date = trim($_POST['release_date'] ?? '');
        $rating = trim($_POST['rating'] ?? '');
        $category_id = trim($_POST['category_id'] ?? '');
        $type = trim($_POST['type'] ?? '');
        $status = trim($_POST['status'] ?? '');
        $country = trim($_POST['country'] ?? '');

        $posterPath = $movie['poster']; 
        $bannerPath = $movie['backdrop']; 

        if(isset($_FILES['poster']) && $_FILES['poster']['error'] == 0) {
            $posterExt = pathinfo($_FILES['poster']['name'], PATHINFO_EXTENSION);
            $posterFileName = '/posters/' . $id . '.' . $posterExt;

            if(move_uploaded_file($_FILES['poster']['tmp_name'], '../../posters/' . $id . '.' . $posterExt)) {
                $posterPath = $posterFileName;
            }
        }

        if(isset($_FILES['banner']) && $_FILES['banner']['error'] == 0) {
            $bannerExt = pathinfo($_FILES['banner']['name'], PATHINFO_EXTENSION);
            $bannerFileName = '/backdrop/' . $id . '.' . $bannerExt;

            if(move_uploaded_file($_FILES['banner']['tmp_name'], '../../backdrop/' . $id . '.' . $bannerExt)) {
                $bannerPath = $bannerFileName;
            }
        }

        $errors = [];
        if (empty($name)) $errors[] = "İsim gereklidir";
        if (empty($description)) $errors[] = "Açıklama gereklidir";
        if (!is_numeric($duration) || $duration <= 0) $errors[] = "Geçerli bir süre giriniz";
        if (empty($trailer)) $errors[] = "Fragman URL'si gereklidir";
        if (empty($release_date)) $errors[] = "Yayın tarihi gereklidir";
        if (!is_numeric($rating) || $rating < 0 || $rating > 10) $errors[] = "Geçerli bir puan giriniz (0-10)";
        if (empty($category_id)) $errors[] = "Kategori gereklidir";
        if (empty($country)) $errors[] = "Ülke gereklidir";
        if (!in_array($type, ['movie', 'series'])) $errors[] = "Geçersiz tür";
        if (!in_array($status, ['active', 'coming_soon', 'ended'])) $errors[] = "Geçersiz durum";

        if (empty($errors)) {
            try {
                $stmt = $conn->prepare("UPDATE movie_and_series SET 
                        name = :name,
                        original_name = :original_name,
                        description = :description,
                        country = :country,
                        duration = :duration,
                        trailer = :trailer,
                        release_date = :release_date,
                        rating = :rating,
                        category_id = :category_id,
                        type = :type,
                        status = :status,
                        poster = :poster,
                        backdrop = :backdrop
                        WHERE id = :id");

                $stmt->execute([
                    ':name' => $name,
                    ':original_name' => $original_name,
                    ':description' => $description,
                    ':country' => $country,
                    ':duration' => $duration,
                    ':trailer' => $trailer,
                    ':release_date' => $release_date,
                    ':rating' => $rating,
                    ':category_id' => $category_id,
                    ':type' => $type,
                    ':status' => $status,
                    ':poster' => $posterPath,
                    ':backdrop' => $bannerPath,
                    ':id' => $id
                ]);

                echo "<script>
                    Swal.fire({
                        title: 'Başarılı!',
                        text: 'İçerik başarıyla güncellendi',
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.href = 'index.php';
                    });
                </script>";
            } catch (Exception $e) {
                $errors[] = "Güncelleme hatası: " . $e->getMessage();
            }
        }

        if (!empty($errors)) {
            echo "<div class='bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4'><ul>";
            foreach ($errors as $error) {
                echo "<li>" . htmlspecialchars($error) . "</li>";
            }
            echo "</ul></div>";
        }
    }
?>

<!-- Form alanları -->
<form method="POST" class="space-y-4" enctype="multipart/form-data">
    <!-- Ad ve Orijinal Ad -->
    <div class="grid grid-cols-2 gap-4">
        <div>
            <label for="name">Adı</label>
            <input type="text" id="name" name="name" required 
                value="<?php echo htmlspecialchars($movie['name'] ?? ''); ?>"
                class="w-full p-2 border rounded">
        </div>
        <div>
            <label for="original_name">Orijinal Adı</label>
            <input type="text" id="original_name" name="original_name"
                value="<?php echo htmlspecialchars($movie['original_name'] ?? ''); ?>"
                class="w-full p-2 border rounded">
        </div>
    </div>

    <!-- Açıklama -->
    <div>
        <label for="description">Açıklama</label>
        <textarea id="description" name="description" required rows="4"
            class="w-full p-2 border rounded"><?php echo htmlspecialchars($movie['description'] ?? ''); ?></textarea>
    </div>

    <!-- Poster ve Banner -->
       <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-gray-700 dark:text-gray-200 text-sm font-semibold mb-2">Poster</label>
            <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 dark:border-gray-600 border-dashed rounded-lg hover:border-blue-500 transition-colors">
                <div class="space-y-1 text-center">
                    <div id="posterPreview" class="<?php echo isset($movie['poster']) ? '' : 'hidden'; ?> mb-3">
                        <img src="<?php echo isset($movie['poster']) ? $movie['poster'] : ''; ?>" 
                            alt="Poster önizleme" class="mx-auto h-32 w-auto rounded-lg shadow-md">
                    </div>
                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <div class="flex text-sm text-gray-600 dark:text-gray-400">
                        <label for="poster" class="relative cursor-pointer rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                            <span>Dosya Seçin</span>
                            <input id="poster" name="poster" type="file" accept="image/*" class="sr-only" onchange="previewImage(this, 'posterPreview')">
                        </label>
                        <p class="pl-1">veya sürükleyip bırakın</p>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">PNG, JPG, GIF max 2MB</p>
                </div>
            </div>
        </div>

        <div>
            <label class="block text-gray-700 dark:text-gray-200 text-sm font-semibold mb-2">Banner</label>
            <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 dark:border-gray-600 border-dashed rounded-lg hover:border-blue-500 transition-colors">
                <div class="space-y-1 text-center">
                    <div id="bannerPreview" class="<?php echo isset($movie['backdrop']) ? '' : 'hidden'; ?> mb-3">
                        <img src="<?php echo isset($movie['backdrop']) ? $movie['backdrop'] : ''; ?>" 
                            alt="Banner önizleme" class="mx-auto h-32 w-auto rounded-lg shadow-md">
                    </div>
                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <div class="flex text-sm text-gray-600 dark:text-gray-400">
                        <label for="banner" class="relative cursor-pointer rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                            <span>Dosya Seçin</span>
                            <input id="banner" name="banner" type="file" accept="image/*" class="sr-only" onchange="previewImage(this, 'bannerPreview')">
                        </label>
                        <p class="pl-1">veya sürükleyip bırakın</p>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">PNG, JPG, GIF max 10MB</p>
                </div>
            </div>
        </div>
    </div>
    <!-- Diğer Bilgiler -->
    <div class="grid grid-cols-3 gap-4">
        <div>
            <label for="duration">Süre (dakika)</label>
            <input type="number" id="duration" name="duration" required
                value="<?php echo htmlspecialchars($movie['duration'] ?? ''); ?>"
                class="w-full p-2 border rounded">
        </div>
        <div>
            <label for="trailer">Fragman URL</label>
            <input type="url" id="trailer" name="trailer" required
                value="<?php echo htmlspecialchars($movie['trailer'] ?? ''); ?>"
                class="w-full p-2 border rounded">
        </div>
        <div>
            <label for="rating">Puan</label>
            <input type="number" id="rating" name="rating" step="0.1" min="0" max="10" required
                value="<?php echo htmlspecialchars($movie['rating'] ?? ''); ?>"
                class="w-full p-2 border rounded">
        </div>
    </div>

    <div class="grid grid-cols-3 gap-4">
        <div>
            <label for="release_date">Yayın Tarihi</label>
            <input type="date" id="release_date" name="release_date" required
                value="<?php echo htmlspecialchars($movie['release_date'] ?? ''); ?>"
                class="w-full p-2 border rounded">
        </div>
        <div>
            <label for="country">Ülke</label>
            <input type="text" id="country" name="country" required
                value="<?php echo htmlspecialchars($movie['country'] ?? ''); ?>"
                class="w-full p-2 border rounded">
        </div>
        <div>
            <label for="category_id">Kategori</label>
            <select id="category_id" name="category_id" required class="w-full p-2 border rounded">
                <?php foreach($categories_result as $category): ?>
                    <option value="<?php echo $category['id']; ?>" 
                        <?php echo ($movie['category_id'] ?? '') == $category['id'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($category['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div>
            <label for="type">Tip</label>
            <select id="type" name="type" required class="w-full p-2 border rounded">
                <option value="movie" <?php echo ($movie['type'] ?? '') == 'movie' ? 'selected' : ''; ?>>Film</option>
                <option value="series" <?php echo ($movie['type'] ?? '') == 'series' ? 'selected' : ''; ?>>Dizi</option>
            </select>
        </div>
        <div>
            <label for="status">Durum</label>
            <select id="status" name="status" required class="w-full p-2 border rounded">
                <option value="active" <?php echo ($movie['status'] ?? '') == 'active' ? 'selected' : ''; ?>>Yayında</option>
                <option value="coming_soon" <?php echo ($movie['status'] ?? '') == 'coming_soon' ? 'selected' : ''; ?>>Yakında</option>
                <option value="ended" <?php echo ($movie['status'] ?? '') == 'ended' ? 'selected' : ''; ?>>Sonlandı</option>
            </select>
        </div>
    </div>

    <div class="mt-6">
        <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">Güncelle</button>
        <a href="index.php" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600 ml-2">İptal</a>
    </div>
</form>

<script>
function previewImage(input, previewId) {
    const preview = document.getElementById(previewId);
    const previewImg = preview.querySelector('img');

    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            previewImg.src = e.target.result;
            preview.classList.remove('hidden');
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
</div>

<script>

function previewImage(input, previewId) {
    const preview = document.getElementById(previewId);
    const previewImg = preview.querySelector('img');

    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            previewImg.src = e.target.result;
            preview.classList.remove('hidden');
        }
        reader.readAsDataURL(input.files[0]);
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const posterPreview = document.getElementById('posterPreview');
    const bannerPreview = document.getElementById('bannerPreview');

    if ('<?php echo $movie['poster'] ?? ''; ?>') {
        posterPreview.querySelector('img').src = '<?php echo $movie['poster']; ?>';
        posterPreview.classList.remove('hidden');
    }

    if ('<?php echo $movie['backdrop'] ?? ''; ?>') {
        bannerPreview.querySelector('img').src = '<?php echo $movie['backdrop']; ?>';
        bannerPreview.classList.remove('hidden');
    }
});
</script>

<?php include '../layout/footer.php'; ?>