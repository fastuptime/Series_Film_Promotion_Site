<?php include '../layout/header.php'; ?>

<div class="bg-white dark:bg-dashboard-card p-6 rounded-xl shadow-md">
<?php
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $name = trim($_POST['name'] ?? '');
        $original_name = trim($_POST['original_name'] ?? '');
        $description = trim($_POST['description'] ?? ''); 
        $country = trim($_POST['country'] ?? '');       
        $duration = trim($_POST['duration'] ?? '');
        $trailer = trim($_POST['trailer'] ?? '');
        $release_date = trim($_POST['release_date'] ?? '');
        $rating = trim($_POST['rating'] ?? '');
        $category_id = trim($_POST['category_id'] ?? '');
        $type = trim($_POST['type'] ?? '');
        $status = trim($_POST['status'] ?? '');

        $errors = [];

        if (empty($name)) $errors[] = "İsim gereklidir";
        if (empty($description)) $errors[] = "Açıklama gereklidir";
        if (empty($country)) $errors[] = "Ülke gereklidir";
        if (empty($duration)) $errors[] = "Geçersiz süre";
        if (empty($trailer)) $errors[] = "Geçersiz fragman URL'si";
        if (empty($release_date)) $errors[] = "Yayın tarihi gereklidir";
        if (empty($rating)) $errors[] = "Geçersiz puan";
        if (empty($category_id)) $errors[] = "Kategori gereklidir";
        if (!in_array($type, ['movie', 'series'])) $errors[] = "Geçersiz tür";
        if (!in_array($status, ['active', 'coming_soon', 'ended'])) $errors[] = "Geçersiz durum";


        if (!isset($_FILES['poster']) || $_FILES['poster']['error'] !== UPLOAD_ERR_OK || !isset($_FILES['banner']) || $_FILES['banner']['error'] !== UPLOAD_ERR_OK) {
            $errors[] = "Poster / Banner yüklemesi başarısız.";
        } else {
            $poster = $_FILES['poster'];
            $posterTmpPath = $poster['tmp_name'];
            $posterName = $poster['name'];
            $posterSize = $poster['size'];
            $posterType = $poster['type'];
            $posterExtension = strtolower(pathinfo($posterName, PATHINFO_EXTENSION));

            $banner = $_FILES['banner'];
            $bannerTmpPath = $banner['tmp_name'];
            $bannerName = $banner['name'];
            $bannerSize = $banner['size'];
            $bannerType = $banner['type'];
            $bannerExtension = strtolower(pathinfo($bannerName, PATHINFO_EXTENSION));

            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
            if (!in_array($posterExtension, $allowedExtensions)) {
                $errors[] = "Geçersiz poster dosya türü. Sadece JPG, JPEG, PNG ve GIF izinlidir.";
            }

            if (!in_array($bannerExtension, $allowedExtensions)) {
                $errors[] = "Geçersiz banner dosya türü. Sadece JPG, JPEG, PNG ve GIF izinlidir.";
            }

            if ($posterSize > 2 * 1024 * 1024) {
                $errors[] = "Poster dosya boyutu çok büyük (maksimum 2MB).";
            }

            if ($bannerSize > 10 * 1024 * 1024) {
                $errors[] = "Banner dosya boyutu çok büyük (maksimum 10MB).";
            }
        }

        if (empty($errors)) {
            try {
                $conn->beginTransaction();

                $posterID = uniqid();
                $posterPath = "../../posters/$posterID.$posterExtension";
                if (!move_uploaded_file($posterTmpPath, $posterPath)) {
                    throw new Exception("Poster dosyası yüklenemedi.");
                }

                $posterPath = "/posters/$posterID.$posterExtension";

                $bannerID = uniqid();
                $bannerPath = "../../backdrop/$bannerID.$bannerExtension";
                if (!move_uploaded_file($bannerTmpPath, $bannerPath)) {
                    throw new Exception("Banner dosyası yüklenemedi.");
                }

                $stmt = $conn->prepare("INSERT INTO movie_and_series (name, description, country, duration, trailer, release_date, rating, category_id, type, status, poster, backdrop) VALUES (:name, :description, :country, :duration, :trailer, :release_date, :rating, :category_id, :type, :status, :poster, :backdrop)");
                $stmt->execute([
                    ':name' => $name,
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
                    ':backdrop' => $bannerPath
                ]);

                $conn->commit();

                echo "<script>Swal.fire('Başarılı', 'İçerik başarıyla eklendi', 'success');</script>";
            } catch (Exception $e) {
                $conn->rollBack();
                $errors[] = "İçerik eklenirken bir hata oluştu: " . $e->getMessage();
            }
        }

        if (!empty($errors)) {
            echo "<div class='errors'><ul>";
            foreach ($errors as $error) {
                echo "<li>" . htmlspecialchars($error) . "</li>";
            }
            echo "</ul></div>";
        }
    }

    try {
        $stmt = $conn->prepare("SELECT id, name FROM category");
        $stmt->execute();
        $categories_result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Sorgu Hatası: " . $e->getMessage();
        exit;
    }
    
?>
<h1 class="text-2xl font-bold mb-6">İçerik Ekle</h1>
    
    <?php if (!empty($errors)): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <?php foreach ($errors as $error): ?>
                <p><?php echo htmlspecialchars($error); ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php if (isset($success_message)): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <?php echo htmlspecialchars($success_message); ?>
        </div>
    <?php endif; ?>

    <form method="POST" class="space-y-4" enctype="multipart/form-data">
        <div>
            <label class="block text-gray-700 text-sm font-bold mb-2" for="name">Adı</label>
            <input type="text" name="name" required 
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
        </div>

        <div>
            <label class="block text-gray-700 text-sm font-bold mb-2" for="original_name">Orjinal Adı</label>
            <input type="text" name="original_name" 
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
        </div>

        <div>
            <label class="block text-gray-700 text-sm font-bold mb-2" for="description">Açıklama</label>
            <textarea name="description" required 
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"></textarea>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2" for="duration">Süre (dakika)</label>
                <input type="number" name="duration" required 
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>

            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2" for="rating">Rating</label>
                <input type="number" step="0.1" name="rating" required 
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>
        </div>
        
        <div class="grid grid-cols-1 gap-4">
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2" for="trailer">Trailer URL</label>
                <input type="url" name="trailer" required 
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>
        </div>
        
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-gray-700 dark:text-gray-200 text-sm font-semibold mb-2">Poster</label>
                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 dark:border-gray-600 border-dashed rounded-lg hover:border-blue-500 transition-colors">
                    <div class="space-y-1 text-center">
                        <div id="posterPreview" class="hidden mb-3">
                            <img src="" alt="Poster önizleme" class="mx-auto h-32 w-auto rounded-lg shadow-md">
                        </div>
                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <div class="flex text-sm text-gray-600 dark:text-gray-400">
                            <label for="poster" class="relative cursor-pointer rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                <span>Dosya Seçin</span>
                                <input id="poster" name="poster" type="file" accept="image/*" class="sr-only" required onchange="previewImage(this, 'posterPreview')">
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
                        <div id="bannerPreview" class="hidden mb-3">
                            <img src="" alt="Banner önizleme" class="mx-auto h-32 w-auto rounded-lg shadow-md">
                        </div>
                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <div class="flex text-sm text-gray-600 dark:text-gray-400">
                            <label for="banner" class="relative cursor-pointer rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                <span>Dosya Seçin</span>
                                <input id="banner" name="banner" type="file" accept="image/*" class="sr-only" required onchange="previewImage(this, 'bannerPreview')">
                            </label>
                            <p class="pl-1">veya sürükleyip bırakın</p>
                        </div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">PNG, JPG, GIF max 10MB</p>
                    </div>
                </div>
            </div>
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
        </script>

        <div class="grid grid-cols-3 gap-4">
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2" for="release_date">Yayın Tarihi</label>
                <input type="date" name="release_date" required 
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>

            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2" for="country">Ülke</label>
                <input type="text" name="country" required 
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>

            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2" for="category_id">Kategori</label>
                <select name="category_id" required 
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    <?php foreach($categories_result as $category): ?>
                        <option value="<?php echo htmlspecialchars($category['id']); ?>">
                            <?php echo htmlspecialchars($category['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2" for="type">Tip</label>
                <select name="type" required 
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    <option value="movie">Film</option>
                    <option value="series">Seri</option>
                </select>
            </div>

            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2" for="status">Durum</label>
                <select name="status" required 
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    <option value="active">Yayında</option>
                    <option value="coming_soon">Yakında</option>
                    <option value="ended">Sonlandı</option>
                </select>
            </div>
        </div>

        <div>
            <button type="submit" 
                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                Ekleyin
            </button>
        </div>
    </form>
<?php include '../layout/footer.php'; ?>