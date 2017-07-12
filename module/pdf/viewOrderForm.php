<?php

include './headerPdf.php';

/************DEFINITION DES VARIABLES DE BASE**********/
$offerReference = $_GET['offerReference'];

$pdf = new OfferPDF($offerReference);

$offer = $pdf->getOffer();
$lineDefVehicle = $pdf->getLineDefVehicle();

$pdf->AliasNbPages();
$pdf->SetTitle(utf8_decode('Bon de commande N°'.$offerReference));

$pdf->AddPage();
$pdf->SetFont('Arial','B',16);
$pdf->printProtagonistInformation($offerReference);
$pdf->Ln(20);
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(200, 12, utf8_decode('Bon de Commande'), '', 0, 'C');
$pdf->Ln(5);
$pdf->SetFont('Arial', '', 9);
$pdf->Cell(200, 12, utf8_decode('En date du '.$offer->getCreationDate()->format('d/m/Y').' - Validité de '.$GLOBALS['__VALIDITY_DURATION'].' jours sauf modification du tarif constructeur'), '', 0, 'C');
$pdf->Ln(4);
$pdf->Cell(200, 12, utf8_decode('Les montants présentés dans cette offre sont calculés au taux de TVA en vigueur de '.$offer->getVatRate().'%'), '', 0, 'C');
$pdf->Ln(15);
$pdf->printBlocVehicleWithImage();
$pdf->Ln(20);
$pdf->printBlocSerialEquipments();
$pdf->AddPage();
$pdf->SetFont('Arial', '', 12);
$pdf->WriteHTML($lineDefVehicle);
$pdf->Ln(20);
$pdf->printBlocOptionalEquipment();
$pdf->Ln(15);
$pdf->printBlocPacks();
$pdf->Ln(15);
$pdf->printBlocColor();
$pdf->Ln(15);
$pdf->printBlocRims();
$pdf->AddPage();
$pdf->SetFont('Arial', '', 12);
$pdf->WriteHTML($lineDefVehicle);
$pdf->Ln(20);
$pdf->printBlocRecapPrice();
$pdf->Ln(20);
$pdf->printBlocRecapPackageContent();
$pdf->Ln(20);
$pdf->printBlocSignatures();

$nbPage = $pdf->setSourceFile($_SERVER["DOCUMENT_ROOT"].getAppPath().'/ressources/pdf/demande_de_carte_grise.pdf');
for($i = 0; $i < $nbPage; $i++){
    $pdf->addPageToIgnoreHeader($pdf->PageNo() + 1);
    $pdf->addPageToIgnoreFooter($pdf->PageNo() + 1);
    $pdf->AddPage();
    $tplIdx = $pdf->importPage($i + 1);
    $pdf->useTemplate($tplIdx);
}
$nbPage = $pdf->setSourceFile($_SERVER["DOCUMENT_ROOT"].getAppPath().'/ressources/pdf/procuration_modele.pdf');
for($i = 0; $i < $nbPage; $i++){
    $pdf->addPageToIgnoreHeader($pdf->PageNo() + 1);
    $pdf->addPageToIgnoreFooter($pdf->PageNo() + 1);
    $pdf->AddPage();
    $tplIdx = $pdf->importPage($i + 1);
    $pdf->useTemplate($tplIdx);
}
$nbPage = $pdf->setSourceFile($_SERVER["DOCUMENT_ROOT"].getAppPath().'/ressources/pdf/cession.pdf');
for($i = 0; $i < $nbPage; $i++){
    $pdf->addPageToIgnoreHeader($pdf->PageNo() + 1);
    $pdf->addPageToIgnoreFooter($pdf->PageNo() + 1);
    $pdf->AddPage();
    $tplIdx = $pdf->importPage($i + 1);
    $pdf->useTemplate($tplIdx);
}
$pdf->Output();