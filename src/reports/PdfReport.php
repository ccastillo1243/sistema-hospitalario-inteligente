<?php

class PdfReport
{
    public static function tabla(string $titulo, array $encabezados, array $filas, string $nombreArchivo): void
    {
        $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8');
        $pdf->SetCreator('Sistema Hospitalario Inteligente');
        $pdf->SetTitle($titulo);
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetMargins(15, 15, 15);
        $pdf->AddPage();

        $pdf->SetFont('helvetica', 'B', 14);
        $pdf->Cell(0, 10, $titulo, 0, 1, 'L');
        $pdf->SetFont('helvetica', '', 8);
        $pdf->Cell(0, 6, 'Generado: ' . date('Y-m-d H:i'), 0, 1, 'L');
        $pdf->Ln(4);

        $anchoColumna = 180 / max(count($encabezados), 1);

        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->SetFillColor(230, 230, 230);
        foreach ($encabezados as $h) {
            $pdf->Cell($anchoColumna, 8, $h, 1, 0, 'L', true);
        }
        $pdf->Ln();

        $pdf->SetFont('helvetica', '', 8);
        foreach ($filas as $fila) {
            foreach ($fila as $valor) {
                $pdf->Cell($anchoColumna, 7, (string) $valor, 1, 0, 'L');
            }
            $pdf->Ln();
        }

        $pdf->Output($nombreArchivo, 'D');
        exit;
    }
}
