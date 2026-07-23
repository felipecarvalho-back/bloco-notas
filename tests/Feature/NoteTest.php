<?php

use App\Models\Note;

test('pode carregar a página inicial do bloco de notas', function () {
    Note::factory()->create(['title' => 'Nota Teste 1']);

    $response = $this->get('/');

    $response->assertStatus(200)
        ->assertSee('Nota Teste 1')
        ->assertSee('Bloco de Notas');
});

test('pode criar uma nova nota via api', function () {
    $response = $this->postJson('/notes', [
        'category' => 'Trabalho',
    ]);

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
        ]);

    $this->assertDatabaseHas('notes', [
        'title' => 'Nova Nota',
        'category' => 'Trabalho',
    ]);
});

test('pode atualizar uma nota com auto save', function () {
    $note = Note::factory()->create([
        'title' => 'Título Antigo',
        'content' => 'Conteúdo antigo',
    ]);

    $response = $this->patchJson("/notes/{$note->id}", [
        'title' => 'Título Atualizado',
        'content' => 'Conteúdo atualizado do bloco de notas.',
    ]);

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
        ]);

    $this->assertDatabaseHas('notes', [
        'id' => $note->id,
        'title' => 'Título Atualizado',
        'content' => 'Conteúdo atualizado do bloco de notas.',
    ]);
});

test('pode alternar o status de fixada de uma nota', function () {
    $note = Note::factory()->create([
        'is_pinned' => false,
    ]);

    $response = $this->postJson("/notes/{$note->id}/pin");

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'is_pinned' => true,
        ]);

    expect($note->fresh()->is_pinned)->toBeTrue();
});

test('pode excluir uma nota', function () {
    $note = Note::factory()->create();

    $response = $this->deleteJson("/notes/{$note->id}");

    $response->assertStatus(200)
        ->assertJson(['success' => true]);

    $this->assertDatabaseMissing('notes', [
        'id' => $note->id,
    ]);
});

test('pode exportar uma nota em formato txt', function () {
    $note = Note::factory()->create([
        'title' => 'Nota Exportação',
        'content' => 'Linha 1 de conteúdo',
    ]);

    $response = $this->get("/notes/{$note->id}/export");

    $response->assertStatus(200)
        ->assertHeader('Content-Type', 'text/plain; charset=utf-8')
        ->assertSee('Nota Exportação')
        ->assertSee('Linha 1 de conteúdo');
});

test('pode importar um arquivo de texto para criar uma nova nota', function () {
    $response = $this->postJson('/notes/import', [
        'title' => 'documento_importado',
        'content' => 'Conteúdo importado do arquivo .txt local.',
        'category' => 'Geral',
    ]);

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
        ]);

    $this->assertDatabaseHas('notes', [
        'title' => 'documento_importado',
        'content' => 'Conteúdo importado do arquivo .txt local.',
    ]);
});
