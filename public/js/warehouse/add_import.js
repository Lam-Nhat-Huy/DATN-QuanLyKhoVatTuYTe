let equipmentData = [];

// Kiểm tra trùng số lô trước khi thêm vào danh sách thiết bị
// async function checkBatchNumber(batch_number, equipment_code) {
//     try {
//         const response = await fetch(
//             `/system/warehouse/check-batch-number/${batch_number}/${equipment_code}`
//         );
//         const data = await response.json();
//         return data.exists;
//     } catch (error) {
//         console.error("Error checking batch number:", error);
//         return false;
//     }
// }

// Thêm thiết bị vào danh sách
async function addEquipment() {
    const supplier_code = document.getElementById("supplier_code").value;
    const note = document.getElementById("note").value;
    const equipment_code = document.getElementById("equipment_code").value;
    const receipt_date = document.getElementById("receipt_date").value;
    const created_by = document.getElementById("created_by").value;
    const receipt_no = document.getElementById("receipt_no").value;
    const batch_number = document.getElementById("batch_number").value;
    const product_date = document.getElementById("product_date").value;
    const expiry_date = document.getElementById("expiry_date").value;
    const price = parseFloat(document.getElementById("price").value);
    const quantity = parseInt(document.getElementById("quantity").value);
    const discount = parseFloat(document.getElementById("discount_rate").value);
    const VAT = parseFloat(document.getElementById("VAT").value);

    let errors = [];

    const today = new Date();
    const receiptDate = new Date(receipt_date);
    const productDate = new Date(product_date);
    const expiryDate = new Date(expiry_date);

    // Các quy tắc kiểm tra
    if (!supplier_code) errors.push("Vui lòng chọn nhà cung cấp.");
    if (!receipt_no) errors.push("Vui lòng nhập số hóa đơn.");
    if (!equipment_code) errors.push("Vui lòng chọn tên thiết bị.");
    if (!batch_number) errors.push("Vui lòng nhập số lô.");
    if (!receipt_date || receiptDate > today)
        errors.push("Ngày nhập phải nhỏ hơn hoặc bằng ngày hôm nay.");
    if (!product_date || productDate > receiptDate)
        errors.push("Ngày sản xuất phải nhỏ hơn hoặc bằng ngày nhập.");
    if (!expiry_date || expiryDate <= productDate)
        errors.push("Hạn sử dụng phải lớn hơn ngày sản xuất.");
    if (!price || price <= 0) errors.push("Vui lòng nhập giá nhập hợp lệ.");
    if (!quantity || quantity <= 0)
        errors.push("Vui lòng nhập số lượng hợp lệ.");
    if (!discount || discount < 0 || discount > 100)
        errors.push("Vui lòng nhập chiết khấu hợp lệ (0-100%).");
    if (!VAT || VAT < 0 || VAT > 100)
        errors.push("Vui lòng nhập VAT hợp lệ (0-100%).");

    // Kiểm tra trùng số lô trong danh sách hiện tại
    const isDuplicateInList = equipmentData.some(
        (equipment) =>
            equipment.batch_number === batch_number &&
            equipment.equipment_code === equipment_code
    );
    if (isDuplicateInList) {
        errors.push(
            "Thiết bị " +
                equipment_code +
                " với số lô " +
                batch_number +
                " đã được thêm vào danh sách."
        );
    }

    // Kiểm tra nếu số lô đã được sử dụng cho thiết bị khác trong danh sách
    const isBatchDuplicateForDifferentEquipment = equipmentData.some(
        (equipment) =>
            equipment.batch_number === batch_number &&
            equipment.equipment_code !== equipment_code
    );
    if (isBatchDuplicateForDifferentEquipment) {
        errors.push(
            "Số lô " +
                batch_number +
                " đã được chỉ định cho thiết bị khác trong danh sách."
        );
    }

    // Kiểm tra trùng số lô trong cơ sở dữ liệu qua AJAX
    const isDuplicateBatchInDB = await checkBatchNumber(
        batch_number,
        equipment_code
    );
    if (isDuplicateBatchInDB) {
        errors.push("Số lô này đã được sử dụng. Vui lòng chọn số lô khác");
    }

    // Hiển thị lỗi nếu có
    if (errors.length > 0) {
        const errorMessages = document.getElementById("errorMessages");
        const errorList = document.getElementById("errorList");

        errorList.innerHTML = ""; // Xóa lỗi trước đó
        errors.forEach((error) => {
            const li = document.createElement("li");
            li.textContent = error;
            errorList.appendChild(li);
        });

        errorMessages.style.display = "block"; // Hiển thị thông báo lỗi
        return; // Dừng thực hiện nếu có lỗi
    }

    document.getElementById("errorMessages").style.display = "none";

    // Nếu không có lỗi, tính toán tổng giá
    const total_price =
        price * quantity * (1 - discount / 100) * (1 + VAT / 100);

    const equipment = {
        supplier_code,
        note,
        receipt_no,
        receipt_date,
        created_by,
        batch_number,
        expiry_date,
        product_date,
        quantity,
        VAT,
        discount,
        equipment_code,
        price,
        total_price,
    };

    equipmentData.push(equipment); // Thêm thiết bị vào danh sách

    // Thêm thiết bị mới vào bảng
    const tableBody = document.getElementById("equipmentList");
    if (tableBody) {
        const row = document.createElement("tr");
        const index = equipmentData.length - 1;

        row.innerHTML = `
            <td style="font-size: 11px;" class="text-center">${equipment_code}</td>
            <td style="font-size: 11px;" class="text-center">${supplier_code}</td>
            <td style="font-size: 11px;" class="text-center">${quantity}</td>
            <td style="font-size: 11px;" class="text-center">${price
                .toLocaleString("vi-VN", { style: "currency", currency: "VND" })
                .replace(",00", "")}</td>
            <td style="font-size: 11px;" class="text-center">${batch_number}</td>
            <td style="font-size: 11px;" class="text-center">${expiry_date}</td>
            <td style="font-size: 11px;" class="text-center">${discount}</td>
            <td style="font-size: 11px;" class="text-center">${VAT}</td>
            <td style="font-size: 11px;" class="text-center">${total_price
                .toLocaleString("vi-VN", { style: "currency", currency: "VND" })
                .replace(",00", "")}</td>
            <td class="text-center">
                <button onclick="removeEquipment(${index}, this)">
                    <i class="fas fa-trash-alt"></i>
                </button>
            </td>
        `;
        tableBody.appendChild(row);
    }

    // Ẩn thông báo 'không có dữ liệu' nếu đã có thiết bị được thêm
    if (equipmentData.length > 0) {
        noDataAlert.style.display = "none";
    } else {
        noDataAlert.style.display = "table-row";
    }

    // Cập nhật tổng số
    calculateTotals();
}

// Xóa thiết bị khỏi danh sách
function removeEquipment(index, element) {
    equipmentData.splice(index, 1);
    const row = element.closest("tr");
    row.remove();

    if (equipmentData.length === 0) {
        document.getElementById("noDataAlert").style.display = "table-row";
    }

    calculateTotals();
}

// Tính Tiền
function calculateTotals() {
    let totalDiscount = 0;
    let totalVAT = 0;
    let totalAmount = 0;

    equipmentData.forEach((equipment) => {
        const itemDiscount =
            (equipment.price * equipment.quantity * equipment.discount) / 100;
        const itemVAT =
            ((equipment.price * equipment.quantity - itemDiscount) *
                equipment.VAT) /
            100;
        const itemTotal = parseFloat(equipment.total_price);

        totalDiscount += itemDiscount;
        totalVAT += itemVAT;
        totalAmount += itemTotal;
    });

    document.getElementById("totalDiscount").textContent =
        totalDiscount.toLocaleString("vi-VN", {
            style: "currency",
            currency: "VND",
        });
    document.getElementById("totalVAT").textContent = totalVAT.toLocaleString(
        "vi-VN",
        { style: "currency", currency: "VND" }
    );
    document.getElementById("totalAmount").textContent =
        totalAmount.toLocaleString("vi-VN", {
            style: "currency",
            currency: "VND",
        });
}

// function submitEquipments() {
//     document.getElementById("equipmentData").value =
//         JSON.stringify(equipmentData);
// }

// document
//     .getElementById("equipment_code")
//     .addEventListener("change", function () {
//         const equipmentCode = this.value;

//         if (equipmentCode) {
//             // Gửi yêu cầu AJAX để lấy dữ liệu của thiết bị
//             fetch(`/system/system/warehouse/get_equipment/${equipmentCode}`)
//                 .then((response) => response.json())
//                 .then((data) => {
//                     // Điền dữ liệu vào các trường liên quan
//                     document.getElementById("price").value = data.price || "";
//                     document.getElementById("batch_number").value =
//                         data.batch_number || "";
//                     document.getElementById("product_date").value =
//                         data.product_date || "";
//                     document.getElementById("expiry_date").value =
//                         data.expiry_date || "";
//                 })
//                 .catch((error) => {
//                     console.error("Error fetching equipment data:", error);
//                 });
//         }
//     });

// function formatCurrency(input) {
//     let value = input.value.replace(/[^0-9]/g, "");

//     if (value) {
//         value = parseFloat(value).toLocaleString("vi-VN", {
//             style: "currency",
//             currency: "VND",
//         });
//     } else {
//         value = "0 VNĐ";
//     }

//     input.value = value.replace("VNĐ", "").trim();
// }

// function filterProducts() {
//     var input = document.getElementById("equipment_name"); // Lấy tên thiết bị để tìm kiếm
//     var filter = input.value.toUpperCase();
//     var dropdown = document.getElementById("productDropdown");

//     dropdown.style.display = filter ? "block" : "none";
//     dropdown.innerHTML = ""; // Xóa nội dung trước đó

//     var filteredProducts = products.filter(function (product) {
//         return product.name.toUpperCase().indexOf(filter) > -1;
//     });

//     filteredProducts.forEach(function (product) {
//         var item = `
//             <a class="dropdown-item d-flex align-items-center" style="background-color: white !important; color: #000;" onclick="selectProduct('${product.code}', '${product.name}')">
//                 <img src="https://png.pngtree.com/template/20190316/ourlarge/pngtree-medical-health-logo-image_79595.jpg" alt="Product Image" class="me-2" style="width: 40px; height: 40px;">
//                 <div>
//                     <div class="fw-bold">${product.name}</div>
//                     <small>Mã thiết bị: ${product.code}</small>
//                 </div>
//             </a>
//         `;
//         dropdown.insertAdjacentHTML("beforeend", item);
//     });
// }

// function selectProduct(productCode, productName) {
//     document.getElementById("equipment_name").value = productName; // Hiển thị tên thiết bị
//     document.getElementById("equipment_code").value = productCode; // Lưu mã thiết bị trong input ẩn
//     document.getElementById("productDropdown").style.display = "none";
// }
