<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Wisata;
use Illuminate\Http\Request;

class DataKomentarController extends Controller
{
    public function index(Request $request)
    {
        // 1. KARTU STATISTIK
        $totalKomentar = Comment::count();
        $rataRataRating = $totalKomentar > 0 ? round(Comment::avg('rating'), 1) : 0;
        $totalBintang5 = Comment::where('rating', 5)->count();
        $komentarHariIni = Comment::whereDate('created_at', today())->count();

        // 2. QUERY & FILTER
        $wisataList = Wisata::select('id', 'nama')->orderBy('nama', 'asc')->get();

        $query = Comment::with(['user', 'wisata']);

        // Search (Isi komentar, nama user, atau nama wisata)
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('comment', 'like', '%' . $search . '%')
                  ->orWhereHas('user', function ($userQ) use ($search) {
                      $userQ->where('username', 'like', '%' . $search . '%')
                            ->orWhere('email', 'like', '%' . $search . '%');
                  })
                  ->orWhereHas('wisata', function ($wisataQ) use ($search) {
                      $wisataQ->where('nama', 'like', '%' . $search . '%');
                  });
            });
        }

        // Filter Wisata
        if ($request->filled('wisata_id')) {
            $query->where('wisata_id', $request->input('wisata_id'));
        }

        // Filter Rating
        if ($request->filled('rating')) {
            $query->where('rating', $request->input('rating'));
        }

        // Filter Tanggal
        if ($request->filled('tgl_awal') && $request->filled('tgl_akhir')) {
            $query->whereBetween('created_at', [
                $request->input('tgl_awal') . ' 00:00:00',
                $request->input('tgl_akhir') . ' 23:59:59'
            ]);
        }

        $comments = $query->latest()->paginate(10)->withQueryString();

        return view('pages.data-komentar', compact(
            'comments',
            'wisataList',
            'totalKomentar',
            'rataRataRating',
            'totalBintang5',
            'komentarHariIni'
        ));
    }

    public function destroy(Comment $comment)
    {
        $comment->delete();
        return redirect()->route('komentar.index')->with('success', 'Komentar berhasil dihapus.');
    }
}
