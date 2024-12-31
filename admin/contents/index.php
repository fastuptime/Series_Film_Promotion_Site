<?php include '../layout/header.php'; ?>

<a href="add.php" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 transition mb-4 inline-block">İçerik Ekle</a>
<div class="bg-white dark:bg-dashboard-card p-6 rounded-xl shadow-md">
    <h3 class="text-xl font-semibold mb-6 text-gray-800 dark:text-gray-100">İçerikler</h3>
    <div class="overflow-x-auto">
        <?php
            $query = $conn->query("SELECT * FROM movie_and_series ORDER BY id DESC");
            $contents = $query->fetchAll(PDO::FETCH_ASSOC);
        ?>
        
        

        <table class="min-w-full table-auto" id="category">
            <thead class="bg-gray-800 text-white">
                <tr>
                    <th class="px-4 py-2">ID</th>
                    <th class="px-4 py-2">Poster</th>
                    <th class="px-4 py-2">İsim</th>
                    <th class="px-4 py-2">Süre</th>
                    <th class="px-4 py-2">Fragman</th>
                    <th class="px-4 py-2">Yayın Tarihi</th>
                    <th class="px-4 py-2">Puan</th>
                    <th class="px-4 py-2">Ülke</th>
                    <th class="px-4 py-2">Tür</th>
                    <th class="px-4 py-2">Durum</th>
                    <th class="px-4 py-2">Görüntülenme</th>
                    <th class="px-4 py-2">İşlemler</th>
                </tr>
            </thead>
            <tbody class="bg-white">
                <?php foreach ($contents as $content): ?>
                    <tr class="border-b hover:bg-gray-100">
                        <td class="px-4 py-2 text-center"><?php echo htmlspecialchars($content['id']); ?></td>
                        <td class="px-4 py-2 text-center">
                            <img src="<?php echo htmlspecialchars($content['poster']); ?>" alt="<?php echo htmlspecialchars($content['name']); ?>" class="w-16 h-24 object-cover mx-auto rounded">
                        </td>
                        <td class="px-4 py-2"  title="<?php echo htmlspecialchars($content['description']); ?>"><?php echo htmlspecialchars($content['name']); ?></td>
                        <td class="px-4 py-2 text-center"><?php echo htmlspecialchars($content['duration']); ?></td>
                        <td class="px-4 py-2 text-center">
                            <a href="<?php echo htmlspecialchars($content['trailer']); ?>" target="_blank" class="text-blue-500 hover:underline">Fragman</a>
                        </td>
                        <td class="px-4 py-2 text-center"><?php echo htmlspecialchars($content['release_date']); ?></td>
                        <td class="px-4 py-2 text-center"><?php echo htmlspecialchars($content['rating']); ?></td>
                        <td class="px-4 py-2"><?php echo htmlspecialchars($content['country']); ?></td>
                        <td class="px-4 py-2 text-center"><?php echo htmlspecialchars($content['type']); ?></td>
                        <td class="px-4 py-2 text-center">
                            <?php if($content['status'] == 'active'): ?>
                                <span class="text-teal-500 font-semibold border border-teal-500 px-2 py-1 rounded-full">Yayında</span>
                            <?php elseif($content['status'] == 'coming_soon'): ?>
                                <span class="text-yellow-500 font-semibold">Yakında</span>
                            <?php elseif($content['status'] == 'ended'): ?>
                                <span class="text-red-500 font-semibold">Bitmiş</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-2 text-center"><?php echo htmlspecialchars($content['views']); ?></td>
                        <td class="px-4 py-2 text-center">
                            <a class="bg-red-500 text-white px-3 py-1 rounded-full text-sm hover:bg-red-600 focus:outline-none" href="delete.php?id=<?php echo $content['id']; ?>">Sil</a>
                            <a class="bg-blue-500 text-white px-3 py-1 rounded-full text-sm hover:bg-blue-600 focus:outline-none" href="edit.php?id=<?php echo $content['id']; ?>">Düzenle</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/2.1.8/js/dataTables.js"></script>
<script src="https://cdn.datatables.net/2.1.8/js/dataTables.tailwindcss.js"></script>

<script>
    $(document).ready(function() {
        $('#category').DataTable();
    });
</script>

<?php include '../layout/footer.php'; ?>