<!DOCTYPE html>
<html lang="pt-BR" class="h-full dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Bloco de Notas Desktop</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            -webkit-font-smoothing: antialiased;
        }
        .font-mono-editor {
            font-family: 'JetBrains Mono', monospace;
        }
        /* Custom scrollbar for modern desktop look */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: rgba(15, 23, 42, 0.6);
        }
        ::-webkit-scrollbar-thumb {
            background: rgba(51, 65, 85, 0.7);
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: rgba(71, 85, 105, 0.9);
        }
    </style>
</head>
<body class="h-full bg-slate-950 text-slate-100 antialiased overflow-hidden flex flex-col select-none">

    <!-- Top Window Drag Handle / Titlebar area -->
    <header class="h-10 bg-slate-900 border-b border-slate-800/80 flex items-center justify-between px-4 shrink-0" style="-webkit-app-region: drag">
        <div class="flex items-center gap-2.5">
            <div class="w-3 h-3 rounded-full bg-indigo-500/80 ring-2 ring-indigo-400/20"></div>
            <span class="text-xs font-semibold tracking-wide text-slate-300">Bloco de Notas</span>
            <span class="text-[10px] bg-indigo-950 text-indigo-300 font-medium px-2 py-0.5 rounded-full border border-indigo-800/40">Desktop v1.0</span>
        </div>
        <div class="text-xs text-slate-500 font-medium">NativePHP</div>
    </header>

    <!-- Main Application Container -->
    <div class="flex-1 flex overflow-hidden">

        <!-- Sidebar Section -->
        <aside class="w-80 bg-slate-900/90 border-r border-slate-800/80 flex flex-col shrink-0">
            <!-- Sidebar Header & Create Button -->
            <div class="p-3.5 border-b border-slate-800/80 space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Minhas Notas</span>
                    <button id="btn-new-note" onclick="createNewNote()" class="inline-flex items-center justify-center gap-1.5 px-3 py-1.5 bg-indigo-600 hover:bg-indigo-500 active:bg-indigo-700 text-white text-xs font-medium rounded-lg transition shadow-sm shadow-indigo-600/20 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Nova Nota
                    </button>
                </div>

                <!-- Search Input -->
                <div class="relative">
                    <input type="text" id="search-input" value="{{ $search }}" placeholder="Pesquisar notas..." class="w-full bg-slate-950/80 text-xs text-slate-200 placeholder-slate-500 pl-8 pr-7 py-2 rounded-lg border border-slate-800 focus:outline-none focus:border-indigo-500 transition">
                    <svg class="w-4 h-4 absolute left-2.5 top-2.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    @if($search)
                        <button onclick="clearSearch()" class="absolute right-2.5 top-2 text-slate-500 hover:text-slate-300">×</button>
                    @endif
                </div>

                <!-- Category Filters -->
                <div class="flex gap-1 overflow-x-auto pb-1 no-scrollbar text-[11px]">
                    <a href="{{ route('notes.index', ['category' => 'Todas', 'search' => $search]) }}" class="px-2.5 py-1 rounded-md transition whitespace-nowrap {{ ($category == 'Todas' || !$category) ? 'bg-indigo-600/20 text-indigo-300 font-medium border border-indigo-500/30' : 'text-slate-400 hover:bg-slate-800' }}">Todas</a>
                    <a href="{{ route('notes.index', ['category' => 'Fixadas', 'search' => $search]) }}" class="px-2.5 py-1 rounded-md transition whitespace-nowrap {{ $category == 'Fixadas' ? 'bg-amber-500/20 text-amber-300 font-medium border border-amber-500/30' : 'text-slate-400 hover:bg-slate-800' }}">📍 Fixadas</a>
                    @foreach($categories as $cat)
                        <a href="{{ route('notes.index', ['category' => $cat, 'search' => $search]) }}" class="px-2.5 py-1 rounded-md transition whitespace-nowrap {{ $category == $cat ? 'bg-indigo-600/20 text-indigo-300 font-medium border border-indigo-500/30' : 'text-slate-400 hover:bg-slate-800' }}">{{ $cat }}</a>
                    @endforeach
                </div>
            </div>

            <!-- Notes List -->
            <div id="notes-list" class="flex-1 overflow-y-auto p-2 space-y-1.5">
                @forelse($notes as $note)
                    <div id="note-card-{{ $note->id }}" onclick="selectNote({{ $note->id }})" class="p-3 rounded-xl cursor-pointer transition border border-transparent group relative {{ ($activeNote && $activeNote->id == $note->id) ? 'bg-slate-800/90 border-slate-700/80 shadow-md' : 'hover:bg-slate-800/40 text-slate-400 hover:text-slate-200' }}">
                        <div class="flex items-start justify-between gap-2 mb-1">
                            <h3 class="note-card-title text-xs font-semibold text-slate-200 truncate flex-1">
                                {{ $note->title ?: 'Sem título' }}
                            </h3>
                            <div class="flex items-center gap-1 shrink-0">
                                @if($note->is_pinned)
                                    <span class="text-amber-400 text-xs" title="Nota fixada">📍</span>
                                @endif
                                <span class="text-[10px] px-1.5 py-0.5 rounded font-medium bg-slate-800 text-slate-400 border border-slate-700/50">
                                    {{ $note->category }}
                                </span>
                            </div>
                        </div>
                        <p class="note-card-snippet text-[11px] text-slate-400 line-clamp-2 leading-relaxed">
                            {{ $note->snippet }}
                        </p>
                        <div class="mt-2 text-[10px] text-slate-500 flex items-center justify-between">
                            <span>{{ $note->updated_at->diffForHumans() }}</span>
                        </div>
                    </div>
                @empty
                    <div class="py-12 px-4 text-center">
                        <svg class="w-10 h-10 mx-auto text-slate-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                        <p class="text-xs text-slate-400 font-medium">Nenhuma nota encontrada</p>
                        <p class="text-[11px] text-slate-500 mt-1">Clique em "Nova Nota" para começar.</p>
                    </div>
                @endforelse
            </div>
        </aside>

        <!-- Editor Area -->
        <main class="flex-1 flex flex-col bg-slate-950 relative overflow-hidden">
            @if($activeNote)
                <!-- Editor Toolbar -->
                <div class="h-12 border-b border-slate-800/80 px-6 flex items-center justify-between shrink-0 bg-slate-900/40">
                    <div class="flex items-center gap-3">
                        <!-- Category Selector -->
                        <div class="flex items-center gap-1.5">
                            <span class="text-xs text-slate-400">Categoria:</span>
                            <select id="editor-category" onchange="autoSaveNote()" class="bg-slate-900 text-xs text-slate-200 rounded-md border border-slate-700 px-2 py-1 focus:outline-none focus:border-indigo-500">
                                @foreach($categories as $cat)
                                    <option value="{{ $cat }}" {{ $activeNote->category == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Pin Button -->
                        <button id="btn-pin" onclick="togglePinNote()" class="p-1.5 rounded-lg border border-slate-800 hover:bg-slate-800 transition text-xs flex items-center gap-1 {{ $activeNote->is_pinned ? 'text-amber-400 bg-amber-950/30 border-amber-800/50' : 'text-slate-400' }}">
                            <span id="pin-icon">📍</span>
                            <span id="pin-text" class="hidden sm:inline">{{ $activeNote->is_pinned ? 'Fixada' : 'Fixar' }}</span>
                        </button>
                    </div>

                    <div class="flex items-center gap-2">
                        <!-- Save status indicator -->
                        <div id="save-status" class="text-xs text-emerald-400 flex items-center gap-1.5 font-medium mr-2">
                            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                            <span>Salvo</span>
                        </div>

                        <!-- Export Button -->
                        <a id="btn-export" href="{{ route('notes.export', $activeNote) }}" class="p-1.5 text-slate-400 hover:text-slate-200 hover:bg-slate-800 rounded-lg border border-slate-800 transition text-xs flex items-center gap-1" title="Exportar para .txt">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                            <span class="hidden md:inline">Exportar</span>
                        </a>

                        <!-- Delete Button -->
                        <button onclick="deleteNote()" class="p-1.5 text-rose-400 hover:text-rose-300 hover:bg-rose-950/40 rounded-lg border border-slate-800 hover:border-rose-800/50 transition text-xs flex items-center gap-1" title="Excluir nota">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        </button>
                    </div>
                </div>

                <!-- Editor Workspace -->
                <div class="flex-1 flex flex-col p-6 overflow-y-auto">
                    <!-- Title Input -->
                    <input type="text" id="editor-title" value="{{ $activeNote->title }}" placeholder="Título da Nota" oninput="onTitleInput()" class="bg-transparent text-2xl font-bold text-slate-100 placeholder-slate-600 focus:outline-none mb-3 border-b border-transparent focus:border-slate-800 pb-2 transition">

                    <!-- Note Textarea -->
                    <textarea id="editor-content" placeholder="Comece a digitar sua nota aqui..." oninput="onContentInput()" class="flex-1 w-full bg-transparent text-slate-200 text-sm leading-relaxed placeholder-slate-600 resize-none focus:outline-none font-sans font-normal border-none p-0 selection:bg-indigo-600 selection:text-white">{{ $activeNote->content }}</textarea>
                </div>

                <!-- Editor Footer / Statistics Bar -->
                <div class="h-8 border-t border-slate-800/80 px-6 flex items-center justify-between text-[11px] text-slate-500 shrink-0 bg-slate-900/60">
                    <div class="flex items-center gap-4">
                        <span id="stat-words">{{ $activeNote->word_count }} palavras</span>
                        <span>•</span>
                        <span id="stat-chars">{{ $activeNote->char_count }} caracteres</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span id="last-saved-time">Última alteração: {{ $activeNote->updated_at->format('H:i:s') }}</span>
                    </div>
                </div>
            @else
                <!-- Empty State when no note is active -->
                <div class="flex-1 flex flex-col items-center justify-center text-center p-8">
                    <div class="w-16 h-16 rounded-2xl bg-indigo-950/60 border border-indigo-800/40 flex items-center justify-center text-indigo-400 mb-4 shadow-inner">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                    </div>
                    <h2 class="text-base font-semibold text-slate-200">Nenhuma nota selecionada</h2>
                    <p class="text-xs text-slate-400 mt-1 max-w-sm">Selecione uma nota existente na lista lateral ou crie uma nova nota para começar a escrever.</p>
                    <button onclick="createNewNote()" class="mt-4 px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-medium rounded-lg transition shadow-md shadow-indigo-600/20">
                        + Criar Nova Nota
                    </button>
                </div>
            @endif
        </main>

    </div>

    <script>
        // State variables
        let activeNoteId = {{ $activeNote ? $activeNote->id : 'null' }};
        let saveTimeout = null;
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Dynamic search input filter
        const searchInput = document.getElementById('search-input');
        if (searchInput) {
            let searchTimeout;
            searchInput.addEventListener('input', (e) => {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    const val = e.target.value;
                    const url = new URL(window.location.href);
                    if (val) {
                        url.searchParams.set('search', val);
                    } else {
                        url.searchParams.delete('search');
                    }
                    window.location.href = url.toString();
                }, 400);
            });
        }

        function clearSearch() {
            const url = new URL(window.location.href);
            url.searchParams.delete('search');
            window.location.href = url.toString();
        }

        // Create new note via AJAX
        async function createNewNote() {
            const categorySelect = document.getElementById('editor-category');
            const category = categorySelect ? categorySelect.value : 'Geral';

            try {
                const response = await fetch("{{ route('notes.store') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ category: category })
                });

                const data = await response.json();
                if (data.success) {
                    window.location.href = "{{ route('notes.index') }}?active=" + data.note.id;
                }
            } catch (err) {
                console.error('Erro ao criar nota:', err);
            }
        }

        // Select note
        function selectNote(noteId) {
            if (activeNoteId === noteId) return;
            const url = new URL(window.location.href);
            url.searchParams.set('active', noteId);
            window.location.href = url.toString();
        }

        // Live input handlers & Auto-save
        function onTitleInput() {
            updateStats();
            triggerAutoSave();
        }

        function onContentInput() {
            updateStats();
            triggerAutoSave();
        }

        function updateStats() {
            const content = document.getElementById('editor-content')?.value || '';
            const charCount = content.length;
            const wordCount = content.trim() ? content.trim().split(/\s+/).length : 0;

            const statWords = document.getElementById('stat-words');
            const statChars = document.getElementById('stat-chars');
            if (statWords) statWords.innerText = `${wordCount} palavras`;
            if (statChars) statChars.innerText = `${charCount} caracteres`;
        }

        function triggerAutoSave() {
            showSaveStatus('Salvando...', 'amber');
            clearTimeout(saveTimeout);
            saveTimeout = setTimeout(() => {
                autoSaveNote();
            }, 600);
        }

        // Save note via PATCH API
        async function autoSaveNote() {
            if (!activeNoteId) return;

            const title = document.getElementById('editor-title')?.value || 'Sem título';
            const content = document.getElementById('editor-content')?.value || '';
            const category = document.getElementById('editor-category')?.value || 'Geral';

            try {
                const response = await fetch(`/notes/${activeNoteId}`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        title: title,
                        content: content,
                        category: category
                    })
                });

                const data = await response.json();
                if (data.success) {
                    showSaveStatus('Salvo', 'emerald');
                    
                    // Update active card in sidebar dynamically
                    const cardTitle = document.querySelector(`#note-card-${activeNoteId} .note-card-title`);
                    const cardSnippet = document.querySelector(`#note-card-${activeNoteId} .note-card-snippet`);
                    if (cardTitle) cardTitle.innerText = data.note.title || 'Sem título';
                    if (cardSnippet) cardSnippet.innerText = data.snippet;

                    const lastSavedText = document.getElementById('last-saved-time');
                    if (lastSavedText) lastSavedText.innerText = `Última alteração: ${data.updated_at_formatted}`;
                }
            } catch (err) {
                console.error('Erro ao salvar nota:', err);
                showSaveStatus('Erro ao salvar', 'rose');
            }
        }

        function showSaveStatus(text, color) {
            const saveStatus = document.getElementById('save-status');
            if (!saveStatus) return;

            let dotColorClass = 'bg-emerald-400 animate-pulse';
            let textColorClass = 'text-emerald-400';

            if (color === 'amber') {
                dotColorClass = 'bg-amber-400 animate-ping';
                textColorClass = 'text-amber-400';
            } else if (color === 'rose') {
                dotColorClass = 'bg-rose-500';
                textColorClass = 'text-rose-400';
            }

            saveStatus.className = `text-xs ${textColorClass} flex items-center gap-1.5 font-medium mr-2`;
            saveStatus.innerHTML = `<span class="w-2 h-2 rounded-full ${dotColorClass}"></span><span>${text}</span>`;
        }

        // Toggle Pin status
        async function togglePinNote() {
            if (!activeNoteId) return;

            try {
                const response = await fetch(`/notes/${activeNoteId}/pin`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();
                if (data.success) {
                    window.location.reload();
                }
            } catch (err) {
                console.error('Erro ao fixar nota:', err);
            }
        }

        // Delete note
        async function deleteNote() {
            if (!activeNoteId) return;
            if (!confirm('Deseja realmente excluir esta nota?')) return;

            try {
                const response = await fetch(`/notes/${activeNoteId}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();
                if (data.success) {
                    window.location.href = "{{ route('notes.index') }}";
                }
            } catch (err) {
                console.error('Erro ao excluir nota:', err);
            }
        }
    </script>
</body>
</html>
