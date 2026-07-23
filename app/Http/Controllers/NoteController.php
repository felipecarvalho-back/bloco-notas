<?php

namespace App\Http\Controllers;

use App\Models\Note;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Illuminate\View\View;

class NoteController extends Controller
{
    /**
     * Exibe a interface principal do Bloco de Notas.
     */
    public function index(Request $request): View
    {
        $search = $request->query('search');
        $category = $request->query('category');
        $activeId = $request->query('active');

        $query = Note::query()->where('is_archived', false);

        if ($category && $category !== 'Todas') {
            if ($category === 'Fixadas') {
                $query->where('is_pinned', true);
            } else {
                $query->where('category', $category);
            }
        }

        if (! empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%");
            });
        }

        $notes = $query->orderByDesc('is_pinned')
            ->orderByDesc('updated_at')
            ->get();

        $activeNote = null;
        if ($activeId) {
            $activeNote = Note::find($activeId);
        }

        if (! $activeNote && $notes->isNotEmpty()) {
            $activeNote = $notes->first();
        }

        $categories = ['Geral', 'Trabalho', 'Pessoal', 'Ideias'];

        return view('notes.index', compact('notes', 'activeNote', 'categories', 'category', 'search'));
    }

    /**
     * Cria uma nova nota em branco.
     */
    public function store(Request $request): JsonResponse
    {
        $category = $request->input('category', 'Geral');

        $note = Note::create([
            'title' => 'Nova Nota',
            'content' => '',
            'category' => in_array($category, ['Todas', 'Fixadas']) ? 'Geral' : $category,
            'is_pinned' => false,
        ]);

        return response()->json([
            'success' => true,
            'note' => $note,
            'snippet' => $note->snippet,
            'updated_at_human' => $note->updated_at->diffForHumans(),
        ]);
    }

    /**
     * Atualiza uma nota (Auto-save).
     */
    public function update(Request $request, Note $note): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'content' => 'nullable|string',
            'category' => 'nullable|string|max:100',
            'is_pinned' => 'nullable|boolean',
        ]);

        if (array_key_exists('title', $validated) && trim($validated['title']) === '') {
            $validated['title'] = 'Sem título';
        }

        $note->update($validated);

        return response()->json([
            'success' => true,
            'note' => $note,
            'snippet' => $note->snippet,
            'word_count' => $note->word_count,
            'char_count' => $note->char_count,
            'updated_at_formatted' => $note->updated_at->format('H:i:s'),
        ]);
    }

    /**
     * Alterna o status de fixada de uma nota.
     */
    public function togglePin(Note $note): JsonResponse
    {
        $note->update([
            'is_pinned' => ! $note->is_pinned,
        ]);

        return response()->json([
            'success' => true,
            'is_pinned' => $note->is_pinned,
        ]);
    }

    /**
     * Deleta uma nota.
     */
    public function destroy(Note $note): JsonResponse
    {
        $note->delete();

        return response()->json([
            'success' => true,
        ]);
    }

    /**
     * Exporta a nota como arquivo de texto .txt
     */
    public function export(Note $note): Response
    {
        $filename = Str::slug($note->title ?: 'nota').'.txt';
        $content = "{$note->title}\n".str_repeat('=', mb_strlen($note->title ?: 'nota'))."\n\n".($note->content ?? '');

        return response($content, 200, [
            'Content-Type' => 'text/plain; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }

    /**
     * Importa um arquivo de texto criando uma nova nota.
     */
    public function import(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'category' => 'nullable|string|max:100',
        ]);

        $note = Note::create([
            'title' => $validated['title'],
            'content' => $validated['content'] ?? '',
            'category' => $validated['category'] ?? 'Geral',
            'is_pinned' => false,
        ]);

        return response()->json([
            'success' => true,
            'note' => $note,
        ]);
    }
}
