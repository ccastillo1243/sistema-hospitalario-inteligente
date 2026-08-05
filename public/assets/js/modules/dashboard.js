$(function () {
    var paleta = ['#2563eb', '#059669', '#d97706', '#dc2626', '#7c3aed', '#0891b2'];

    function loadKpis() {
        apiRequest({ url: '/dashboard/summary', method: 'GET' }).done(function (d) {
            var $grid = $('#kpiGrid').empty();
            var kpis = [
                { valor: d.totalPacientes, etiqueta: 'Pacientes' },
                { valor: d.camasOcupadas + '/' + d.camasTotal, etiqueta: 'Camas ocupadas' },
                { valor: d.citasHoy, etiqueta: 'Citas hoy' },
                { valor: d.examenesPendientes, etiqueta: 'Exámenes pendientes' },
                { valor: d.casosEnEspera, etiqueta: 'Casos en espera' },
            ];
            kpis.forEach(function (k) {
                $grid.append(
                    $('<div class="kpi-card">').append(
                        $('<div class="valor">').text(k.valor),
                        $('<div class="etiqueta">').text(k.etiqueta)
                    )
                );
            });
        });
    }

    function loadBedChart() {
        apiRequest({ url: '/dashboard/bed-occupancy', method: 'GET' }).done(function (d) {
            var labels = d.items.map(function (i) { return 'Piso ' + i.piso; });
            var ocupadas = d.items.map(function (i) { return Number(i.camas_ocupadas); });
            var libres = d.items.map(function (i) { return Number(i.camas_libres); });

            new Chart(document.getElementById('bedChart'), {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [
                        { label: 'Ocupadas', data: ocupadas, backgroundColor: '#dc2626' },
                        { label: 'Libres', data: libres, backgroundColor: '#059669' },
                    ],
                },
                options: { responsive: true, scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } } },
            });
        });
    }

    function loadStockChart() {
        apiRequest({ url: '/dashboard/low-stock', method: 'GET' }).done(function (d) {
            var labels = d.items.map(function (i) { return i.nombre; });
            var valores = d.items.map(function (i) { return Number(i.stock_total); });

            new Chart(document.getElementById('stockChart'), {
                type: 'bar',
                data: { labels: labels, datasets: [{ label: 'Stock', data: valores, backgroundColor: '#d97706' }] },
                options: { responsive: true, indexAxis: 'y' },
            });
        });
    }

    function loadEmergencyChart() {
        apiRequest({ url: '/dashboard/emergency-by-priority', method: 'GET' }).done(function (d) {
            var labels = d.items.map(function (i) { return i.nombre; });
            var valores = d.items.map(function (i) { return Number(i.total); });

            new Chart(document.getElementById('emergencyChart'), {
                type: 'doughnut',
                data: { labels: labels, datasets: [{ data: valores, backgroundColor: paleta }] },
                options: { responsive: true },
            });
        });
    }

    loadKpis();
    loadBedChart();
    loadStockChart();
    loadEmergencyChart();
});
