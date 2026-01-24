<?php

namespace App\Http\Controllers;

use App\Models\InterviewQuestion;
use Illuminate\Http\Request;

class InterviewQuestionController extends Controller
{
    public function index()
    {
        // Pisahkan soal untuk Wali dan Santri agar rapi di View
        $questionsWali = InterviewQuestion::where('target', 'Wali')->orderBy('order')->get();
        $questionsSantri = InterviewQuestion::where('target', 'Santri')->orderBy('order')->get();

        return view('admin.interview.questions', compact('questionsWali', 'questionsSantri'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'question' => 'required|string',
            'target'   => 'required|in:Wali,Santri',
            'type'     => 'required|in:text,choice,scale',
            'options'  => 'nullable|string', // Input berupa string dipisah koma
        ]);

        // Proses Opsi Jawaban (Jika tipe Choice)
        $optionsArray = null;
        if ($request->type == 'choice' && $request->options) {
            // Ubah "Ya, Tidak" menjadi ["Ya", "Tidak"]
            $rawOptions = explode(',', $request->options);
            $optionsArray = array_map('trim', $rawOptions); // Hapus spasi berlebih
        }

        InterviewQuestion::create([
            'question' => $request->question,
            'target'   => $request->target,
            'type'     => $request->type,
            'options'  => $optionsArray, // Disimpan otomatis sebagai JSON oleh Model Casts
            'order'    => InterviewQuestion::max('order') + 1, // Urutan otomatis di akhir
        ]);

        return back()->with('success', 'Pertanyaan berhasil ditambahkan ke Bank Soal.');
    }

    public function destroy($id)
    {
        $q = InterviewQuestion::findOrFail($id);
        $q->delete();
        return back()->with('success', 'Pertanyaan dihapus.');
    }
}