<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class OrganizationRouteController extends Controller
{

    public function showDashboard(Request $request)
    {
        $user = Auth::user();

        // Konversi role menjadi array
        $roles = json_decode($user->role, true);

        // Pastikan role berhasil didecode menjadi array
        if (!is_array($roles)) {
            $roles = []; // Default ke array kosong jika role tidak valid
        }

        // Hitung jumlah konten yang disetujui
        $approvedContents = DB::table('contents')
            ->when(!in_array(1, $roles), function ($query) use ($user) {
                // Jika bukan admin, filter berdasarkan user_id
                return $query->where('user_id', $user->id);
            })
            ->where('reason_phrase', 'APPROVED')
            ->count();

        // Hitung status smart_contract berdasarkan status_contract
        $smart_contract = DB::table('smart_contract')
            ->select('status_contract', DB::raw('COUNT(*) as count'))
            ->when(!in_array(1, $roles), function ($query) use ($user) {
                // Jika bukan admin, filter berdasarkan user_id
                return $query->where('user_id', $user->id);
            })
            ->groupBy('status_contract')
            ->get();

        // Hitung total semua smart_contract
        $totalSC = DB::table('smart_contract')
            ->when(!in_array(1, $roles), function ($query) use ($user) {
                // Jika bukan admin, filter berdasarkan user_id
                return $query->where('user_id', $user->id);
            })
            ->count();

        // Ambil jumlah approved dan rejected smart_contract
        $approvedCountSC = $smart_contract->where('status_contract', 1)->first()->count ?? 0;
        $rejectedCountSC = $smart_contract->where('status_contract', 0)->first()->count ?? 0;

        return view('organization.dashboard.index', [
            'approvedContents' => $approvedContents,
            'totalSC' => $totalSC,
            'rejectedCountSC' => $rejectedCountSC,
            'approvedCountSC' => $approvedCountSC,
        ]);
    }



    public function showNotificationOrg(Request $request)
    {
        $user = Auth::user();
        $roles = json_decode($user->role, true);
        // Ambil data log email
        $logs = DB::table('email_logs')
            ->select([
                'id',
                'email_type',
                'recipient_email',
                'from_email',
                'name',
                'status',
                'response_data',
                'created_at'
            ])
            ->when(!in_array(1, json_decode($user->role, true) ?? []), function ($query) use ($user) {
                // Jika bukan admin, filter berdasarkan email pengguna
                return $query->where('recipient_email', $user->email);
            })
            ->whereIn('email_type', ['SMART CONTRACT'])
            ->orderBy('id', 'desc')
            ->get();

        if ($request->ajax()) {

            $table = DataTables::of($logs)->addIndexColumn();
            $table->addColumn('status', function ($row) {
                $statusClass = $row->status === 'SUCCESS' ? 'success' : 'danger';
                return '<span class="badge bg-' . $statusClass . ' p-2">' . $row->status . '</span>';
            });
            $table->addColumn('action', function ($row) {
                $button = '<div class="d-flex justify-content-center align-items-center">
                                <button class="btn btn-icon btn-sm btn-info-transparent rounded-pill me-2"
                                        data-bs-toggle="modal" data-bs-target="#modalView-' . $row->id . '">
                                        <i class="ri-eye-line fw-bold"></i>
                                    </button>
                            </div>
                    ';
                return $button;
            });


            $table->rawColumns(['status', 'action']);

            return $table->make(true);
        }


        return view('organization.notification.index', [
            'datas' => $logs
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->flush();
        $request->session()->regenerateToken();

        return redirect(env('XBUG_URL') . '/login')->with('success', 'You have been logged out successfully!');
    }
    public function redirectSmartContractOrg(Request $request)
    {
        // Ambil data user (contoh)
        $user = Auth::user();

        // Validasi 1: Premium Feature (is_gpt)
        if ($user->is_gpt === 0) {
            $errorMessage = "[NOTICE] This feature is available exclusively for premium account holders. "
                . "Please upgrade to a premium account to access this functionality. "
                . "For more details, contact us at [help-center@xbug.online].";

            return view('organization.contentBlockchain.block', [
                'errorMessage' => $errorMessage,
                'xBugBlockchainUrl' => null,
                'redirect'     => false,
            ]);
        }

        // Validasi 2: Eligible for Smart Contract (is_smart_contract)

        // Validasi 3: Status Tidak Diblokir (is_smart_contract_status)
        if ($user->is_smart_contract_status === 0) {
            $errorMessage = "[NOTICE] Access to xBug Smart Contract has been restricted for your account. "
                . "For assistance, please contact us at [help-center@xbug.online].";

            return view('organization.contentBlockchain.block', [
                'errorMessage' => $errorMessage,
                'xBugBlockchainUrl' => null,
                'redirect'     => false,
            ]);
        }

        // Jika semua lolos validasi:
        return view('organization.contentBlockchain.block', [
            'errorMessage'      => null,
            'redirect'          => true,
            'xBugBlockchainUrl' => '/protected/dashboard',
        ]);
    }
}
