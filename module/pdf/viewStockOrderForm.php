<?php

include './headerPdf.php';

/************DEFINITION DES VARIABLES DE BASE**********/
$offerReference = $_GET['offerReference'];

$pdf = new StockOfferPDF($offerReference);

$db = databaseConnection();
$vat = \Prices\VatManager::fetchFrenchVat($db);
$db = null;

$offer = $pdf->getOffer();
$lineDefVehicle = $pdf->getLineDefVehicle();

$pdf->AliasNbPages();
$pdf->SetTitle(utf8_decode('Offre de prix N°'.$offerReference));

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
$pdf->Cell(200, 12, utf8_decode('Les montants présentés dans cette offre sont calculés au taux de TVA en vigueur de '.$vat->getAmount().'%'), '', 0, 'C');
$pdf->Ln(15);
$pdf->printBlocVehicleWithImage();
$pdf->Ln(20);
$pdf->printBlocEquipments();

//Vérification pour savoir si on continue sur la même page ou si on en ajoute une nouvelle
if($pdf->GetY() > 120){
    $pdf->AddPage();
    $pdf->SetFont('Arial', '', 12);
    $pdf->WriteHTML($lineDefVehicle);
}

$pdf->AddPage();
$pdf->SetFont('Arial', '', 12);
$pdf->WriteHTML(utf8_decode($lineDefVehicle));
$pdf->Ln(20);
$pdf->printBlocRecapPrice();
$pdf->Ln(20);
$pdf->printBlocRecapPackageContent();
$pdf->Ln(20);
$pdf->printBlocSignatures();
$pdf->Output();