<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Wisata;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class CommentApiController extends Controller
{
    /**
     * Mengambil daftar komentar berdasarkan ID Wisata
     * Endpoint: GET /api/dolanbanyumas/wisata/{wisataId}/comments
     */
    public function getCommentsByWisata($wisataId): JsonResponse
    {
        $wisata = Wisata::find($wisataId);

        if (!$wisata) {
            return response()->json([
                'success' => false,
                'message' => 'Destinasi wisata tidak ditemukan',
                'data' => []
            ], 404);
        }

        $comments = Comment::with(['user:id,username,email'])
            ->where('wisata_id', $wisataId)
            ->latest()
            ->get();

        $totalComments = $comments->count();
        $averageRating = $totalComments > 0 ? round($comments->avg('rating'), 1) : 0;

        $formattedComments = $comments->map(function ($comment) {
            return [
                'id' => $comment->id,
                'wisata_id' => $comment->wisata_id,
                'comment' => $comment->comment,
                'rating' => (int) $comment->rating,
                'created_at' => $comment->created_at ? $comment->created_at->format('Y-m-d H:i:s') : null,
                'time_ago' => $comment->created_at ? $comment->created_at->diffForHumans() : '',
                'user' => [
                    'id' => $comment->user->id ?? null,
                    'username' => $comment->user->username ?? 'Pengguna',
                    'email' => $comment->user->email ?? '',
                ],
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Komentar berhasil diambil',
            'wisata_id' => (int) $wisataId,
            'wisata_nama' => $wisata->nama,
            'total_comments' => $totalComments,
            'average_rating' => $averageRating,
            'data' => $formattedComments
        ], 200);
    }

    /**
     * Menambahkan komentar baru dari aplikasi Flutter
     * Endpoint: POST /api/dolanbanyumas/comments
     * Header: Authorization: Bearer <token>
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'wisata_id' => 'required|exists:wisatas,id',
            'comment' => 'required|string|min:2|max:1000',
            'rating' => 'nullable|integer|min:1|max:5',
        ], [
            'wisata_id.required' => 'ID wisata wajib diisi.',
            'wisata_id.exists' => 'Wisata tidak ditemukan.',
            'comment.required' => 'Kolom komentar tidak boleh kosong.',
            'comment.min' => 'Komentar minimal 2 karakter.',
            'comment.max' => 'Komentar maksimal 1000 karakter.',
            'rating.integer' => 'Rating harus berupa angka.',
            'rating.min' => 'Rating minimal 1 bintang.',
            'rating.max' => 'Rating maksimal 5 bintang.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $request->user();

        $comment = Comment::create([
            'user_id' => $user->id,
            'wisata_id' => $request->wisata_id,
            'comment' => $request->comment,
            'rating' => $request->input('rating', 5),
        ]);

        $comment->load('user:id,username,email');

        return response()->json([
            'success' => true,
            'message' => 'Komentar berhasil dikirim!',
            'data' => [
                'id' => $comment->id,
                'wisata_id' => $comment->wisata_id,
                'comment' => $comment->comment,
                'rating' => (int) $comment->rating,
                'created_at' => $comment->created_at->format('Y-m-d H:i:s'),
                'time_ago' => $comment->created_at->diffForHumans(),
                'user' => [
                    'id' => $user->id,
                    'username' => $user->username,
                    'email' => $user->email,
                ]
            ]
        ], 201);
    }

    /**
     * Mengambil riwayat komentar yang pernah dibuat oleh user yang sedang login
     * Endpoint: GET /api/dolanbanyumas/user/comments
     */
    public function getUserComments(Request $request): JsonResponse
    {
        $user = $request->user();

        $comments = Comment::with(['wisata:id,nama,gambar_url'])
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        $formatted = $comments->map(function ($comment) {
            return [
                'id' => $comment->id,
                'comment' => $comment->comment,
                'rating' => (int) $comment->rating,
                'created_at' => $comment->created_at->format('Y-m-d H:i:s'),
                'time_ago' => $comment->created_at->diffForHumans(),
                'wisata' => [
                    'id' => $comment->wisata->id ?? null,
                    'nama' => $comment->wisata->nama ?? 'Wisata Tidak Ditemukan',
                    'gambar_url' => $comment->wisata ? $comment->wisata->image_url : null,
                ]
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Riwayat komentar user berhasil diambil',
            'data' => $formatted
        ], 200);
    }

    /**
     * Menghapus komentar milik user sendiri
     * Endpoint: DELETE /api/dolanbanyumas/comments/{id}
     */
    public function destroy(Request $request, $id): JsonResponse
    {
        $user = $request->user();
        $comment = Comment::find($id);

        if (!$comment) {
            return response()->json([
                'success' => false,
                'message' => 'Komentar tidak ditemukan'
            ], 404);
        }

        // Cek apakah pemilik komentar atau admin
        if ($comment->user_id !== $user->id && $user->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki hak untuk menghapus komentar ini'
            ], 403);
        }

        $comment->delete();

        return response()->json([
            'success' => true,
            'message' => 'Komentar berhasil dihapus'
        ], 200);
    }
}
