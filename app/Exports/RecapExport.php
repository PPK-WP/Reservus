<?php

namespace App\Exports;

use OpenSpout\Common\Entity\Row;
use OpenSpout\Writer\XLSX\Writer;

/**
 * Membangun berkas ekspor rekap (CSV/XLSX) dari data yang sama dengan tabel
 * (README §9.4, A11). Langsung memakai writer openspout; XLSX zonder ext-gd.
 *
 * Keamanan (AGENTS §H): setiap sel yang diawali karakter formula (=, +, -, @)
 * diberi prefiks `'` sehingga diekspor sebagai teks, bukan dievaluasi Excel.
 */
class RecapExport
{
    /**
     * @param  list<string>  $header
     * @param  list<array<string, mixed>>  $baris
     */
    public function __construct(
        private readonly array $header,
        private readonly array $baris,
    ) {}

    /** Netralkan sel berbahaya untuk spreadsheet. */
    public static function escapeFormula(mixed $nilai): mixed
    {
        if (is_string($nilai) && $nilai !== '' && in_array($nilai[0], ['=', '+', '-', '@'], true)) {
            return "'".$nilai;
        }

        return $nilai;
    }

    /** Unduh CSV ber-BOM UTF-8 lewat stream. */
    public function streamCsv(string $namaFile): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $header = $this->header;
        $baris = $this->baris;

        return response()->streamDownload(function () use ($header, $baris): void {
            $stream = fopen('php://output', 'w');
            // BOM agar Excel membaca UTF-8 dengan benar.
            fwrite($stream, "\xEF\xBB\xBF");
            fputcsv($stream, array_map([self::class, 'escapeFormula'], $header));
            foreach ($baris as $satu) {
                fputcsv($stream, array_map([self::class, 'escapeFormula'], array_values($satu)));
            }
            fclose($stream);
        }, $namaFile, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    /** Unduh berkas XLSX via openspout (file sementara dihapus setelah terkirim). */
    public function downloadXlsx(string $namaFile): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $path = tempnam(sys_get_temp_dir(), 'reservus-rekap');

        $writer = new Writer;
        $writer->openToFile($path);
        $writer->addRow(Row::fromValues(array_map([self::class, 'escapeFormula'], $this->header)));
        foreach ($this->baris as $satu) {
            $writer->addRow(Row::fromValues(array_map([self::class, 'escapeFormula'], array_values($satu))));
        }
        $writer->close();

        return response()->download($path, $namaFile)->deleteFileAfterSend(true);
    }
}