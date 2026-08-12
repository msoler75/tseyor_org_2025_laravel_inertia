<?php

namespace App\Contracts;

use Illuminate\Database\Eloquent\Model;

/**
 * Interfaz para modelos que pueden generar PDF.
 * Usada por PDFGenerator para generar documentos PDF desde el contenido del modelo.
 */
interface PdfGenerable
{
    /**
     * Ruta relativa donde se almacena el PDF generado.
     */
    public function getPdfPath(): string;

    /**
     * Nombre del archivo PDF.
     */
    public function getPdfFilename(): string;

    /**
     * Título del contenido para el PDF.
     */
    public function getTituloPdf(): string;

    /**
     * Texto en markdown del contenido.
     */
    public function getTextoPdf(): string;

    /**
     * Ruta de la imagen del contenido (para portada del PDF).
     */
    public function getImagenPdf(): ?string;

    /**
     * Timestamp de la última actualización.
     */
    public function getUpdatedAtTimestamp(): int;
}
