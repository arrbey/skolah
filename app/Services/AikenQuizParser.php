<?php

namespace App\Services;

/**
 * Parser untuk Aiken Format — format soal pilihan ganda standar dari Moodle.
 *
 * Format:
 *   Pertanyaan di baris pertama.
 *   A. Pilihan pertama
 *   B. Pilihan kedua
 *   C. Pilihan ketiga
 *   D. Pilihan keempat
 *   ANSWER: C
 *
 *   Pertanyaan berikutnya?
 *   A. ...
 *   ANSWER: A
 *
 * Aturan:
 * - Antar soal dipisahkan dengan baris kosong.
 * - Pilihan diawali huruf kapital A-Z diikuti titik atau kurung.
 * - Baris jawaban: "ANSWER: <huruf>" (case-insensitive).
 * - Minimal 2 pilihan per soal.
 */
class AikenQuizParser
{
    /** @var list<string> */
    protected array $errors = [];

    /**
     * Parse string Aiken → array of questions.
     *
     * @return list<array{question:string, options:list<array{text:string,correct:bool}>, correct_letter:string}>
     */
    public function parse(string $text): array
    {
        $this->errors = [];

        // Normalisasi newline
        $text = str_replace(["\r\n", "\r"], "\n", $text);

        // Split per block (baris kosong)
        $blocks = preg_split('/\n\s*\n/', trim($text));

        $questions = [];

        foreach ($blocks as $index => $block) {
            $block = trim($block);
            if ($block === '') {
                continue;
            }

            $parsed = $this->parseBlock($block, $index + 1);
            if ($parsed !== null) {
                $questions[] = $parsed;
            }
        }

        return $questions;
    }

    /**
     * Parse 1 block soal.
     *
     * @return array{question:string, options:list<array{text:string,correct:bool}>, correct_letter:string}|null
     */
    protected function parseBlock(string $block, int $blockNum): ?array
    {
        $lines = array_values(array_filter(
            array_map('trim', explode("\n", $block)),
            fn ($l) => $l !== ''
        ));

        if (count($lines) < 3) {
            $this->errors[] = "Soal #{$blockNum}: Block terlalu pendek (butuh minimal pertanyaan + 2 pilihan + ANSWER).";
            return null;
        }

        // Baris terakhir yang diawali "ANSWER:" = jawaban
        $answerLetter = null;
        $answerIndex  = null;
        foreach ($lines as $i => $line) {
            if (preg_match('/^ANSWER\s*:\s*([A-Z])\s*$/i', $line, $m)) {
                $answerLetter = strtoupper($m[1]);
                $answerIndex  = $i;
                break;
            }
        }

        if ($answerLetter === null) {
            $this->errors[] = "Soal #{$blockNum}: Tidak ditemukan baris 'ANSWER: <huruf>'.";
            return null;
        }

        // Pilihan = baris yang match pattern "A. text" atau "A) text", sebelum ANSWER
        $optionLines  = [];
        $questionLines = [];
        foreach ($lines as $i => $line) {
            if ($i >= $answerIndex) {
                break;
            }

            if (preg_match('/^([A-Z])[\.\)]\s*(.+)$/', $line, $m)) {
                $optionLines[] = [
                    'letter' => strtoupper($m[1]),
                    'text'   => trim($m[2]),
                ];
            } else {
                // Bukan pilihan → bagian pertanyaan
                if (empty($optionLines)) {
                    $questionLines[] = $line;
                } else {
                    // Baris setelah pilihan dimulai tapi bukan pilihan = append ke pilihan terakhir
                    $last = array_key_last($optionLines);
                    $optionLines[$last]['text'] .= ' ' . $line;
                }
            }
        }

        $question = trim(implode(' ', $questionLines));

        if ($question === '') {
            $this->errors[] = "Soal #{$blockNum}: Pertanyaan kosong.";
            return null;
        }

        if (count($optionLines) < 2) {
            $this->errors[] = "Soal #{$blockNum}: Minimal 2 pilihan jawaban.";
            return null;
        }

        // Validasi huruf jawaban ada di pilihan
        $letters = array_column($optionLines, 'letter');
        if (! in_array($answerLetter, $letters, true)) {
            $this->errors[] = "Soal #{$blockNum}: Huruf jawaban '{$answerLetter}' tidak ada di pilihan.";
            return null;
        }

        $options = array_map(fn ($opt) => [
            'text'    => $opt['text'],
            'correct' => $opt['letter'] === $answerLetter,
        ], $optionLines);

        return [
            'question'       => $question,
            'options'        => array_values($options),
            'correct_letter' => $answerLetter,
        ];
    }

    /** @return list<string> */
    public function getErrors(): array
    {
        return $this->errors;
    }

    public function hasErrors(): bool
    {
        return count($this->errors) > 0;
    }
}
