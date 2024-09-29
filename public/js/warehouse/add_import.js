let materialData = [];

function addMaterial() {
    const supplier_code = document.getElementById('supplier_code').value;
    const note = document.getElementById('note').value;
    const equipment_code = document.getElementById('equipment_code').value;
    const receipt_date = document.getElementById('receipt_date').value;
    const created_by = document.getElementById('created_by').value;
    const receipt_no = document.getElementById('receipt_no').value;
    const batch_number = document.getElementById('batch_number').value;
    const product_date = document.getElementById('product_date').value;
    const expiry_date = document.getElementById('expiry_date').value;
    const price = parseFloat(document.getElementById('price').value);
    const quantity = parseInt(document.getElementById('quantity').value);
    const discount = parseFloat(document.getElementById('discount_rate').value);
    const VAT = parseFloat(document.getElementById('VAT').value);

    let errors = [];

    const today = new Date();
    const receiptDate = new Date(receipt_date);
    const productDate = new Date(product_date);
    const expiryDate = new Date(expiry_date);

    if (!supplier_code) {
        errors.push('Vui lòng chọn nhà cung cấp.');
    }
    if (!receipt_no) {
        errors.push('Vui lòng nhập số hóa đơn.');
    }
    if (!equipment_code) {
        errors.push('Vui lòng chọn tên vật tư.');
    }
    if (!batch_number) {
        errors.push('Vui lòng nhập số lô.');
    }
    if (!receipt_date || receiptDate > today) {
        errors.push('Ngày nhập phải nhỏ hơn hoặc bằng ngày hôm nay.');
    }
    if (!product_date || productDate > receiptDate) {
        errors.push('Ngày sản xuất phải nhỏ hơn hoặc bằng ngày nhập.');
    }
    if (!expiry_date || expiryDate <= productDate) {
        errors.push('Hạn sử dụng phải lớn hơn ngày sản xuất.');
    }
    if (!price || price <= 0) {
        errors.push('Vui lòng nhập giá nhập hợp lệ.');
    }
    if (!quantity || quantity <= 0) {
        errors.push('Vui lòng nhập số lượng hợp lệ.');
    }
    if (!discount || discount < 0 || discount > 100) {
        errors.push('Vui lòng nhập chiết khấu hợp lệ (0-100%).');
    }
    if (!VAT || VAT < 0 || VAT > 100) {
        errors.push('Vui lòng nhập VAT hợp lệ (0-100%).');
    }

    // Kiểm tra số lô trùng
    const isDuplicateBatch = materialData.some(material => material.batch_number === batch_number && material.equipment_code === equipment_code);
    if (isDuplicateBatch) {
        errors.push('Số lô này đã tồn tại cho vật tư ' + equipment_code);
    }

    if (errors.length > 0) {
        const errorMessages = document.getElementById('errorMessages');
        const errorList = document.getElementById('errorList');

        errorList.innerHTML = '';

        errors.forEach(error => {
            const li = document.createElement('li');
            li.textContent = error;
            errorList.appendChild(li);
        });

        errorMessages.style.display = 'block';

        return;
    }

    document.getElementById('errorMessages').style.display = 'none';

    const total_price = price * quantity * (1 - discount / 100) * (1 + VAT / 100);

    const material = {
        supplier_code,
        note,
        receipt_no,
        receipt_date,
        created_by,
        batch_number,
        expiry_date,
        quantity,
        VAT,
        discount,
        equipment_code,
        price,
        total_price: total_price
    };
    materialData.push(material);

    const tableBody = document.getElementById('materialList');
    if (tableBody) {
        const row = document.createElement('tr');
        const index = materialData.length - 1;

        row.innerHTML = `
            <td style="font-size: 11px;" class="text-center">${equipment_code}</td>
            <td style="font-size: 11px;" class="text-center">${supplier_code}</td>
            <td style="font-size: 11px;" class="text-center">${quantity}</td>
            <td style="font-size: 11px;" class="text-center">${price.toLocaleString('vi-VN', { style: 'currency', currency: 'VND' }).replace(',00', '')}</td>
            <td style="font-size: 11px;" class="text-center">${batch_number}</td>
            <td style="font-size: 11px;" class="text-center">${expiry_date}</td>
            <td style="font-size: 11px;" class="text-center">${discount}</td>
            <td style="font-size: 11px;" class="text-center">${VAT}</td>
            <td style="font-size: 11px;" class="text-center">${total_price.toLocaleString('vi-VN', { style: 'currency', currency: 'VND' }).replace(',00', '')}</td>
            <td class="text-center">
                <button onclick="removeMaterial(${index}, this)">
                    <i class="fas fa-trash-alt"></i>
                </button>
            </td>
        `;
        tableBody.appendChild(row);
    }

    // Kiểm tra và ẩn noDataAlert nếu có dữ liệu
    if (materialData.length > 0) {
        noDataAlert.style.display = 'none';
    } else {
        noDataAlert.style.display = 'table-row';
    }

    calculateTotals();
}

function removeMaterial(index, element) {
    materialData.splice(index, 1);
    const row = element.closest('tr');
    row.remove();

    if (materialData.length === 0) {
        document.getElementById('noDataAlert').style.display = 'table-row';
    }

    calculateTotals();
}

function calculateTotals() {
    let totalDiscount = 0;
    let totalVAT = 0;
    let totalAmount = 0;

    materialData.forEach(material => {
        const itemDiscount = material.price * material.quantity * material.discount / 100;
        const itemVAT = (material.price * material.quantity - itemDiscount) * material.VAT / 100;
        const itemTotal = parseFloat(material.total_price);

        totalDiscount += itemDiscount;
        totalVAT += itemVAT;
        totalAmount += itemTotal;
    });

    document.getElementById('totalDiscount').textContent = totalDiscount.toLocaleString('vi-VN', { style: 'currency', currency: 'VND' });
    document.getElementById('totalVAT').textContent = totalVAT.toLocaleString('vi-VN', { style: 'currency', currency: 'VND' });
    document.getElementById('totalAmount').textContent = totalAmount.toLocaleString('vi-VN', { style: 'currency', currency: 'VND' });
}


function submitMaterials() {
    document.getElementById('materialData').value = JSON.stringify(materialData);
}

document.getElementById('equipment_code').addEventListener('change', function() {
    const equipmentCode = this.value;

    if (equipmentCode) {
        // Gửi yêu cầu AJAX để lấy dữ liệu của vật tư
        fetch(`/system/warehouse/get_equipment/${equipmentCode}`)
            .then(response => response.json())
            .then(data => {
                // Điền dữ liệu vào các trường liên quan
                document.getElementById('price').value = data.price || '';
                document.getElementById('batch_number').value = data.batch_number || '';
                document.getElementById('product_date').value = data.product_date || '';
                document.getElementById('expiry_date').value = data.expiry_date || '';
            })
            .catch(error => {
                console.error('Error fetching material data:', error);
            });
    }
});
function formatCurrency(input) {
    let value = input.value.replace(/[^0-9]/g, '');

    if (value) {
        value = parseFloat(value).toLocaleString('vi-VN', { style: 'currency', currency: 'VND' });
    } else {
        value = '0 VNĐ';
    }

    input.value = value.replace('VNĐ', '').trim();
}


function filterProducts() {
    var input = document.getElementById('equipment_name'); // Lấy tên vật tư để tìm kiếm
    var filter = input.value.toUpperCase();
    var dropdown = document.getElementById('productDropdown');

    dropdown.style.display = filter ? 'block' : 'none';
    dropdown.innerHTML = ''; // Xóa nội dung trước đó

    var filteredProducts = products.filter(function(product) {
        return product.name.toUpperCase().indexOf(filter) > -1;
    });

    filteredProducts.forEach(function(product) {
        var item = `
            <a class="dropdown-item d-flex align-items-center" style="background-color: white !important; color: #000;" onclick="selectProduct('${product.code}', '${product.name}')">
                <img src="https://png.pngtree.com/template/20190316/ourlarge/pngtree-medical-health-logo-image_79595.jpg" alt="Product Image" class="me-2" style="width: 40px; height: 40px;">
                <div>
                    <div class="fw-bold">${product.name}</div>
                    <small>Mã vật tư: ${product.code}</small>
                </div>
            </a>
        `;
        dropdown.insertAdjacentHTML('beforeend', item);
    });
}

function selectProduct(productCode, productName) {
    document.getElementById('equipment_name').value = productName; // Hiển thị tên vật tư
    document.getElementById('equipment_code').value = productCode; // Lưu mã vật tư trong input ẩn
    document.getElementById('productDropdown').style.display = 'none';
}


