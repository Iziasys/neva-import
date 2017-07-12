<?php

include './headerPdf.php';

/************DEFINITION DES VARIABLES DE BASE**********/
$vehicleId = (int)$_GET['vehicleId'];

$pdf = new VehicleInStockPDF($vehicleId);

$vehicle = $pdf->getVehicle();

$pdf->AliasNbPages();
$pdf->SetTitle(utf8_decode($vehicle->getBrand().' '.$vehicle->getModel().' '.$vehicle->getFinish()));

$pdf->AddPage();
$pdf->printVehicleDetails();
$pdf->Ln(20);
$pdf->printVehicleEquipments();
$pdf->Ln(20);
$pdf->printMoreInformation();
if(is_file($_SERVER["DOCUMENT_ROOT"].getAppPath().'/ressources/vehicleImages/VO/'.$vehicle->getImage1())
    || is_file($_SERVER["DOCUMENT_ROOT"].getAppPath().'/ressources/vehicleImages/VO/'.$vehicle->getImage2())
    || is_file($_SERVER["DOCUMENT_ROOT"].getAppPath().'/ressources/vehicleImages/VO/'.$vehicle->getImage3())
    || is_file($_SERVER["DOCUMENT_ROOT"].getAppPath().'/ressources/vehicleImages/VO/'.$vehicle->getImage4())
){
    $pdf->AddPage();
}
$pdf->printAllPictures();
$pdf->printVehiclePrice();

$pdf->Output();