function resetModal() {
    document.getElementById("modalForm").reset();
}

function resetModal2() {
    document.getElementById("modalForm2").reset();
}

fetch("/expenseCategories").then((data) => {

    return data.json();
}).then((objectData) => {

    let tableData = "";
    objectData.map((values) => {

        tableData += ` <tr>
		<td>${values.name}</td>
		</tr>`;
    });

    document.getElementById("tableBody").innerHTML = tableData;
})