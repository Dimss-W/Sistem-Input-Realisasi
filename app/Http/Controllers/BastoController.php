<?php

namespace App\Http\Controllers;

use App\Models\Basto;
use App\Models\Kontrak;
use App\Models\Realisasi;
use App\Models\Invoice;
use App\Models\User;
use App\Traits\LogsActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BastoController extends Controller
{
    use LogsActivity;

    public function __construct()
    {
        // DMO dapat membuat BASTO, SM dapat mereview/approve, QC dapat memverifikasi
        $this->middleware('role:dmo,admin')->only(['create', 'store', 'submitForReview', 'reuploadRevision', 'batchSubmit', 'destroy']);
        $this->middleware('role:osm_service_manager,admin')->only(['approve', 'reject']);
        $this->middleware('role:osm_qc,admin')->only(['qcVerify']);
    }

    // =========================================================================
    // INDEX — Daftar semua BASTO (filtered by role)
    // =========================================================================
    public function index(Request $request)
    {
        $filters = $request->only(['search', 'status', 'qc_status', 'project_id', 'dmo_user_id', 'sm_user_id', 'tab']);

        $query = Basto::with(['dmo', 'sm', 'qcUser'])->filter($filters);

        // Service Manager hanya melihat BASTO yang ditujukan padanya
        if (auth()->user()->hasRole('osm_service_manager')) {
            $query->where('sm_user_id', auth()->id());
        }

        // Tab Filter untuk DMO, SM & QC
        if (!empty($filters['tab'])) {
            if ($filters['tab'] === 'ready_to_approve') {
                $query->where('status', 'submitted')->where('qc_status', 'verified');
            } elseif ($filters['tab'] === 'waiting_qc') {
                $query->where('status', 'submitted')->where(function ($q) {
                    $q->whereNull('qc_status')->orWhere('qc_status', 'pending');
                });
            } elseif ($filters['tab'] === 'pending_review') {
                $query->where('status', 'submitted');
            } elseif ($filters['tab'] === 'approved') {
                $query->where('status', 'approved');
            } elseif ($filters['tab'] === 'rejected') {
                $query->where('status', 'rejected');
            } elseif ($filters['tab'] === 'qc_pending') {
                $query->where('status', 'submitted')
                      ->where(function ($q) {
                          $q->whereNull('qc_status')
                            ->orWhere('qc_status', 'pending');
                      });
            } elseif ($filters['tab'] === 'qc_revision' || $filters['tab'] === 'revision_needed') {
                $query->where('qc_status', 'revision_needed');
            } elseif ($filters['tab'] === 'qc_verified') {
                $query->where('qc_status', 'verified');
            } elseif ($filters['tab'] === 'dmo_draft') {
                $query->where('status', 'draft');
            } elseif ($filters['tab'] === 'dmo_in_progress') {
                $query->where('status', 'submitted')->where(function ($q) {
                    $q->whereNull('qc_status')->orWhere('qc_status', '!=', 'revision_needed');
                });
            }
        }

        $perPage = ($request->boolean('print') || $request->get('per_page') === 'all') ? 10000 : 20;
        $bastos = $query->orderBy('updated_at', 'desc')->paginate($perPage)->withQueryString();

        $statusCounts = [
            'draft'     => Basto::where('status', 'draft')->count(),
            'submitted' => Basto::where('status', 'submitted')->count(),
            'approved'  => Basto::where('status', 'approved')->count(),
            'rejected'  => Basto::where('status', 'rejected')->count(),
        ];

        // Scoped counts for Service Manager tabs
        $smBase = Basto::query();
        if (auth()->user()->hasRole('osm_service_manager')) {
            $smBase->where('sm_user_id', auth()->id());
        }

        $smTabCounts = [
            'ready_to_approve' => (clone $smBase)->where('status', 'submitted')->where('qc_status', 'verified')->count(),
            'waiting_qc'       => (clone $smBase)->where('status', 'submitted')->where(function ($q) {
                                      $q->whereNull('qc_status')->orWhere('qc_status', 'pending');
                                  })->count(),
            'pending_review'   => (clone $smBase)->where('status', 'submitted')->count(),
            'approved'         => (clone $smBase)->where('status', 'approved')->count(),
            'rejected'         => (clone $smBase)->where('status', 'rejected')->count(),
            'all'              => (clone $smBase)->count(),
        ];

        // Scoped counts for DMO tabs
        $dmoBase = Basto::query();
        if (auth()->user()->hasRole('dmo')) {
            $dmoBase->where('dmo_user_id', auth()->id());
        }

        $dmoTabCounts = [
            'all'             => (clone $dmoBase)->count(),
            'revision_needed' => (clone $dmoBase)->where('qc_status', 'revision_needed')->count(),
            'draft'           => (clone $dmoBase)->where('status', 'draft')->count(),
            'in_progress'     => (clone $dmoBase)->where('status', 'submitted')->where(function ($q) {
                                     $q->whereNull('qc_status')->orWhere('qc_status', '!=', 'revision_needed');
                                 })->count(),
            'approved'        => (clone $dmoBase)->where('status', 'approved')->count(),
        ];

        $qcCounts = [
            'pending'  => Basto::where('status', 'submitted')
                               ->where(function ($q) {
                                   $q->whereNull('qc_status')->orWhere('qc_status', 'pending');
                               })->count(),
            'revision' => Basto::where('qc_status', 'revision_needed')->count(),
            'verified' => Basto::where('qc_status', 'verified')->count(),
            'total'    => Basto::count(),
        ];

        // Dropdown
        $projectIds = Basto::distinct()->orderBy('project_id')->pluck('project_id');
        $smList     = User::where('role', 'osm_service_manager')->where('status', 'active')->get();

        return view('basto.index', compact('bastos', 'statusCounts', 'smTabCounts', 'dmoTabCounts', 'qcCounts', 'projectIds', 'smList', 'filters'));
    }

    // =========================================================================
    // CREATE — Form Pengajuan BASTO Baru
    // =========================================================================
    public function create()
    {
        $kontrakList = Kontrak::orderBy('project_name')->get([
            'project_id', 'project_name', 'service_manager',
            'contract_number', 'project_value', 'start_date', 'end_date', 'costbased', 'resource_management'
        ]);
        $smList      = User::where('role', 'osm_service_manager')->where('status', 'active')->get();

        return view('basto.create', compact('kontrakList', 'smList'));
    }

    // =========================================================================
    // STORE — Simpan Draft BASTO
    // =========================================================================
    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id'      => 'required|string|max:50',
            'project_name'    => 'required|string|max:255',
            'cost_no'         => 'nullable|string|max:100',
            'cost_value'      => 'nullable|numeric|min:0',
            'cost_date_start' => 'nullable|date',
            'cost_date_end'   => 'nullable|date|after_or_equal:cost_date_start',
            'cost_based'      => 'nullable|numeric|min:0',
            'supply_chain'    => 'nullable|string|max:255',
            'sm_user_id'      => 'required|exists:users,id',
            'notes'           => 'nullable|string|max:5000',
            'attachment_file' => 'nullable|file|mimes:pdf|max:10240',
        ], [
            'project_id.required'          => 'Project ID wajib diisi.',
            'project_name.required'        => 'Nama project wajib diisi.',
            'sm_user_id.required'          => 'Service Manager tujuan wajib dipilih.',
            'sm_user_id.exists'            => 'Service Manager yang dipilih tidak valid.',
            'cost_value.numeric'           => 'Cost Value harus berupa angka nominal.',
            'cost_based.numeric'           => 'Cost Based harus berupa angka nominal.',
            'cost_date_end.after_or_equal' => 'Cost Date (End) harus sama atau setelah Cost Date (Start).',
            'attachment_file.mimes'        => 'Berkas BASTO fisik harus berformat PDF.',
            'attachment_file.max'          => 'Ukuran file PDF maksimal 10MB.',
        ]);

        if ($request->hasFile('attachment_file')) {
            $path = $request->file('attachment_file')->store('basto_attachments', 'public');
            $validated['attachment_file'] = $path;
        }

        // Generate nomor BASTO unik: BASTO-YYYYMM-XXXX
        $validated['basto_number'] = 'BASTO-' . now()->format('Ym') . '-' . strtoupper(Str::random(4));
        $validated['dmo_user_id']  = auth()->id();
        $validated['status']       = 'submitted';
        $validated['submitted_at'] = now();

        $basto = Basto::create($validated);

        // Auto-sinkronisasi nomor SPK & vendor dari BASTO ke Kontrak & Realisasi
        $this->syncBastoToProject($basto);

        $this->logActivity('SUBMIT', 'basto', (string) $basto->id, null, $basto->toArray(), 'DMO membuat dan mengajukan BASTO');

        return redirect()->route('basto.show', $basto->id)
            ->with('success', "BASTO #{$basto->basto_number} berhasil dibuat dan dikirim ke Service Manager.");
    }

    // =========================================================================
    // SHOW — Detail BASTO
    // =========================================================================
    public function show(Basto $basto)
    {
        $basto->load(['dmo', 'sm']);
        return view('basto.show', compact('basto'));
    }

    // =========================================================================
    // EDIT — Form Edit BASTO (hanya jika masih draft)
    // =========================================================================
    public function edit(Basto $basto)
    {
        if ($basto->status !== 'draft') {
            return redirect()->route('basto.show', $basto->id)
                ->with('error', 'BASTO hanya dapat diedit saat masih berstatus Draft.');
        }

        $kontrakList = Kontrak::orderBy('project_name')->get([
            'project_id', 'project_name', 'service_manager',
            'contract_number', 'project_value', 'start_date', 'end_date', 'costbased', 'resource_management'
        ]);
        $smList      = User::where('role', 'osm_service_manager')->where('status', 'active')->get();

        return view('basto.edit', compact('basto', 'kontrakList', 'smList'));
    }

    // =========================================================================
    // UPDATE — Simpan Perubahan BASTO (Draft Only)
    // =========================================================================
    public function update(Request $request, Basto $basto)
    {
        if ($basto->status !== 'draft') {
            return redirect()->route('basto.show', $basto->id)
                ->with('error', 'BASTO yang sudah diajukan tidak dapat diedit.');
        }

        $validated = $request->validate([
            'project_id'      => 'required|string|max:50',
            'project_name'    => 'required|string|max:255',
            'cost_no'         => 'nullable|string|max:100',
            'cost_value'      => 'nullable|numeric|min:0',
            'cost_date_start' => 'nullable|date',
            'cost_date_end'   => 'nullable|date|after_or_equal:cost_date_start',
            'cost_based'      => 'nullable|numeric|min:0',
            'supply_chain'    => 'nullable|string|max:255',
            'sm_user_id'      => 'required|exists:users,id',
            'notes'           => 'nullable|string|max:5000',
        ], [
            'project_id.required'          => 'Project ID wajib diisi.',
            'project_name.required'        => 'Nama project wajib diisi.',
            'sm_user_id.required'          => 'Service Manager tujuan wajib dipilih.',
            'sm_user_id.exists'            => 'Service Manager yang dipilih tidak valid.',
            'cost_value.numeric'           => 'Cost Value harus berupa angka nominal.',
            'cost_based.numeric'           => 'Cost Based harus berupa angka nominal.',
            'cost_date_end.after_or_equal' => 'Cost Date (End) harus sama atau setelah Cost Date (Start).',
        ]);

        $oldData = $basto->toArray();
        $basto->update($validated);
        $this->logActivity('UPDATE', 'basto', (string) $basto->id, $oldData, $basto->toArray(), 'Edit BASTO draft');

        return redirect()->route('basto.show', $basto->id)
            ->with('success', 'BASTO berhasil diperbarui.');
    }

    // =========================================================================
    // SUBMIT — DMO mengajukan BASTO ke SM untuk direview
    // =========================================================================
    public function submitForReview(Request $request, Basto $basto)
    {
        if ($basto->status !== 'draft') {
            return redirect()->route('basto.show', $basto->id)
                ->with('error', 'Hanya BASTO berstatus Draft yang dapat diajukan.');
        }

        $oldData = $basto->toArray();
        $basto->update([
            'status'       => 'submitted',
            'submitted_at' => now(),
        ]);

        $this->logActivity('SUBMIT', 'basto', (string) $basto->id, $oldData, $basto->toArray(), 'DMO mengajukan BASTO ke Service Manager');

        return redirect()->route('basto.show', $basto->id)
            ->with('success', "BASTO #{$basto->basto_number} berhasil diajukan ke Service Manager.");
    }

    // =========================================================================
    // BATCH SUBMIT — DMO mengajukan beberapa BASTO draft sekaligus ke SM
    // =========================================================================
    public function batchSubmit(Request $request)
    {
        $validated = $request->validate([
            'basto_ids'   => 'required|array|min:1',
            'basto_ids.*' => 'integer|exists:bastos,id',
        ], [
            'basto_ids.required' => 'Pilih setidaknya satu draf BASTO untuk diajukan.',
            'basto_ids.min'      => 'Pilih setidaknya satu draf BASTO untuk diajukan.',
        ]);

        $isAdmin = auth()->user()->hasRole('admin');
        $userId  = auth()->id();

        $query = Basto::whereIn('id', $validated['basto_ids'])
            ->where('status', 'draft');

        if (!$isAdmin) {
            $query->where('dmo_user_id', $userId);
        }

        $bastosToSubmit = $query->get();

        if ($bastosToSubmit->isEmpty()) {
            return redirect()->route('basto.index')
                ->with('error', 'Tidak ada draf BASTO yang valid untuk diajukan oleh Anda.');
        }

        $count = 0;
        $submittedNumbers = [];
        foreach ($bastosToSubmit as $basto) {
            $oldData = $basto->toArray();
            $basto->update([
                'status'       => 'submitted',
                'submitted_at' => now(),
            ]);
            $submittedNumbers[] = $basto->basto_number;
            $count++;

            $this->logActivity('SUBMIT', 'basto', (string) $basto->id, $oldData, $basto->toArray(), 'DMO batch submit BASTO ke Service Manager');
        }

        return redirect()->route('basto.index')
            ->with('success', "Berhasil mengajukan {$count} BASTO sekaligus ke Service Manager (" . implode(', ', array_slice($submittedNumbers, 0, 3)) . ($count > 3 ? " dan " . ($count - 3) . " lainnya" : "") . ").");
    }

    // =========================================================================
    // REUPLOAD REVISION — DMO mengunggah berkas revisi fisik pasca temuan QC
    // =========================================================================
    public function reuploadRevision(Request $request, Basto $basto)
    {
        // Pastikan hanya BASTO yang berstatus revision_needed yang dapat diunggah ulang
        if ($basto->qc_status !== 'revision_needed') {
            return redirect()->back()->with('error', 'Hanya BASTO yang memerlukan revisi mutu QC yang dapat diunggah ulang berkasnya.');
        }

        // Pastikan user adalah pengaju DMO itu sendiri atau admin
        if (!auth()->user()->hasRole('admin') && $basto->dmo_user_id !== auth()->id()) {
            return redirect()->back()->with('error', 'Anda hanya dapat mengunggah revisi untuk BASTO yang Anda ajukan.');
        }

        $validated = $request->validate([
            'attachment_file' => 'required|file|mimes:pdf,jpg,jpeg,png,webp|max:10240',
            'revision_notes'  => 'nullable|string|max:2000',
        ], [
            'attachment_file.required' => 'Berkas lampiran fisik revisi wajib diunggah.',
            'attachment_file.mimes'    => 'Berkas lampiran harus berformat PDF, JPG, PNG, atau WebP.',
            'attachment_file.max'      => 'Ukuran berkas fisik revisi maksimal 10MB.',
        ]);

        $oldData = $basto->toArray();

        // Hapus file lama jika ada
        if ($basto->attachment_file && Storage::disk('public')->exists($basto->attachment_file)) {
            Storage::disk('public')->delete($basto->attachment_file);
        }

        // Simpan berkas fisik baru
        $path = $request->file('attachment_file')->store('basto_attachments', 'public');

        // Gabungkan catatan revisi DMO
        $timestamp = now()->format('d/m/Y H:i');
        $userNotes = trim($validated['revision_notes'] ?? '');
        $newNotes = $basto->notes;
        if (!empty($userNotes)) {
            $newNotes = ($newNotes ? $newNotes . "\n\n" : "") . "[Revisi DMO {$timestamp}]: " . $userNotes;
        }

        // Reset status QC kembali ke pending, kosongkan kode sertifikasi
        $basto->update([
            'attachment_file'      => $path,
            'notes'                => $newNotes,
            'qc_status'            => 'pending',
            'qc_verification_code' => null,
            'qc_verified_at'       => null,
            'submitted_at'         => now(),
        ]);

        $this->logActivity('UPDATE', 'basto', (string) $basto->id, $oldData, $basto->toArray(), "DMO mengunggah berkas revisi BASTO #{$basto->basto_number} ke QC");

        return redirect()->route('basto.show', $basto->id)
            ->with('success', "Berkas fisik revisi BASTO #{$basto->basto_number} berhasil dikirim! Status QC kini kembali 'Menunggu Pemeriksaan'.");
    }

    // =========================================================================
    // APPROVE — Service Manager menyetujui BASTO
    // =========================================================================
    public function approve(Request $request, Basto $basto)
    {
        if ($basto->status !== 'submitted') {
            return redirect()->route('basto.show', $basto->id)
                ->with('error', 'Hanya BASTO berstatus Submitted yang dapat disetujui.');
        }

        if ($basto->sm_user_id !== auth()->id() && !auth()->user()->hasRole('admin')) {
            return redirect()->route('basto.index')
                ->with('error', 'Anda tidak berwenang menyetujui BASTO ini.');
        }

        // QC Gatekeeper: Dokumen fisik/teknis harus lolos verifikasi mutu QC sebelum disetujui Service Manager
        if ($basto->qc_status !== 'verified' && !auth()->user()->hasRole('admin')) {
            $reason = $basto->qc_status === 'revision_needed' 
                ? "sedang dalam status 'Perlu Revisi Mutu'" 
                : "belum diperiksa / diverifikasi oleh tim Quality Control";
            return redirect()->back()
                ->with('error', "Persetujuan Ditolak (QC Gatekeeper): BASTO #{$basto->basto_number} {$reason}. Dokumen fisik/teknis harus dinyatakan 'Lolos Verifikasi QC' terlebih dahulu sebelum dapat disetujui Service Manager.");
        }

        $oldData = $basto->toArray();
        $basto->update([
            'status'      => 'approved',
            'reviewed_at' => now(),
        ]);

        // Auto-sinkronisasi nomor SPK & vendor dari BASTO ke Kontrak & Realisasi
        $this->syncBastoToProject($basto);

        $this->logActivity('APPROVE', 'basto', (string) $basto->id, $oldData, $basto->toArray(), 'Service Manager approve BASTO');

        return redirect()->route('basto.show', $basto->id)
            ->with('success', "BASTO #{$basto->basto_number} telah DISETUJUI.");
    }

    // =========================================================================
    // BATCH APPROVE — Service Manager menyetujui beberapa BASTO sekaligus
    // =========================================================================
    public function batchApprove(Request $request)
    {
        $validated = $request->validate([
            'basto_ids'   => 'required|array|min:1',
            'basto_ids.*' => 'integer|exists:bastos,id',
        ], [
            'basto_ids.required' => 'Pilih setidaknya satu BASTO untuk disetujui.',
            'basto_ids.min'      => 'Pilih setidaknya satu BASTO untuk disetujui.',
        ]);

        $isAdmin = auth()->user()->hasRole('admin');
        $smId    = auth()->id();

        $query = Basto::whereIn('id', $validated['basto_ids'])
            ->where('status', 'submitted');

        if (!$isAdmin) {
            $query->where('sm_user_id', $smId);
        }

        $bastosToApprove = $query->get();

        if ($bastosToApprove->isEmpty()) {
            return redirect()->route('basto.index')
                ->with('error', 'Tidak ada BASTO berstatus Submitted yang valid untuk disetujui oleh Anda.');
        }

        // QC Gatekeeper: Cegah persetujuan massal bila ada BASTO yang belum 'verified'
        $unverifiedBastos = $bastosToApprove->where('qc_status', '!==', 'verified');
        if ($unverifiedBastos->isNotEmpty() && !$isAdmin) {
            $unverifiedNumbers = $unverifiedBastos->pluck('basto_number')->implode(', ');
            return redirect()->route('basto.index')
                ->with('error', "Persetujuan Massal Dibatalkan (QC Gatekeeper): Terdapat dokumen BASTO yang belum lolos verifikasi mutu QC ({$unverifiedNumbers}). Hanya dokumen yang telah diverifikasi dan berstatus 'Lolos QC' yang dapat disetujui.");
        }

        $count = 0;
        $approvedNumbers = [];
        foreach ($bastosToApprove as $basto) {
            $oldData = $basto->toArray();
            $basto->update([
                'status'      => 'approved',
                'reviewed_at' => now(),
            ]);

            // Auto-sinkronisasi nomor SPK & vendor dari BASTO ke Kontrak & Realisasi
            $this->syncBastoToProject($basto);

            $approvedNumbers[] = $basto->basto_number;
            $count++;

            $this->logActivity('APPROVE', 'basto', (string) $basto->id, $oldData, $basto->toArray(), 'Service Manager batch approve BASTO');
        }

        return redirect()->route('basto.index')
            ->with('success', "Berhasil menyetujui {$count} BASTO sekaligus (" . implode(', ', array_slice($approvedNumbers, 0, 3)) . ($count > 3 ? " dan " . ($count - 3) . " lainnya" : "") . ").");
    }

    // =========================================================================
    // REJECT — Service Manager menolak BASTO
    // =========================================================================
    public function reject(Request $request, Basto $basto)
    {
        if ($basto->status !== 'submitted') {
            return redirect()->route('basto.show', $basto->id)
                ->with('error', 'Hanya BASTO berstatus Submitted yang dapat ditolak.');
        }

        if ($basto->sm_user_id !== auth()->id() && !auth()->user()->hasRole('admin')) {
            return redirect()->route('basto.index')
                ->with('error', 'Anda tidak berwenang menolak BASTO ini.');
        }

        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:1000',
        ], [
            'rejection_reason.required' => 'Alasan penolakan wajib diisi.',
        ]);

        $oldData = $basto->toArray();
        $basto->update([
            'status'           => 'rejected',
            'rejection_reason' => $validated['rejection_reason'],
            'reviewed_at'      => now(),
        ]);

        $this->logActivity('REJECT', 'basto', (string) $basto->id, $oldData, $basto->toArray(), 'Service Manager reject BASTO: ' . $validated['rejection_reason']);

        return redirect()->route('basto.show', $basto->id)
            ->with('warning', "BASTO #{$basto->basto_number} DITOLAK.");
    }

    // =========================================================================
    // QC VERIFY — Quality Control memverifikasi checklist teknis BASTO
    // =========================================================================
    public function qcVerify(Request $request, Basto $basto)
    {
        $validated = $request->validate([
            'qc_status'    => 'required|in:verified,revision_needed',
            'qc_notes'     => 'nullable|string|max:2000',
            'qc_checklist' => 'nullable|array',
        ], [
            'qc_status.required' => 'Status verifikasi QC wajib dipilih.',
            'qc_status.in'       => 'Status QC tidak valid.',
        ]);

        $oldData = $basto->toArray();

        // Siapkan standarisasi 4 kriteria checklist QC digital
        $checklistInput = $request->input('qc_checklist', []);
        $checklist = [
            'admin_doc'         => !empty($checklistInput['admin_doc']),
            'spk_compliance'    => !empty($checklistInput['spk_compliance']),
            'baut_teknis'       => !empty($checklistInput['baut_teknis']),
            'physical_evidence' => !empty($checklistInput['physical_evidence']),
        ];

        // Generate atau revoke kode verifikasi sertifikasi QC
        $verificationCode = $basto->qc_verification_code;
        if ($validated['qc_status'] === 'verified') {
            if (!$verificationCode) {
                $verificationCode = 'QC-PASS-' . now()->format('Ym') . '-' . strtoupper(Str::random(6));
            }
        } else {
            // Jika revision needed, reset kode sertifikasi
            $verificationCode = null;
        }

        $basto->update([
            'qc_user_id'           => auth()->id(),
            'qc_status'            => $validated['qc_status'],
            'qc_notes'             => $validated['qc_notes'],
            'qc_checklist'         => $checklist,
            'qc_verification_code' => $verificationCode,
            'qc_verified_at'       => now(),
        ]);

        $statusText = $validated['qc_status'] === 'verified' 
            ? "TERVERIFIKASI MUTU (Sertifikat: {$verificationCode})" 
            : 'MEMERLUKAN REVISI MUTU';

        $this->logActivity('UPDATE', 'basto', (string) $basto->id, $oldData, $basto->toArray(), "QC (" . auth()->user()->name . ") verifikasi BASTO #{$basto->basto_number}: {$statusText}");

        return redirect()->back()
            ->with('success', "Inspeksi Mutu QC untuk BASTO #{$basto->basto_number} berhasil disimpan ({$statusText}).");
    }

    // =========================================================================
    // ATTACHMENT — Buka/Unduh Berkas Fisik PDF BASTO
    // =========================================================================
    public function downloadAttachment(Basto $basto)
    {
        if (!$basto->attachment_file || !Storage::disk('public')->exists($basto->attachment_file)) {
            abort(404, 'Berkas fisik BASTO tidak ditemukan pada server storage.');
        }

        $path = Storage::disk('public')->path($basto->attachment_file);
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        $mimeTypes = [
            'pdf'  => 'application/pdf',
            'jpg'  => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png'  => 'image/png',
            'webp' => 'image/webp',
        ];
        $contentType = $mimeTypes[$extension] ?? (function_exists('mime_content_type') ? mime_content_type($path) : 'application/pdf');

        return response()->file($path, [
            'Content-Type' => $contentType,
            'Content-Disposition' => 'inline; filename="' . $basto->basto_number . '.' . $extension . '"',
        ]);
    }

    // =========================================================================
    // DESTROY — Hapus BASTO Draft
    // =========================================================================
    public function destroy(Basto $basto)
    {
        if ($basto->status !== 'draft' && !auth()->user()->hasRole('admin')) {
            return redirect()->route('basto.index')
                ->with('error', 'Hanya BASTO berstatus Draft yang dapat dihapus. Hubungi Administrator untuk penghapusan.');
        }

        $projectId = $basto->project_id;
        $attachment = $basto->attachment_file;
        $oldData = $basto->toArray();

        // Hapus file fisik lampiran jika ada
        if ($attachment && Storage::disk('public')->exists($attachment)) {
            Storage::disk('public')->delete($attachment);
        }

        $basto->delete();
        $this->logActivity('DELETE', 'basto', (string) $basto->id, $oldData, null, 'Hapus BASTO draft');

        // Auto-cleanup master kontrak jika sudah tidak ada sisa transaksi untuk proyek ini
        if ($projectId) {
            $hasRemainingRealisasi = \App\Models\Realisasi::where('project_id', $projectId)->exists();
            $hasBasto             = Basto::where('project_id', $projectId)->exists();
            $hasInvoice           = \App\Models\Invoice::where('project_id', $projectId)->exists();

            if (!$hasRemainingRealisasi && !$hasBasto && !$hasInvoice) {
                \App\Models\Kontrak::where('project_id', $projectId)->delete();
            }
        }

        return redirect()->route('basto.index')
            ->with('success', 'BASTO berhasil dihapus.');
    }

    /**
     * Sinkronisasi data BASTO (Nomor SPK/Kontrak & Vendor) ke Master Kontrak & Realisasi.
     */
    private function syncBastoToProject(Basto $basto): void
    {
        if (!empty($basto->cost_no)) {
            Kontrak::where('project_id', $basto->project_id)
                ->where(function ($q) {
                    $q->whereNull('contract_number')->orWhere('contract_number', '');
                })
                ->update(['contract_number' => $basto->cost_no]);
        }

        if (!empty($basto->supply_chain)) {
            Realisasi::where('project_id', $basto->project_id)
                ->where(function ($q) {
                    $q->whereNull('vendor')->orWhere('vendor', '')->orWhere('vendor', 'Umum / Tidak Terdata');
                })
                ->update(['vendor' => $basto->supply_chain]);

            Kontrak::where('project_id', $basto->project_id)
                ->where(function ($q) {
                    $q->whereNull('resource_management')->orWhere('resource_management', '');
                })
                ->update(['resource_management' => $basto->supply_chain]);
        }
    }
}
