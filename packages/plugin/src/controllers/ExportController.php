<?php
/*
 * Freeform Surveys & Polls plugin for Craft CMS 3.x
 *
 * @link      https://solspace.com
 * @copyright Copyright (c) 2022 Solspace
 */

namespace Solspace\SurveysPolls\controllers;

use craft\web\Controller;

class ExportController extends Controller
{
    public function actionPdf()
    {
        $images = \Craft::$app->request->post('imageData');

        $pdf = new \TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT);
        $pdf->setAuthor(\Craft::$app->getUser()->getIdentity()->getFullName());
        $pdf->setTitle('Export of data');

        $pdf->setJPEGQuality(75);

        foreach ($images as $image) {
            list($_, $encoded) = explode(',', $image);
            $decoded = base64_decode($encoded);

            $pdf->AddPage();
            $pdf->Image('@'.$decoded, 10, 20, 190);
        }

        $pdf->lastPage();

        $pdf->Output('some_pdf');

        exit();
    }

    public function actionImages()
    {
        $images = \Craft::$app->request->post('imageData');

        $zip = new \ZipArchive();

        $tmp = tempnam('.', '');
        $zip->open($tmp, \ZipArchive::CREATE);

        $count = 0;
        foreach ($images as $image) {
            $name = (++$count).'_field.jpg';

            list($_, $encoded) = explode(',', $image);
            $decoded = base64_decode($encoded);

            $zip->addFromString($name, $decoded);
        }

        $zip->close();

        // send the file to the browser as a download
        header('Content-disposition: attachment; filename=download.zip');
        header('Content-type: application/zip');
        readfile($tmp);
    }
}
