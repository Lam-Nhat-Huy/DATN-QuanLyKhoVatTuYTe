let materialData = [];
let totalCount = 0;
let matchedCount = 0;
let mismatchedCount = 0;
let uncheckedCount = 0;

function showDropdown() {
    var dropdown = document.getElementById("productDropdown");
    dropdown.style.display = "block";
    filterProducts();
}

function filterProducts() {
    var input = document.getElementById("searchProductInput");
    var filter = input.value.toUpperCase();
    var dropdown = document.getElementById("productDropdown");

    dropdown.style.display =
        filter || input === document.activeElement ? "block" : "none";
    dropdown.innerHTML = "";

    var filteredProducts = products.filter(function (product) {
        return product.name.toUpperCase().indexOf(filter) > -1;
    });

    if (filteredProducts.length === 0) {
        var noResultItem = `
            <div class="dropdown-item text-center">
                Không tìm thấy kết quả
            </div>
        `;
        dropdown.insertAdjacentHTML("beforeend", noResultItem);
    } else {
        filteredProducts.forEach(function (product) {
            product.inventories.forEach(function (inventory) {
                var imageUrl = product.image
                    ? `/storage/${product.image}`
                    : "https://st4.depositphotos.com/14953852/24787/v/380/depositphotos_247872612-stock-illustration-no-image-available-icon-vector.jpg";
                var item = `
                    <a class="dropdown-item d-flex align-items-center" href="#"
                        onclick="selectProduct(this, '${product.name}', '${inventory.equipment_code}', ${inventory.current_quantity}, '${inventory.batch_number}')">
                        <img src="${imageUrl}" alt="Product Image" class="me-2" style="width: 40px; height: 40px;">
                        <div>
                            <div class="fw-bold">${product.name}</div>
                            <small>${inventory.equipment_code} - Tồn kho: ${inventory.current_quantity} - Lô: ${inventory.batch_number}</small>
                        </div>
                    </a>
                `;
                dropdown.insertAdjacentHTML("beforeend", item);
            });
        });
    }
}

document.addEventListener("click", function (event) {
    var dropdown = document.getElementById("productDropdown");
    var searchInput = document.getElementById("searchProductInput");

    if (
        !dropdown.contains(event.target) &&
        !searchInput.contains(event.target)
    ) {
        dropdown.style.display = "none";
    }
});

function selectProduct(
    element,
    name,
    equipment_code,
    current_quantity,
    batch_number
) {
    addProductToTable(name, equipment_code, current_quantity, batch_number);
    document.getElementById("productDropdown").style.display = "none";
    document.getElementById("searchProductInput").value = "";
}

function addProductToTable(
    name,
    equipment_code,
    current_quantity,
    batch_number
) {
    var existingMaterial = materialData.find(
        (material) =>
            material.equipment_code === equipment_code &&
            material.batch_number === batch_number
    );

    if (existingMaterial) {
        return;
    }

    var tableBody = document.getElementById("materialList");
    var rowCount = materialData.length;

    var product = {
        name,
        equipment_code,
        current_quantity,
        actual_quantity: null,
        unequal: 0,
        batch_number,
        equipment_note: "",
    };

    var row = generateTableRow(rowCount, product);
    tableBody.insertAdjacentHTML("beforeend", row);

    materialData.push(product);

    uncheckedCount++;
    updateCounts();

    if (tableBody.rows.length > 0) {
        document.getElementById("noDataAlert").style.display = "none";
    }

    addListeners();
    checkInputs();
}

function populateTableWithProducts() {
    var tableBody = document.getElementById("materialList");
    tableBody.innerHTML = "";

    if (!Array.isArray(productDetails)) {
        return;
    }

    materialData = [];

    productDetails.forEach((product, index) => {
        var row = generateTableRow(index, product);
        tableBody.insertAdjacentHTML("beforeend", row);

        materialData.push({
            equipment_code: product.equipment_code,
            current_quantity: product.current_quantity,
            actual_quantity: product.actual_quantity || 0,
            unequal: product.unequal || 0,
            batch_number: product.batch_number,
            equipment_note: "",
        });
    });

    if (productDetails.length === 0) {
        document.getElementById("noDataAlert").style.display = "table-row";
    } else {
        document.getElementById("noDataAlert").style.display = "none";
    }

    addListeners();
}

function generateTableRow(index, product) {
    const productName =
        product.equipment && product.equipment.name
            ? product.equipment.name
            : product.name || "Không có tên";
    return `
        <tr data-index="${index}" class="unchecked">
            <td>${index + 1}</td>
            <td class="text-left equipment-code">${product.equipment_code}</td>
            <td style="max-width: 150px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="${productName}">
                ${productName}
            </td>
            <td>${product.batch_number || ""}</td>
            <td>${product.current_quantity}</td>
            <td>${product.actual_quantity}</td>
            <td>
                <input type="number" min="0" class="actual-quantity-input" 
                    value="" 
                    oninput="validateQuantity(this, ${index}); checkInputs(); updateUnequal(${index}, this.value)" 
                    style="width: 70px; height: 40px; border-radius: 8px;" />
            </td>
            <td class="unequal-count" id="unequal-count-${index}">
                ${product.unequal || 0}
            </td>
            <td>
                <textarea class="equipment_note rounded-3" 
                    name="equipment_note_${index}" 
                    style="width: 150px; height: 40px; border-radius: 8px; padding: 5px; font-size: 12px;">${
                        product.equipment_note || ""
                    }</textarea>
            </td>
        </tr>
    `;
}

function addListeners() {
    document
        .querySelectorAll('textarea[name^="equipment_note_"]')
        .forEach((textarea, index) => {
            textarea.addEventListener("input", function () {
                materialData[index].equipment_note = this.value;
            });
        });

    const actualQuantityInputs = document.querySelectorAll(
        ".actual-quantity-input"
    );
    actualQuantityInputs.forEach((input, index) => {
        input.addEventListener("keydown", function (event) {
            if (event.key === "Enter") {
                event.preventDefault();
                const nextInput = actualQuantityInputs[index + 1];
                if (nextInput) nextInput.focus();
            } else if (event.key === "Shift") {
                event.preventDefault();
                const prevInput = actualQuantityInputs[index - 1];
                if (prevInput) prevInput.focus();
            }
        });
    });
}

function updateUnequal(index, actualQuantity) {
    const row = document.querySelectorAll("#materialList tr")[index];
    const currentQuantity = parseInt(row.cells[4].innerText);
    const unequalInput = row.querySelector(".unequal-input");

    const unequal = Math.abs(currentQuantity - actualQuantity);
    unequalInput.value = unequal;

    materialData[index].actual_quantity = parseInt(actualQuantity) || 0;
}

populateTableWithProducts();

function submitMaterials() {
    const actualQuantityInputs = document.querySelectorAll(
        ".actual-quantity-input"
    );
    const equipmentNoteInputs = document.querySelectorAll(".equipment_note");
    let hasEmptyInput = false;

    actualQuantityInputs.forEach((input) => {
        if (input.value.trim() === "") {
            input.style.border = "2px solid red";
            hasEmptyInput = true;
        } else {
            input.style.border = "";
        }
    });

    if (hasEmptyInput) {
        return false;
    }

    var checkDate = document.getElementById("check_date").value;
    var note = document.getElementById("note").value;
    var created_by = document.getElementById("created_by").value;
    var status = document.querySelector('button[name="status"]:focus').value;

    materialData = materialData.map(function (material, index) {
        const actualQuantity =
            parseInt(
                document.querySelectorAll(".actual-quantity-input")[index].value
            ) || 0;
        const unequal = Math.abs(material.current_quantity - actualQuantity);

        const equipmentNote = equipmentNoteInputs[index].value.trim();

        return {
            ...material,
            check_date: checkDate,
            note: note,
            status: status,
            created_by: created_by,
            actual_quantity: actualQuantity,
            unequal: unequal,
            equipment_note: equipmentNote,
        };
    });

    document.getElementById("materialData").value =
        JSON.stringify(materialData);
}

function checkInputs() {
    const actualQuantityInputs = document.querySelectorAll(
        ".actual-quantity-input"
    );
    let allFilled = true;

    actualQuantityInputs.forEach((input) => {
        if (!input.value.trim()) {
            allFilled = false;
        }
    });

    const saveButton = document.querySelector(
        'button[name="status"][value="0"]'
    );
    const completeButton = document.querySelector(
        'button[data-bs-target="#completeModal"]'
    );

    const isAdmin = document.body.dataset.isAdmin === "true";

    if (allFilled) {
        saveButton.disabled = false;
        saveButton.textContent = "Lưu phiếu tạm";

        if (isAdmin) {
            completeButton.disabled = false;
            completeButton.textContent = "Lưu và duyệt phiếu";
        } else {
            completeButton.disabled = true;
            completeButton.textContent = "Lưu và duyệt phiếu";
        }
    } else {
        saveButton.disabled = true;
        saveButton.textContent = "Vui lòng nhập đủ số lượng";
        completeButton.disabled = true;
        completeButton.textContent = "Không thể hoàn thành";
    }
}

// Sự kiện để lắng nghe phím Alt + Q
document.addEventListener("keydown", function (event) {
    if (event.altKey && event.key.toLowerCase() === "q") {
        const focusedElement = document.activeElement;

        if (
            focusedElement &&
            focusedElement.classList.contains("actual-quantity-input")
        ) {
            const row = focusedElement.closest("tr");
            const rowIndex = parseInt(row.getAttribute("data-index"), 10);

            console.log("Row index:", rowIndex); // Kiểm tra chỉ số hàng

            // Đảm bảo hàng và dữ liệu tồn tại
            if (materialData[rowIndex]) {
                const currentQuantity = materialData[rowIndex].current_quantity;
                console.log("Current quantity:", currentQuantity); // Kiểm tra số lượng hiện tại

                // Điền giá trị vào ô input và cập nhật dữ liệu
                focusedElement.value = currentQuantity;
                updateProduct(rowIndex, currentQuantity);
                checkInputs();
            } else {
                console.error("Không tìm thấy dữ liệu cho hàng này.");
            }
        }
    }
});

function validateQuantity(input, rowCount) {
    if (input.value < 0) {
        input.value = 0;
    }
    updateProduct(rowCount, input.value);
}

function removeProduct(index) {
    const tableBody = document.getElementById("materialList");
    const row = tableBody.querySelector(`tr[data-index="${index}"]`);

    if (row) {
        tableBody.removeChild(row);
        materialData.splice(index, 1); // Xóa sản phẩm khỏi mảng dữ liệu

        updateRowIndices(); // Cập nhật lại chỉ số hàng
        updateCounts();

        if (tableBody.rows.length === 0) {
            document.getElementById("noDataAlert").style.display = "table-row";
        }
    }
}

function updateRowIndices() {
    const tableBody = document.getElementById("materialList");

    Array.from(tableBody.rows).forEach((row, index) => {
        row.setAttribute("data-index", index); // Cập nhật lại data-index
        row.cells[0].textContent = index + 1; // Cập nhật số thứ tự

        // Đảm bảo mảng materialData được đồng bộ với thứ tự hàng
        if (materialData[index]) {
            materialData[index].index = index;
        }
    });
}

function updateProduct(index, value) {
    if (materialData[index]) {
        materialData[index].actual_quantity = value;

        const current_quantity = materialData[index].current_quantity;
        const unequal = value - current_quantity;
        materialData[index].unequal = unequal;

        const unequalCountCell = document.getElementById(
            `unequal-count-${index}`
        );
        unequalCountCell.textContent = unequal;

        const tableRow = document.querySelector(`tr[data-index="${index}"]`);

        const inputField = tableRow.querySelector(".actual-quantity-input");

        if (value === "") {
            tableRow.style.backgroundColor = "";
            tableRow.classList.add("unchecked");
            tableRow.classList.remove("matched", "mismatch");
            inputField.style.border = "2px dashed red";
        } else {
            inputField.style.border = "";
            if (unequal < 0) {
                tableRow.style.backgroundColor = "#ffcccb";
                tableRow.classList.remove("unchecked", "matched");
                tableRow.classList.add("mismatch");
            } else if (unequal > 0) {
                tableRow.style.backgroundColor = "#ffebc8";
                tableRow.classList.remove("unchecked", "matched");
                tableRow.classList.add("mismatch");
            } else {
                tableRow.style.backgroundColor = "#d1f0d1";
                tableRow.classList.remove("unchecked", "mismatch");
                tableRow.classList.add("matched");
            }
        }

        if (value !== "") {
            uncheckedCount = Math.max(0, uncheckedCount - 1);
            if (unequal === 0) {
                matchedCount++;
                mismatchedCount = Math.max(0, mismatchedCount - 1);
            } else {
                mismatchedCount++;
                matchedCount = Math.max(0, matchedCount - 1);
            }
        } else {
            uncheckedCount++;
            matchedCount = Math.max(0, matchedCount - 1);
            mismatchedCount = Math.max(0, mismatchedCount - 1);
        }

        updateCounts();
    }
}

function updateCounts() {
    totalCount = materialData.length;
    matchedCount = materialData.filter(
        (material) =>
            material.unequal === 0 && material.actual_quantity !== null
    ).length;
    mismatchedCount = materialData.filter(
        (material) =>
            material.unequal !== 0 && material.actual_quantity !== null
    ).length;
    uncheckedCount = materialData.filter(
        (material) => material.actual_quantity === null
    ).length;

    document.getElementById("totalCount").textContent = totalCount;
    document.getElementById("matchedCount").textContent = matchedCount;
    document.getElementById("mismatchedCount").textContent = mismatchedCount;
    document.getElementById("uncheckedCount").textContent = uncheckedCount;
}

function filterTable(type) {
    const rows = document.querySelectorAll("#materialList tr");
    let hasVisibleRow = false;

    rows.forEach((row) => {
        row.style.display = "none";
        console.log(row.classList);

        if (type === "all") {
            row.style.display = "";
            hasVisibleRow = true;
        } else if (type === "matched" && row.classList.contains("matched")) {
            row.style.display = "";
            hasVisibleRow = true;
        } else if (type === "mismatch" && row.classList.contains("mismatch")) {
            row.style.display = "";
            hasVisibleRow = true;
        } else if (
            type === "unchecked" &&
            row.classList.contains("unchecked")
        ) {
            row.style.display = "";
            hasVisibleRow = true;
        }
    });

    if (hasVisibleRow) {
        document.getElementById("noDataAlert").style.display = "none";
    } else {
        document.getElementById("noDataAlert").style.display = "table-row";
    }
}

document.getElementById("filterAll").addEventListener("click", function () {
    filterTable("all");
});

document.getElementById("filterMatched").addEventListener("click", function () {
    filterTable("matched");
});

document
    .getElementById("filterMismatched")
    .addEventListener("click", function () {
        filterTable("mismatch");
    });

document
    .getElementById("filterUnchecked")
    .addEventListener("click", function () {
        filterTable("unchecked");
    });

function addAllProducts() {
    var tableBody = document.getElementById("materialList");

    products.forEach((product) => {
        product.inventories.forEach((inventory) => {
            addProductToTable(
                product.name,
                inventory.equipment_code,
                inventory.current_quantity,
                inventory.batch_number
            );
        });
    });
}

function autoFillQuantity(index, current_quantity) {
    var inputField = document.querySelector(
        `tr[data-index="${index}"] input[type="number"]`
    );
    inputField.value = current_quantity;
    updateProduct(index, current_quantity);
}

function autoFillAllQuantities() {
    materialData.forEach((material, index) => {
        var current_quantity = material.current_quantity;

        var inputField = document.querySelector(
            `tr[data-index="${index}"] input[type="number"]`
        );

        if (inputField) {
            inputField.value = current_quantity;

            updateProduct(index, current_quantity);
        }
    });
}

function checkNoDataAlert() {
    const rows = document.querySelectorAll("#materialList tr");
    let hasData = false;

    rows.forEach((row) => {
        if (row.style.display !== "none" && !row.id.includes("noDataAlert")) {
            hasData = true;
        }
    });

    if (hasData) {
        document.getElementById("noDataAlert").style.display = "none";
    } else {
        document.getElementById("noDataAlert").style.display = "table-row";
    }
}
