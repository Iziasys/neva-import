<?php

/**
 * Created by PhpStorm.
 * User: avngrp
 * Date: 29/02/2016
 * Time: 16:33
 */
class PDF extends FPDI{
    public $regularLn = 5, $smallLn = 3, $largeLn = 10;
    public $chrEuro;
    protected $structure, $user;
    protected $pagesToIgnoreHeader, $pagesToIgnoreFooter;

    /*******************SETTERS & GETTERS*****************/
    /**
     * @param \Users\Structure $structure
     */
    public function setStructure(\Users\Structure $structure){
        $this->structure = $structure;
    }

    /**
     * @return \Users\Structure
     */
    public function getStructure():\Users\Structure{
        return $this->structure;
    }

    /**
     * @param \Users\User $user
     */
    public function setUser(\Users\User $user){
        $this->user = $user;
    }

    /**
     * @return \Users\User
     */
    public function getUser():\Users\User{
        return $this->user;
    }

    /**
     * @param array $pagesToIgnoreHeader
     */
    public function setPagesToIgnoreHeader(array $pagesToIgnoreHeader){
        $this->pagesToIgnoreHeader = $pagesToIgnoreHeader;
    }

    /**
     * @return array
     */
    public function getPagesToIgnoreHeader():array{
        return $this->pagesToIgnoreHeader;
    }

    /**
     * @param int $pageNumber
     */
    public function addPageToIgnoreHeader(\int $pageNumber){
        $this->pagesToIgnoreHeader[] = $pageNumber;
    }

    /**
     * @param array $pagesToIgnoreFooter
     */
    public function setPagesToIgnoreFooter(array $pagesToIgnoreFooter){
        $this->pagesToIgnoreFooter = $pagesToIgnoreFooter;
    }

    /**
     * @return array
     */
    public function getPagesToIgnoreFooter():array{
        return $this->pagesToIgnoreFooter;
    }

    /**
     * @param int $pageNumber
     */
    public function addPageToIgnoreFooter(\int $pageNumber){
        $this->pagesToIgnoreFooter[] = $pageNumber;
    }

    /**
     * @param string $chrEuro
     */
    public function setChrEuro(\string $chrEuro){
        $this->chrEuro = $chrEuro;
    }

    /**
     * @return string
     */
    public function getChrEuro():\string{
        return $this->chrEuro;
    }
    /*******************SETTERS & GETTERS*****************/

    /*******************CONSTRUCTOR*****************/
    /**
     * PDF constructor.
     *
     * @param \Users\Structure $structure
     * @param \Users\User      $user
     */
    public function __construct(\Users\Structure $structure, \Users\User $user)
    {
        parent::__construct();
        $this->setStructure($structure);
        $this->setUser($user);

        $this->fontpath = $_SERVER["DOCUMENT_ROOT"].getAppPath().'/theme/font/';
        $this->chrEuro = utf8_encode(chr(128));

        $this->setPagesToIgnoreHeader(array());
        $this->setPagesToIgnoreFooter(array());
    }
    /*******************CONSTRUCTOR*****************/

    //En-tete
    public function Header(){
        if(!in_array($this->PageNo(), $this->getPagesToIgnoreHeader())){
            $structure = $this->getStructure();
            if($structure->getIsPartner()){
                $db = databaseConnection();
                $printedStructure = \Users\StructureManager::fetchPrimaryStructure($db);
                $db = null;
            }
            else{
                $printedStructure = $structure;
            }
            $this->SetFont('Arial', 'B', 12);
            $this->Cell(50, 0, utf8_decode($printedStructure->getStructureName()));
            $this->Ln($this->regularLn);
            $this->SetFont('Arial', '', 12);
            $this->Cell(50, 0, utf8_decode($printedStructure->getAddress()));
            $this->Ln($this->regularLn);
            $this->Cell(50, 0, utf8_decode($printedStructure->getPostalCode().' '.$printedStructure->getTown()));
            $this->Ln($this->regularLn);
            $this->Cell(50, 0, utf8_decode('Tél : '.getPhoneNumber($printedStructure->getPhone())));
            $this->Ln($this->largeLn);
            $this->Cell(50, 0, utf8_decode($printedStructure->getEmail()));
            $this->Ln($this->regularLn);
            $this->setY(5);
            $this->Cell(115);
            //Ici multiple try pour etre sur d'afficher le logo renseigné
            if(is_file($_SERVER["DOCUMENT_ROOT"].getAppPath().'/theme/images/bani/'.$printedStructure->getImageName())){
                try{
                    $imageInformation = explode('.', $printedStructure->getImageName());
                    $imageExtension = $imageInformation[count($imageInformation) - 1];
                    $this->Image($_SERVER["DOCUMENT_ROOT"].getAppPath().'/theme/images/bani/'.$printedStructure->getImageName(), 100, 10, 0, 20, $imageExtension);
                }
                catch(Exception $e){
                    try{
                        $this->Image($_SERVER["DOCUMENT_ROOT"].getAppPath().'/theme/images/bani/'.$printedStructure->getImageName(), 100, 10, 0, 20, 'PNG');
                    }
                    catch(Exception $e){
                        $this->Image($_SERVER["DOCUMENT_ROOT"].getAppPath().'/theme/images/bani/'.$printedStructure->getImageName(), 100, 10, 0, 20, 'JPG');
                    }
                }
            }

            $this->SetY(50);
        }
    }

    //Pied de page
    public function Footer(){
        if(!in_array($this->PageNo(), $this->getPagesToIgnoreFooter())){
            $structure = $this->getStructure();
            if($structure->getIsPartner()){
                $db = databaseConnection();
                $printedStructure = \Users\StructureManager::fetchPrimaryStructure($db);
                $db = null;
            }
            else{
                $printedStructure = $structure;
            }
            $this->setY(-20);
            $this->SetFont('Arial', 'I', 8);
            $this->Cell(0, 10, 'Page '.$this->PageNo().'/{nb}', 0, 0, 'R');
            $this->Ln();
            $this->Cell(0, 0, $printedStructure->getStructureName().' - '.$printedStructure->getAddress().' - '.$printedStructure->getPostalCode().' '.$printedStructure->getTown(), 0, 0, 'C');
            $this->Ln(3);
            $this->Cell(0, 0, utf8_decode('Tél : '.getPhoneNumber($printedStructure->getPhone()).' '.preg_replace_callback('/([€]+)/', function(){ return $_SESSION['euros']; }, $printedStructure->getSocietyDetails()).' - SIRET '.$printedStructure->getSiret().' APE '.$printedStructure->getApe()), 0, 0, 'C');
        }
    }

    public function PDF($orientation='P', $unit='mm', $size='A4'){
        // Appel au constructeur parent
        $this->FPDF($orientation,$unit,$size);
        // Initialisation
        $this->B = 0;
        $this->I = 0;
        $this->U = 0;
        $this->HREF = '';
    }

    var $B;
    var $I;
    var $U;
    var $HREF;
    var $fontList;
    var $issetfont;
    var $issetcolor;

    public function PDF_HTML($orientation='P', $unit='mm', $format='A4')
    {
        //Call parent constructor
        $this->FPDF($orientation,$unit,$format);
        //Initialization
        $this->B=0;
        $this->I=0;
        $this->U=0;
        $this->HREF='';
        $this->fontlist=array('arial', 'times', 'courier', 'helvetica', 'symbol');
        $this->issetfont=false;
        $this->issetcolor=false;
    }

    public function WriteHTML($html)
    {
        //HTML parser
        $html=strip_tags($html,"<b><u><i><a><img><p><br><strong><em><font><tr><blockquote>"); //supprime tous les tags sauf ceux reconnus
        $html=str_replace("\n",' ',$html); //remplace retour à la ligne par un espace
        $a=preg_split('/<(.*)>/U',$html,-1,PREG_SPLIT_DELIM_CAPTURE); //éclate la chaîne avec les balises
        foreach($a as $i=>$e)
        {
            if($i%2==0)
            {
                //Text
                if($this->HREF)
                    $this->PutLink($this->HREF,$e);
                else
                    $this->Write(5,stripslashes(txtentities($e)));
            }
            else
            {
                //Tag
                if($e[0]=='/')
                    $this->CloseTag(strtoupper(substr($e,1)));
                else
                {
                    //Extract attributes
                    $a2=explode(' ',$e);
                    $tag=strtoupper(array_shift($a2));
                    $attr=array();
                    foreach($a2 as $v)
                    {
                        if(preg_match('/([^=]*)=["\']?([^"\']*)/',$v,$a3))
                            $attr[strtoupper($a3[1])]=$a3[2];
                    }
                    $this->OpenTag($tag,$attr);
                }
            }
        }
    }

    public function OpenTag($tag, $attr)
    {
        //Opening tag
        switch($tag){
            case 'STRONG':
                $this->SetStyle('B',true);
                break;
            case 'EM':
                $this->SetStyle('I',true);
                break;
            case 'B':
            case 'I':
            case 'U':
                $this->SetStyle($tag,true);
                break;
            case 'A':
                $this->HREF=$attr['HREF'];
                break;
            case 'IMG':
                if(isset($attr['SRC']) && (isset($attr['WIDTH']) || isset($attr['HEIGHT']))) {
                    if(!isset($attr['WIDTH']))
                        $attr['WIDTH'] = 0;
                    if(!isset($attr['HEIGHT']))
                        $attr['HEIGHT'] = 0;
                    $this->Image($attr['SRC'], $this->GetX(), $this->GetY(), px2mm($attr['WIDTH']), px2mm($attr['HEIGHT']));
                }
                break;
            case 'TR':
            case 'BLOCKQUOTE':
            case 'BR':
                $this->Ln(5);
                break;
            case 'P':
                $this->Ln(10);
                break;
            case 'FONT':
                if (isset($attr['COLOR']) && $attr['COLOR']!='') {
                    $coul=hex2dec($attr['COLOR']);
                    $this->SetTextColor($coul['R'],$coul['V'],$coul['B']);
                    $this->issetcolor=true;
                }
                if (isset($attr['FACE']) && in_array(strtolower($attr['FACE']), $this->fontlist)) {
                    $this->SetFont(strtolower($attr['FACE']));
                    $this->issetfont=true;
                }
                break;
        }
    }

    public function CloseTag($tag)
    {
        //Closing tag
        if($tag=='STRONG')
            $tag='B';
        if($tag=='EM')
            $tag='I';
        if($tag=='B' || $tag=='I' || $tag=='U')
            $this->SetStyle($tag,false);
        if($tag=='A')
            $this->HREF='';
        if($tag=='FONT'){
            if ($this->issetcolor==true) {
                $this->SetTextColor(0);
            }
            if ($this->issetfont) {
                $this->SetFont('arial');
                $this->issetfont=false;
            }
        }
    }

    public function SetStyle($tag, $enable)
    {
        //Modify style and select corresponding font
        $this->$tag+=($enable ? 1 : -1);
        $style='';
        foreach(array('B','I','U') as $s)
        {
            if($this->$s>0)
                $style.=$s;
        }
        $this->SetFont('',$style);
    }

    public function PutLink($URL, $txt)
    {
        //Put a hyperlink
        $this->SetTextColor(0,0,255);
        $this->SetStyle('U',true);
        $this->Write(5,$txt,$URL);
        $this->SetStyle('U',false);
        $this->SetTextColor(0);
    }

    var $widths;
    var $aligns;

    public function SetWidths($w){
        //Tableau des largeurs de colonnes
        $this->widths=$w;
    }

    public function SetAligns($a){
        //Tableau des alignements de colonnes
        $this->aligns=$a;
    }

    public function Row($data){
        $pageBreak = 0;
        //Calcule la hauteur de la ligne
        $nb=0;
        for($i=0;$i<count($data);$i++)
            $nb=max($nb,$this->NbLines($this->widths[$i],$data[$i]));
        $h=3.5*$nb;
        //Effectue un saut de page si nécessaire
        if($pageBreak = $this->CheckPageBreak($h)){
            $lineDefVehicle = $this->getLineDefVehicle();
            $this->SetFont('Arial', '', 12);
            $this->WriteHTML($lineDefVehicle);
            $this->Ln($this->regularLn);
            $this->Ln($this->regularLn);
            $this->printHeaderEquipmentFamilies();
            $this->SetFont('Arial', '', 9);
        }

        //Dessine les cellules
        for($i=0;$i<count($data);$i++){
            $w=$this->widths[$i];
            $a=isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
            //Sauve la position courante
            $x=$this->GetX();
            $y=$this->GetY();
            //Imprime le texte
            $this->MultiCell($w,3,utf8_decode($data[$i]),0,$a);
            //Repositionne à droite
            $this->SetXY($x+$w,$y);
        }
        //Va à la ligne
        $this->Ln($h);
    }

    public function CheckPageBreak($h){
        //Si la hauteur h provoque un débordement, saut de page manuel
        if($this->GetY()+$h>$this->PageBreakTrigger){
            $this->AddPage($this->CurOrientation);
            return 1;
        }
        return 0;
    }

    public function NbLines($w,$txt){
        //Calcule le nombre de lignes qu'occupe un MultiCell de largeur w
        $cw=&$this->CurrentFont['cw'];
        if($w==0)
            $w=$this->w-$this->rMargin-$this->x;
        $wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
        $s=str_replace("\r",'',$txt);
        $nb=strlen($s);
        if($nb>0 and $s[$nb-1]=="\n")
            $nb--;
        $sep=-1;
        $i=0;
        $j=0;
        $l=0;
        $nl=1;
        while($i<$nb){
            $c=$s[$i];
            if($c=="\n"){
                $i++;
                $sep=-1;
                $j=$i;
                $l=0;
                $nl++;
                continue;
            }
            if($c==' ')
                $sep=$i;
            $l+=$cw[$c];
            if($l>$wmax){
                if($sep==-1){
                    if($i==$j)
                        $i++;
                }
                else
                    $i=$sep+1;
                $sep=-1;
                $j=$i;
                $l=0;
                $nl++;
            }
            else
                $i++;
        }
        return $nl;
    }
}