<?php

namespace App\Http\Controllers;

use App\Models\WorkOrder;
use App\Models\Kontrak;
use App\Models\User;
use App\Traits\LogsActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class WorkOrderController extends Controller
{
    use LogsActivity;

    protected array $statusList   = ['open', 'in_progress', 'on_hold', 'closed', 'cancelled'];
    protected array $priorityList = ['low', 'normal', 'high', 'critical'];
    protected array $categoryList = ['Maintenance', 'Project', 'Service', 'Procurement', 'Support', 'Others'];

    public function __construct()
    {
        // DMO & Admin dapat membuat/edit WO; SM, QC & Procurement view
        $this->middleware('role:dmo,admin')->only(['create', 'store', 'edit', 'update', 'destroy']);
    }

    // =========================================================================
    // INDEX — Daftar Work Order
    // =========================================================================
    public function index(Request $request)
    {
        $filters = $request->only(['search', 'status', 'priority', 'project_id']);

        $query = WorkOrder::with(['assignedTo', 'createdBy'])->filter($filters);

        $isSM = auth()->check() && auth()->user()->hasRole('osm_service_manager');
        $smProjectIds = collect();

        // SM only sees WOs for their supervised projects
        if ($isSM) {
            $smNames = auth()->user()->getSupervisedServiceManagers();
            $smProjectIds = Kontrak::whereIn('service_manager', $smNames)
                ->pluck('project_id');
            $query->whereIn('project_id', $smProjectIds);
        }

        $workOrders = $query->orderByDesc('updated_at')->paginate(20)->withQueryString();

        // Status summary counts
        $statusCountsQuery = WorkOrder::query();
        $overdueQuery = WorkOrder::whereNotIn('status', ['closed', 'cancelled'])
            ->whereNotNull('due_date')
            ->where('due_date', '<', now());

        if ($isSM) {
            $statusCountsQuery->whereIn('project_id', $smProjectIds);
            $overdueQuery->whereIn('project_id', $smProjectIds);
        }

        $statusCounts = [
            'open'        => (clone $statusCountsQuery)->where('status', 'open')->count(),
            'in_progress' => (clone $statusCountsQuery)->where('status', 'in_progress')->count(),
            'on_hold'     => (clone $statusCountsQuery)->where('status', 'on_hold')->count(),
            'closed'      => (clone $statusCountsQuery)->where('status', 'closed')->count(),
            'cancelled'   => (clone $statusCountsQuery)->where('status', 'cancelled')->count(),
        ];

        $overdueCount = $overdueQuery->count();

        // Project filter options
        if ($isSM) {
            $projectIds = WorkOrder::whereIn('project_id', $smProjectIds)->distinct()->orderBy('project_id')->pluck('project_id');
        } else {
            $projectIds = WorkOrder::distinct()->orderBy('project_id')->pluck('project_id');
        }

        return view('work_order.index', compact(
            'workOrders', 'statusCounts', 'overdueCount',
            'projectIds', 'filters'
        ));
    }

    // =========================================================================
    // CREATE — Form Buat WO Baru
    // =========================================================================
    public function create()
    {
        $kontrakList  = Kontrak::orderBy('project_name')->get(['project_id', 'project_name']);
        $userList     = User::where('status', 'active')->whereNotIn('role', ['admin'])->orderBy('name')->get();

        return view('work_order.create', compact('kontrakList', 'userList'));
    }

    // =========================================================================
    // STORE — Simpan WO Baru
    // =========================================================================
    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id'         => 'required|string|max:50',
            'project_name'       => 'required|string|max:255',
            'title'              => 'required|string|max:255',
            'description'        => 'nullable|string',
            'category'           => 'nullable|string|max:100',
            'priority'           => 'required|in:low,normal,high,critical',
            'assigned_to_user_id'=> 'nullable|exists:users,id',
            'vendor'             => 'nullable|string|max:255',
            'start_date'         => 'nullable|date',
            'due_date'           => 'nullable|date|after_or_equal:start_date',
            'estimated_cost'     => 'nullable|numeric|min:0',
            'notes'              => 'nullable|string',
        ]);

        $validated['wo_number']          = 'WO-' . now()->format('Ym') . '-' . strtoupper(Str::random(5));
        $validated['status']             = 'open';
        $validated['created_by_user_id'] = auth()->id();

        $wo = WorkOrder::create($validated);
        $this->logActivity('CREATE', 'work_order', (string) $wo->id, null, $wo->toArray(), 'Buat Work Order baru');

        return redirect()->route('work-order.show', $wo->id)
            ->with('success', "Work Order #{$wo->wo_number} berhasil dibuat.");
    }

    public function show(WorkOrder $workOrder)
    {
        if ($isSM) {
            $smNames = auth()->user()->getSupervisedServiceManagers();
            $allowedProjectIds = Kontrak::whereIn('service_manager', $smNames)->pluck('project_id')->toArray();
            if (!in_array($workOrder->project_id, $allowedProjectIds)) {
                abort(403, 'Unauthorized access to Work Order.');
            }
        }

        $workOrder->load(['assignedTo', 'createdBy']);
        return view('work_order.show', compact('workOrder'));
    }

    // =========================================================================
    // EDIT — Form Edit WO
    // =========================================================================
    public function edit(WorkOrder $workOrder)
    {
        $kontrakList = Kontrak::orderBy('project_name')->get(['project_id', 'project_name']);
        $userList    = User::where('status', 'active')->whereNotIn('role', ['admin'])->orderBy('name')->get();

        return view('work_order.edit', compact('workOrder', 'kontrakList', 'userList'));
    }

    // =========================================================================
    // UPDATE — Simpan Perubahan WO
    // =========================================================================
    public function update(Request $request, WorkOrder $workOrder)
    {
        $validated = $request->validate([
            'project_id'         => 'required|string|max:50',
            'project_name'       => 'required|string|max:255',
            'title'              => 'required|string|max:255',
            'description'        => 'nullable|string',
            'category'           => 'nullable|string|max:100',
            'priority'           => 'required|in:low,normal,high,critical',
            'status'             => 'required|in:open,in_progress,on_hold,closed,cancelled',
            'assigned_to_user_id'=> 'nullable|exists:users,id',
            'vendor'             => 'nullable|string|max:255',
            'start_date'         => 'nullable|date',
            'due_date'           => 'nullable|date',
            'completed_date'     => 'nullable|date',
            'estimated_cost'     => 'nullable|numeric|min:0',
            'actual_cost'        => 'nullable|numeric|min:0',
            'notes'              => 'nullable|string',
        ]);

        // Auto-set completed_date if status changes to closed
        if ($validated['status'] === 'closed' && !$workOrder->completed_date) {
            $validated['completed_date'] = now()->toDateString();
        }

        $oldData = $workOrder->toArray();
        $workOrder->update($validated);
        $this->logActivity('UPDATE', 'work_order', (string) $workOrder->id, $oldData, $workOrder->toArray(), 'Edit Work Order');

        return redirect()->route('work-order.show', $workOrder->id)
            ->with('success', "Work Order #{$workOrder->wo_number} berhasil diperbarui.");
    }

    // =========================================================================
    // DESTROY — Hapus WO
    // =========================================================================
    public function destroy(WorkOrder $workOrder)
    {
        if (in_array($workOrder->status, ['in_progress'])) {
            return redirect()->route('work-order.index')
                ->with('error', 'Work Order yang sedang dikerjakan tidak dapat dihapus.');
        }

        $oldData = $workOrder->toArray();
        $workOrder->delete();
        $this->logActivity('DELETE', 'work_order', (string) $workOrder->id, $oldData, null, 'Hapus Work Order');

        return redirect()->route('work-order.index')
            ->with('success', 'Work Order berhasil dihapus.');
    }
}
