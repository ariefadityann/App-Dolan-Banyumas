<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Wisata;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class WisataApiController extends Controller
{
    /**
     * Get all wisata data with optional filters
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Wisata::query();

            // Filter by search (nama atau alamat)
            if ($request->filled('search')) {
                $search = $request->input('search');
                $query->where(function($q) use ($search) {
                    $q->where('nama', 'like', '%' . $search . '%')
                      ->orWhere('alamat', 'like', '%' . $search . '%');
                });
            }

            // Filter by kategori
            if ($request->filled('kategori')) {
                $query->where('kategori', $request->input('kategori'));
            }

            // Sorting
            $sortBy = $request->input('sort_by', 'created_at');
            $sortOrder = $request->input('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            // Pagination
            $perPage = $request->input('per_page', 10);
            $wisatas = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'message' => 'Data wisata berhasil diambil',
                'data' => $wisatas->items(),
                'pagination' => [
                    'current_page' => $wisatas->currentPage(),
                    'last_page' => $wisatas->lastPage(),
                    'per_page' => $wisatas->perPage(),
                    'total' => $wisatas->total(),
                    'from' => $wisatas->firstItem(),
                    'to' => $wisatas->lastItem(),
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data wisata',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get single wisata by ID
     * 
     * @param int $id
     * @return JsonResponse
     */
    public function show($id): JsonResponse
    {
        try {
            $wisata = Wisata::findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Data wisata berhasil diambil',
                'data' => $wisata
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data wisata tidak ditemukan',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data wisata',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all wisata without pagination (for mobile app or map)
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function all(Request $request): JsonResponse
    {
        try {
            $query = Wisata::query();

            // Filter by kategori
            if ($request->filled('kategori')) {
                $query->where('kategori', $request->input('kategori'));
            }

            // Filter by search
            if ($request->filled('search')) {
                $search = $request->input('search');
                $query->where(function($q) use ($search) {
                    $q->where('nama', 'like', '%' . $search . '%')
                      ->orWhere('alamat', 'like', '%' . $search . '%');
                });
            }

            $wisatas = $query->orderBy('nama', 'asc')->get();

            return response()->json([
                'success' => true,
                'message' => 'Data wisata berhasil diambil',
                'data' => $wisatas,
                'total' => $wisatas->count()
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data wisata',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get wisata by kategori
     * 
     * @param string $kategori
     * @return JsonResponse
     */
    public function byKategori($kategori): JsonResponse
    {
        try {
            $wisatas = Wisata::where('kategori', $kategori)
                ->orderBy('nama', 'asc')
                ->get();

            return response()->json([
                'success' => true,
                'message' => "Data wisata kategori {$kategori} berhasil diambil",
                'data' => $wisatas,
                'total' => $wisatas->count()
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data wisata',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get available categories
     * 
     * @return JsonResponse
     */
    public function categories(): JsonResponse
    {
        try {
            $categories = Wisata::select('kategori')
                ->distinct()
                ->orderBy('kategori', 'asc')
                ->pluck('kategori');

            return response()->json([
                'success' => true,
                'message' => 'Kategori wisata berhasil diambil',
                'data' => $categories
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil kategori wisata',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
