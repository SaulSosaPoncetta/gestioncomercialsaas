<?php

namespace App\Traits;

trait ExportaExcel
{
    protected function exportarExcel(string $nombreArchivo, array $encabezados, iterable $filas)
    {
        $html = '<html xmlns:x="urn:schemas-microsoft-com:office:excel">';
        $html .= '<head><meta charset="UTF-8"></head><body>';
        $html .= '<table border="1">';

        $html .= '<thead><tr>';
        foreach ($encabezados as $encabezado) {
            $html .= '<th style="background:#14532d;color:#ffffff;font-weight:bold;padding:6px;">' . htmlspecialchars($encabezado) . '</th>';
        }
        $html .= '</tr></thead><tbody>';

        foreach ($filas as $fila) {
            $html .= '<tr>';
            foreach ($fila as $valor) {
                $html .= '<td style="padding:6px;">' . htmlspecialchars((string) $valor) . '</td>';
            }
            $html .= '</tr>';
        }

        $html .= '</tbody></table></body></html>';

        return response($html, 200, [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $nombreArchivo . '.xls"',
        ]);
    }
}