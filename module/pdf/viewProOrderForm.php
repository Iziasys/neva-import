<?php

include './headerPdf.php';

/************DEFINITION DES VARIABLES DE BASE**********/
$offerReference = $_GET['offerReference'];

$pdf = new ProOfferPDF($offerReference);

$offer = $pdf->getOffer();
$lineDefVehicle = $pdf->getLineDefVehicle();
$today = new DateTime();

$pdf->AliasNbPages();
$pdf->SetTitle(utf8_decode('Bon de commande N°'.$offerReference));

$pdf->AddPage();
$pdf->SetFont('Arial','B',16);
$pdf->callPrintProProtagonistInformation();
$pdf->Ln(20);
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(200, 12, utf8_decode('Bon de Commande'), '', 0, 'C');
$pdf->Ln(5);
$pdf->SetFont('Arial', '', 9);
$pdf->Cell(200, 12, utf8_decode('En date du '.$today->format('d/m/Y').' - Validité de '.$GLOBALS['__VALIDITY_DURATION'].' jours sauf modification du tarif constructeur'), '', 0, 'C');
$pdf->Ln(4);
$pdf->Cell(200, 12, utf8_decode('Les montants présentés dans cette offre sont calculés au taux de TVA en vigueur de '.$offer->getVatRate().'%'), '', 0, 'C');
$pdf->Ln(15);
$pdf->callPrintProBlocVehicleWithImage();
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
$pdf->Output();