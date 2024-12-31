<?php include '../layout/header.php'; ?>

<div class="bg-white dark:bg-dashboard-card p-6 rounded-xl shadow-md">
    <h3 class="text-xl font-semibold mb-6 text-gray-800 dark:text-gray-100">Yorumlar</h3>
    <div class="overflow-x-auto">
        <table class="w-full text-left" id="comments">
            <thead>
                <tr class="border-b dark:border-gray-700">
                    <th class="pb-3 text-gray-500 dark:text-gray-400">Kullanıcı</th>
                    <th class="pb-3 text-gray-500 dark:text-gray-400">Yorum</th>
                    <th class="pb-3 text-gray-500 dark:text-gray-400">Durum</th>
                    <th class="pb-3 text-gray-500 dark:text-gray-400">Tarih</th>
                    <th class="pb-3 text-gray-500 dark:text-gray-400">İşlemler</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $query = $conn->query("SELECT * FROM comment ORDER BY created_at DESC");
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
                    <td class="py-3"><?php echo date('d.m.Y H:i', strtotime($comment['created_at'])); ?></td>
                    <td class="py-3">
                        <button class="bg-red-500 text-white px-2 py-1 rounded-full text-xs" onclick="deleteComment(<?php echo $comment['id']; ?>)">Sil</button>
                        <?php if ($comment['status'] == 'pending') { ?>
                            <button class="bg-green-500 text-white px-2 py-1 rounded-full text-xs" onclick="approveComment(<?php echo $comment['id']; ?>)">Onayla</button>
                        <?php } ?>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/2.1.8/js/dataTables.js"></script>
<script src="https://cdn.datatables.net/2.1.8/js/dataTables.tailwindcss.js"></script>

<script>
    $(document).ready(function() {
        $('#comments').DataTable();
    });

    function deleteComment(id) {
        Swal.fire({
            title: 'Emin misiniz?',
            text: "Bu işlemi geri alamazsınız!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Evet, sil!',
            cancelButtonText: 'Vazgeç'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/admin/comments/controllers/delete.php',
                    type: 'POST',
                    data: {
                        id: id
                    },
                    success: function(response) {
                        Swal.fire(
                            'Silindi!',
                            'Yorum başarıyla silindi.',
                            'success'
                        ).then((result) => {
                            location.reload();
                        });
                    }
                });
            }
        });
    }

    function approveComment(id) {
        Swal.fire({
            title: 'Emin misiniz?',
            text: "Bu işlemi geri alamazsınız!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Evet, onayla!',
            cancelButtonText: 'Vazgeç'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/admin/comments/controllers/approve.php',
                    type: 'POST',
                    data: {
                        id: id
                    },
                    success: function(response) {
                        Swal.fire(
                            'Onaylandı!',
                            'Yorum başarıyla onaylandı.',
                            'success'
                        ).then((result) => {
                            location.reload();
                        });
                    }
                });
            }
        });
    }
</script>

<?php include '../layout/footer.php'; ?>