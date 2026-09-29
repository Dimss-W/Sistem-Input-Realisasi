@extends('layouts.main')

@section('title', 'Kalender Pembayaran Kontrak')
@section('page-title', 'Kalender Pembayaran & Penagihan')

@section('breadcrumb')
    <li class="breadcrumb-item active"><i class="bi bi-calendar3 me-1"></i>Kalender Pembayaran</li>
@endsection

@section('content')
<!-- Header Filter Banner -->
<div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4" 
     style="background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 60%, #0284c7 100%);">
    <div class="card-body p-4 p-lg-4 text-white">
        <div class="row align-items-center g-3">
            <div class="col-lg-6">
                <div class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill mb-2" 
                     style="background: rgba(255, 255, 255, 0.15); font-size: 0.75rem; font-weight: 600; border: 1px solid rgba(255, 255, 255, 0.25);">
                    <i class="bi bi-calendar-check-fill text-warning"></i>
                    Schedule Monitoring &bull; PT PGAS Telekomunikasi Nusantara (PGNCOM)
                </div>
                <h3 class="fw-bold text-white mb-1" style="letter-spacing: -0.02em;">
                    Kalender Pembayaran & Jatuh Tempo Kontrak
                </h3>
                <p class="text-white text-opacity-80 small mb-0">
                    Pantau secara visual jadwal penagihan, termin pembayaran, dan masa berlaku kontrak proyek.
                </p>
            </div>
            <div class="col-lg-6">
                <div class="row g-2 align-items-center justify-content-lg-end">
                    <div class="col-sm-7">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                            <input type="text" id="calendarSearch" class="form-control border-start-0" placeholder="Cari client, ID, atau project..." style="height: 42px; font-size: 0.88rem;">
                        </div>
                    </div>
                    <div class="col-sm-5">
                        <select id="yearFilter" class="form-select" style="height: 42px; font-size: 0.88rem; font-weight: 600;">
                            <option value="">Semua Tahun</option>
                            <option value="2025">Tahun 2025</option>
                            <option value="2026" selected>Tahun 2026</option>
                            <option value="2027">Tahun 2027</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    <!-- Sidebar Info Panel (Left) -->
    <div class="col-xl-3 col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 h-100 mb-4">
            <div class="card-header bg-white border-0 pt-4 px-4 pb-2 d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-bell-fill text-warning me-1.5"></i>Penagihan Terdekat</h6>
                    <p class="text-muted small mb-0">Jatuh tempo terdekat</p>
                </div>
                <span class="badge bg-warning bg-opacity-15 text-dark fw-bold px-2.5 py-1 rounded-pill fs-8">
                    Upcoming
                </span>
            </div>
            <div class="card-body px-4 pb-4">
                <div class="d-flex flex-column gap-2.5" id="upcomingList" style="max-height: 540px; overflow-y: auto;">
                    @php
                        $upcoming = collect($events)
                            ->sortBy('start')
                            ->take(15);
                    @endphp

                    @forelse($upcoming as $up)
                        <div class="upcoming-item p-3 rounded-3 border bg-light bg-opacity-50 hover-scale transition-all cursor-pointer" 
                             onclick="showEventDetail('{{ $up['id'] }}')">
                            <div class="d-flex justify-content-between align-items-center mb-1.5">
                                <span class="badge bg-warning bg-opacity-20 text-dark fw-bold px-2 py-1 rounded" style="font-size: 0.7rem; color: #b45309 !important; border: 1px solid rgba(245, 158, 11, 0.3);">
                                    <i class="bi bi-calendar-event me-1"></i>{{ date('d M Y', strtotime($up['start'])) }}
                                </span>
                                <code class="fw-bold text-primary fs-8 px-1.5 py-0.5 rounded bg-primary bg-opacity-10">
                                    {{ $up['id'] }}
                                </code>
                            </div>
                            <div class="fw-bold text-dark small mb-1 text-truncate" title="{{ $up['title'] }}">
                                {{ $up['title'] }}
                            </div>
                            <div class="text-secondary small d-flex align-items-center justify-content-between">
                                <span><i class="bi bi-wallet2 text-success me-1"></i>Jatuh Tempo</span>
                                <i class="bi bi-chevron-right fs-8 text-muted"></i>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-5 text-muted">
                            <i class="bi bi-calendar-check fs-2 mb-2 d-block text-secondary opacity-50"></i>
                            Tidak ada jadwal penagihan kontrak terdekat.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Calendar View Panel (Right) -->
    <div class="col-xl-9 col-lg-8">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-header bg-white border-0 pt-4 px-4 pb-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-primary rounded-circle p-1.5" style="width:10px; height:10px;"></span>
                    <h5 class="fw-bold mb-0 text-dark">Tampilan Kalender Proyek</h5>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge px-3 py-1.5 rounded-pill d-inline-flex align-items-center gap-1.5 fs-8" 
                          style="background: #fef3c7; color: #b45309; border: 1px solid #fde047; font-weight: 700;">
                        <span class="rounded-circle" style="width: 8px; height: 8px; background-color: #d97706; display: inline-block;"></span>
                        Tanggal Ada Jadwal Pembayaran
                    </span>
                </div>
            </div>
            <div class="card-body p-4 pt-2">
                <!-- Calendar Div Container -->
                <div id="fullCalendar" class="rounded-3" style="min-height: 620px;"></div>
            </div>
        </div>
    </div>
</div>

<!-- Premium Detail Modal -->
<div class="modal fade" id="eventDetailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow-lg overflow-hidden">
            <div class="modal-header border-0 pb-3 px-4 pt-4 text-white" style="background: linear-gradient(135deg, #0f172a, #1e3a8a);">
                <div>
                    <span class="badge bg-white bg-opacity-20 text-white fw-bold px-2.5 py-1 rounded-pill fs-8 mb-1">
                        Detail Penagihan Kontrak
                    </span>
                    <h5 class="modal-title fw-bold text-white mb-0" id="modalProjectIdHeader">--</h5>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="p-3 rounded-4 mb-4 border d-flex gap-3 align-items-center" style="background: #f0f9ff; border-color: #bae6fd !important;">
                    <div class="rounded-circle bg-primary text-white p-3 d-flex justify-content-center align-items-center shadow-sm" style="width: 52px; height: 52px;">
                        <i class="bi bi-wallet2 fs-4"></i>
                    </div>
                    <div>
                        <div class="text-secondary small fw-bold text-uppercase fs-8">Nilai Pembayaran / Kontrak</div>
                        <h3 class="fw-bold text-primary mb-0" id="modalValue" style="letter-spacing: -0.02em;">Rp 0</h3>
                    </div>
                </div>

                <div class="d-flex flex-column gap-3">
                    <div class="p-2.5 bg-light rounded-3">
                        <label class="text-secondary fs-8 text-uppercase fw-bold"><i class="bi bi-hash me-1 text-primary"></i> Project ID</label>
                        <div class="fw-bold text-dark fs-6 mt-0.5" id="modalProjectId">--</div>
                    </div>
                    <div class="p-2.5 bg-light rounded-3">
                        <label class="text-secondary fs-8 text-uppercase fw-bold"><i class="bi bi-folder-fill me-1 text-primary"></i> Nama Project</label>
                        <div class="fw-bold text-dark mt-0.5" id="modalProjectName">--</div>
                    </div>
                    <div class="p-2.5 bg-light rounded-3">
                        <label class="text-secondary fs-8 text-uppercase fw-bold"><i class="bi bi-building me-1 text-primary"></i> Client / Pelanggan</label>
                        <div class="fw-bold text-dark mt-0.5" id="modalClient">--</div>
                    </div>
                    <div class="p-2.5 bg-light rounded-3">
                        <label class="text-secondary fs-8 text-uppercase fw-bold"><i class="bi bi-calendar-check-fill me-1 text-success"></i> Tanggal Jatuh Tempo</label>
                        <div class="fw-bold text-dark mt-0.5" id="modalDate">--</div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top-0 pt-0 pb-4 px-4">
                <button type="button" class="btn btn-primary w-100 rounded-3 py-2.5 fw-bold shadow-sm" data-bs-dismiss="modal">Tutup Rincian</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<!-- FullCalendar CSS via CDN -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css">
<style>
    .fc {
        font-family: 'Plus Jakarta Sans', 'Inter', sans-serif;
    }
    .fc-theme-standard td, .fc-theme-standard th {
        border-color: #e2e8f0;
    }
    .fc-theme-standard .fc-scrollgrid {
        border-color: #cbd5e1;
        border-radius: 14px;
        overflow: hidden;
    }
    .fc .fc-toolbar-title {
        font-size: 1.2rem;
        font-weight: 800;
        color: #0f172a;
    }
    .fc .fc-button-primary {
        background-color: #0284c7;
        border-color: #0284c7;
        font-weight: 700;
        border-radius: 10px;
        font-size: 0.85rem;
        padding: 6px 14px;
    }
    .fc .fc-button-primary:hover {
        background-color: #0369a1;
        border-color: #0369a1;
    }
    .fc .fc-button-primary:disabled {
        background-color: #cbd5e1;
        border-color: #cbd5e1;
    }
    .fc-event {
        cursor: pointer;
        padding: 4px 8px;
        font-weight: 700;
        border-radius: 8px;
        font-size: 0.78rem;
        border: none !important;
        background: linear-gradient(135deg, #0284c7, #1e3a8a) !important;
        color: #ffffff !important;
        box-shadow: 0 2px 6px rgba(2, 132, 199, 0.3);
        transition: transform 0.2s ease;
    }
    .fc-event:hover {
        transform: scale(1.02);
    }
    .upcoming-item {
        border-color: #e2e8f0 !important;
        transition: all 0.2s ease-in-out;
    }
    .upcoming-item:hover {
        background-color: #fef3c7 !important;
        border-color: #fde047 !important;
        transform: translateY(-2px);
    }
    /* Scrollbar style */
    #upcomingList::-webkit-scrollbar {
        width: 5px;
    }
    #upcomingList::-webkit-scrollbar-track {
        background: transparent;
    }
    #upcomingList::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 10px;
    }
    #upcomingList::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }
    
    /* Year view compact grids */
    .fc-multimonth {
        display: grid !important;
        grid-template-columns: repeat(4, 1fr) !important;
        gap: 8px !important;
    }
    .fc-multimonth-month {
        width: 100% !important;
        padding: 6px !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 12px !important;
        box-shadow: 0 2px 4px rgba(0,0,0,0.02) !important;
    }
    .fc-multimonth-title {
        font-size: 0.88rem !important;
        font-weight: 800 !important;
        padding: 4px 0 !important;
        color: #0f172a !important;
    }
    .fc-multimonth-month .fc-daygrid-day-number {
        font-size: 0.72rem !important;
        padding: 1px 3px !important;
        font-weight: 600;
    }
    .fc-multimonth-month .fc-col-header-cell-cushion {
        font-size: 0.72rem !important;
        padding: 2px 0 !important;
        font-weight: 700;
        color: #64748b;
    }
    .fc-multimonth-month .fc-daygrid-day {
        min-height: 26px !important;
    }
</style>
@endpush

@push('scripts')
<!-- FullCalendar JS via CDN -->
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
<script>
    const rawEvents = @json($events);
    let calendar;

    document.addEventListener('DOMContentLoaded', function() {
        const calendarEl = document.getElementById('fullCalendar');
        
        calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            locale: 'id',
            multiMonthMaxColumns: 4,
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'multiMonthYear,dayGridMonth,timeGridWeek,listMonth'
            },
            buttonText: {
                today: 'Hari Ini',
                multiMonthYear: 'Tahun',
                month: 'Bulan',
                week: 'Minggu',
                list: 'Daftar'
            },
            events: rawEvents,
            eventClick: function(info) {
                showEventDetailByObj(info.event);
            },
            // Highlight cells that contain payments
            dayCellDidMount: function(info) {
                const d = info.date;
                const year = d.getFullYear();
                const month = String(d.getMonth() + 1).padStart(2, '0');
                const day = String(d.getDate()).padStart(2, '0');
                const dateStr = `${year}-${month}-${day}`;

                const hasEvent = rawEvents.some(e => e.start === dateStr);
                if (hasEvent) {
                    // Soft amber background & border to highlight dates with payment
                    info.el.style.backgroundColor = '#fef3c7'; 
                    info.el.style.boxShadow = 'inset 0 0 0 2px #fde047';
                    
                    // Add micro indicators in the cell header
                    const cellHeader = info.el.querySelector('.fc-daygrid-day-top');
                    if (cellHeader) {
                        const badge = document.createElement('span');
                        badge.className = 'badge bg-danger rounded-circle p-1 ms-1';
                        badge.style.width = '7px';
                        badge.style.height = '7px';
                        badge.style.display = 'inline-block';
                        badge.title = 'Ada Pembayaran/Jatuh Tempo';
                        cellHeader.appendChild(badge);
                    }
                }
            }
        });

        calendar.render();

        // Implement Search & Year Filter
        const searchInput = document.getElementById('calendarSearch');
        const yearFilter = document.getElementById('yearFilter');

        function filterEvents(isInitial = false) {
            const searchVal = searchInput.value.toLowerCase();
            const yearVal = yearFilter.value;

            const filtered = rawEvents.filter(e => {
                const matchesSearch = e.title.toLowerCase().includes(searchVal) || 
                                     e.description.toLowerCase().includes(searchVal);
                
                const eventYear = e.start.split('-')[0];
                const matchesYear = yearVal === '' || eventYear === yearVal;

                return matchesSearch && matchesYear;
            });
            
            calendar.removeAllEvents();
            calendar.addEventSource(filtered);

            // Navigate to selected year's start if specified and NOT initial load
            if (yearVal !== '' && !isInitial) {
                calendar.gotoDate(`${yearVal}-01-01`);
            }
        }

        searchInput.addEventListener('input', () => filterEvents(false));
        yearFilter.addEventListener('change', () => filterEvents(false));

        // Run initial filter on load, keeping the calendar focused on today's date
        filterEvents(true);
    });

    // Helper functions to show Modal Detail
    function showEventDetail(compositeId) {
        const ev = rawEvents.find(e => e.id === compositeId);
        if (ev) {
            populateAndShowModal(ev);
        }
    }

    function showEventDetailByObj(fcEvent) {
        const id = fcEvent.id;
        const ev = rawEvents.find(e => e.id === id);
        if (ev) {
            populateAndShowModal(ev);
        }
    }

    function populateAndShowModal(ev) {
        // Parse composite description
        const descLines = ev.description.split('\n');
        let projectId = '';
        let projectName = '';
        let client = '';
        let value = '';
        let date = '';

        descLines.forEach(line => {
            if (line.startsWith('Project ID:')) projectId = line.replace('Project ID:', '').trim();
            if (line.startsWith('Nama Project:')) projectName = line.replace('Nama Project:', '').trim();
            if (line.startsWith('Client:')) client = line.replace('Client:', '').trim();
            if (line.startsWith('Nilai:')) value = line.replace('Nilai:', '').trim();
            if (line.startsWith('Tanggal Jatuh Tempo:')) date = line.replace('Tanggal Jatuh Tempo:', '').trim();
        });

        document.getElementById('modalValue').innerText = value || 'Rp 0';
        document.getElementById('modalProjectIdHeader').innerText = projectId || 'Jadwal Kontrak';
        document.getElementById('modalProjectId').innerText = projectId || '--';
        document.getElementById('modalProjectName').innerText = projectName || '--';
        document.getElementById('modalClient').innerText = client || '--';
        document.getElementById('modalDate').innerText = date || '--';

        const myModal = new bootstrap.Modal(document.getElementById('eventDetailModal'));
        myModal.show();
    }
</script>
@endpush
