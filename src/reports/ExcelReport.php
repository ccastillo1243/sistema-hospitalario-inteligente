<?php

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ExcelReport
{
    public static function tabla(string $titulo, array $encabezados, array $filas, string $nombreArchivo): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle(substr($titulo, 0, 31));

        foreach ($encabezados as $col => $h) {
            $sheet->setCellValue(Coordinate::stringFromColumnIndex($col + 1) . '1', $h);
        }
        $sheet->getStyle('1:1')->getFont()->setBold(true);

        $rowNum = 2;
        foreach ($filas as $fila) {
            $colNum = 1;
            foreach ($fila as $valor) {
                $sheet->setCellValue(Coordinate::stringFromColumnIndex($colNum) . $rowNum, $valor);
                $colNum++;
            }
            $rowNum++;
        }

        foreach (range(1, count($encabezados)) as $col) {
            $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($col))->setAutoSize(true);
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $nombreArchivo . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}
