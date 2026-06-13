document.addEventListener("DOMContentLoaded", () => {
    const orderBody = document.getElementById("orderBody");
    const grandTotalBox = document.getElementById("grandTotalBox");

    function createMenuSelectHTML() {
        const grouped = menuData.reduce((acc, item) => {
            const cat = item.category_name || "Uncategorized";
            acc[cat] = acc[cat] || [];
            acc[cat].push(item);
            return acc;
        }, {});

        let html = `<select name="item_id[]" class="menuSelect" required><option value="">Select Item</option>`;
        for (const category in grouped) {
            html += `<optgroup label="${category}">`;
            grouped[category].forEach(item => {
                html += `<option value="${item.id}" data-price="${parseFloat(item.price).toFixed(2)}" data-name="${item.name}">${item.name}</option>`;
            });
            html += `</optgroup>`;
        }
        html += `</select>`;
        return html;
    }

    function updateRow(row) {
        const select = row.querySelector(".menuSelect");
        const qtyInput = row.querySelector(".qtyInput");
        const priceCell = row.querySelector(".price");
        const totalCell = row.querySelector(".itemTotal");

        const selected = select.selectedOptions[0];
        const price = selected ? parseFloat(selected.dataset.price) : 0;
        const qty = Math.max(parseInt(qtyInput.value) || 1, 1);

        qtyInput.value = qty;
        const lineTotal = price * qty;
        totalCell.dataset.lineTotal = lineTotal.toFixed(2);
        priceCell.textContent = `Rs ${price.toFixed(2)}`;
        totalCell.textContent = `Rs ${lineTotal.toFixed(2)}`;
        updateGrandTotal();
    }

    window.removeRow = (row) => { row.remove(); updateGrandTotal(); };

    function updateGrandTotal() {
        const totals = [...document.querySelectorAll(".itemTotal")];
        const sum = totals.reduce((acc, cell) => acc + (parseFloat(cell.dataset.lineTotal) || 0), 0);
        grandTotalBox.textContent = `Grand Total: Rs ${sum.toFixed(2)}`;
    }

    window.addRow = () => {
        const row = document.createElement("tr");
        row.innerHTML = `
            <td>${createMenuSelectHTML()}</td>
            <td class="price">Rs 0.00</td>
            <td><input type="number" name="quantity[]" class="qtyInput" value="1" min="1"></td>
            <td class="itemTotal" data-line-total="0.00">Rs 0.00</td>
            <td><button type="button" class="remove-btn">X</button></td>
        `;
        orderBody.appendChild(row);

        row.querySelector(".menuSelect").addEventListener("change", () => updateRow(row));
        row.querySelector(".qtyInput").addEventListener("input", () => updateRow(row));
        row.querySelector(".remove-btn").addEventListener("click", () => window.removeRow(row));

        updateRow(row);
    };

    window.addRow();

    window.confirmOrder = async () => {
        const rows = [...document.querySelectorAll("#orderBody tr")];
        if (rows.length === 0) { alert("Add at least one item."); return; }

        const orderItems = [];
        let grandTotal = 0;

        for (const row of rows) {
            const select = row.querySelector(".menuSelect");
            const qtyInput = row.querySelector(".qtyInput");
            const itemTotal = row.querySelector(".itemTotal");

            if (!select.value) { alert("Select every item."); return; }

            const selected = select.selectedOptions[0];
            const data = {
                item_id: select.value,
                price_at_order: parseFloat(selected.dataset.price),
                quantity: parseInt(qtyInput.value),
                line_total: parseFloat(itemTotal.dataset.lineTotal)
            };
            orderItems.push(data);
            grandTotal += data.line_total;
        }

        const orderData = {
            items: orderItems,
            grand_total: grandTotal.toFixed(2),
            message: document.querySelector(".message-textarea").value
        };

        try {
            const response = await fetch("submit_order.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify(orderData)
            });

            const result = await response.json();
            if (result.success) {
                orderBody.innerHTML = "";
                document.querySelector(".message-textarea").value = "";
                window.addRow();
                document.getElementById("thankyouModal").style.display = "flex";
            } else {
                alert(result.message || "Order failed.");
            }
        } catch (error) {
            console.error(error);
            alert("Unexpected error.");
        }
    };

    window.closeModal = () => {
        document.getElementById("thankyouModal").style.display = "none";
    };
});
