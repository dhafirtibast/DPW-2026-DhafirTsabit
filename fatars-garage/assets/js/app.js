// Hamburger menu (JS-driven, menggantikan checkbox hack)
function initNavToggle() {
    const toggleBtn = document.getElementById("nav-toggle-btn");
    const nav = document.querySelector("header nav");
    if (!toggleBtn || !nav) return;

    toggleBtn.addEventListener("click", function () {
        nav.classList.toggle("nav-open");
    });
}

function initHapusConfirm() {
    document.addEventListener("click", function (e) {
        const btn = e.target.closest(".btn-hapus");
        if (!btn) return;

        const row = btn.closest("tr");
        const nama = row ? row.querySelector("td")?.textContent : "data ini";
        const yakin = confirm("Yakin ingin menghapus \"" + nama + "\"?");
        if (yakin && row) {
            row.remove();
        }
    });
}

function initTableFilter() {
    const input = document.getElementById("search-input");
    const table = document.querySelector(".table-responsive table");
    if (!input || !table) return;

    input.addEventListener("keyup", function () {
        const keyword = input.value.toLowerCase();
        const rows = table.querySelectorAll("tbody tr");
        rows.forEach(function (row) {
            const teks = row.textContent.toLowerCase();
            row.style.display = teks.includes(keyword) ? "" : "none";
        });
    });
}

// Validasi form (client-side)
function tampilkanError(input, pesan) {
    hapusError(input);
    const span = document.createElement("span");
    span.className = "error";
    span.textContent = pesan;
    input.insertAdjacentElement("afterend", span);
}

function hapusError(input) {
    const next = input.nextElementSibling;
    if (next && next.classList.contains("error")) {
        next.remove();
    }
}

function initValidasiForm() {
    const form = document.getElementById("form-tambah");
    if (!form) return;

    form.addEventListener("submit", function (e) {
        let valid = true;

        const nama = form.querySelector("[name='nama']");
        if (nama && nama.value.trim() === "") {
            tampilkanError(nama, "Field ini waib diisi.");
            valid = false;
        } else if (nama) {
            hapusError(nama);
        }

        const merk = form.querySelector("[name='merk']");
        if (merk && merk.value.trim() === "") {
            tampilkanError(merk, "Field ini waib diisi.");
            valid = false;
        } else if (merk) {
            hapusError(merk);
        }

        const tahun = form.querySelector("[name='tahun']");
        if (tahun) {
            const nilai = parseInt(tahun.value, 10);
            if (isNaN(nilai) || nilai < 1990 || nilai > 2026) {
                tampilkanError(tahun, "Tahun harus di antara 1990-2026.");
                valid = false;
            } else {
                hapusError(tahun);
            }
        }

        const harga = form.querySelector("[name='harga']");
        if (harga) {
            const nilai = parseInt(harga.value, 10);
            if (isNaN(nilai) || nilai < 0) {
                tampilkanError(harga, "Harga tidak boleh negatif.");
                valid = false;
            } else {
                hapusError(harga);
            }
        }

        if (!valid) {
            e.preventDefault();
        }
    });
}

document.addEventListener("DOMContentLoaded", function () {
    initNavToggle();
    initHapusConfirm();
    initTableFilter();    
    initValidasiForm();
});