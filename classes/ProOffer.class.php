<?php

/**
 * Created by PhpStorm.
 * User: DECOCK Stéphane
 * Date: 18/03/2016
 * Time: 11:12
 */
trait ProOffer
{
    function printProProtagonistInformation(\ProOfferPDF $pdf, \string $offerReference, \Users\Structure $sellingStructure, \Users\Structure $buyingStructure){
        $sellerPhone = $sellingStructure->getPhone();
        $sellerMail = $sellingStructure->getEmail();
        $clientPhone = $buyingStructure->getPhone();
        $clientMail = $buyingStructure->getEmail();

        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(90, 12, '');
        $pdf->Cell(90, 12, utf8_decode('Offre N°'.$offerReference), '', 0, 'R');
        $pdf->Ln($pdf->largeLn);
        $pdf->Cell(90, 12, utf8_decode($sellingStructure->getStructureName()));
        $pdf->Cell(90, 12, utf8_decode($buyingStructure->getStructureName()), '', 0, 'R');
        $pdf->Ln($pdf->regularLn);
        $pdf->Cell(90, 12, utf8_decode($sellingStructure->getAddress()));
        $pdf->Cell(90, 12, utf8_decode($buyingStructure->getAddress()), '', 0, 'R');
        $pdf->Ln($pdf->regularLn);
        $pdf->Cell(90, 12, utf8_decode($sellingStructure->getPostalCode().' '.$sellingStructure->getTown()));
        $pdf->Cell(90, 12, utf8_decode($buyingStructure->getPostalCode().' '.$buyingStructure->getTown()), '', 0, 'R');
        $pdf->Ln($pdf->regularLn);
        $pdf->Cell(90, 12, utf8_decode(getPhoneNumber($sellerPhone)));
        $pdf->Cell(90, 12, utf8_decode(getPhoneNumber($clientPhone)), '', 0, 'R');
        $pdf->Ln($pdf->regularLn);
        $pdf->Cell(90, 12,utf8_decode($sellerMail));
        $pdf->Cell(90, 12, utf8_decode($clientMail), '', 0, 'R');
    }

    function printProBlocVehicleWithImage(\ProOfferPDF $pdf, \Offers\Offer $offer, \Vehicle\Details $vehicle, \string $lineDefVehicle){
        $db = databaseConnection();
        $colorItem = \Vehicle\ExternalColorManager::fetchColor($db, $offer->getColor()->getItemId());
        $db = null;
        if(is_a($colorItem, '\Exception')){
            $color = 'De série';
        }
        else{
            $color = $colorItem->getName().' '.$colorItem->getDetails();
        }

        $finish = $vehicle->getFinish();
        $finishName = $finish->getName();
        $model = $finish->getModel();
        $modelName = $model->getName();
        $brand = $model->getBrand();
        $brandName = $brand->getName();

        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(185, 8, utf8_decode('Votre véhicule'), 'B');
        $pdf->Ln($pdf->largeLn);
        $pdf->Ln($pdf->smallLn);
        $pdf->SetFont('Arial', '', 12);
        $pdf->WriteHTML($lineDefVehicle);
        $pdf->Ln($pdf->largeLn);
        $pdf->Cell(50, 12, utf8_decode('Carrosserie :'));
        $pdf->Cell(50, 12, utf8_decode($vehicle->getBodywork()->getName()));
        $pdf->Ln($pdf->regularLn);
        $pdf->Cell(50, 12, utf8_decode('Nb Portes/Places :'));
        $pdf->Cell(50, 12, utf8_decode($vehicle->getDoorsAmount().' portes/ '.$vehicle->getSitsAmount().' places'));
        $pdf->Ln($pdf->regularLn);
        $pdf->Cell(50, 12, utf8_decode('Type :'));
        $pdf->Cell(50, 12, utf8_decode('VP / Véhicule Neuf (0 à 60 kms)'));
        $pdf->Ln($pdf->regularLn);
        $pdf->Cell(50, 12, utf8_decode('Couleur :'));
        $pdf->Cell(50, 12, utf8_decode($color));
        $pdf->Ln($pdf->regularLn);
        $pdf->Cell(50, 12, utf8_decode('Carburant :'));
        $pdf->Cell(50, 12, utf8_decode($vehicle->getFuel()->getName()));
        $pdf->Ln($pdf->regularLn);
        $pdf->Cell(50, 12, utf8_decode('Puissance Fiscale :'));
        $pdf->Cell(50, 12, utf8_decode($vehicle->getFiscalPower().' CV'));
        $pdf->Ln($pdf->regularLn);
        $pdf->Cell(50, 12, utf8_decode('Boite vitesse :'));
        $pdf->Cell(50, 12, utf8_decode($vehicle->getGearbox()->getName()));
        $pdf->Ln($pdf->regularLn);
        $pdf->Cell(50, 12, utf8_decode('Disponibilité :'));
        $pdf->Cell(50, 12, utf8_decode('Commande usine'));
        $pdf->SetY(130);
        $pdf->SetFont('Arial', '', 9);
        $pdf->Cell(185, 12, utf8_decode('Photo non contractuelle'), '', 0, 'R');
        $imagePath = '/ressources/vehicleImages/'.$brand->getName().'/'.$model->getName().'/'.$finish->getName().'/'.$vehicle->getBodywork()->getName().'_'.$vehicle->getDoorsAmount().'.png';
        if(!is_file($_SERVER["DOCUMENT_ROOT"].getAppPath().$imagePath)){
            try{
                $imagePath = '/ressources/vehicleImages/'.$brand->getName().'/'.$model->getName().'/'.$finish->getName().'/'.$vehicle->getBodywork()->getName().'_'.$vehicle->getDoorsAmount().'.jpg';
                $pdf->Image($_SERVER["DOCUMENT_ROOT"].getAppPath().$imagePath, 125, 135, 75, 0, 'JPG');
            }
            catch(Exception $e){
                $pdf->Image($_SERVER["DOCUMENT_ROOT"].getAppPath().$imagePath, 125, 135, 75, 0, 'PNG');
            }
        }
        else{
            $pdf->Image($_SERVER["DOCUMENT_ROOT"].getAppPath().$imagePath, 125, 135, 75, 0, 'PNG');
        }

        $pdf->SetY(185);
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(185, 12, utf8_decode('Prix du véhicule (Hors option) : '.number_format($pdf->getPriceDetails()->getPostTaxesDealerBuyingPrice(), 2, '.', ' ').' '.$pdf->chrEuro.' TTC'), '', 0, 'R');
        $pdf->Ln($pdf->largeLn);
    }

    public function printProBlocRecapPrice(\ProOfferPDF $pdf, \Prices\PriceDetails $priceDetails){
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(185, 8, utf8_decode('Total pour votre véhicule'), 'T');
        $pdf->Ln($pdf->largeLn);
        $pdf->SetFont('Arial', '', 11);

        $leftPadding = 90;
        $wordingWidth = 65;
        $priceWidth = 30;
        $pdf->Cell($leftPadding, 12);
        $pdf->Cell($wordingWidth, 12, utf8_decode('Prix du véhicule (Hors option) :'));
        $pdf->Cell($priceWidth, 12, utf8_decode(number_format($priceDetails->getPostTaxesBuyingPriceInEuro(), 2, '.', ' ').' '.$pdf->chrEuro.' TTC'), '', 0, 'R');
        $pdf->Ln($pdf->regularLn);
        $pdf->Cell($leftPadding, 12);
        $pdf->Cell($wordingWidth, 12, utf8_decode('Total options :'));
        $pdf->Cell($priceWidth, 12, utf8_decode(number_format($priceDetails->getPostTaxesOptionPrice(), 2, '.', ' ').' '.$pdf->chrEuro.' TTC'), '', 0, 'R');
        $pdf->Ln($pdf->regularLn);
        $pdf->Cell($leftPadding, 12);
        $pdf->Cell($wordingWidth, 12, utf8_decode('Forfait de Mise à disposition :'));
        $pdf->Cell($priceWidth, 12, utf8_decode(number_format($priceDetails->getPostTaxesPackageProvision(), 2, '.', ' ').' '.$pdf->chrEuro.' TTC'), '', 0, 'R');
        $pdf->Ln($pdf->largeLn);
        $pdf->Cell($leftPadding, 12);
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell($wordingWidth, 8, utf8_decode('Montant total :'), 'TLB');
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell($priceWidth, 8, utf8_decode(number_format($priceDetails->getPostTaxesAllIncludedPrice(), 2, '.', ' ').' '.$pdf->chrEuro.' TTC'), 'TRB', 0, 'R');
        $pdf->Ln($pdf->largeLn);
        $pdf->Cell($leftPadding, 12);
        $pdf->Cell($wordingWidth, 12, utf8_decode('Bonus/Malus** :'));
        $pdf->Cell($priceWidth, 12, utf8_decode(number_format($priceDetails->getBonusPenalty(), 2, '.', ' ').' '.$pdf->chrEuro.' TTC'), '', 0, 'R');
        $pdf->Ln($pdf->regularLn);
        $pdf->Cell($leftPadding, 12);
        $pdf->Cell($wordingWidth, 12, utf8_decode('Carte Grise** :'));
        $pdf->Cell($priceWidth, 12, utf8_decode(number_format($priceDetails->getRegistrationCardAmount(), 2, '.', ' ').' '.$pdf->chrEuro.' TTC'), '', 0, 'R');
        $pdf->Ln($pdf->largeLn);
        /*$pdf->Cell($leftPadding, 12);
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell($wordingWidth, 8, utf8_decode('Total clé en mains :'), 'TLB');
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell($priceWidth, 8, utf8_decode(number_format($priceDetails->getPostTaxesKeyInHandPrice(), 2, '.', ' ').' '.$pdf->chrEuro.' TTC***'), 'TRB', 0, 'R');
        $pdf->Ln($pdf->largeLn);*/

        $pdf->SetFont('Arial', 'I', 9);
        $pdf->Cell(185, 12, utf8_decode('*Taux donné à titre indicatif.'));
        $pdf->Ln($pdf->smallLn);
        $pdf->Cell(185, 12, utf8_decode('**Sous réserve de modification des tarifs du cheval fiscal et du malus écologique en vigueur et de confirmation du taux de Co2.'));
        /*$pdf->Ln($pdf->smallLn);
        $pdf->Cell(185, 12, utf8_decode('***Sous réserve de la disponibilité du véhicule.'));*/
    }
}