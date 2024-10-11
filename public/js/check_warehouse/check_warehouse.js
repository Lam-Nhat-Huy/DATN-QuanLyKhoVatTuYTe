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
        document.getElementById(
            "importantNotificationContent"
        ).innerHTML = `Đã thêm tất cả thiết bị vào danh sách. Vui lòng tiến hành kiểm kê kho hàng!`;
        $("#importantNotificationModal").modal("show");
        return;
    }

    var tableBody = document.getElementById("materialList");
    var rowCount = materialData.length;

    var row = `
    <tr data-index="${rowCount}" class="unchecked">
        <td>${rowCount + 1}</td>
        <td>${equipment_code}</td>
        <td style="max-width: 150px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">${name}</td>
        <td>${batch_number}</td>
        <td>${current_quantity}</td>
        <td>
            <input type="number" class="actual-quantity-input" style="width: 70px; height: 40px; border-radius: 8px;" oninput="updateProduct(${rowCount}, this.value)">
        </td>
        <td class="unequal-count" id="unequal-count-${rowCount}">0</td>
        <td>
            <a href="#" class="text-dark" title="Xóa thiết bị" onclick="removeProduct(${rowCount})">
                <i class="fa fa-trash"></i>
            </a>
        </td>
    </tr>
    `;

    tableBody.insertAdjacentHTML("beforeend", row);

    materialData.push({
        equipment_code: equipment_code,
        current_quantity: current_quantity,
        actual_quantity: null,
        unequal: 0,
        batch_number: batch_number,
    });

    uncheckedCount++;
    updateCounts();

    if (tableBody.rows.length > 0) {
        document.getElementById("noDataAlert").style.display = "none";
    }

    const actualQuantityInputs = document.querySelectorAll(
        ".actual-quantity-input"
    );
    actualQuantityInputs.forEach((input, index) => {
        input.addEventListener("keydown", function (event) {
            if (event.key === "Enter") {
                event.preventDefault();
                const nextInput = actualQuantityInputs[index + 1];
                if (nextInput) {
                    nextInput.focus();
                }
            } else if (event.key === "Shift") {
                event.preventDefault();
                const prevInput = actualQuantityInputs[index - 1];
                if (prevInput) {
                    prevInput.focus();
                }
            }
        });
    });
}

function removeProduct(index) {
    var tableBody = document.getElementById("materialList");
    var row = tableBody.querySelector(`tr[data-index="${index}"]`);
    if (row) {
        tableBody.removeChild(row);
        materialData.splice(index, 1);
        totalCount--;
        uncheckedCount--;
        updateRowIndices();
        updateCounts();
        if (tableBody.rows.length === 0) {
            document.getElementById("noDataAlert").style.display = "table-row";
        }
    }
}

function updateRowIndices() {
    var tableBody = document.getElementById("materialList");
    Array.from(tableBody.rows).forEach((row, index) => {
        row.setAttribute("data-index", index);
        row.cells[1].textContent = index + 1;
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

    // Cập nhật hiển thị số lượng
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

function submitMaterials() {
    const actualQuantityInputs = document.querySelectorAll(
        ".actual-quantity-input"
    );
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

    materialData = materialData.map(function (material) {
        return {
            ...material,
            check_date: checkDate,
            note: note,
            status: status,
            created_by: created_by,
        };
    });

    document.getElementById("materialData").value =
        JSON.stringify(materialData);
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

document.addEventListener("keydown", function (event) {
    if (event.altKey && event.key === "q") {
        const focusedElement = document.activeElement;

        if (
            focusedElement &&
            focusedElement.classList.contains("actual-quantity-input")
        ) {
            const rowIndex = focusedElement
                .closest("tr")
                .getAttribute("data-index");

            const current_quantity = materialData[rowIndex].current_quantity;

            focusedElement.value = current_quantity;
            updateProduct(rowIndex, current_quantity);
        }
    }
});
