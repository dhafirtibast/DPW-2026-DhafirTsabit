async function muatDaftarPenjual() {
    const tbody = document.querySelector(".table-responsive table tbody");
    const loading = document.getElementById("loading-indicator");
    if (!tbody) return;

    loading.style.display = "block";
    tbody.innerHTML = "";

    try {
        await new Promise((resolve) => setTimeout(resolve, 600));

        const res = await fetch("../data/penjual.json");
        if (!res.ok) {
            throw new Error("Gagal mengambil data (status " + res.status + ")");
        }
        const daftarPenjual = await res.json();

        daftarPenjual.forEach(function (penjual) {
            const tr = document.createElement("tr");
            tr.innerHTML =
                "<td>" + penjual.id + "</td>" +
                "<td>" + penjual.nama + "</td>" +
                "<td>" + penjual.kota + "</td>" +
                "<td>" + penjual.no_hp + "</td>" +
                "<td>" + penjual.email + "</td>" +
                "<td>" +
                "<button type=\"button\">Edit</button> " +
                "<button type=\"button\" class=\"btn-hapus\">Hapus</button>" +
                "</td>";
            tbody.appendChild(tr);
        });
    } catch (err) {
        tbody.innerHTML =
            "<tr><td colspan=\"6\">Gagal memuat data: " + err.message + "</td></tr>";
    } finally {
        loading.style.display = "none";
    }
}

document.addEventListener("DOMContentLoaded", muatDaftarPenjual);