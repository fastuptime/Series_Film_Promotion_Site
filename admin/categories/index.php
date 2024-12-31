<?php include '../layout/header.php'; ?>

<div class="bg-white dark:bg-dashboard-card p-6 rounded-xl shadow-md">
    <div class="flex justify-between items-center mb-6">
        <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-100">Kategoriler</h3>
        <button onclick="openCreateModal()" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-md">
            Kategori Ekle
        </button>
    </div>

    <h3 class="text-xl font-semibold mb-6 text-gray-800 dark:text-gray-100">Kategoriler</h3>
    <div class="overflow-x-auto">
        <table class="w-full text-left" id="category">
            <thead>
                <tr class="border-b dark:border-gray-700">
                    <th class="pb-3 text-gray-500 dark:text-gray-400">ID</th>
                    <th class="pb-3 text-gray-500 dark:text-gray-400">Kategori Adı</th>
                    <th class="pb-3 text-gray-500 dark:text-gray-400">URL Dostu Adı</th>
                    <th class="pb-3 text-gray-500 dark:text-gray-400">İşlemler</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $query = $conn->query("SELECT * FROM category ORDER BY id DESC");
                    $categories = $query->fetchAll(PDO::FETCH_ASSOC);

                    foreach ($categories as $category) {
                ?>
                <tr class="border-b dark:border-gray-700">
                    <td class="py-3"><?php echo $category['id']; ?></td>
                    <td class="py-3"><?php echo $category['name']; ?></td>
                    <td class="py-3"><?php echo $category['slug']; ?></td>
                    <td class="py-3">
                        <button class="bg-red-500 text-white px-2 py-1 rounded-full text-xs" onclick="deleteCategory(<?php echo $category['id']; ?>)">Sil</button>
                        <button class="bg-blue-500 text-white px-2 py-1 rounded-full text-xs" onclick="editCategory(<?php echo $category['id']; ?>)">Düzenle</button>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <div id="createModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium">Yeni Kategori</h3>
                <form id="createForm" class="mt-2 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Kategori Adı</label>
                        <input type="text" id="create_name" name="name" required 
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                    </div>
                    <div class="mt-4">
                        <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded-md">Ekle</button>
                        <button type="button" onclick="closeCreateModal()" 
                            class="ml-2 bg-gray-500 text-white px-4 py-2 rounded-md">İptal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div id="editModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium">Kategori Düzenle</h3>
            <form id="editForm" class="mt-2 space-y-4">
                <input type="hidden" id="edit_id" name="id">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Kategori Adı</label>
                    <input type="text" id="edit_name" name="name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                </div>
                <div class="mt-4">
                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-md">Güncelle</button>
                    <button type="button" onclick="closeModal()" class="ml-2 bg-gray-500 text-white px-4 py-2 rounded-md">İptal</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/2.1.8/js/dataTables.js"></script>
<script src="https://cdn.datatables.net/2.1.8/js/dataTables.tailwindcss.js"></script>


<script>
    $(document).ready(function() {
        $('#category').DataTable();
    });

    function openCreateModal() {
    $('#createModal').removeClass('hidden');
}

    function closeCreateModal() {
        $('#createModal').addClass('hidden');
        $('#createForm')[0].reset();
    }

    $('#createForm').on('submit', function(e) {
        e.preventDefault();
        
        $.ajax({
            url: 'controllers/create.php',
            type: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                if(response.success) {
                    Swal.fire(
                        'Başarılı!',
                        'Kategori eklendi.',
                        'success'
                    ).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire(
                        'Hata!',
                        'Kategori eklenemedi.',
                        'error'
                    );
                }
            }
        });
    });

    function deleteCategory(id) {
        Swal.fire({
            title: 'Emin misiniz?',
            text: "Bu işlem geri alınamaz!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Evet, sil!',
            cancelButtonText: 'İptal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: 'controllers/delete.php',
                    type: 'POST',
                    data: { id: id },
                    success: function(response) {
                        if(response.success) {
                            Swal.fire(
                                'Silindi!',
                                'Kategori başarıyla silindi.',
                                'success'
                            ).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire(
                                'Hata!',
                                'Silme işlemi başarısız.',
                                'error'
                            );
                        }
                    }
                });
            }
        });
    }

    function editCategory(id) {
        // Kategori bilgilerini getir
        $.ajax({
            url: 'controllers/get_category.php',
            type: 'GET',
            data: { id: id },
            success: function(response) {
                if(response.success) {
                    $('#edit_id').val(response.category.id);
                    $('#edit_name').val(response.category.name);
                    $('#editModal').removeClass('hidden');
                }
            }
        });
    }

    function closeModal() {
        $('#editModal').addClass('hidden');
    }

    // Form submit
    $('#editForm').on('submit', function(e) {
        e.preventDefault();
        
        $.ajax({
            url: 'controllers/update.php',
            type: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                if(response.success) {
                    Swal.fire(
                        'Başarılı!',
                        'Kategori güncellendi.',
                        'success'
                    ).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire(
                        'Hata!',
                        'Güncelleme başarısız.',
                        'error'
                    );
                }
            }
        });
    });
</script>

<?php include '../layout/footer.php'; ?>