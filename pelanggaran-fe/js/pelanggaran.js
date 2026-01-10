// Pagination state
let currentPage = 1;
let perPage = 20;
let currentSearch = '';

// Load pelanggaran data
async function loadPelanggaran(page = 1, search = '') {
    try {
        const response = await apiCall(`/pelanggaran?page=${page}&per_page=${perPage}&search=${encodeURIComponent(search)}`);

        if (response && response.status) {
            const tableBody = document.getElementById('pelanggaranTableBody');
            const paginationContainer = document.getElementById('paginationContainer');

            if (response.data && response.data.length > 0) {
                let html = '';
                response.data.forEach((item, index) => {
                    const no = (page - 1) * perPage + index + 1;
                    html += `
                        <tr>
                            <td>${no}</td>
                            <td><span class="badge ${getPelanggaranBadge(item.tipe_pelanggaran)}">${formatPelanggaranType(item.tipe_pelanggaran)}</span></td>
                            <td>${item.lokasi}</td>
                            <td>${formatDate(item.tanggal)}</td>
                            <td>${item.deskripsi || '-'}</td>
                            <td>${item.created_by_name || '-'}</td>
                            <td>
                                <button class="btn btn-sm btn-info btn-view" data-id="${item.id}">
                                    <i class="bi bi-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-warning btn-edit" data-id="${item.id}">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button class="btn btn-sm btn-danger btn-delete" data-id="${item.id}">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                    `;
                });

                tableBody.innerHTML = html;

                // Setup action buttons
                setupActionButtons();

                // Show pagination
                if (response.pagination) {
                    renderPagination(response.pagination);
                    paginationContainer.style.display = 'block';
                }
            } else {
                tableBody.innerHTML = `
                    <tr>
                        <td colspan="7" class="text-center text-muted">
                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                            Tidak ada data
                        </td>
                    </tr>
                `;
                paginationContainer.style.display = 'none';
            }
        }
    } catch (error) {
        console.error('Error loading pelanggaran:', error);
        showAlert('Gagal memuat data pelanggaran', 'danger');
    }
}

// Render pagination
function renderPagination(pagination) {
    const paginationElement = document.getElementById('pagination');
    let html = '';

    // Previous button
    if (pagination.current_page > 1) {
        html += `
            <li class="page-item">
                <a class="page-link" href="#" data-page="${pagination.current_page - 1}">Previous</a>
            </li>
        `;
    } else {
        html += `
            <li class="page-item disabled">
                <span class="page-link">Previous</span>
            </li>
        `;
    }

    // Page numbers
    const totalPages = pagination.total_pages;
    const maxVisiblePages = 5;
    let startPage = Math.max(1, pagination.current_page - Math.floor(maxVisiblePages / 2));
    let endPage = Math.min(totalPages, startPage + maxVisiblePages - 1);

    if (endPage - startPage + 1 < maxVisiblePages) {
        startPage = Math.max(1, endPage - maxVisiblePages + 1);
    }

    if (startPage > 1) {
        html += `<li class="page-item"><a class="page-link" href="#" data-page="1">1</a></li>`;
        if (startPage > 2) {
            html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
        }
    }

    for (let i = startPage; i <= endPage; i++) {
        if (i === pagination.current_page) {
            html += `
                <li class="page-item active">
                    <span class="page-link">${i}</span>
                </li>
            `;
        } else {
            html += `
                <li class="page-item">
                    <a class="page-link" href="#" data-page="${i}">${i}</a>
                </li>
            `;
        }
    }

    if (endPage < totalPages) {
        if (endPage < totalPages - 1) {
            html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
        }
        html += `<li class="page-item"><a class="page-link" href="#" data-page="${totalPages}">${totalPages}</a></li>`;
    }

    // Next button
    if (pagination.current_page < totalPages) {
        html += `
            <li class="page-item">
                <a class="page-link" href="#" data-page="${pagination.current_page + 1}">Next</a>
            </li>
        `;
    } else {
        html += `
            <li class="page-item disabled">
                <span class="page-link">Next</span>
            </li>
        `;
    }

    paginationElement.innerHTML = html;

    // Setup pagination click events
    paginationElement.querySelectorAll('.page-link').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const page = this.getAttribute('data-page');
            if (page && !this.parentElement.classList.contains('disabled')) {
                currentPage = parseInt(page);
                loadPelanggaran(currentPage, currentSearch);
            }
        });
    });
}

// Setup action buttons
function setupActionButtons() {
    // View buttons
    document.querySelectorAll('.btn-view').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            viewPelanggaran(id);
        });
    });

    // Edit buttons
    document.querySelectorAll('.btn-edit').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            editPelanggaran(id);
        });
    });

    // Delete buttons
    document.querySelectorAll('.btn-delete').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            deletePelanggaran(id);
        });
    });
}

// View pelanggaran
async function viewPelanggaran(id) {
    try {
        const response = await apiCall(`/pelanggaran/${id}`);

        if (response && response.status && response.data) {
            const item = response.data;
            const message = `
                <strong>Tipe:</strong> ${formatPelanggaranType(item.tipe_pelanggaran)}<br>
                <strong>Lokasi:</strong> ${item.lokasi}<br>
                <strong>Tanggal:</strong> ${formatDate(item.tanggal)}<br>
                <strong>Deskripsi:</strong> ${item.deskripsi || '-'}<br>
                <strong>Dibuat oleh:</strong> ${item.created_by_name || '-'}
            `;

            // Create modal dynamically
            const modalHtml = `
                <div class="modal fade" id="viewModal" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Detail Data Pelanggaran</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                ${message}
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                            </div>
                        </div>
                    </div>
                </div>
            `;

            // Remove existing modal
            const existingModal = document.getElementById('viewModal');
            if (existingModal) {
                existingModal.remove();
            }

            // Add modal to body
            document.body.insertAdjacentHTML('beforeend', modalHtml);

            // Show modal
            const modal = new bootstrap.Modal(document.getElementById('viewModal'));
            modal.show();
        }
    } catch (error) {
        console.error('Error viewing pelanggaran:', error);
        showAlert('Gagal memuat detail data', 'danger');
    }
}

// Edit pelanggaran (placeholder for now)
function editPelanggaran(id) {
    showAlert('Fitur edit belum diimplementasikan', 'info');
}

// Delete pelanggaran
async function deletePelanggaran(id) {
    if (!confirm('Apakah Anda yakin ingin menghapus data ini?')) {
        return;
    }

    try {
        const response = await apiCall(`/pelanggaran/${id}`, 'DELETE');

        if (response && response.status) {
            showAlert('Data berhasil dihapus', 'success');
            loadPelanggaran(currentPage, currentSearch);
        } else {
            showAlert(response?.message || 'Gagal menghapus data', 'danger');
        }
    } catch (error) {
        console.error('Error deleting pelanggaran:', error);
        showAlert('Gagal menghapus data', 'danger');
    }
}

// Add new pelanggaran
document.getElementById('submitAddBtn').addEventListener('click', async function() {
    const tipe = document.getElementById('tipe_pelanggaran').value;
    const lokasi = document.getElementById('lokasi').value;
    const tanggal = document.getElementById('tanggal').value;
    const deskripsi = document.getElementById('deskripsi').value;

    if (!tipe || !lokasi || !tanggal) {
        showAlert('Harap isi semua field yang wajib diisi', 'warning');
        return;
    }

    try {
        const response = await apiCall('/pelanggaran', 'POST', {
            tipe_pelanggaran: tipe,
            lokasi: lokasi,
            tanggal: tanggal,
            deskripsi: deskripsi
        });

        if (response && response.status) {
            showAlert('Data berhasil ditambahkan', 'success');

            // Close modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('addModal'));
            modal.hide();

            // Reset form
            document.getElementById('addForm').reset();

            // Reload data
            loadPelanggaran(currentPage, currentSearch);
        } else {
            showAlert(response?.message || 'Gagal menambahkan data', 'danger');
        }
    } catch (error) {
        console.error('Error adding pelanggaran:', error);
        showAlert('Gagal menambahkan data', 'danger');
    }
});

// Search functionality
document.getElementById('searchBtn').addEventListener('click', function() {
    currentSearch = document.getElementById('searchInput').value;
    currentPage = 1;
    loadPelanggaran(currentPage, currentSearch);
});

document.getElementById('searchInput').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        currentSearch = this.value;
        currentPage = 1;
        loadPelanggaran(currentPage, currentSearch);
    }
});

// Load data on page load
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(() => {
        loadPelanggaran(currentPage, currentSearch);
    }, 100);
});
