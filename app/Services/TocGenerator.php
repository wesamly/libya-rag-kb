<?php

namespace App\Services;

use DOMDocument;
use DOMXPath;
use Illuminate\Support\Str;

class TocGenerator
{
    protected array $toc = [];
    protected int $counter = 1;

    public function generate(string $html, array $headingLevels = ['h1', 'h2', 'h3']): array
    {
        $this->toc = [];
        $this->counter = 1;

        $dom = new DOMDocument();
        // Suppress warnings for malformed HTML
        libxml_use_internal_errors(true);
        $dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'), LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();

        $xpath = new DOMXPath($dom);
        $headings = $xpath->query('//' . implode('|//', $headingLevels));

        foreach ($headings as $heading) {
            $title = trim($heading->textContent);
            if (empty($title)) continue;

            // Generate slug-based ID (fallback to section-{n})
            $id = $this->generateId($title);

            // Ensure uniqueness (e.g., duplicate headings)
            $originalId = $id;
            $attempt = 0;
            while (in_array($id, array_column($this->toc, 'id'))) {
                $id = $originalId . '-' . (++$attempt);
            }

            // Set/override id attribute
            $heading->setAttribute('id', $id);

            $this->toc[] = [
                'id' => $id,
                'title' => $title,
                'level' => intval(ltrim($heading->nodeName, 'h')), // e.g., h2 → 2
            ];
        }

        // Save updated HTML
        $updatedHtml = $dom->saveHTML();

        return [
            'toc' => $this->toc,
            'content' => $updatedHtml,
        ];
    }

    protected function generateId(string $title): string
    {
        // Laravel-style slug: lowercase, kebab-case, remove unsafe chars
        $slug = Str::slug($title, '-', 'utf-8');
        return $slug ?: 'section-' . $this->counter++;
    }
}