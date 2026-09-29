<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MasterVendor;
use App\Models\MasterClient;
use App\Models\Kontrak;
use App\Models\User;
use App\Models\Realisasi;
use App\Models\ActivityLog;
use App\Models\AppSetting;
use Illuminate\Support\Facades\DB;

class MasterDataController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin,dmo']);
        $this->middleware('role:admin')->only(['updateSigners']);
        $this->middleware('role:dmo')->only(['storeProject', 'updateProject', 'deleteProject']);
    }

    public function index(Request $request)
    {
        $tab = $request->input('tab', 'projects');
        $search = $request->input('search');

        // Master Proyek & Pagu Kontrak
        $projectQuery = Kontrak::query();
        if ($search && $tab === 'projects') {
            $projectQuery->where(function($q) use ($search) {
                $q->where('project_id', 'like', "%{$search}%")
                  ->orWhere('project_name', 'like', "%{$search}%")
                  ->orWhere('service_manager', 'like', "%{$search}%")
                  ->orWhere('project_client', 'like', "%{$search}%")
                  ->orWhere('contract_number', 'like', "%{$search}%");
            });
        }
        $projects = $projectQuery->orderBy('project_id', 'asc')->paginate(15, ['*'], 'project_page');

        // Agregasi realisasi per project_id untuk live budget calculation
        $allProjectIds = Kontrak::pluck('project_id')->toArray();
        $realisasiSums = Realisasi::groupBy('project_id')
            ->whereIn('project_id', $allProjectIds)
            ->select('project_id', DB::raw('SUM(realisasi_biaya_final) as total_spent'))
            ->pluck('total_spent', 'project_id')
            ->toArray();

        $totalProjects = Kontrak::count();
        $totalPagu = (float) Kontrak::sum('project_value');
        $totalRealisasiPagu = (float) Realisasi::sum('realisasi_biaya_final');

        // Service Manager & Client lists untuk formulir modal
        $smDropdownList = User::where('role', 'osm_service_manager')->orderBy('name')->pluck('name')->toArray();
        $extraSMs = Kontrak::whereNotNull('service_manager')->where('service_manager', '!=', '')->distinct()->pluck('service_manager')->toArray();
        $smList = array_values(array_unique(array_filter(array_merge($smDropdownList, $extraSMs))));
        sort($smList);

        $clientList = MasterClient::orderBy('nama_client')->pluck('nama_client')->toArray();
        if (empty($clientList)) {
            $clientList = ['PT PGN Tbk', 'PERTAMINA GAS', 'PGN SOLUTION', 'EXTERNAL'];
        }

        // Master Vendor
        $vendorQuery = MasterVendor::query();
        if ($search && $tab === 'vendors') {
            $vendorQuery->where(function($q) use ($search) {
                $q->where('nama_vendor', 'like', "%{$search}%")
                  ->orWhere('kode_vendor', 'like', "%{$search}%")
                  ->orWhere('pic_vendor', 'like', "%{$search}%");
            });
        }
        $vendors = $vendorQuery->orderBy('nama_vendor', 'asc')->paginate(15, ['*'], 'vendor_page');

        // Master Client
        $clientQuery = MasterClient::query();
        if ($search && $tab === 'clients') {
            $clientQuery->where(function($q) use ($search) {
                $q->where('nama_client', 'like', "%{$search}%")
                  ->orWhere('kode_client', 'like', "%{$search}%")
                  ->orWhere('kategori', 'like', "%{$search}%");
            });
        }
        $clients = $clientQuery->orderBy('nama_client', 'asc')->paginate(15, ['*'], 'client_page');

        $totalVendors = MasterVendor::count();
        $totalClients = MasterClient::count();
        $signers = AppSetting::whereIn('group', ['signers', 'organization'])->pluck('value', 'key')->toArray();

        return view('admin.master_data.index', compact(
            'projects',
            'realisasiSums',
            'totalProjects',
            'totalPagu',
            'totalRealisasiPagu',
            'smList',
            'clientList',
            'vendors',
            'clients',
            'tab',
            'search',
            'totalVendors',
            'totalClients',
            'signers'
        ));
    }

    /**
     * Sinkronisasi otomatis master data dari transaksi realisasi yang sudah ada.
     */
    public function sync()
    {
        $existingVendors = Realisasi::whereNotNull('vendor')
            ->where('vendor', '!=', '')
            ->distinct()
            ->pluck('vendor');

        $addedVendors = 0;
        foreach ($existingVendors as $v) {
            $trimmed = trim($v);
            if (!empty($trimmed) && !MasterVendor::where('nama_vendor', $trimmed)->exists()) {
                MasterVendor::create([
                    'nama_vendor' => $trimmed,
                    'is_active'   => true,
                ]);
                $addedVendors++;
            }
        }

        // Client/Customer dari Invoice & Satuan Kerja Realisasi
        $invoiceCustomers = \App\Models\Invoice::whereNotNull('customer')
            ->where('customer', '!=', '')
            ->distinct()
            ->pluck('customer')
            ->toArray();

        $satuanKerjas = Realisasi::whereNotNull('satuan_kerja')
            ->where('satuan_kerja', '!=', '')
            ->distinct()
            ->pluck('satuan_kerja')
            ->toArray();

        $existingClients = array_unique(array_merge($invoiceCustomers, $satuanKerjas));

        $addedClients = 0;
        foreach ($existingClients as $c) {
            $trimmed = trim($c);
            if (!empty($trimmed) && !MasterClient::where('nama_client', $trimmed)->exists()) {
                MasterClient::create([
                    'nama_client' => $trimmed,
                    'kategori'    => 'Corporate / Partner',
                    'is_active'   => true,
                ]);
                $addedClients++;
            }
        }

        ActivityLog::log(
            'SYNC_MASTER_DATA',
            'MasterData',
            null,
            null,
            ['added_vendors' => $addedVendors, 'added_clients' => $addedClients],
            "Admin mensinkronkan master data: {$addedVendors} vendor baru & {$addedClients} client baru ditemukan."
        );

        return redirect()->route('admin.master-data.index')
            ->with('success', "Sinkronisasi selesai! {$addedVendors} Vendor baru dan {$addedClients} Client baru berhasil didaftarkan.");
    }

    // Vendor CRUD
    public function storeVendor(Request $request)
    {
        $request->validate([
            'nama_vendor' => 'required|string|max:191|unique:master_vendors,nama_vendor',
            'kode_vendor' => 'nullable|string|max:50|unique:master_vendors,kode_vendor',
            'pic_vendor'  => 'nullable|string|max:100',
            'kontak'      => 'nullable|string|max:100',
            'email'       => 'nullable|email|max:100',
            'alamat'      => 'nullable|string',
        ]);

        MasterVendor::create($request->all());

        return redirect()->route('admin.master-data.index', ['tab' => 'vendors'])
            ->with('success', "Vendor '{$request->nama_vendor}' berhasil ditambahkan.");
    }

    public function updateVendor(Request $request, $id)
    {
        $vendor = MasterVendor::findOrFail($id);
        $request->validate([
            'nama_vendor' => 'required|string|max:191|unique:master_vendors,nama_vendor,' . $vendor->id,
            'kode_vendor' => 'nullable|string|max:50|unique:master_vendors,kode_vendor,' . $vendor->id,
            'pic_vendor'  => 'nullable|string|max:100',
            'kontak'      => 'nullable|string|max:100',
            'email'       => 'nullable|email|max:100',
            'alamat'      => 'nullable|string',
        ]);

        $vendor->update($request->all());

        return redirect()->route('admin.master-data.index', ['tab' => 'vendors'])
            ->with('success', "Data vendor '{$vendor->nama_vendor}' berhasil diperbarui.");
    }

    public function deleteVendor($id)
    {
        $vendor = MasterVendor::findOrFail($id);
        $nama = $vendor->nama_vendor;
        $vendor->delete();

        return redirect()->route('admin.master-data.index', ['tab' => 'vendors'])
            ->with('success', "Vendor '{$nama}' berhasil dihapus.");
    }

    // Client CRUD
    public function storeClient(Request $request)
    {
        $request->validate([
            'nama_client' => 'required|string|max:191|unique:master_clients,nama_client',
            'kode_client' => 'nullable|string|max:50|unique:master_clients,kode_client',
            'kategori'    => 'nullable|string|max:100',
        ]);

        MasterClient::create($request->all());

        return redirect()->route('admin.master-data.index', ['tab' => 'clients'])
            ->with('success', "Client '{$request->nama_client}' berhasil ditambahkan.");
    }

    public function updateClient(Request $request, $id)
    {
        $client = MasterClient::findOrFail($id);
        $request->validate([
            'nama_client' => 'required|string|max:191|unique:master_clients,nama_client,' . $client->id,
            'kode_client' => 'nullable|string|max:50|unique:master_clients,kode_client,' . $client->id,
            'kategori'    => 'nullable|string|max:100',
        ]);

        $client->update($request->all());

        return redirect()->route('admin.master-data.index', ['tab' => 'clients'])
            ->with('success', "Data client '{$client->nama_client}' berhasil diperbarui.");
    }

    public function deleteClient($id)
    {
        $client = MasterClient::findOrFail($id);
        $nama = $client->nama_client;
        $client->delete();

        return redirect()->route('admin.master-data.index', ['tab' => 'clients'])
            ->with('success', "Client '{$nama}' berhasil dihapus.");
    }

    /**
     * Update profil pejabat penandatangan laporan resmi & identitas instansi.
     */
    public function updateSigners(Request $request)
    {
        $validated = $request->validate([
            'signer_vp_name'       => 'required|string|max:191',
            'signer_vp_title'      => 'required|string|max:191',
            'signer_vp_nip'        => 'nullable|string|max:100',
            'signer_qc_lead_name'  => 'required|string|max:191',
            'signer_qc_lead_title' => 'required|string|max:191',
            'signer_qc_lead_nip'   => 'nullable|string|max:100',
            'company_name'         => 'nullable|string|max:191',
            'division_name'        => 'nullable|string|max:191',
        ]);

        foreach ($validated as $key => $val) {
            $group = in_array($key, ['company_name', 'division_name']) ? 'organization' : 'signers';
            AppSetting::setValue($key, $val, $group);
        }

        return redirect()->route('admin.master-data.index', ['tab' => 'signers'])
            ->with('success', 'Profil Pejabat Penandatangan Laporan Resmi berhasil diperbarui.');
    }

    // =========================================================================
    // MASTER PROYEK & PAGU KONTRAK CRUD
    // =========================================================================

    /**
     * Daftarkan Proyek Baru beserta Pagu Anggaran (Nilai Kontrak).
     */
    public function storeProject(Request $request)
    {
        $request->validate([
            'project_id'       => 'required|string|max:100|unique:kontrak,project_id',
            'project_name'     => 'required|string|max:1000',
            'project_value'    => 'required',
            'service_manager'  => 'nullable|string|max:150',
            'project_client'   => 'nullable|string|max:100',
            'tahun'            => 'nullable|integer',
            'contract_number'  => 'nullable|string|max:255',
            'amandemen'        => 'nullable|string',
        ], [
            'project_id.required'   => 'Kode Project ID wajib diisi.',
            'project_id.unique'     => 'Project ID tersebut sudah terdaftar di master kontrak.',
            'project_name.required' => 'Nama lengkap proyek wajib diisi.',
            'project_value.required'=> 'Nilai Pagu Anggaran wajib diisi.',
        ]);

        $rawVal = $request->input('project_value');
        $cleanVal = preg_replace('/[^\d]/', '', (string) $rawVal);
        $projectValue = !empty($cleanVal) ? (float) $cleanVal : 0;

        $kontrak = Kontrak::create([
            'project_id'       => strtoupper(trim($request->project_id)),
            'project_name'     => trim($request->project_name),
            'project_value'    => $projectValue,
            'service_manager'  => $request->service_manager ? strtoupper(trim($request->service_manager)) : null,
            'project_client'   => $request->project_client ? trim($request->project_client) : 'PT PGN Tbk',
            'tahun'            => $request->tahun ?: date('Y'),
            'contract_number'  => $request->contract_number ? trim($request->contract_number) : null,
            'status'           => 'ACTIVE',
            'amandemen'        => $request->amandemen ? trim($request->amandemen) : null,
        ]);

        ActivityLog::log(
            'CREATE',
            'kontrak',
            (string) $kontrak->id,
            null,
            $kontrak->toArray(),
            "Menambahkan Master Proyek & Pagu {$kontrak->project_id} (Pagu: Rp " . number_format($projectValue, 0, ',', '.') . ")"
        );

        return redirect()->route('admin.master-data.index', ['tab' => 'projects'])
            ->with('success', "Proyek '{$kontrak->project_id}' berhasil didaftarkan dengan Pagu Rp " . number_format($projectValue, 0, ',', '.') . ".");
    }

    /**
     * Update Data Proyek, Pagu Anggaran, atau Catatan Addendum.
     */
    public function updateProject(Request $request, $id)
    {
        $kontrak = Kontrak::findOrFail($id);

        $request->validate([
            'project_id'       => 'required|string|max:100|unique:kontrak,project_id,' . $kontrak->id,
            'project_name'     => 'required|string|max:1000',
            'project_value'    => 'required',
            'service_manager'  => 'nullable|string|max:150',
            'project_client'   => 'nullable|string|max:100',
            'tahun'            => 'nullable|integer',
            'contract_number'  => 'nullable|string|max:255',
            'amandemen'        => 'nullable|string',
        ], [
            'project_id.required'   => 'Kode Project ID wajib diisi.',
            'project_id.unique'     => 'Project ID tersebut sudah digunakan pada proyek lain.',
            'project_name.required' => 'Nama lengkap proyek wajib diisi.',
            'project_value.required'=> 'Nilai Pagu Anggaran wajib diisi.',
        ]);

        $oldData = $kontrak->toArray();
        $rawVal = $request->input('project_value');
        $cleanVal = preg_replace('/[^\d]/', '', (string) $rawVal);
        $projectValue = !empty($cleanVal) ? (float) $cleanVal : 0;

        $kontrak->update([
            'project_id'       => strtoupper(trim($request->project_id)),
            'project_name'     => trim($request->project_name),
            'project_value'    => $projectValue,
            'service_manager'  => $request->service_manager ? strtoupper(trim($request->service_manager)) : null,
            'project_client'   => $request->project_client ? trim($request->project_client) : 'PT PGN Tbk',
            'tahun'            => $request->tahun ?: ($kontrak->tahun ?: date('Y')),
            'contract_number'  => $request->contract_number ? trim($request->contract_number) : null,
            'amandemen'        => $request->amandemen ? trim($request->amandemen) : null,
        ]);

        ActivityLog::log(
            'UPDATE',
            'kontrak',
            (string) $kontrak->id,
            $oldData,
            $kontrak->toArray(),
            "Memperbarui Master Proyek & Pagu {$kontrak->project_id} (Pagu: Rp " . number_format($projectValue, 0, ',', '.') . ")"
        );

        return redirect()->route('admin.master-data.index', ['tab' => 'projects'])
            ->with('success', "Data proyek & pagu '{$kontrak->project_id}' berhasil diperbarui.");
    }

    /**
     * Hapus Master Proyek dengan Proteksi Integritas Relasi.
     */
    public function deleteProject(Request $request, $id)
    {
        $kontrak = Kontrak::findOrFail($id);
        $pid = $kontrak->project_id;

        // Cek apakah ada transaksi realisasi aktif yang berelasi
        $realisasiCount = Realisasi::where('project_id', $pid)->count();
        if ($realisasiCount > 0) {
            return redirect()->route('admin.master-data.index', ['tab' => 'projects'])
                ->with('error', "Proyek '{$pid}' tidak dapat dihapus karena sudah memiliki {$realisasiCount} transaksi realisasi. Harap hapus atau pindahkan transaksi terkait terlebih dahulu.");
        }

        // Cek apakah ada dokumen BASTO yang berelasi
        $bastoCount = \App\Models\Basto::where('project_id', $pid)->count();
        if ($bastoCount > 0) {
            return redirect()->route('admin.master-data.index', ['tab' => 'projects'])
                ->with('error', "Proyek '{$pid}' tidak dapat dihapus karena sudah memiliki {$bastoCount} berkas BASTO terdaftar.");
        }

        // Cek apakah ada faktur invoice vendor yang berelasi
        $invCount = \App\Models\Invoice::where('project_id', $pid)->count();
        if ($invCount > 0) {
            return redirect()->route('admin.master-data.index', ['tab' => 'projects'])
                ->with('error', "Proyek '{$pid}' tidak dapat dihapus karena sudah memiliki {$invCount} faktur tagihan invoice vendor terdaftar.");
        }

        $oldData = $kontrak->toArray();
        $kontrak->delete();

        ActivityLog::log(
            'DELETE',
            'kontrak',
            (string) $id,
            $oldData,
            null,
            "Menghapus Master Proyek {$pid}"
        );

        return redirect()->route('admin.master-data.index', ['tab' => 'projects'])
            ->with('success', "Proyek '{$pid}' berhasil dihapus dari master kontrak.");
    }
}
