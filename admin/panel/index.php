<?php include '../layout/header.php'; ?>
<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <div class="bg-white dark:bg-dashboard-card p-6 rounded-xl shadow-md hover:shadow-lg transition">
        <div class="flex justify-between items-center">
            <div>
                <h3 class="text-gray-500 dark:text-gray-400 text-sm">Toplam İçerik</h3>
                <p class="text-2xl font-bold text-gray-800 dark:text-white">
                    <?php 
                        $query = $conn->query("SELECT COUNT(*) as total FROM movie_and_series");
                        $total = $query->fetch(PDO::FETCH_ASSOC);
                        echo $total['total'];
                    ?>
                </p>
            </div>
            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z" />
            </svg>
        </div>
    </div>

    <div class="bg-white dark:bg-dashboard-card p-6 rounded-xl shadow-md hover:shadow-lg transition">
        <div class="flex justify-between items-center">
            <div>
                <h3 class="text-gray-500 dark:text-gray-400 text-sm">Aktif Kullanıcı</h3>
                <p class="text-2xl font-bold text-gray-800 dark:text-white">
                    <?php 
                        $query = $conn->query("SELECT COUNT(*) as total FROM user WHERE is_active = 1");
                        $total = $query->fetch(PDO::FETCH_ASSOC);
                        echo $total['total'];
                    ?>
            </div>
            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
        </div>
    </div>

    <div class="bg-white dark:bg-dashboard-card p-6 rounded-xl shadow-md hover:shadow-lg transition">
        <div class="flex justify-between items-center">
            <div>
                <h3 class="text-gray-500 dark:text-gray-400 text-sm">Son 1 Hafta Yorum</h3>
                <p class="text-2xl font-bold text-gray-800 dark:text-white">
                    <?php 
                        $query = $conn->query("SELECT COUNT(*) as total FROM comment WHERE created_at >= DATE_SUB(NOW(), INTERVAL 1 WEEK)");
                        $total = $query->fetch(PDO::FETCH_ASSOC);
                        echo $total['total'];
                    ?>
                </p>
            </div>
            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-purple-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
            </svg>
        </div>
    </div>
</div>

<div class="bg-white dark:bg-dashboard-card p-6 rounded-xl shadow-md">
    <h3 class="text-xl font-semibold mb-6 text-gray-800 dark:text-gray-100">Son Yorumlar</h3>
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="border-b dark:border-gray-700">
                    <th class="pb-3 text-gray-500 dark:text-gray-400">Kullanıcı</th>
                    <th class="pb-3 text-gray-500 dark:text-gray-400">Yorum</th>
                    <th class="pb-3 text-gray-500 dark:text-gray-400">Durum</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $query = $conn->query("SELECT * FROM comment ORDER BY created_at DESC LIMIT 10");
                    $comments = $query->fetchAll(PDO::FETCH_ASSOC);

                    foreach ($comments as $comment) {
                        $query = $conn->query("SELECT * FROM movie_and_series WHERE id = {$comment['movie_and_series_id']}");
                        $movie = $query->fetch(PDO::FETCH_ASSOC);

                        $query = $conn->query("SELECT * FROM user WHERE id = {$comment['user_id']}");
                        $user = $query->fetch(PDO::FETCH_ASSOC);
                ?>
                <tr class="border-b dark:border-gray-700">
                    <td class="py-3"><?php echo $user['username']; ?></td>
                    <td class="py-3"><?php echo $comment['content']; ?></td>
                    <td class="py-3">
                        <?php if ($comment['status'] == 'active') { ?>
                            <span class="bg-green-500 text-white px-2 py-1 rounded-full text-xs">Aktif</span>
                        <?php } else if ($comment['status'] == 'pending') { ?>
                            <span class="bg-yellow-500 text-white px-2 py-1 rounded-full text-xs">Beklemede</span>
                        <?php } else { ?>
                            <span class="bg-red-500 text-white px-2 py-1 rounded-full text-xs">Silinmiş</span>
                        <?php } ?>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<div class="bg-white dark:bg-dashboard-card p-6 rounded-xl shadow-md">
    <h3 class="text-xl font-semibold mb-6 text-gray-800 dark:text-gray-100">Son Kullanıcılar</h3>
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="border-b dark:border-gray-700">
                    <th class="pb-3 text-gray-500 dark:text-gray-400">ID</th>
                    <th class="pb-3 text-gray-500 dark:text-gray-400">Kullanıcı Adı</th>
                    <th class="pb-3 text-gray-500 dark:text-gray-400">Ip Adresi</th>
                    <th class="pb-3 text-gray-500 dark:text-gray-400">E-Posta</th>
                    <th class="pb-3 text-gray-500 dark:text-gray-400">Rol</th>
                    <th class="pb-3 text-gray-500 dark:text-gray-400">Durum</th>
                    <th class="pb-3 text-gray-500 dark:text-gray-400">Son Giriş</th>
                    <th class="pb-3 text-gray-500 dark:text-gray-400">Kayıt Tarihi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $query = $conn->query("SELECT * FROM user ORDER BY created_at DESC LIMIT 10");
                    $users = $query->fetchAll(PDO::FETCH_ASSOC);

                    foreach ($users as $user) {
                ?>
                <tr class="border-b dark:border-gray-700">
                    <td class="py-3"><?php echo $user['id']; ?></td>
                    <td class="py-3"><?php echo $user['username']; ?></td>
                    <td class="py-3"><?php echo $user['ip_address']; ?></td>
                    <td class="py-3"><?php echo $user['email']; ?></td>
                    <td class="py-3">
                        <?php if ($user['role'] == 'admin') { ?>
                            <span class="bg-red-500 text-white px-2 py-1 rounded-full text-xs">Admin</span>
                        <?php } else if ($user['role'] == 'moderator') { ?>
                            <span class="bg-yellow-500 text-white px-2 py-1 rounded-full text-xs">Moderatör</span>
                        <?php } else { ?>
                            <span class="bg-green-500 text-white px-2 py-1 rounded-full text-xs">Kullanıcı</span>
                        <?php } ?>
                    </td>
                    <td class="py-3">
                        <?php if ($user['is_active']) { ?>
                            <span class="bg-green-500 text-white px-2 py-1 rounded-full text-xs">Aktif</span>
                        <?php } else { ?>
                            <span class="bg-red-500 text-white px-2 py-1 rounded-full text-xs">Pasif</span>
                        <?php } ?>
                    </td>
                    <td class="py-3"><?php echo $user['last_login']; ?></td>
                    <td class="py-3"><?php echo $user['created_at']; ?></td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<div class="bg-white dark:bg-dashboard-card p-6 rounded-xl shadow-md">
    <h3 class="text-xl font-semibold mb-6 text-gray-800 dark:text-gray-100">Son Eklenen Film/Seriler</h3>
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="border-b dark:border-gray-700">
                    <th class="pb-3 text-gray-500 dark:text-gray-400">ID</th>
                    <th class="pb-3 text-gray-500 dark:text-gray-400">Adı</th>
                    <th class="pb-3 text-gray-500 dark:text-gray-400">Orijinal Adı</th>
                    <th class="pb-3 text-gray-500 dark:text-gray-400">Kategori</th>
                    <th class="pb-3 text-gray-500 dark:text-gray-400">Tür</th>
                    <th class="pb-3 text-gray-500 dark:text-gray-400">Durum</th>
                    <th class="pb-3 text-gray-500 dark:text-gray-400">Oluşturulma Tarihi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $query = $conn->query("SELECT * FROM movie_and_series ORDER BY created_at DESC LIMIT 10");
                    $movies = $query->fetchAll(PDO::FETCH_ASSOC);

                    foreach ($movies as $movie) {
                        $query = $conn->query("SELECT * FROM category WHERE id = {$movie['category_id']}");
                        $category = $query->fetch(PDO::FETCH_ASSOC);
                ?>
                <tr class="border-b dark:border-gray-700">
                    <td class="py-3"><?php echo $movie['id']; ?></td>
                    <td class="py-3"><?php echo $movie['name']; ?></td>
                    <td class="py-3"><?php echo $movie['original_name']; ?></td>
                    <td class="py-3"><?php echo $category['name']; ?></td>
                    <td class="py-3">
                        <?php if ($movie['type'] == 'movie') { ?>
                            <span class="bg-red-500 text-white px-2 py-1 rounded-full text-xs">Film</span>
                        <?php } else { ?>
                            <span class="bg-blue-500 text-white px-2 py-1 rounded-full text-xs">Dizi</span>
                        <?php } ?>
                    </td>
                    <td class="py-3">
                        <?php if ($movie['status'] == 'active') { ?>
                            <span class="bg-green-500 text-white px-2 py-1 rounded-full text-xs">Aktif</span>
                        <?php } else if ($movie['status'] == 'coming_soon') { ?>
                            <span class="bg-yellow-500 text-white px-2 py-1 rounded-full text-xs">Yakında</span>
                        <?php } else { ?>
                            <span class="bg-red-500 text-white px-2 py-1 rounded-full text-xs">Bitmiş</span>
                        <?php } ?>
                    </td>
                    <td class="py-3"><?php echo $movie['created_at']; ?></td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../layout/footer.php'; ?>