<?php

namespace App\Models;

use Database\Factories\NoteFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    /** @use HasFactory<NoteFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'content',
        'category',
        'is_pinned',
        'is_archived',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_pinned' => 'boolean',
            'is_archived' => 'boolean',
        ];
    }

    /**
     * Retorna um snippet limpo do conteúdo para a lista lateral.
     */
    public function getSnippetAttribute(): string
    {
        if (empty($this->content)) {
            return 'Nota vazia...';
        }

        return mb_strimwidth(strip_tags($this->content), 0, 80, '...');
    }

    /**
     * Retorna a contagem de palavras do conteúdo.
     */
    public function getWordCountAttribute(): int
    {
        if (empty($this->content)) {
            return 0;
        }

        return str_word_count(strip_tags($this->content));
    }

    /**
     * Retorna a contagem de caracteres do conteúdo.
     */
    public function getCharCountAttribute(): int
    {
        return mb_strlen($this->content ?? '');
    }
}
