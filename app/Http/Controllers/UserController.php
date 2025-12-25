<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $query = User::query();
        
        if (request()->has('search') && request('search')) {
            $query->where('name', 'like', '%' . request('search') . '%');
        }
        
        if (request()->has('name')) {
            $query->where('name', 'like', '%' . request('name') . '%');
        }
        
        if (request()->has('role')) {
            $query->where('role', request('role'));
        }
        
        if (request()->has('is_active')) {
            $query->where('is_active', request('is_active'));
        }
        
        // Pagination
        $perPage = request('per_page', 10);
        $page = request('page', 1);
        
        $users = $query->orderBy('name', 'asc')->paginate($perPage, ['*'], 'page', $page);
        
        // Transform response untuk sesuai dengan FE (ResponseListType)
        $transformedData = $users->map(function($user) {
            return [
                'id' => $user->id,
                'nama' => $user->name, // Map name to nama for FE
                'name' => $user->name,
                'email' => $user->email,
                'username' => $user->username,
                'role' => $user->role,
                'hak_akses' => $user->hak_akses ?? [],
                'is_active' => $user->is_active,
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at,
            ];
        });
        
        return response()->json([
            'data' => $transformedData->values()->all(),
            'page' => $users->currentPage(),
            'page_size' => $users->perPage(),
            'total_pages' => $users->total(),
            'total_rows' => $users->total(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserRequest $request)
    {
        $user = User::create([
            'name' => $request->nama ?? $request->name,
            'email' => $request->email ?? $request->username . '@example.com',
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'hak_akses' => $request->hak_akses ?? [],
            'is_active' => $request->is_active ?? true,
        ]);

        return response()->json([
            'id' => $user->id,
            'nama' => $user->name,
            'name' => $user->name,
            'email' => $user->email,
            'username' => $user->username,
            'role' => $user->role,
            'hak_akses' => $user->hak_akses ?? [],
            'is_active' => $user->is_active,
            'created_at' => $user->created_at,
            'updated_at' => $user->updated_at,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = User::findOrFail($id);
        return response()->json([
            'id' => $user->id,
            'nama' => $user->name,
            'name' => $user->name,
            'email' => $user->email,
            'username' => $user->username,
            'role' => $user->role,
            'hak_akses' => $user->hak_akses ?? [],
            'is_active' => $user->is_active,
            'created_at' => $user->created_at,
            'updated_at' => $user->updated_at,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UserRequest $request, string $id)
    {
        $user = User::findOrFail($id);
        $data = $request->validated();
        
        // Map nama to name if provided
        if (isset($data['nama'])) {
            $data['name'] = $data['nama'];
            unset($data['nama']);
        }
        
        // Handle password hashing if provided
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }
        
        $user->update($data);
        
        // Refresh user to get latest data
        $user->refresh();
        
        return response()->json([
            'id' => $user->id,
            'nama' => $user->name,
            'name' => $user->name,
            'email' => $user->email,
            'username' => $user->username,
            'role' => $user->role,
            'hak_akses' => $user->hak_akses ?? [],
            'is_active' => $user->is_active,
            'created_at' => $user->created_at,
            'updated_at' => $user->updated_at,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return response()->json(null, 204);
    }

    /**
     * Change user password
     */
    public function changePassword()
    {
        $request = request();
        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::findOrFail($request->route('user'));
        
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json(['message' => 'Current password is incorrect'], 400);
        }

        $user->update([
            'password' => Hash::make($request->new_password)
        ]);

        return response()->json(['message' => 'Password changed successfully']);
    }

    /**
     * Export users to CSV/Excel
     */
    public function export()
    {
        $query = User::query();
        
        // Get filter parameters from request
        $request = request();
        
        if ($request->has('search') && $request->search) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        
        if ($request->has('name') && $request->name) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }
        
        if ($request->has('role') && $request->role) {
            $query->where('role', $request->role);
        }
        
        if ($request->has('is_active') && $request->is_active !== null) {
            $query->where('is_active', $request->is_active);
        }
        
        $users = $query->orderBy('name', 'asc')->get();
        
        // Create CSV content
        $filename = 'users_export_' . date('Y-m-d_His') . '.csv';
        
        $callback = function() use ($users) {
            $file = fopen('php://output', 'w');
            
            // Add BOM for UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Add headers
            fputcsv($file, ['ID', 'Nama', 'Username', 'Email', 'Role', 'Hak Akses', 'Status', 'Tanggal Dibuat', 'Tanggal Diupdate']);
            
            // Add data
            foreach ($users as $user) {
                fputcsv($file, [
                    $user->id,
                    $user->name,
                    $user->username,
                    $user->email,
                    $user->role,
                    is_array($user->hak_akses) ? implode(', ', $user->hak_akses) : '',
                    $user->is_active ? 'Aktif' : 'Tidak Aktif',
                    $user->created_at ? $user->created_at->format('Y-m-d H:i:s') : '',
                    $user->updated_at ? $user->updated_at->format('Y-m-d H:i:s') : '',
                ]);
            }
            
            fclose($file);
        };
        
        return response()->streamDownload($callback, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
