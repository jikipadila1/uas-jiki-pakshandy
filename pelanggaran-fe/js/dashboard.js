// Dashboard functionality
async function loadDashboard() {
    try {
        // Load pelanggaran stats
        const pelanggaranResponse = await apiCall('/pelanggaran?page=1&per_page=5');

        if (pelanggaranResponse && pelanggaranResponse.status) {
            const totalElement = document.getElementById('totalPelanggaran');
            if (totalElement && pelanggaranResponse.pagination) {
                totalElement.textContent = pelanggaranResponse.pagination.total || '0';
            }

            // Load recent pelanggaran activity
            const recentActivityElement = document.getElementById('recentActivity');
            if (recentActivityElement && pelanggaranResponse.data && pelanggaranResponse.data.length > 0) {
                let html = `
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Tipe</th>
                                    <th>Lokasi</th>
                                    <th>Tanggal</th>
                                </tr>
                            </thead>
                            <tbody>
                `;

                pelanggaranResponse.data.forEach(item => {
                    html += `
                        <tr>
                            <td><span class="badge ${getPelanggaranBadge(item.tipe_pelanggaran)}">${formatPelanggaranType(item.tipe_pelanggaran)}</span></td>
                            <td>${item.lokasi}</td>
                            <td>${formatDate(item.tanggal)}</td>
                        </tr>
                    `;
                });

                html += `
                            </tbody>
                        </table>
                    </div>
                `;

                recentActivityElement.innerHTML = html;
            }
        }

        // Load objek melintas stats
        const objekResponse = await apiCall('/objek-melintas?page=1&per_page=1');

        if (objekResponse && objekResponse.status) {
            const totalElement = document.getElementById('totalObjekMelintas');
            if (totalElement && objekResponse.pagination) {
                totalElement.textContent = objekResponse.pagination.total || '0';
            }
        }
    } catch (error) {
        console.error('Error loading dashboard:', error);
        showAlert('Gagal memuat dashboard', 'danger');
    }
}

// Load dashboard on page load
document.addEventListener('DOMContentLoaded', function() {
    // Wait for auth check
    setTimeout(loadDashboard, 100);
});
