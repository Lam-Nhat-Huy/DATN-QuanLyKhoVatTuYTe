$(document).ready(function () {
    // Sự kiện khi thay đổi chọn vật tư
    $('#material_code').on('change', function () {
        const equipmentCode = $(this).val();
        const batchInfoContainer = $('#batch_info');
        batchInfoContainer.html(''); // Xóa thông tin lô cũ
        if (equipmentCode) {
            $.ajax({
                url: postExportUrl, // Gửi yêu cầu để lấy thông tin lô
                method: 'POST',
                data: {
                    _token: csrfToken,
                    equipment_code: equipmentCode
                },
                success: function (response) {
                    let tableContent = `
                <table class="table table-hover align-middle text-center" id="batch-table">
                    <thead class="table-dark">
                        <tr>
                            <th class="text-center">Số lô</th>  
                            <th class="text-center">Tồn kho</th>
                            <th class="text-center">Hạn dùng</th>
                        </tr>
                    </thead>
                    <tbody>
                `;

                    if (response.length > 0) {
                        response.forEach(inventory => {
                            const currentDate = new Date();
                            let displayExpiryDate = 'Không có'; // Mặc định là 'Không có'
                            let monthsDifference = null; // Khởi tạo biến

                            if (inventory.expiry_date) { // Nếu có hạn sử dụng
                                const expiryDate = new Date(inventory.expiry_date);
                                monthsDifference = (expiryDate - currentDate) / (1000 * 60 * 60 * 24 * 30); // Tính số tháng còn lại

                                displayExpiryDate = monthsDifference > 5 ? formatDate(inventory.expiry_date) : 'Hết hạn';
                            }

                            tableContent += `
                            <tr class="batch-row ${monthsDifference !== null && monthsDifference <= 5 ? 'expired' : ''}" style="cursor:pointer;" data-batch-number="${inventory.batch_number}" data-current-quantity="${inventory.current_quantity}">
                                <td class="text-center">${inventory.batch_number}</td>
                                <td class="text-center">${inventory.current_quantity}</td>
                                <td class="text-center">${displayExpiryDate}</td>
                            </tr>
                            `;
                        });
                    } else {
                        tableContent += `
                    <tr id="noDataAlert">
                        <td colspan="4" class="text-center">
                            <div class="alert alert-secondary d-flex flex-column align-items-center justify-content-center p-4"
                                role="alert"
                                style="border: 2px dashed #6c757d; background-color: #f8f9fa; color: #495057;">
                                <div class="mb-3">
                                    <i class="fas fa-file-invoice"
                                        style="font-size: 36px; color: #6c757d;"></i>
                                </div>
                                <div class="text-center">
                                    <h5 style="font-size: 16px; font-weight: 600; color: #495057;">Thông tin tồn kho trống</h5>
                                    <p style="font-size: 14px; color: #6c757d; margin: 0;">
                                        Hiện tại chưa có vật tư nào được thêm vào. Vui lòng kiểm tra lại hoặc tạo mới vật tư để bắt đầu.
                                    </p>
                                </div>
                            </div>
                        </td>
                    </tr>
                    `;
                    }

                    tableContent += `</tbody></table>`;
                    batchInfoContainer.html(tableContent);

                    // Bỏ sự kiện click cho hàng đã hết hạn
                    $('.batch-row').on('click', function () {
                        if ($(this).hasClass('expired')) {
                            return; // Không làm gì nếu hàng đã hết hạn
                        }

                        const batchNumber = $(this).data('batch-number');
                        const currentQuantity = $(this).data(
                            'current-quantity');

                        // Xóa class bg-success khỏi tất cả các hàng
                        $('.batch-row').removeClass('bg-success');

                        // Thêm class bg-success vào hàng được chọn
                        $(this).addClass('bg-success');

                        // Mở modal nhập số lượng
                        $('#batchNumberModal').val(
                            batchNumber); // Lưu số lô vào modal
                        $('#currentQuantityModal').val(
                            currentQuantity); // Lưu số lượng tồn vào modal
                        $('#inputQuantity').val(
                            ''); // Reset input trước khi mở modal
                        $('#quantityError').text(''); // Reset lỗi
                        $('#quantityModal').modal('show');
                    });


                    // Sự kiện lưu số lượng sau khi nhập trong modal
                    $('#saveQuantity').on('click', function () {
                        const inputQuantity = parseInt($('#inputQuantity')
                            .val());
                        const batchNumber = $('#batchNumberModal').val();
                        const currentQuantity = parseInt($(
                            '#currentQuantityModal').val());

                        // Kiểm tra tính hợp lệ của số lượng nhập vào
                        if (!inputQuantity || isNaN(inputQuantity) ||
                            inputQuantity <= 0 || inputQuantity >
                            currentQuantity) {
                            $('#quantityError').text(
                                'Số lượng nhập không hợp lệ hoặc lớn hơn số lượng tồn.'
                            );
                            $('#inputQuantity').addClass('is-invalid');
                            return;
                        } else {
                            $('#inputQuantity').removeClass('is-invalid');
                        }

                        const materialListBody = $('#material-list-body');
                        const existingRow = materialListBody.find(
                            `tr[data-batch-number="${batchNumber}"]`);

                        if (existingRow.length > 0) {
                            // Nếu số lô đã tồn tại, cộng dồn số lượng
                            const currentMaterialQuantity = parseInt(existingRow
                                .find('td:nth-child(3)').text());
                            const newMaterialQuantity =
                                currentMaterialQuantity + inputQuantity;
                            existingRow.find('td:nth-child(3)').text(
                                newMaterialQuantity);

                            // Cập nhật lại số lượng tồn kho
                            const remainingQuantity = currentQuantity -
                                inputQuantity;
                            $(`#batch-table tr[data-batch-number="${batchNumber}"] td:nth-child(2)`)
                                .text(remainingQuantity);
                            $(`#batch-table tr[data-batch-number="${batchNumber}"]`)
                                .data('current-quantity', remainingQuantity);
                        } else {
                            // Nếu số lô chưa tồn tại, thêm dòng mới vào bảng
                            const newRow = `
                                <tr data-batch-number="${batchNumber}">
                                    <td>${$('#material_code option:selected').text()}</td>
                                    <td>${batchNumber}</td>
                                    <td>${inputQuantity}</td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-dark edit-material" style="font-size:10px"><i class="fa fa-edit"></i></button>
                                        <button type="button" class="btn btn-danger btn-sm remove-material" style="font-size:10px"><i class="fa fa-trash"></i></button>
                                    </td>
                                </tr>`;
                            materialListBody.append(newRow);
                            $('#no-material-alert')
                                .remove(); // Xóa alert khi không có vật tư

                            // Cập nhật số lượng tồn kho
                            const remainingQuantity = currentQuantity -
                                inputQuantity;
                            $(`#batch-table tr[data-batch-number="${batchNumber}"] td:nth-child(2)`)
                                .text(remainingQuantity);
                            $(`#batch-table tr[data-batch-number="${batchNumber}"]`)
                                .data('current-quantity', remainingQuantity);
                        }

                        // Đóng modal sau khi lưu
                        $('#quantityModal').modal('hide');
                    });
                },
                error: function (xhr, status, error) {
                    console.error(error);
                    batchInfoContainer.html(
                        '<div class="alert alert-danger text-center">Đã xảy ra lỗi khi lấy dữ liệu.</div>'
                    );
                }
            });
        } else {
            batchInfoContainer.html('<div class="alert alert-danger">Bạn chưa chọn vật tư</div>');
        }
    });

    // Sự kiện xác nhận xóa vật tư trong danh sách
    $(document).on('click', '.remove-material', function () {
        const rowToDelete = $(this).closest('tr');
        const batchNumber = rowToDelete.data('batch-number');
        const materialQuantity = parseInt(rowToDelete.find('td:nth-child(3)').text());

        // Hiển thị modal xác nhận xóa
        $('#confirmDelete').data('row-to-delete', rowToDelete).data('batch-number', batchNumber)
            .data('material-quantity', materialQuantity);
        $('#confirmDeleteModal').modal('show');
    });

    $('#confirmDelete').on('click', function () {
        const rowToDelete = $(this).data('row-to-delete');
        const batchNumber = $(this).data('batch-number');
        const materialQuantity = $(this).data('material-quantity');

        // Xóa dòng khỏi bảng vật tư
        rowToDelete.remove();

        // Cập nhật lại số lượng tồn kho bên trên
        const rowInBatchTable = $(`#batch-table tr[data-batch-number="${batchNumber}"]`);
        const currentBatchQuantity = parseInt(rowInBatchTable.data('current-quantity'));
        const updatedBatchQuantity = currentBatchQuantity + materialQuantity;
        rowInBatchTable.find('td:nth-child(2)').text(updatedBatchQuantity);
        rowInBatchTable.data('current-quantity', updatedBatchQuantity);

        // Kiểm tra nếu không còn vật tư nào trong bảng
        if ($('#material-list-body').children().length === 0) {
            $('#material-list-body').append(`
                <tr id="no-material-alert">
                    <td colspan="4" class="text-center pe-0 px-0" style="box-shadow: none !important;">
                        <div class="alert alert-warning mb-0" role="alert">
                            Chưa có vật tư nào được thêm vào danh sách.
                        </div>
                    </td>
                </tr>
            `);
        }

        // Đóng modal xác nhận xóa
        $('#confirmDeleteModal').modal('hide');
    });

    // Sự kiện khi chọn "Sửa" trong danh sách vật tư
    $(document).on('click', '.edit-material', function () {
        const row = $(this).closest('tr');
        const batchNumber = row.data('batch-number');
        const oldQuantity = row.find('td:nth-child(3)').text(); // Lấy số lượng đã nhập từ cột 3

        // Đặt giá trị vào input của modal và lưu dữ liệu vào modal
        $('#editInputQuantity').val(oldQuantity);
        $('#editMaterialModal').data('batch-number', batchNumber); // Lưu số lô vào modal

        // Hiển thị modal để người dùng sửa
        $('#editMaterialModal').modal('show');
    });

    // Sự kiện khi nhấn "Lưu" sau khi chỉnh sửa số lượng
    $('#saveEditMaterial').on('click', function () {
        const batchNumber = $('#editMaterialModal').data('batch-number'); // Lấy số lô từ modal
        const newQuantity = $('#editInputQuantity').val(); // Lấy số lượng mới từ input

        // Lấy số lượng tồn kho hiện tại trong bảng batch-table
        const rowInBatchTable = $(`#batch-table tr[data-batch-number="${batchNumber}"]`);
        const currentBatchQuantity = parseInt(rowInBatchTable.data('current-quantity'));

        // Lấy số lượng vật tư đã nhập trước đó (số lượng cũ)
        const oldQuantity = parseInt($('#material-list-body').find(
            `tr[data-batch-number="${batchNumber}"] td:nth-child(3)`).text());

        const availableQuantity = currentBatchQuantity + oldQuantity;

        if (!newQuantity || isNaN(newQuantity) || parseInt(newQuantity) <= 0 || parseInt(
            newQuantity) > availableQuantity) {
            $('#editQuantityError').text('Số lượng không hợp lệ hoặc lớn hơn số lượng tồn kho.');
            $('#editInputQuantity').addClass('is-invalid');
            return;
        } else {
            $('#editInputQuantity').removeClass('is-invalid');
            $('#editQuantityError').text('');
        }

        // Cập nhật hàng trong bảng vật tư
        const rowToUpdate = $('#material-list-body').find(`tr[data-batch-number="${batchNumber}"]`);
        rowToUpdate.find('td:nth-child(3)').text(newQuantity); // Cập nhật số lượng mới vào cột 3

        // Tính lại số lượng tồn kho sau khi sửa
        const remainingQuantity = availableQuantity - parseInt(newQuantity);
        rowInBatchTable.find('td:nth-child(2)').text(
            remainingQuantity); // Cập nhật số lượng tồn kho mới
        rowInBatchTable.data('current-quantity', remainingQuantity); // Cập nhật thuộc tính data

        // Đóng modal sau khi cập nhật thành công
        $('#editMaterialModal').modal('hide');
    });

    $('#inputQuantity').on('keypress', function (event) {
        if (event.which === 13) { // 13 là mã phím cho Enter
            event.preventDefault(); // Ngăn chặn hành vi mặc định
            $('#saveQuantity').click(); // Gọi sự kiện click của nút lưu
        }
    });

    // Sự kiện để kiểm tra input cho số lượng nhập
    $('#inputQuantity').on('input', function () {
        const inputQuantity = $(this).val();
        if (inputQuantity && !isNaN(inputQuantity) && parseInt(inputQuantity) > 0) {
            $('#inputQuantity').removeClass('is-invalid');
            $('#quantityError').text('');
        } else {
            $('#inputQuantity').addClass('is-invalid');
        }
    });

    // Format ngày theo dd/mm/yyyy
    // Format ngày theo dd/mm/yyyy
    function formatDate(dateString) {
        const date = new Date(dateString);
        const day = ('0' + date.getDate()).slice(-2); // Thêm số 0 nếu ngày nhỏ hơn 10
        const month = ('0' + (date.getMonth() + 1)).slice(-2); // Tháng bắt đầu từ 0, nên cần cộng 1
        const year = date.getFullYear();
        return `${day}/${month}/${year}`; // Trả về định dạng dd/mm/yyyy
    }

});
$(document).ready(function () {
    // Hàm để cập nhật dữ liệu từ bảng vào input ẩn
    function updateMaterialListInput() {
        const materialList = [];

        // Lấy equipment_code từ select
        const equipmentCode = $('#material_code').val();

        $('#material-list-body tr').each(function () {
            const batchNumber = $(this).data('batch-number'); // Lấy số lô từ thuộc tính data
            const quantity = $(this).find('td:nth-child(3)').text(); // Lấy số lượng từ cột thứ 3

            if (batchNumber && quantity) { // Kiểm tra nếu có dữ liệu
                materialList.push({
                    equipment_code: equipmentCode, // Thêm equipment_code vào danh sách
                    batch_number: batchNumber,
                    quantity: quantity
                });
            }
        });

        // Lưu mảng dữ liệu vật tư dưới dạng JSON vào input ẩn
        $('#material_list_input').val(JSON.stringify(materialList));
    }

    // Khi form submit, gọi hàm để lưu dữ liệu vào input ẩn
    $('#warehouse-export-form').on('submit', function () {
        updateMaterialListInput();
    });
});
