<?php

namespace App\Http\Controllers;

use App\Models\LogisticTask;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LogisticTaskController extends Controller
{
    // ================= LEADER / SUB LEADER =================

    public function adminDashboard(Request $request)
    {
        $query = LogisticTask::with('operator');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('task_code', 'like', "%{$search}%")
                    ->orWhere('title', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $tasks = $query->latest()->get();

        $stats = [
            'total' => LogisticTask::count(),
            'approved' => LogisticTask::where('status', 'approved')->count(),
            'pending' => LogisticTask::where('status', 'pending')->count(),
            'waiting' => LogisticTask::where('status', 'waiting_approval')->count(),
            'active_operators' => User::where('role', 'operator')->count(),
        ];

        // Mengambil seluruh data personel untuk tabel manajemen akun
        $allOperators = User::orderBy('role')->orderBy('username')->get();

        // Mengambil data laporan box retur masuk dari operator
        $incomingReports = \App\Models\IncomingInventory::with('operator')->latest()->get();

        return view('admin.dashboard', compact('tasks', 'stats', 'allOperators', 'incomingReports'));
    }

    public function create()
    {
        $operators = User::where('role', 'operator')->get();
        return view('admin.create', compact('operators'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'task_code' => 'required|string',
            'title' => 'required|string',
            'operator_id' => 'required|exists:users,id',
            'task_date' => 'required|date',
            'part_name' => 'required|string',
            'part_number' => 'required|string',
            'total_qty' => 'required|numeric',
            'qty_per_box' => 'required|numeric',
            'delivery_date' => 'required|date',
            'shipping_company' => 'required|string',
            'company_destination' => 'required|string',
        ]);

        $formattedDescription = "Part Name: {$request->part_name}\n"
            . "Part Number: {$request->part_number}\n"
            . "Total Qty: {$request->total_qty} pcs | Qty/Box: {$request->qty_per_box} pcs\n"
            . "Delivery Date: {$request->delivery_date}\n"
            . "Shipping: {$request->shipping_company}\n"
            . "Destination: {$request->company_destination}";

        LogisticTask::create([
            'task_code' => $request->task_code,
            'title' => $request->title,
            'description' => $formattedDescription,
            'task_date' => $request->task_date,
            'operator_id' => $request->operator_id,
            'status' => 'pending',
        ]);

        return redirect()->route('admin.dashboard')->with('success', 'Tugas operasional (SPK) berhasil dibuat dan ditugaskan!');
    }

    public function reviewTask(Request $request, $id)
    {
        $task = LogisticTask::findOrFail($id);
        $action = $request->action;

        if ($action === 'approve') {
            $task->update([
                'status' => 'approved',
                'admin_feedback' => $request->admin_feedback,
            ]);
            return back()->with('success', 'Tugas disetujui & dinyatakan selesai!');
        } else {
            $request->validate(['admin_feedback' => 'required']);
            $task->update([
                'status' => 'need_revision',
                'admin_feedback' => $request->admin_feedback,
            ]);
            return back()->with('warning', 'Tugas dikembalikan ke operator untuk direvisi.');
        }
    }

    // ================= OPERATOR =================

    public function operatorDashboard()
    {
        $user = Auth::user();
        $today = date('Y-m-d');

        $tasksToday = LogisticTask::where('operator_id', $user->id)
            ->whereDate('task_date', $today)
            ->get();

        $allTasks = LogisticTask::where('operator_id', $user->id)->latest()->get();

        return view('operator.dashboard', compact('tasksToday', 'allTasks'));
    }

    public function startTask($id)
    {
        $task = LogisticTask::where('id', $id)->where('operator_id', Auth::id())->firstOrFail();
        $task->update(['status' => 'in_progress']);
        return back()->with('success', 'Status tugas diubah menjadi Sedang Dikerjakan.');
    }

    public function submitReport(Request $request, $id)
    {
        $task = LogisticTask::where('id', $id)->where('operator_id', Auth::id())->firstOrFail();

        $request->validate([
            'operator_report' => 'required',
        ]);

        $task->update([
            'operator_report' => $request->operator_report,
            'issue_note' => $request->issue_note,
            'status' => 'waiting_approval',
        ]);

        return back()->with('success', 'Laporan & catatan kendala berhasil dikirim ke Leader untuk ditinjau.');
    }

    public function createIncomingReport()
    {
        // Mengambil riwayat laporan pemasukan hari ini oleh operator yang login
        $todayReports = \App\Models\IncomingInventory::where('operator_id', Auth::id())
            ->whereDate('report_date', date('Y-m-d'))
            ->latest()->get();

        return view('operator.inventory-incoming', compact('todayReports'));
    }

    public function storeIncomingReport(Request $request)
    {
        $request->validate([
            'part_name' => 'required|string',
            'quantity' => 'required|numeric|min:1',
            'notes' => 'nullable|string',
        ]);

        \App\Models\IncomingInventory::create([
            'operator_id' => Auth::id(),
            'part_name' => $request->part_name,
            'part_number' => '-', // Diisi strip otomatis karena kolom kode box sudah dihapus
            'quantity' => $request->quantity,
            'report_date' => date('Y-m-d'),
            'notes' => $request->notes,
        ]);

        return back()->with('success', 'Laporan pemasukan persediaan produksi berhasil dikirim!');
    }

    // ================= CETAK LAPORAN =================

    public function printReport(Request $request)
    {
        $user = Auth::user();
        
        $query = LogisticTask::with('operator');

        // Jika user adalah operator biasa, hanya tampilkan tugasnya sendiri
        if ($user->role === 'operator') {
            $query->where('operator_id', $user->id);
        }

        // Logika Filter Berdasarkan Rentang Tanggal (Pencarian)
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('task_date', [$request->start_date, $request->end_date]);
        } elseif ($request->filled('start_date')) {
            $query->whereDate('task_date', '>=', $request->start_date);
        } elseif ($request->filled('end_date')) {
            $query->whereDate('task_date', '<=', $request->end_date);
        }

        $tasks = $query->latest()->get();

        return view('reports.print', compact('tasks', 'user'));
    }

    // ================= MANAJEMEN OPERATOR / PERSONEL =================

    public function createOperator()
    {
        return view('admin.create-operator');
    }

    public function storeOperator(Request $request)
    {
        $request->validate([
            'username' => 'required|string|unique:users,username',
            'code' => 'required|string|max:12|unique:users,code',
            'role' => 'required|in:leader,sub_leader,operator',
        ], [
            'username.unique' => 'Nama lengkap/username sudah terdaftar di sistem.',
            'code.unique' => 'Kode personel ini sudah digunakan user lain.',
        ]);

        User::create([
            'username' => $request->username,
            'code' => strtoupper(trim($request->code)),
            'role' => $request->role,
        ]);

        return redirect()->route('admin.dashboard')->with('success', 'Operator baru berhasil ditambahkan!');
    }

    public function editOperator($id)
    {
        $operator = User::findOrFail($id);

        // Pastikan 'admin.edit-operator' sesuai dengan nama file blade lu (misal: admin/edit-operator.blade.php)
        return view('admin.edit-operator', compact('operator'));
    }

    public function updateOperator(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // 1. Validasi sudah dibersihkan dari kolom email
        $request->validate([
            'username' => 'required|string|unique:users,username,' . $user->id,
            'code' => 'required|string|max:12|unique:users,code,' . $user->id,
            'role' => 'required|in:leader,sub_leader,operator',
        ], [
            'username.unique' => 'Username sudah digunakan akun lain.',
            'code.unique' => 'Kode akses sudah digunakan akun lain.',
        ]);

        // 2. Data yang disimpan murni hanya 3 kolom ini
        $dataToUpdate = [
            'username' => $request->username,
            'code' => strtoupper(trim($request->code)),
            'role' => $request->role,
        ];

        // 3. Update database
        $user->update($dataToUpdate);

        return redirect()->route('admin.dashboard')->with('success', "Data kerja personel {$user->username} berhasil diperbarui!");
    }
}