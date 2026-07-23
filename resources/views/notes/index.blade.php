<!DOCTYPE html>
<html lang="pt-BR" class="h-full dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Bloco de Notas</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            -webkit-font-smoothing: antialiased;
        }
        .font-mono-editor {
            font-family: 'JetBrains Mono', monospace !important;
        }
        /* Custom scrollbar matching Windows 11 dark mode */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        ::-webkit-scrollbar-track {
            background: #0f172a;
        }
        ::-webkit-scrollbar-thumb {
            background: #334155;
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #475569;
        }
        .win-tab-active {
            background-color: #1e293b;
            border-color: #334155;
        }
    </style>
</head>
<body class="h-full bg-slate-950 text-slate-100 antialiased overflow-hidden flex flex-col select-none">

    <!-- Windows 11 Notepad Top Header Bar with Tabs -->
    <header class="bg-slate-900 border-b border-slate-800/90 flex items-center justify-between px-3 pt-2 shrink-0">
        <!-- Tabs Container -->
        <div class="flex items-center gap-1 overflow-x-auto no-scrollbar max-w-[80vw]">
            @foreach($notes as $note)
                <div id="tab-note-{{ $note->id }}" onclick="selectNote({{ $note->id }})" class="group flex items-center gap-2 px-3 py-1.5 rounded-t-lg border-t border-x text-xs cursor-pointer transition-all duration-150 {{ ($activeNote && $activeNote->id == $note->id) ? 'win-tab-active text-slate-100 font-medium border-slate-700/80 shadow-sm' : 'bg-slate-900/60 border-transparent text-slate-400 hover:bg-slate-800/60 hover:text-slate-300' }}">
                    <svg class="w-3.5 h-3.5 {{ $note->is_pinned ? 'text-amber-400' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <span class="truncate max-w-[140px]">{{ $note->title ?: 'Sem título' }}</span>
                    @if($note->is_pinned)
                        <span class="text-[10px] text-amber-400" title="Fixada">📍</span>
                    @endif
                    <button onclick="event.stopPropagation(); deleteNoteById({{ $note->id }})" class="opacity-0 group-hover:opacity-100 text-slate-500 hover:text-rose-400 p-0.5 rounded transition" title="Fechar nota">
                        ✕
                    </button>
                </div>
            @endforeach

            <!-- New Tab Button -->
            <button onclick="createNewNote()" class="p-1.5 ml-1 text-slate-400 hover:text-white hover:bg-slate-800 rounded-md transition cursor-pointer" title="Nova Nota (Ctrl+N)">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            </button>
        </div>

        <!-- Windows App Badge -->
        <div class="text-[11px] text-slate-500 font-medium px-2 py-1">
            Bloco de Notas
        </div>
    </header>

    <!-- Windows Notepad Menu Bar (Arquivo, Editar, Exibir) -->
    <nav class="bg-slate-900/90 border-b border-slate-800/80 px-4 py-1 flex items-center justify-between shrink-0 text-xs text-slate-300">
        <div class="flex items-center gap-1">
            <!-- Menu Dropdown: Arquivo -->
            <div class="relative group">
                <button class="px-2.5 py-1 rounded hover:bg-slate-800 transition font-medium focus:outline-none">Arquivo</button>
                <div class="absolute left-0 top-full mt-0.5 w-48 bg-slate-900 border border-slate-700/80 rounded-lg shadow-xl py-1 hidden group-hover:block z-50">
                    <button onclick="createNewNote()" class="w-full text-left px-3 py-1.5 hover:bg-slate-800 flex items-center justify-between">
                        <span>Nova Nota</span> <span class="text-[10px] text-slate-500">Ctrl+N</span>
                    </button>
                    @if($activeNote)
                        <button onclick="autoSaveNote()" class="w-full text-left px-3 py-1.5 hover:bg-slate-800 flex items-center justify-between">
                            <span>Salvar</span> <span class="text-[10px] text-slate-500">Ctrl+S</span>
                        </button>
                        <a href="{{ route('notes.export', $activeNote) }}" class="w-full text-left px-3 py-1.5 hover:bg-slate-800 flex items-center justify-between block">
                            <span>Exportar (.txt)</span> <span class="text-[10px] text-slate-500">Ctrl+E</span>
                        </a>
                        <hr class="border-slate-800 my-1">
                        <button onclick="deleteNote()" class="w-full text-left px-3 py-1.5 hover:bg-rose-950/60 text-rose-400 flex items-center justify-between">
                            <span>Excluir Nota</span> <span class="text-[10px] text-slate-500">Del</span>
                        </button>
                    @endif
                </div>
            </div>

            <!-- Menu Dropdown: Editar -->
            <div class="relative group">
                <button class="px-2.5 py-1 rounded hover:bg-slate-800 transition font-medium focus:outline-none">Editar</button>
                <div class="absolute left-0 top-full mt-0.5 w-48 bg-slate-900 border border-slate-700/80 rounded-lg shadow-xl py-1 hidden group-hover:block z-50">
                    @if($activeNote)
                        <button onclick="togglePinNote()" class="w-full text-left px-3 py-1.5 hover:bg-slate-800 flex items-center justify-between">
                            <span>{{ $activeNote->is_pinned ? 'Desfixar Nota' : 'Fixar no Topo' }}</span> <span>📍</span>
                        </button>
                    @endif
                    <button onclick="toggleFontFamily()" class="w-full text-left px-3 py-1.5 hover:bg-slate-800 flex items-center justify-between">
                        <span>Alternar Fonte (Mono/Sans)</span> <span class="text-[10px] text-slate-500">🔤</span>
                    </button>
                </div>
            </div>

            <!-- Menu Dropdown: Exibir / Categorias -->
            <div class="relative group">
                <button class="px-2.5 py-1 rounded hover:bg-slate-800 transition font-medium focus:outline-none">Exibir</button>
                <div class="absolute left-0 top-full mt-0.5 w-44 bg-slate-900 border border-slate-700/80 rounded-lg shadow-xl py-1 hidden group-hover:block z-50">
                    <a href="{{ route('notes.index', ['category' => 'Todas']) }}" class="block px-3 py-1.5 hover:bg-slate-800">Todas as Notas</a>
                    <a href="{{ route('notes.index', ['category' => 'Fixadas']) }}" class="block px-3 py-1.5 hover:bg-slate-800">📍 Notas Fixadas</a>
                    <hr class="border-slate-800 my-1">
                    @foreach($categories as $cat)
                        <a href="{{ route('notes.index', ['category' => $cat]) }}" class="block px-3 py-1.5 hover:bg-slate-800">{{ $cat }}</a>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Right Menu Toolbar Controls -->
        <div class="flex items-center gap-3">
            @if($activeNote)
                <!-- Category badge selector -->
                <div class="flex items-center gap-1 text-[11px] bg-slate-950 px-2 py-0.5 rounded border border-slate-800">
                    <span class="text-slate-400">Categoria:</span>
                    <select id="editor-category" onchange="autoSaveNote()" class="bg-transparent text-slate-200 focus:outline-none cursor-pointer">
                        @foreach($categories as $cat)
                            <option value="{{ $cat }}" class="bg-slate-900" {{ $activeNote->category == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Auto-save Status Indicator -->
                <div id="save-status" class="text-[11px] text-emerald-400 flex items-center gap-1 font-medium">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span>Salvo</span>
                </div>
            @endif

            <!-- Compact Search -->
            <div class="relative">
                <input type="text" id="search-input" value="{{ $search }}" placeholder="Localizar..." class="bg-slate-950 text-[11px] text-slate-200 placeholder-slate-500 pl-6 pr-2 py-1 rounded border border-slate-800 focus:outline-none focus:border-indigo-500 w-32 focus:w-48 transition-all">
                <svg class="w-3 h-3 absolute left-2 top-2 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
        </div>
    </nav>

    <!-- Main Full-Width Editor Canvas (Windows Notepad Canvas) -->
    <main class="flex-1 flex flex-col bg-slate-950 relative overflow-hidden">
        @if($activeNote)
            <div class="flex-1 flex flex-col p-6 max-w-6xl mx-auto w-full">
                <!-- Note Title Input -->
                <input type="text" id="editor-title" value="{{ $activeNote->title }}" placeholder="Sem título" oninput="onTitleInput()" class="bg-transparent text-2xl font-semibold text-slate-100 placeholder-slate-600 focus:outline-none mb-4 border-b border-slate-800/40 pb-2 transition">

                <!-- Note Main Content Textarea -->
                <textarea id="editor-content" placeholder="Digite o texto aqui..." oninput="onContentInput()" onkeyup="updateCursorPos(this)" onclick="updateCursorPos(this)" class="flex-1 w-full bg-transparent text-slate-200 text-sm leading-relaxed placeholder-slate-600 resize-none focus:outline-none font-sans font-normal border-none p-0 selection:bg-indigo-600 selection:text-white">{{ $activeNote->content }}</textarea>
            </div>

            <!-- Windows 11 Notepad Authentic Status Bar -->
            <footer class="h-6 bg-slate-900/90 border-t border-slate-800/80 px-4 flex items-center justify-between text-[11px] text-slate-400 shrink-0 select-none">
                <div class="flex items-center gap-4">
                    <span id="cursor-pos">Ln 1, Col 1</span>
                    <span>|</span>
                    <span>100%</span>
                    <span>|</span>
                    <span>Windows (CRLF)</span>
                    <span>|</span>
                    <span>UTF-8</span>
                </div>
                <div class="flex items-center gap-4">
                    <span id="stat-words">{{ $activeNote->word_count }} palavras</span>
                    <span>•</span>
                    <span id="stat-chars">{{ $activeNote->char_count }} caracteres</span>
                    <span>|</span>
                    <span id="last-saved-time">Alterado às {{ $activeNote->updated_at->format('H:i:s') }}</span>
                </div>
            </footer>
        @else
            <!-- Empty State when no note is open -->
            <div class="flex-1 flex flex-col items-center justify-center text-center p-8">
                <div class="w-16 h-16 rounded-2xl bg-slate-900 border border-slate-800 flex items-center justify-center text-slate-500 mb-4 shadow-sm">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                </div>
                <h2 class="text-base font-semibold text-slate-200">Nenhum documento aberto</h2>
                <p class="text-xs text-slate-400 mt-1 max-w-sm">Crie uma nova nota ou selecione uma aba existente para começar a digitar.</p>
                <button onclick="createNewNote()" class="mt-4 px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-medium rounded-lg transition shadow-md shadow-indigo-600/20">
                    + Nova Nota (Ctrl+N)
                </button>
            </div>
        @endif
    </main>

    <script>
        let activeNoteId = {{ $activeNote ? $activeNote->id : 'null' }};
        let saveTimeout = null;
        let isMonospace = false;
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Keyboard Shortcuts (Ctrl+N, Ctrl+S, Ctrl+E)
        document.addEventListener('keydown', (e) => {
            if (e.ctrlKey || e.metaKey) {
                if (e.key.toLowerCase() === 'n') {
                    e.preventDefault();
                    createNewNote();
                } else if (e.key.toLowerCase() === 's') {
                    e.preventDefault();
                    autoSaveNote();
                } else if (e.key.toLowerCase() === 'e') {
                    e.preventDefault();
                    if (activeNoteId) {
                        window.location.href = `/notes/${activeNoteId}/export`;
                    }
                }
            }
        });

        // Search Input Filter
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

        // Toggle Font Family (Sans <-> Mono)
        function toggleFontFamily() {
            const editorContent = document.getElementById('editor-content');
            if (editorContent) {
                isMonospace = !isMonospace;
                if (isMonospace) {
                    editorContent.classList.add('font-mono-editor');
                } else {
                    editorContent.classList.remove('font-mono-editor');
                }
            }
        }

        // Cursor Position tracker for status bar
        function updateCursorPos(textarea) {
            if (!textarea) return;
            const text = textarea.value.substr(0, textarea.selectionStart);
            const lines = text.split('\n');
            const currentLine = lines.length;
            const currentCol = lines[lines.length - 1].length + 1;
            
            const cursorPosEl = document.getElementById('cursor-pos');
            if (cursorPosEl) {
                cursorPosEl.innerText = `Ln ${currentLine}, Col ${currentCol}`;
            }
        }

        // Create new note
        async function createNewNote() {
            try {
                const response = await fetch("{{ route('notes.store') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ category: 'Geral' })
                });

                const data = await response.json();
                if (data.success) {
                    window.location.href = "{{ route('notes.index') }}?active=" + data.note.id;
                }
            } catch (err) {
                console.error('Erro ao criar nota:', err);
            }
        }

        // Select note tab
        function selectNote(noteId) {
            if (activeNoteId === noteId) return;
            const url = new URL(window.location.href);
            url.searchParams.set('active', noteId);
            window.location.href = url.toString();
        }

        // Auto-save & stats update
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
                    body: JSON.stringify({ title, content, category })
                });

                const data = await response.json();
                if (data.success) {
                    showSaveStatus('Salvo', 'emerald');
                    
                    // Update tab title
                    const activeTabTitle = document.querySelector(`#tab-note-${activeNoteId} span`);
                    if (activeTabTitle) activeTabTitle.innerText = data.note.title || 'Sem título';

                    const lastSavedText = document.getElementById('last-saved-time');
                    if (lastSavedText) lastSavedText.innerText = `Alterado às ${data.updated_at_formatted}`;
                }
            } catch (err) {
                console.error('Erro ao salvar nota:', err);
                showSaveStatus('Erro ao salvar', 'rose');
            }
        }

        function showSaveStatus(text, color) {
            const saveStatus = document.getElementById('save-status');
            if (!saveStatus) return;

            let dotColor = 'bg-emerald-400 animate-pulse';
            let textColor = 'text-emerald-400';

            if (color === 'amber') {
                dotColor = 'bg-amber-400 animate-ping';
                textColor = 'text-amber-400';
            } else if (color === 'rose') {
                dotColor = 'bg-rose-500';
                textColor = 'text-rose-400';
            }

            saveStatus.className = `text-[11px] ${textColor} flex items-center gap-1 font-medium`;
            saveStatus.innerHTML = `<span class="w-1.5 h-1.5 rounded-full ${dotColor}"></span><span>${text}</span>`;
        }

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
                if (data.success) window.location.reload();
            } catch (err) {
                console.error(err);
            }
        }

        async function deleteNote() {
            if (!activeNoteId) return;
            deleteNoteById(activeNoteId);
        }

        async function deleteNoteById(id) {
            if (!confirm('Deseja realmente excluir esta nota?')) return;

            try {
                const response = await fetch(`/notes/${id}`, {
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
