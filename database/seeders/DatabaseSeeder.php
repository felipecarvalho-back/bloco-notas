<?php

namespace Database\Seeders;

use App\Models\Note;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Note::create([
            'title' => 'Bem-vindo ao seu Bloco de Notas Desktop!',
            'content' => "Este é um aplicativo desktop completo construído com Laravel 13 e NativePHP!\n\nRecursos inclusos:\n- Salvamento automático em tempo real\n- Categorias de organização\n- Fixação de notas importantes\n- Pesquisa instantânea na barra lateral\n- Contador de palavras e caracteres\n- Exportação para arquivo de texto (.txt)\n\nExperimente criar uma nova nota usando o botão na barra lateral!",
            'category' => 'Geral',
            'is_pinned' => true,
            'is_archived' => false,
        ]);

        Note::create([
            'title' => 'Lista de Tarefas da Semana',
            'content' => "1. [x] Configurar ambiente NativePHP\n2. [x] Criar estrutura de banco de dados\n3. [ ] Finalizar o design da interface com Tailwind CSS\n4. [ ] Testar atalhos e auto-save\n5. [ ] Exportar versão desktop",
            'category' => 'Trabalho',
            'is_pinned' => true,
            'is_archived' => false,
        ]);

        Note::create([
            'title' => 'Ideias para futuros projetos',
            'content' => "- Aplicativo de controle financeiro pessoal com relatórios visuais\n- Dashboard de monitoramento de servidores local\n- Leitor de feed RSS minimalista em NativePHP",
            'category' => 'Ideias',
            'is_pinned' => false,
            'is_archived' => false,
        ]);
    }
}
