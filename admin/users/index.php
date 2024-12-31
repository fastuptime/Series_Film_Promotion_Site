<?php include '../layout/header.php'; ?>

<div class="bg-white dark:bg-dashboard-card p-6 rounded-xl shadow-md">
    <h3 class="text-xl font-semibold mb-6 text-gray-800 dark:text-gray-100">Kullanıcılar</h3>
    <div class="overflow-x-auto">
        <table class="w-full text-left" id="usersTable">
            <thead>
                <tr class="border-b dark:border-gray-700">
                    <th class="pb-3 text-gray-500 dark:text-gray-400">ID</th>
                    <th class="pb-3 text-gray-500 dark:text-gray-400">Kullanıcı Adı</th>
                    <th class="pb-3 text-gray-500 dark:text-gray-400">IP Adresi</th>
                    <th class="pb-3 text-gray-500 dark:text-gray-400">E-Posta</th>
                    <th class="pb-3 text-gray-500 dark:text-gray-400">Rol</th>
                    <th class="pb-3 text-gray-500 dark:text-gray-400">Durum</th>
                    <th class="pb-3 text-gray-500 dark:text-gray-400">Yorum Sayısı</th>
                    <th class="pb-3 text-gray-500 dark:text-gray-400">Son Giriş</th>
                    <th class="pb-3 text-gray-500 dark:text-gray-400">Kayıt Tarihi</th>
                    <th class="pb-3 text-gray-500 dark:text-gray-400">İşlemler</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $query = $conn->query("SELECT * FROM user ORDER BY created_at DESC LIMIT 10");
                    $users = $query->fetchAll(PDO::FETCH_ASSOC);

                    foreach ($users as $user) {
                        $commentCountQuery = $conn->prepare("SELECT COUNT(*) FROM comment WHERE user_id = :user_id");
                        $commentCountQuery->execute(['user_id' => $user['id']]);
                        $commentCount = $commentCountQuery->fetchColumn();
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
                    <td class="py-3"><?php echo $commentCount; ?></td>
                    <td class="py-3"><?php echo $user['last_login']; ?></td>
                    <td class="py-3"><?php echo $user['created_at']; ?></td>
                    <td class="py-3">
                        <button onclick="deleteUser(<?php echo $user['id']; ?>)" class="bg-red-500 text-white px-2 py-1 rounded-full text-xs">Sil</button>
                        <button onclick="toggleUserStatus(<?php echo $user['id']; ?>)" class="bg-blue-500 text-white px-2 py-1 rounded-full text-xs"><?php echo $user['is_active'] ? 'Pasif Et' : 'Aktif Et'; ?></button>
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
        $('#usersTable').DataTable();
    });

    function deleteUser(userId) {
        Swal.fire({
            title: 'Emin misiniz?',
            text: "Bu işlem geri alınamaz!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Evet, sil!',
            cancelButtonText: 'Vazgeç'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/admin/users/controllers/delete_user.php',
                    type: 'POST',
                    data: { id: userId },
                    success: function(response) {
                        const res = JSON.parse(response);
                        if(res.success){
                            Swal.fire(
                                'Silindi!',
                                'Kullanıcı başarıyla silindi.',
                                'success'
                            ).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire(
                                'Hata!',
                                'Kullanıcı silinirken bir hata oluştu.',
                                'error'
                            );
                        }
                    }
                });
            }
        });
    }

    function toggleUserStatus(userId) {
        Swal.fire({
            title: 'Emin misiniz?',
            text: "Bu işlem geri alınamaz!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Evet, değiştir!',
            cancelButtonText: 'Vazgeç'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/admin/users/controllers/toggle_user_status.php',
                    type: 'POST',
                    data: { id: userId },
                    success: function(response) {
                        const res = JSON.parse(response);
                        if(res.success){
                            Swal.fire(
                                'Değiştirildi!',
                                'Kullanıcı durumu başarıyla değiştirildi.',
                                'success'
                            ).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire(
                                'Hata!',
                                'Kullanıcı durumu değiştirirken bir hata oluştu.',
                                'error'
                            );
                        }
                    }
                });
            }
        });
    }
</script>

<?php include '../layout/footer.php'; ?>