// Lấy tất cả các nút với class 'load_animation'
const loadAnimationButtons = document.querySelectorAll('.load_animation');

// Thêm sự kiện click cho từng nút
loadAnimationButtons.forEach(button => {
    button.addEventListener('click', function (event) {
        event.preventDefault();

        document.getElementById('loading').style.display = 'block';
        document.getElementById('loading-overlay').style.display = 'block';

        const form = this.closest('form'); // Lấy form cha của nút
        const submitButton = form.querySelector('button[type="submit"]');

        submitButton.disabled = true;

        setTimeout(() => {
            form.submit();
        }, 500);
    });
});

function printInvoice(code) {
    const printContents = document.getElementById(`printArea_${code}`).innerHTML;
    const originalContents = document.body.innerHTML;

    // Thay đổi nội dung trang thành nội dung cần in
    document.body.innerHTML = printContents;

    // Gọi lệnh in của trình duyệt
    window.print();

    // Khôi phục lại trạng thái của trang
    window.location.reload();
}

const notPropagation = document.querySelectorAll('.noPpg');

notPropagation.forEach(item => {
    item.addEventListener('click', function (e) {
        e.stopPropagation();
    });
});

const modalNotPropagation = document.querySelectorAll('.modal');

modalNotPropagation.forEach(item2 => {
    item2.addEventListener('click', function (e2) {
        e2.stopPropagation();
    });
});

const btn = document.querySelectorAll('.btn');

btn.forEach(itemBtn => {
    itemBtn.addEventListener('click', function (eBtn) {
        eBtn.stopPropagation();
    });
});

// Đổi biểu tượng khi bấm vào td có chứa caret
document.querySelectorAll('[data-bs-toggle="collapse"]').forEach(function (td) {
    td.addEventListener('click', function (event) {
        // Tìm phần tử <i> bên trong <td>
        var icon = this.querySelector('i');

        // Kiểm tra nếu có <i> thì thực hiện đổi biểu tượng
        if (icon) {
            // Đổi icon khi click
            if (icon.classList.contains('fa-caret-right')) {
                icon.classList.remove('fa-caret-right');
                icon.classList.add('fa-caret-down');
            } else {
                icon.classList.remove('fa-caret-down');
                icon.classList.add('fa-caret-right');
            }
        }

        // Ngăn chặn việc click ảnh hưởng đến hàng (row)
        event.stopPropagation();
    });
});

// Hàm kiểm tra và ẩn/hiện nút xóa tất cả
function toggleDeleteAction() {
    var anyChecked = false;
    document.querySelectorAll('.row-checkbox').forEach(function (checkbox) {
        if (checkbox.checked) {
            anyChecked = true;
        }
    });

    if (anyChecked) {
        document.getElementById('action_delete_all').classList.remove('d-none');
    } else {
        document.getElementById('action_delete_all').classList.add('d-none');
    }
}

// Khi click vào checkbox "Select All"
document.getElementById('selectAll').addEventListener('change', function () {
    var isChecked = this.checked;
    var checkboxes = document.querySelectorAll('.row-checkbox');
    checkboxes.forEach(function (checkbox) {
        checkbox.checked = isChecked;
        var row = checkbox.closest('tr');
        if (isChecked) {
            row.classList.add('selected-row');
        } else {
            row.classList.remove('selected-row');
        }
    });
    toggleDeleteAction();
});

// Khi checkbox của từng hàng thay đổi
document.querySelectorAll('.row-checkbox').forEach(function (checkbox) {
    checkbox.addEventListener('change', function () {
        var row = this.closest('tr');
        if (this.checked) {
            row.classList.add('selected-row');
        } else {
            row.classList.remove('selected-row');
        }

        var allChecked = true;
        document.querySelectorAll('.row-checkbox').forEach(function (cb) {
            if (!cb.checked) {
                allChecked = false;
            }
        });
        document.getElementById('selectAll').checked = allChecked;
        toggleDeleteAction(); // Gọi hàm kiểm tra nút xóa tất cả
    });
});

// Khi người dùng click vào hàng
document.querySelectorAll('tbody tr').forEach(function (row) {
    row.addEventListener('click', function () {
        var checkbox = this.querySelector('.row-checkbox');
        if (checkbox) {
            checkbox.checked = !checkbox.checked;
            if (checkbox.checked) {
                this.classList.add('selected-row');
            } else {
                this.classList.remove('selected-row');
            }

            var allChecked = true;
            document.querySelectorAll('.row-checkbox').forEach(function (cb) {
                if (!cb.checked) {
                    allChecked = false;
                }
            });
            document.getElementById('selectAll').checked = allChecked;
            toggleDeleteAction(); // Gọi hàm kiểm tra nút xóa tất cả
        }
    });
});

document.addEventListener('DOMContentLoaded', function () {
    // Duyệt tất cả
    if (document.querySelector('#browseAll')) {
        document.querySelector('#browseAll').addEventListener('show.bs.modal', function () {
            document.getElementById('action_type').value = 'browse';
        });
    }

    // Khi nhấn nút "Khôi Phục Tất Cả"
    if (document.querySelector('#restoreAll')) {
        document.querySelector('#restoreAll').addEventListener('show.bs.modal', function () {
            document.getElementById('action_type').value = 'restore';
        });
    }

    // Khi nhấn nút "Xóa Tất Cả"
    if (document.querySelector('#deleteAll')) {
        document.querySelector('#deleteAll').addEventListener('show.bs.modal', function () {
            document.getElementById('action_type').value = 'delete';
        });
    }
});

// Kiểm tra trạng thái ban đầu khi trang được tải
document.addEventListener('DOMContentLoaded', function () {
    toggleDeleteAction();
});
