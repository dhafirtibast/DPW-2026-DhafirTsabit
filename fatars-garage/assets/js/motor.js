// Mengambil & menampilkan daftar buku scr asinkron dari data/buku.json
async function muatDaftarMotor() {
    const tbody = document.querySelector(".table-responsive table tbody");
    const loading = document.getElementById("loading-indicator");
    if (!tbody) return;

    loading.style.display = "block";
    tbody.innerHTML = "";

    try {
        await new Promise((resolve) => setTimeout(resolve, 600));

        const res = await fetch("../data/motor.json");
        if (!res.ok) {
            throw new Error("Gagal mengambil data (status " + res.status + ")");
            
        }
        const daftarMotor = await res.json();

        daftarMotor.forEach(function (motor) {
            const tr = document.createElement("tr");
            tr.innerHTML =
                "<td>" + motor.nama + "</td>" +
                "<td>" + motor.merk + "</td>" +
                "<td>" + motor.tahun + "</td>" +
                "<td class=\"harga\">" + motor.harga.toLocaleString("id-ID") + "</td>" +
                "<td>" + motor.lokasi + "</td>" +
                "<td>" + motor.status + "</td>" +
                "<td>" +
                "<button type=\"button\">Edit</button> " +
                "<button type=\"button\" class=\"btn-hapus\">Hapus</button>" +
                "</td>";
            tbody.appendChild(tr);
        });
    } catch (err) {
        tbody.innerHTML =
        "<tr><td colspan=\"5\">Gagal memuat data: " + err.message + "</td></tr>";
    } finally {
        loading.style.display = "none";
    }
}

document.addEventListener("DOMContentLoaded", muatDaftarMotor);