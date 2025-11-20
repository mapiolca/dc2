<?php
/* Copyright (C) 2004-2014 Laurent Destailleur   <eldy@users.sourceforge.net>
 * Copyright (C) 2005-2011 Regis Houssin         <regis.houssin@capnetworks.com>
 * Copyright (C) 2007      Franky Van Liedekerke <franky.van.liedekerke@telenet.be>
 * Copyright (C) 2010-2014 Juanjo Menent         <jmenent@2byte.es>
 * Copyright (C) 2015       Marcos García        <marcosgdf@gmail.com>
 * Copyright (C) 2017      Ferran Marcet         <fmarcet@2byte.es>
 * Copyright (C) 2018-2025 Pierre Ardoin         <developpeur@lesmetiersdubatiment.fr>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 * or see http://www.gnu.org/
 */

/**
 *	\file       htdocs/core/modules/supplier_order/pdf/pdf_DC42017modules.php
 *	\ingroup    fournisseur
 *	\brief      File of class to generate suppliers orders from Délégation INPOSE model
 */

require_once DOL_DOCUMENT_ROOT.'/core/modules/propale/modules_propale.php';
require_once DOL_DOCUMENT_ROOT.'/product/class/product.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/company.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/pdf.lib.php';

dol_include_once('/dc1/class/dc1.class.php');
dol_include_once('/dc2/class/dc2.class.php');



/**
 *	Class to generate the supplier orders with the Délégation INPOSE model
 */
class pdf_DC22023 extends ModelePDFPropales
{
    var $db;
    var $name;
    var $description;
    var $type;

    var $phpmin = array(4,3,0); // Minimum version of PHP required by module
    var $version = 'dolibarr';

    var $page_largeur;
    var $page_hauteur;
    var $format;
	var $marge_gauche;
	var	$marge_droite;
	var	$marge_haute;
	var	$marge_basse;

	var $emetteur;	// Objet societe qui emet


	/**
	 *	Constructor
	 *
	 *  @param	DoliDB		$db      	Database handler
	 */
	function __construct($db)
	{
		global $conf,$langs,$mysoc;

		$langs->load("main");
		$langs->load("bills");
		$langs->load("dc1@dc1");
		$langs->load("dc2@dc2");

		$this->db = $db;
		$this->name = "DC2 2023";
		$this->description = $langs->trans('PDFDC2LMDBDescription2023');

		// Dimension page pour format A4
		$this->type = 'pdf';
		$formatarray=pdf_getFormat();
		$this->page_largeur = $formatarray['width'];
		$this->page_hauteur = $formatarray['height'];
		$this->format = array($this->page_largeur,$this->page_hauteur);
		$this->marge_gauche=isset($conf->global->MAIN_PDF_MARGIN_LEFT)?$conf->global->MAIN_PDF_MARGIN_LEFT:10;
		$this->marge_droite=isset($conf->global->MAIN_PDF_MARGIN_RIGHT)?$conf->global->MAIN_PDF_MARGIN_RIGHT:10;
		$this->marge_haute =isset($conf->global->MAIN_PDF_MARGIN_TOP)?$conf->global->MAIN_PDF_MARGIN_TOP:10;
		$this->marge_basse =isset($conf->global->MAIN_PDF_MARGIN_BOTTOM)?$conf->global->MAIN_PDF_MARGIN_BOTTOM:10;

		$this->emetteur=$mysoc;
	}


    /**
     *  Function to build pdf onto disk
     *
     *  @param		CommandeFournisseur	$object				Id of object to generate
     *  @param		Translate			$outputlangs		Lang output object
     *  @param		string				$srctemplatepath	Full path of source filename for generator using a template file
     *  @param		int					$hidedetails		Do not show line details
     *  @param		int					$hidedesc			Do not show desc
     *  @param		int					$hideref			Do not show ref
     *  @return		int										1=OK, 0=KO
     */
	function write_file($object,$outputlangs='',$srctemplatepath='',$hidedetails=0,$hidedesc=0,$hideref=0)
	{
		global $user,$langs,$conf,$hookmanager,$mysoc;

		if (! is_object($outputlangs)) $outputlangs=$langs;
		// For backward compatibility with FPDF, force output charset to ISO, because FPDF expect text to be encoded in ISO
		if (! empty($conf->global->MAIN_USE_FPDF)) $outputlangs->charset_output='ISO-8859-1';

		$outputlangs->load("main");
		$outputlangs->load("dict");
		$outputlangs->load("companies");
		$outputlangs->load("bills");
		$outputlangs->load("products");
		$outputlangs->load("propal");


		if ($conf->propal->dir_output)
		{
			$object->fetch_thirdparty();

			$deja_regle = 0;
			$amount_credit_notes_included = 0;
			$amount_deposits_included = 0;
			//$amount_credit_notes_included = $object->getSumCreditNotesUsed();
            //$amount_deposits_included = $object->getSumDepositsUsed();

			// Definition of $dir and $file
			if ($object->specimen)
			{
				$dir = $conf->propal->dir_output;
				$file = $dir . "/SPECIMEN.pdf";
			}
			else
			{
				$objectref = dol_sanitizeFileName($object->ref);
				$dir = $conf->propal->dir_output . "/" . $objectref;
				$file = $dir . "/" . $objectref . " - DC2 - Declaration du candidat individuel ou du groupement.pdf";
			}

			if (! file_exists($dir))
			{
				if (dol_mkdir($dir) < 0)
				{
					$this->error=$langs->transnoentities("ErrorCanNotCreateDir",$dir);
					return 0;
				}
			}

			if (file_exists($dir))
			{
				// Add pdfgeneration hook
				if (! is_object($hookmanager))
				{
					include_once DOL_DOCUMENT_ROOT.'/core/class/hookmanager.class.php';
					$hookmanager=new HookManager($this->db);
				}
				$hookmanager->initHooks(array('pdfgeneration'));
				$parameters=array('file'=>$file,'object'=>$object,'outputlangs'=>$outputlangs);
				global $action;
				$reshook=$hookmanager->executeHooks('beforePDFCreation',$parameters,$object,$action);    // Note that $action and $object may have been modified by some hooks

				$nblignes = count($object->lines);

                $pdf=pdf_getInstance($this->format);
                $default_font_size = pdf_getPDFFontSize($outputlangs);	// Must be after pdf_getInstance
                $heightforinfotot = 50;	// Height reserved to output the info and total part
		        $heightforfreetext= (isset($conf->global->MAIN_PDF_FREETEXT_HEIGHT)?$conf->global->MAIN_PDF_FREETEXT_HEIGHT:5);	// Height reserved to output the free text on last page
	            $heightforfooter = $this->marge_basse + 8;	// Height reserved to output the footer (value include bottom margin)
                $pdf->SetAutoPageBreak(1,0);

                if (class_exists('TCPDF'))
                {
                    $pdf->setPrintHeader(false);
                    $pdf->setPrintFooter(false);
                }
                $pdf->SetFont(pdf_getPDFFont($outputlangs));
                // Set path to the background PDF File

				$pdf->Open();
				$pagenb=0;
				$pdf->SetDrawColor(128,128,128);

				$pdf->SetTitle($outputlangs->convToOutputCharset($object->ref));
				$pdf->SetSubject($outputlangs->transnoentities("CommercialProposal"));
				$pdf->SetCreator("Dolibarr ".DOL_VERSION);
				$pdf->SetAuthor($outputlangs->convToOutputCharset($user->getFullName($outputlangs)));
				$pdf->SetKeyWords($outputlangs->convToOutputCharset($object->ref)." ".$outputlangs->transnoentities("CommercialProposal")." ".$outputlangs->convToOutputCharset($object->thirdparty->name));
				if (! empty($conf->global->MAIN_DISABLE_PDF_COMPRESSION)) $pdf->SetCompression(false);

				$pdf->SetMargins($this->marge_gauche, $this->marge_haute, $this->marge_droite);   // Left, Top, Right

				//Chargement des Valeurs de la DC1 et de la DC2

				$dc1_lines = array();
				$dc2_lines = array();
				if ($conf->dc1->enabled && $conf->dc2->enabled)
				{
					dol_include_once("/dc1/class/dc1.class.php");
					dol_include_once("/dc2/class/dc2.class.php");

					$id = GETPOST('id', 'int');
					$dc1 = new DC1($this->db);
					$dc2 = new DC2($this->db);

					if ($id > 0)
					{
						$result = $object->fetch($id);

						if ($result > 0)
						{
							$dc1->fetch();
							$dc1_lines = $dc1->lines;
							foreach ($dc1_lines as $dc1_line)
							{
								$index++;
								$dc1_line->id_acheteur;
								$dc1_line->objet_consultation;
					            $dc1_line->objet_candidature;
					            $dc1_line->n_lots;
					            $dc1_line->designation_lot;
					            $dc1_line->candidat_statut;
					            $dc1_line->F_engagement;
					            $dc1_line->adresse_internet;
					            $dc1_line->renseignement_adresse;
					            $dc1_line->dc2;
							}

							$dc2->fetch();
							$dc2_lines = $dc2->lines;
							foreach ($dc2_lines as $dc2_line)
							{
								$index++;
								$dc2_line->AB_idem_DC1 ;
					            $dc2_line->A ;
					            $dc2_line->B ;
					            $dc2_line->C1;
					            $dc2_line->C2;
					            $dc2_line->C2_Date;
					            $dc2_line->C2_idem ;
					            $dc2_line->C2_adresse_internet;
					            $dc2_line->C2_renseignement_adresse;
					            $dc2_line->D1_liste;
					            $dc2_line->D1_reference;
					            $dc2_line->D1_idem;
					            $dc2_line->D1_adresse_internet;
					            $dc2_line->D1_renseignement_adresse;
					            $dc2_line->D2;
					            $dc2_line->E1_registre_pro;
					            $dc2_line->E1_registre_spec;
					            $dc2_line->E3_idem;
					            $dc2_line->E3_adresse_internet;
					            $dc2_line->E3_renseignement_adresse;
					            $dc2_line->F_CA3_debut;
					            $dc2_line->F_CA3_fin;
					            $dc2_line->F_CA3_montant;
					            $dc2_line->F_CA2_debut;
					            $dc2_line->F_CA2_fin;
					            $dc2_line->F_CA2_montant;
					            $dc2_line->F_CA1_debut;
					            $dc2_line->F_CA1_fin;
					            $dc2_line->F_CA1_montant;
					            $dc2_line->F_date_creation;
					            $dc2_line->F2;
					            $dc2_line->F3;
					            $dc2_line->F4_idem ;
					            $dc2_line->F4_adresse_internet ;
					            $dc2_line->F4_renseignement_adresse;
					            $dc2_line->G1;
					            $dc2_line->G2_idem ;
					            $dc2_line->G2_adresse_internet ;
					            $dc2_line->G2_renseignement_adresse ;
					            $dc2_line->H ;
					            $dc2_line->I1 ;
					            $dc2_line->I2 ;
							}
							
							if ($object->element != 'propal')// || $object->type != 5)
							{
								$error = true;
								$message = $langs->trans('NotAPropale');
							}

						}
						else
						{
							$error = true;
							$message = $langs->trans('ObjectNotFound');
						}
					}
					else
					{
						$error = true;
						$message = $langs->trans('ObjectNotFound');
					}
					
				}					
				


			// Page 1
				$pdf->AddPage();
				
                $pagecount = $pdf->setSourceFile(DOL_DOCUMENT_ROOT.'/custom/dc2/core/modules/propale/doc/DC2_2023/DC2-2023.pdf');
                $tplidx = $pdf->importPage(1);
                
				
				if (! empty($tplidx)) $pdf->useTemplate($tplidx);

				dol_include_once('/dc1/core/modules/propale/doc/DC1-2019/DC1-2019.php');
				dol_include_once('/dc2/core/modules/propale/doc/DC2-2023/DC2-2023.php');

				$logo=$conf->mycompany->dir_output.'/logos/'.$this->emetteur->logo;

				$height=pdf_getHeightForLogo($logo);
				$pdf->Image($logo, 80, 10, "", 25);

				$this->_pagefoot($pdf,$object,$outputlangs,1);
				if (method_exists($pdf,'AliasNbPages')) $pdf->AliasNbPages();
				$object->fetch_thirdparty();

				//Contenu
				$pdf->SetFont('','B',10); // fixe la police, le type ( 'B' pour gras, 'I' pour italique, '' pour normal,...)

				$Client = '

				'.$object->thirdparty->name.'<br>
				'.$object->thirdparty->address.'<br>
				'.$object->thirdparty->zip.' '.$object->thirdparty->town.'';

				$pdf->writeHTMLCell(150,4, 20, 200, dol_htmlentitiesbr($outputlangs->convToOutputCharset($Client)),0,1);

				$pdf->writeHTMLCell(150,4, 20, 255, dol_htmlentitiesbr($outputlangs->convToOutputCharset($dc1_line->objet_consultation)),0,1);	

				$pdf->SetFont('','B',9); // fixe la police, le type ( 'B' pour gras, 'I' pour italique, '' pour normal,...)
				$pdf->writeHTMLCell(100,4, 80, 275.8, $outputlangs->convToOutputCharset($dc1_line->ref_consultation),0,1);

				if ($dc2_line->D2 == '1') { // Définition du nombre de pages
					$nb_pages = "4";

				} else {
					$nb_pages = "9";
				}
				$pdf->SetFont('','B',8.5); // fixe la police, le type ( 'B' pour gras, 'I' pour italique, '' pour normal,...)
				$pdf->writeHTMLCell(150,4, 179, 275.8, dol_htmlentitiesbr('/'),0,1);
				$pdf->writeHTMLCell(150,4, 183, 275.8, dol_htmlentitiesbr($nb_pages),0,1);


			// Page 2
				$pdf->AddPage();

				$tplidx = $pdf->importPage(2);
                if (! empty($tplidx)) $pdf->useTemplate($tplidx);

				$pdf->Image($logo, 10, 10, "", 10);
				
				// Pied de page
					$this->_pagefoot($pdf, $object, $outputlangs);
					if (method_exists($pdf,'AliasNbPages')) $pdf->AliasNbPages();

				//Contenu
					
				$pdf->SetFont('','B',10); // fixe la police, le type ( 'B' pour gras, 'I' pour italique, '' pour normal,...)
				
				$Societe = '
					'.$conf->global->MAIN_INFO_SOCIETE_ADDRESS.'<br>
					'.$conf->global->MAIN_INFO_SOCIETE_ZIP.' '.$conf->global->MAIN_INFO_SOCIETE_TOWN.'';

				$Forme_Juridique_Societe = getFormeJuridiqueLabel($conf->global->MAIN_INFO_SOCIETE_FORME_JURIDIQUE);

				$carac_emetteur .= pdf_build_address($outputlangs, $this->emetteur, $object->thirdparty);

				$SIREN_Societe = '

				N° SIRET : '.$conf->global->MAIN_INFO_SIRET.'<br>
				N° TVA Intracommunautaire : '.$conf->global->MAIN_INFO_TVAINTRA.' | Code APE : '.$conf->global->MAIN_INFO_APE;

				
				$pdf->writeHTMLCell(190,4, 25, 65, dol_htmlentitiesbr($conf->global->MAIN_INFO_SOCIETE_NOM),0,1);

				$pdf->SetFont('','B',8); // fixe la police, le type ( 'B' pour gras, 'I' pour italique, '' pour normal,...)

				$pdf->writeHTMLCell(100,4, 25, 78, dol_htmlentitiesbr($Societe),0,1);
				
				$pdf->writeHTMLCell(100,4, 25, 90, dol_htmlentitiesbr($langs->trans('Mail').' : '.$conf->global->MAIN_INFO_SOCIETE_MAIL),0,1);

				$pdf->writeHTMLCell(100,4, 25, 103, dol_htmlentitiesbr($langs->trans('Phone').' : '.dol_print_phone($conf->global->MAIN_INFO_SOCIETE_TEL)),0,1);

				if ($conf->global->MAIN_INFO_SOCIETE_MOBILE) {
					$pdf->writeHTMLCell(100,4, 25, 107, dol_htmlentitiesbr($langs->trans('Mobile').' : '.dol_print_phone($conf->global->MAIN_INFO_SOCIETE_MOBILE)),0,1);
				}

				$pdf->writeHTMLCell(100,4, 25, 120, dol_htmlentitiesbr($SIREN_Societe),0,1);
				
				//$pdf->writeHTMLCell(100,4, 25, 78, dol_htmlentitiesbr($carac_emetteur),0,1);

				$pdf->writeHTMLCell(100,4, 25, 138, dol_htmlentitiesbr($Forme_Juridique_Societe),0,1);

				if ($dc2_line->C1 == '1') {
					$pdf->SetFont('','B',10); // fixe la police, le type ( 'B' pour gras, 'I' pour italique, '' pour normal,...)
					$pdf->writeHTMLCell(190,4, 30, 167.3, dol_htmlentitiesbr("X"),0,1);					

				} elseif ($dc2_line->C1 == '2') {
					$pdf->SetFont('','B',10); // fixe la police, le type ( 'B' pour gras, 'I' pour italique, '' pour normal,...)
					$pdf->writeHTMLCell(190,4, 30, 175.8, dol_htmlentitiesbr("X"),0,1);

				} else {
					$pdf->SetFont('','B',14); // fixe la police, le type ( 'B' pour gras, 'I' pour italique, '' pour normal,...)
					$pdf->writeHTMLCell(190,4, 45, 170, $langs->trans("non_renseigne"),0,1);

				}
				$pdf->SetFont('','B',9); // fixe la police, le type ( 'B' pour gras, 'I' pour italique, '' pour normal,...)
				$pdf->writeHTMLCell(100,4, 80, 276.7, $outputlangs->convToOutputCharset($dc1_line->ref_consultation),0,1);
				$pdf->SetFont('','B',8.5); // fixe la police, le type ( 'B' pour gras, 'I' pour italique, '' pour normal,...)
				$pdf->writeHTMLCell(150,4, 179, 276.7, dol_htmlentitiesbr('/'),0,1);
				$pdf->writeHTMLCell(150,4, 183, 276.7, dol_htmlentitiesbr($nb_pages),0,1);

			// Page 3
				$pdf->AddPage();

				$tplidx = $pdf->importPage(3);
                if (! empty($tplidx)) $pdf->useTemplate($tplidx);

				$pdf->Image($logo, 10, 10, "", 10);
				//Nature des Travaux
				//$pdf->setXY(10,10); // fixe les positions x et y courantes
				//$pdf->SetFont('','',10); // fixe la police, le type ( 'B' pour gras, 'I' pour italique, '' pour normal,...)
				//$pdf->writeHTMLCell(190,4, 10, 10, dol_htmlentitiesbr($text3),0,1);

				// Pied de page
					$this->_pagefoot($pdf, $object, $outputlangs);
					if (method_exists($pdf,'AliasNbPages')) $pdf->AliasNbPages();

				//Contenu

				$pdf->SetFont('','B',10); // fixe la police, le type ( 'B' pour gras, 'I' pour italique, '' pour normal,...)


				if ($dc2_line->C2 == '1') {
				
					$pdf->writeHTMLCell(190,4, 55.6, 46.2, dol_htmlentitiesbr("X"),0,1);

					if ($dc2_line->C2_idem == '1') {

						$pdf->SetFont('','B',6); // fixe la police, le type ( 'B' pour gras, 'I' pour italique, '' pour normal,...)

						$pdf->writeHTMLCell(90,4, 100, 49, dol_htmlentitiesbr($dc1_line->adresse_internet),0,1);
						$pdf->writeHTMLCell(110,4, 100, 62, dol_htmlentitiesbr($dc1_line->renseignement_adresse),0,1);							

					} elseif ($dc2_line->C2_idem == '2') {	

						$pdf->SetFont('','B',6); // fixe la police, le type ( 'B' pour gras, 'I' pour italique, '' pour normal,...)

						$pdf->writeHTMLCell(90,4, 100, 49, dol_htmlentitiesbr($dc2_line->C2_adresse_internet),0,1);
						$pdf->writeHTMLCell(110,4, 100, 62, dol_htmlentitiesbr($dc2_line->C2_renseignement_adresse),0,1);

					} else {

						$pdf->writeHTMLCell(110,4, 100, 65, $langs->trans("non_renseigne"),0,1);

					}
					
				} elseif ($dc2_line->C2 == '2') {
					
					$pdf->writeHTMLCell(190,4, 55.6, 91.8, dol_htmlentitiesbr("X"),0,1);

					if ($dc2_line->C2_idem == '1') {

						$pdf->SetFont('','B',10); // fixe la police, le type ( 'B' pour gras, 'I' pour italique, '' pour normal,...)

						$C2_Date = dol_print_date($dc2_line->C2_Date,"day",false,$outputlangs,true);

						//$pdf->writeHTMLCell(190,4, 85, 128,$C2_Date,0,1);

						$pdf->SetFont('','B',6); // fixe la police, le type ( 'B' pour gras, 'I' pour italique, '' pour normal,...)

						$pdf->writeHTMLCell(90,4, 100, 95, dol_htmlentitiesbr($dc1_line->adresse_internet),0,1);
						$pdf->writeHTMLCell(110,4, 100, 108, dol_htmlentitiesbr($dc1_line->renseignement_adresse),0,1);							

					} elseif ($dc2_line->C2_idem == '2') {

						$pdf->SetFont('','B',10); // fixe la police, le type ( 'B' pour gras, 'I' pour italique, '' pour normal,...)

						$C2_Date = dol_print_date($dc2_line->C2_Date,"day",false,$outputlangs,true);

						//$pdf->writeHTMLCell(110,4, 85, 128,$C2_Date,0,1);

						$pdf->SetFont('','B',6); // fixe la police, le type ( 'B' pour gras, 'I' pour italique, '' pour normal,...)

						$pdf->writeHTMLCell(90,4, 100, 95, dol_htmlentitiesbr($dc2_line->C2_adresse_internet),0,1);
						$pdf->writeHTMLCell(110,4, 100, 108, dol_htmlentitiesbr($dc2_line->C2_renseignement_adresse),0,1);

					} else {

						$pdf->writeHTMLCell(110,4, 110, 110, $langs->trans("non_renseigne"),0,1);

					}
					

				} elseif ($dc2_line->C2 == '3') {

					$pdf->writeHTMLCell(190,4, 55.6, 137.3, dol_htmlentitiesbr("X"),0,1);

					if ($dc2_line->C2_idem == '1') {

						$pdf->SetFont('','B',6); // fixe la police, le type ( 'B' pour gras, 'I' pour italique, '' pour normal,...)

						$pdf->writeHTMLCell(90,4, 100, 139, dol_htmlentitiesbr($dc1_line->adresse_internet),0,1);
						$pdf->writeHTMLCell(110,4, 100, 157, dol_htmlentitiesbr($dc1_line->renseignement_adresse),0,1);							

					} elseif ($dc2_line->C2_idem == '2') {	

						$pdf->SetFont('','B',6); // fixe la police, le type ( 'B' pour gras, 'I' pour italique, '' pour normal,...)

						$pdf->writeHTMLCell(90,4, 100, 139, dol_htmlentitiesbr($dc2_line->C2_adresse_internet),0,1);
						$pdf->writeHTMLCell(110,4, 100, 157, dol_htmlentitiesbr($dc2_line->C2_renseignement_adresse),0,1);

					} else {

						$pdf->writeHTMLCell(110,4, 100, 160, $langs->trans("non_renseigne"),0,1);

					}

				} elseif ($dc2_line->C2 == '4') {
					
					$pdf->writeHTMLCell(190,4, 55.6, 187.5, dol_htmlentitiesbr("X"),0,1);

					if ($dc2_line->C2_idem == '1') {

						$pdf->SetFont('','B',6); // fixe la police, le type ( 'B' pour gras, 'I' pour italique, '' pour normal,...)

						$pdf->writeHTMLCell(90,4, 100, 190, dol_htmlentitiesbr($dc1_line->adresse_internet),0,1);
						$pdf->writeHTMLCell(110,4, 100, 206.5, dol_htmlentitiesbr($dc1_line->renseignement_adresse),0,1);							

					} elseif ($dc2_line->C2_idem == '2') {	

						$pdf->SetFont('','B',6); // fixe la police, le type ( 'B' pour gras, 'I' pour italique, '' pour normal,...)

						$pdf->writeHTMLCell(90,4, 100, 190, dol_htmlentitiesbr($dc2_line->C2_adresse_internet),0,1);
						$pdf->writeHTMLCell(110,4, 100, 206.5, dol_htmlentitiesbr($dc2_line->C2_renseignement_adresse),0,1);

					} else {

						$pdf->writeHTMLCell(110,4, 85, 252, $langs->trans("non_renseigne"),0,1);

					}

				} elseif ($dc2_line->C2 == '5') {
					
					$pdf->writeHTMLCell(190,4, 55.6, 233, dol_htmlentitiesbr("X"),0,1);

					if ($dc2_line->C2_idem == '1') {

						$pdf->SetFont('','B',6); // fixe la police, le type ( 'B' pour gras, 'I' pour italique, '' pour normal,...)

						$pdf->writeHTMLCell(90,4, 100, 190, dol_htmlentitiesbr($dc1_line->adresse_internet),0,1);
						$pdf->writeHTMLCell(110,4, 100, 206.5, dol_htmlentitiesbr($dc1_line->renseignement_adresse),0,1);							

					} elseif ($dc2_line->C2_idem == '2') {	

						$pdf->SetFont('','B',6); // fixe la police, le type ( 'B' pour gras, 'I' pour italique, '' pour normal,...)

						$pdf->writeHTMLCell(90,4, 100, 240, dol_htmlentitiesbr($dc2_line->C2_adresse_internet),0,1);
						$pdf->writeHTMLCell(110,4, 100, 257, dol_htmlentitiesbr($dc2_line->C2_renseignement_adresse),0,1);

					} else {

						$pdf->writeHTMLCell(110,4, 85, 252, $langs->trans("non_renseigne"),0,1);

					}
				}
				$pdf->SetFont('','B',9); // fixe la police, le type ( 'B' pour gras, 'I' pour italique, '' pour normal,...)
				$pdf->writeHTMLCell(100,4, 80, 276.7, $outputlangs->convToOutputCharset($dc1_line->ref_consultation),0,1);
				$pdf->SetFont('','B',8.5); // fixe la police, le type ( 'B' pour gras, 'I' pour italique, '' pour normal,...)
				$pdf->writeHTMLCell(150,4, 179, 276.7, dol_htmlentitiesbr('/'),0,1);
				$pdf->writeHTMLCell(150,4, 183, 276.7, dol_htmlentitiesbr($nb_pages),0,1);

			// Page 4
				$pdf->AddPage();

				$tplidx = $pdf->importPage(4);
	            if (! empty($tplidx)) $pdf->useTemplate($tplidx);

				$pdf->Image($logo, 10, 10, "", 10);
				
				// Pied de page
					$this->_pagefoot($pdf, $object, $outputlangs);
					if (method_exists($pdf,'AliasNbPages')) $pdf->AliasNbPages();

				//Contenu

				$pdf->SetFont('','B',10); // fixe la police, le type ( 'B' pour gras, 'I' pour italique, '' pour normal,...)


				$pdf->writeHTMLCell(110,4, 40, 50, dol_htmlentitiesbr($dc2_line->D1_liste),0,1);

				$pdf->writeHTMLCell(130,12, 40, 73, dol_htmlentitiesbr($dc2_line->D1_reference),0,1);


				if ($dc2_line->D1_idem == '1') {

					$pdf->SetFont('','B',6); // fixe la police, le type ( 'B' pour gras, 'I' pour italique, '' pour normal,...)

					$pdf->writeHTMLCell(90,4, 45, 97, dol_htmlentitiesbr($dc1_line->adresse_internet),0,1);
					$pdf->writeHTMLCell(150,4, 45, 108, dol_htmlentitiesbr($dc1_line->renseignement_adresse),0,1);							

				}elseif ($dc2_line->D1_idem == '2') {	

					$pdf->SetFont('','B',6); // fixe la police, le type ( 'B' pour gras, 'I' pour italique, '' pour normal,...)

					$pdf->writeHTMLCell(90,4, 60, 101.8, dol_htmlentitiesbr($dc2_line->D1_adresse_internet),0,1);
					$pdf->writeHTMLCell(150,4, 45, 115, dol_htmlentitiesbr($dc2_line->D1_renseignement_adresse),0,1);

				}else{

					$pdf->writeHTMLCell(150,4, 60, 101.8, $langs->trans("non_renseigne"),0,1);

				}

				$pdf->SetFont('','B',10); // fixe la police, le type ( 'B' pour gras, 'I' pour italique, '' pour normal,...)

				if ($dc2_line->D2 == '1') {

					$pdf->writeHTMLCell(190,4, 29.9, 137.3, dol_htmlentitiesbr("X"),0,1);
				}
                $pdf->SetFont('','B',9); // fixe la police, le type ( 'B' pour gras, 'I' pour italique, '' pour normal,...)
				$pdf->writeHTMLCell(100,4, 80, 276.7, $outputlangs->convToOutputCharset($dc1_line->ref_consultation),0,1);
				$pdf->SetFont('','B',8.5); // fixe la police, le type ( 'B' pour gras, 'I' pour italique, '' pour normal,...)
				$pdf->writeHTMLCell(150,4, 179, 276.7, dol_htmlentitiesbr('/'),0,1);
				$pdf->writeHTMLCell(150,4, 183, 276.7, dol_htmlentitiesbr($nb_pages),0,1);

				if ($dc2_line->D2 == '2') {
										
				
			// Page 5
				$pdf->AddPage();

				$tplidx = $pdf->importPage(5);
                if (! empty($tplidx)) $pdf->useTemplate($tplidx);

				$pdf->Image($logo, 10, 10, "", 10);
				
				// Pied de page
				$this->_pagefoot($pdf, $object, $outputlangs);
				if (method_exists($pdf,'AliasNbPages')) $pdf->AliasNbPages();


				//Contenu

				$pdf->writeHTMLCell(150,4, 35, 60, dol_htmlentitiesbr($dc2_line->E1_registre_pro),0,1);

				$pdf->writeHTMLCell(150,4, 35, 98, dol_htmlentitiesbr($dc2_line->E1_registre_spec),0,1);

				$pdf->SetFont('','B',10); // fixe la police, le type ( 'B' pour gras, 'I' pour italique, '' pour normal,...)

				if ($dc2_line->E3_idem == '1') {

					$pdf->writeHTMLCell(90,4, 35, 153, dol_htmlentitiesbr($dc1_line->adresse_internet),0,1);
					$pdf->writeHTMLCell(150,4, 35, 169, dol_htmlentitiesbr($dc1_line->renseignement_adresse),0,1);							

				}elseif ($dc2_line->E3_idem == '2') {	

					$pdf->writeHTMLCell(90,4, 35, 153, dol_htmlentitiesbr($dc2_line->E3_adresse_internet),0,1);
					$pdf->writeHTMLCell(150,4, 30, 169, dol_htmlentitiesbr($dc2_line->E3_renseignement_adresse),0,1);

				}else{

					$pdf->writeHTMLCell(150,4, 45, 153, $langs->trans("non_renseigne"),0,1);

				}
				$pdf->SetFont('','B',9); // fixe la police, le type ( 'B' pour gras, 'I' pour italique, '' pour normal,...)
				$pdf->writeHTMLCell(100,4, 80, 276.7, $outputlangs->convToOutputCharset($dc1_line->ref_consultation),0,1);
				$pdf->SetFont('','B',8.5); // fixe la police, le type ( 'B' pour gras, 'I' pour italique, '' pour normal,...)
				$pdf->writeHTMLCell(150,4, 179, 276.7, dol_htmlentitiesbr('/'),0,1);
				$pdf->writeHTMLCell(150,4, 183, 276.7, dol_htmlentitiesbr($nb_pages),0,1);

				// Page 6
				$pdf->AddPage();

				$tplidx = $pdf->importPage(6);
                if (! empty($tplidx)) $pdf->useTemplate($tplidx);

				$pdf->Image($logo, 10, 10, "", 10);
				
				// Pied de page
				$this->_pagefoot($pdf, $object, $outputlangs);
				if (method_exists($pdf,'AliasNbPages')) $pdf->AliasNbPages();


				//Contenu

				$pdf->SetFont('','B',9); // fixe la police, le type ( 'B' pour gras, 'I' pour italique, '' pour normal,...)

				if ($dc2_line->F_CA3_debut == '1970-01-01') {


				}else{

					$pdf->writeHTMLCell(150,4, 87, 65, dol_print_date($dc2_line->F_CA3_debut),0,1);

				}

				if ($dc2_line->F_CA3_fin == '1970-01-01') {
					
					

				}else{

					$pdf->writeHTMLCell(150,4, 87, 70, dol_print_date($dc2_line->F_CA3_fin),0,1);

				}

				if ($dc2_line->F_CA3_montant == '0') {
					
					$pdf->writeHTMLCell(150,4, 75, 87, $langs->trans("non_renseigne"),0,1);

				}else{

					$pdf->writeHTMLCell(150,4, 77, 87, price($dc2_line->F_CA3_montant).' €',0,1);

					$ratio_CA3 = round(( $object->total_ht / $dc2_line->F_CA3_montant )*100,2) ;

					$pdf->writeHTMLCell(150,4, 82, 105, price($ratio_CA3),0,1);


				}

				if ($dc2_line->F_CA2_debut == '1970-01-01') {

					
				}else{

					$pdf->writeHTMLCell(150,4, 129, 65, dol_print_date($dc2_line->F_CA2_debut),0,1);

				}

				if ($dc2_line->F_CA2_fin == '1970-01-01') {
					
					

				}else{

					$pdf->writeHTMLCell(150,4, 129, 70, dol_print_date($dc2_line->F_CA2_fin),0,1);

				}

				if ($dc2_line->F_CA2_montant == '0') {
					
					

				}else{

					$pdf->writeHTMLCell(150,4, 120, 87, price($dc2_line->F_CA2_montant).' €',0,1);

					$ratio_CA2 = round(( $object->total_ht / $dc2_line->F_CA2_montant )*100,2) ;

					$pdf->writeHTMLCell(150,4, 125, 105, price($ratio_CA2),0,1);

				}

				if ($dc2_line->F_CA1_debut == '1970-01-01') {

					

				}else{

					$pdf->writeHTMLCell(150,4, 172, 65, dol_print_date($dc2_line->F_CA1_debut),0,1);

				}

				if ($dc2_line->F_CA1_fin == '1970-01-01') {
					
					

				}else{

					$pdf->writeHTMLCell(150,4, 172, 70, dol_print_date($dc2_line->F_CA1_fin),0,1);

				}

				if ($dc2_line->F_CA1_montant == '0') {
					
					

				}else{

					$pdf->writeHTMLCell(150,4, 163, 87, price($dc2_line->F_CA1_montant).' €',0,1);

					$ratio_CA1 == round(($object->total_ht / $dc2_line->F_CA1_montant )*100,2) ;

					$pdf->writeHTMLCell(150,4, 168, 105, price($ratio_CA1).'',0,1);

				}

				$pdf->SetFont('','B',10); // fixe la police, le type ( 'B' pour gras, 'I' pour italique, '' pour normal,...)

				if ($dc2_line->F_date_creation == '1970-01-01') {
					
					$pdf->writeHTMLCell(150,4, 160, 147, $langs->trans("non_renseigne"),0,1);

				}else{
				
					$pdf->writeHTMLCell(150,4, 30, 139, dol_print_date($dc2_line->F_date_creation, '%d'),0,1);
					$pdf->writeHTMLCell(150,4, 40, 139, dol_print_date($dc2_line->F_date_creation, '%m'),0,1);
					$pdf->writeHTMLCell(150,4, 50, 139, dol_print_date($dc2_line->F_date_creation, '%Y'),0,1);

				}

				if (!empty($dc2_line->F2)) {

					$pdf->writeHTMLCell(90,4, 30, 166, dol_htmlentitiesbr($dc2_line->F2),0,1);

				}else{

					$pdf->writeHTMLCell(150,4, 30, 166, $langs->trans("non_renseigne"),0,1);

				}


				if ($dc2_line->F3 == '1') {
				
					$pdf->writeHTMLCell(190,4, 29.9, 188.5, dol_htmlentitiesbr("X"),0,1);					

				}elseif ($dc2_line->F3 == '2') {
					
					

				}else {

					$pdf->writeHTMLCell(190,4, 30, 205, $langs->trans("non_renseigne"),0,1);

				}

				if ($dc2_line->F4_idem == '1') {

					$pdf->writeHTMLCell(90,4, 45, 252, dol_htmlentitiesbr($dc1_line->adresse_internet),0,1);
					$pdf->writeHTMLCell(150,4, 30, 267, dol_htmlentitiesbr($dc1_line->renseignement_adresse),0,1);							

				}elseif ($dc2_line->F4_idem == '2') {	

					$pdf->writeHTMLCell(90,4, 30, 252, dol_htmlentitiesbr($dc2_line->F4_adresse_internet),0,1);
					$pdf->writeHTMLCell(150,4, 30, 267, dol_htmlentitiesbr($dc2_line->F4_renseignement_adresse),0,1);

				}else{

					$pdf->writeHTMLCell(150,4, 30, 252, $langs->trans("non_renseigne"),0,1);

				}
				$pdf->SetFont('','B',9); // fixe la police, le type ( 'B' pour gras, 'I' pour italique, '' pour normal,...)
				$pdf->writeHTMLCell(100,4, 80, 276.7, $outputlangs->convToOutputCharset($dc1_line->ref_consultation),0,1);
				$pdf->SetFont('','B',8.5); // fixe la police, le type ( 'B' pour gras, 'I' pour italique, '' pour normal,...)
				$pdf->writeHTMLCell(150,4, 179, 276.7, dol_htmlentitiesbr('/'),0,1);
				$pdf->writeHTMLCell(150,4, 183, 276.7, dol_htmlentitiesbr($nb_pages),0,1);

				// Page 7
				$pdf->AddPage();

				$tplidx = $pdf->importPage(7);
                if (! empty($tplidx)) $pdf->useTemplate($tplidx);

				$pdf->Image($logo, 10, 10, "", 10);
				
				// Pied de page
				$this->_pagefoot($pdf, $object, $outputlangs);
				if (method_exists($pdf,'AliasNbPages')) $pdf->AliasNbPages();


				//Contenu

				$pdf->SetFont('','B',10); // fixe la police, le type ( 'B' pour gras, 'I' pour italique, '' pour normal,...)

				if ($dc2_line->G1 == '') {

					$pdf->writeHTMLCell(150,4, 30, 64, $langs->trans("non_renseigne"),0,1);
					
				}else{

					$pdf->writeHTMLCell(150,4, 30, 64, dol_htmlentitiesbr($dc2_line->G1),0,1);

				}


				if ($dc2_line->G2_idem == '1') {

					$pdf->writeHTMLCell(90,4, 30, 123, dol_htmlentitiesbr($dc1_line->adresse_internet),0,1);
					$pdf->writeHTMLCell(150,4, 30, 135, dol_htmlentitiesbr($dc1_line->renseignement_adresse),0,1);							

				}elseif ($dc2_line->G2_idem == '2') {	

					$pdf->writeHTMLCell(90,4, 30, 122, dol_htmlentitiesbr($dc2_line->G2_adresse_internet),0,1);
					$pdf->writeHTMLCell(150,4, 30, 135, dol_htmlentitiesbr($dc2_line->G2_renseignement_adresse),0,1);

				}else{

					$pdf->writeHTMLCell(150,4, 30, 123, $langs->trans("non_renseigne"),0,1);

				}
				$pdf->SetFont('','B',9); // fixe la police, le type ( 'B' pour gras, 'I' pour italique, '' pour normal,...)
				$pdf->writeHTMLCell(100,4, 80, 276.7, $outputlangs->convToOutputCharset($dc1_line->ref_consultation),0,1);
				$pdf->SetFont('','B',8.5); // fixe la police, le type ( 'B' pour gras, 'I' pour italique, '' pour normal,...)
				$pdf->writeHTMLCell(150,4, 179, 276.7, dol_htmlentitiesbr('/'),0,1);
				$pdf->writeHTMLCell(150,4, 183, 276.7, dol_htmlentitiesbr($nb_pages),0,1);

				// Page 8
				$pdf->AddPage();

				$tplidx = $pdf->importPage(8);
                if (! empty($tplidx)) $pdf->useTemplate($tplidx);

				$pdf->Image($logo, 10, 10, "", 10);
				
				// Pied de page
				$this->_pagefoot($pdf, $object, $outputlangs);
				if (method_exists($pdf,'AliasNbPages')) $pdf->AliasNbPages();


				//Contenu

				$pdf->SetFont('','B',8.5); // fixe la police, le type ( 'B' pour gras, 'I' pour italique, '' pour normal,...)
				$pdf->writeHTMLCell(150,4, 179, 276.7, dol_htmlentitiesbr('/'),0,1);
				$pdf->writeHTMLCell(150,4, 183, 276.7, dol_htmlentitiesbr($nb_pages),0,1);

				// Page 9
				$pdf->AddPage();

				$tplidx = $pdf->importPage(9);
                if (! empty($tplidx)) $pdf->useTemplate($tplidx);

				$pdf->Image($logo, 10, 10, "", 10);
				
				// Pied de page
				$this->_pagefoot($pdf, $object, $outputlangs);
				if (method_exists($pdf,'AliasNbPages')) $pdf->AliasNbPages();


				$pdf->SetFont('','B',10); // fixe la police, le type ( 'B' pour gras, 'I' pour italique, '' pour normal,...)

				if ($dc2_line->I1 == '') {

					$pdf->writeHTMLCell(150,4, 30, 40, $langs->trans("non_renseigne"),0,1);
					
				}else{

					$pdf->writeHTMLCell(150,4, 30, 40, dol_htmlentitiesbr($dc2_line->I1),0,1);

				}

				if ($dc2_line->I2 == '') {

					$pdf->writeHTMLCell(150,4, 30, 100, $langs->trans("non_renseigne"),0,1);
					
				}else{

					$pdf->writeHTMLCell(150,4, 30, 100, dol_htmlentitiesbr($dc2_line->I2),0,1);

				}
				$pdf->SetFont('','B',9); // fixe la police, le type ( 'B' pour gras, 'I' pour italique, '' pour normal,...)
				$pdf->writeHTMLCell(100,4, 80, 276.7, $outputlangs->convToOutputCharset($dc1_line->ref_consultation),0,1);
				$pdf->SetFont('','B',8.5); // fixe la police, le type ( 'B' pour gras, 'I' pour italique, '' pour normal,...)
				$pdf->writeHTMLCell(150,4, 179, 276.7, dol_htmlentitiesbr('/'),0,1);
				$pdf->writeHTMLCell(150,4, 183, 276.7, dol_htmlentitiesbr($nb_pages),0,1);

			

/*
					            $dc2_line->H ;
					            $dc2_line-> ;
					            $dc2_line->I2 ;
*/					
		}
			// Fermture Formulaire
				$pdf->Close();

				$pdf->Output($file,'F');


				// Add pdfgeneration hook
				$hookmanager->initHooks(array('pdfgeneration'));
				$parameters=array('file'=>$file,'object'=>$object,'outputlangs'=>$outputlangs);
				global $action;
				$reshook=$hookmanager->executeHooks('afterPDFCreation',$parameters,$this,$action);    // Note that $action and $object may have been modified by some hooks

				if (! empty($conf->global->MAIN_UMASK))
				@chmod($file, octdec($conf->global->MAIN_UMASK));

				return 1;   // Pas d'erreur

			}
			else
			{
				$this->error=$langs->trans("ErrorCanNotCreateDir",$dir);
				return 0;
			}
		}
		else
		{
			$this->error=$langs->trans("ErrorConstantNotDefined","PROP_OUTPUTDIR");
			return 0;
		}
	}


/**
	 *  Show top header of page.
	 *
	 *  @param	PDF			$pdf     		Object PDF
	 *  @param  CommandeFournisseur		$object     	Object to show
	 *  @param  int	    	$showaddress    0=no, 1=yes
	 *  @param  Translate	$outputlangs	Object lang for output
	 *  @return	void
	 */
	function _pagehead(&$pdf, $object, $showaddress, $outputlangs)
	{
		global $langs,$conf,$mysoc;

		$outputlangs->load("main");
		$outputlangs->load("bills");
		$outputlangs->load("propal");
		$outputlangs->load("companies");
		$outputlangs->load("sendings");
		$outputlangs->load("dc1@dc1");
		$outputlangs->load("dc2@dc2");

		$object->fetch_projet();

		$default_font_size = pdf_getPDFFontSize($outputlangs);

		// Do not add the BACKGROUND as this is for suppliers
		//pdf_pagehead($pdf,$outputlangs,$this->page_hauteur);

		//Affiche le filigrane brouillon - Print Draft Watermark
		/*if($object->statut==0 && (! empty($conf->global->COMMANDE_DRAFT_WATERMARK)) )
		{
            pdf_watermark($pdf,$outputlangs,$this->page_hauteur,$this->page_largeur,'mm',$conf->global->COMMANDE_DRAFT_WATERMARK);
		}*/
		//Print content

		$pdf->SetTextColor(0,0,60);
		$pdf->SetFont('','B',$default_font_size + 3);

		$posx=$this->page_largeur-$this->marge_droite-100;
		$posy=$this->marge_haute;

		$pdf->SetXY($this->marge_gauche,$posy);

		// Logo
		$logo=$conf->mycompany->dir_output.'/logos/'.$this->emetteur->logo;
		if ($this->emetteur->logo)
		{
			if (is_readable($logo))
			{
			    $height=pdf_getHeightForLogo($logo);
			    $pdf->Image($logo, $this->marge_gauche, $posy, 0, $height);	// width=0 (auto)
			}
			else
			{
				$pdf->SetTextColor(200,0,0);
				$pdf->SetFont('','B', $default_font_size - 2);
				$pdf->MultiCell(100, 3, $outputlangs->transnoentities("ErrorLogoFileNotFound",$logo), 0, 'L');
				$pdf->MultiCell(100, 3, $outputlangs->transnoentities("ErrorGoToModuleSetup"), 0, 'L');
			}
		}
		else
		{
			$text=$this->emetteur->name;
			$pdf->MultiCell(100, 4, $outputlangs->convToOutputCharset($text), 0, 'L');
		}

		$pdf->SetFont('', 'B', $default_font_size + 3);
		$pdf->SetXY($posx,$posy);
		$pdf->SetTextColor(0,0,60);
		$title=$outputlangs->transnoentities("Deleg")." ".$outputlangs->convToOutputCharset($object->ref);
		$pdf->MultiCell(100, 3, $title, '', 'R');
		$posy+=1;


		$pdf->SetFont('','', $default_font_size -1); 

		/*if (! empty($object->date_commande))
		{
			$posy+=4;
			$pdf->SetXY($posx,$posy);
			$pdf->SetTextColor(0,0,60);
			$pdf->MultiCell(100, 3, $outputlangs->transnoentities("OrderDate")." : " . dol_print_date($object->date_commande,"day",false,$outputlangs,true), '', 'R');
		}
		else
		{
			$posy+=4;
			$pdf->SetXY($posx,$posy);
			$pdf->SetTextColor(255,0,0);
			$pdf->MultiCell(100, 3, $outputlangs->transnoentities("OrderToProcess"), '', 'R');
		}*/

		$pdf->SetTextColor(0,0,60);
		$usehourmin='day';
		/*if (!empty($conf->global->SUPPLIER_ORDER_USE_HOUR_FOR_DELIVERY_DATE)) $usehourmin='dayhour';
		if (! empty($object->date_livraison))
		{
			$posy+=4;
			$pdf->SetXY($posx-90,$posy);
			$pdf->MultiCell(190, 3, $outputlangs->transnoentities("DateDeliveryPlanned")." : " . dol_print_date($object->date_livraison,$usehourmin,false,$outputlangs,true), '', 'R');
		}*/

		/*if ($object->thirdparty->code_fournisseur)
		{
			$posy+=4;
			$pdf->SetXY($posx,$posy);
			$pdf->SetTextColor(0,0,60);
			$pdf->MultiCell(100, 3, $outputlangs->transnoentities("SupplierCode")." : " . $outputlangs->transnoentities($object->thirdparty->code_fournisseur), '', 'R');
		}*/

		$posy+=1;
		$pdf->SetTextColor(0,0,60);

		/*
		// Show list of linked objects
		$posy = pdf_writeLinkedObjects($pdf, $object, $outputlangs, $posx, $posy, 100, 3, 'R', $default_font_size);
		*/

		if ($showaddress)
		{
			// Sender properties
			$carac_emetteur = pdf_build_address($outputlangs, $this->emetteur, $object->thirdparty);

			// Show sender
			$posy=42;
			$posx=$this->marge_gauche;
			if (! empty($conf->global->MAIN_INVERT_SENDER_RECIPIENT)) $posx=$this->page_largeur-$this->marge_droite-80;
			$hautcadre=40;

			// Show sender frame
			$pdf->SetTextColor(0,0,0);
			$pdf->SetFont('','', $default_font_size - 2);
			$pdf->SetXY($posx,$posy-5);
			$pdf->MultiCell(66,5, $outputlangs->transnoentities("BillFrom").":", 0, 'L');
			$pdf->SetXY($posx,$posy);
			$pdf->SetFillColor(230,230,230);
			$pdf->MultiCell(82, $hautcadre, "", 0, 'R', 1);
			$pdf->SetTextColor(0,0,60);

			// Show sender name
			$pdf->SetXY($posx+2,$posy+3);
			$pdf->SetFont('','B', $default_font_size);
			$pdf->MultiCell(80, 4, $outputlangs->convToOutputCharset($this->emetteur->name), 0, 'L');
			$posy=$pdf->getY();

			// Show sender information
			$pdf->SetXY($posx+2,$posy);
			$pdf->SetFont('','', $default_font_size - 1);
			$pdf->MultiCell(80, 4, $carac_emetteur, 0, 'L');



			// If BILLING contact defined on order, we use it
			$usecontact=false;
			$arrayidcontact=$object->getIdContact('external','BILLING');
			if (count($arrayidcontact) > 0)
			{
				$usecontact=true;
				$result=$object->fetch_contact($arrayidcontact[0]);
			}

			//Recipient name
			// On peut utiliser le nom de la societe du contact
			if ($usecontact && !empty($conf->global->MAIN_USE_COMPANY_NAME_OF_CONTACT)) {
				$thirdparty = $object->contact;
			} else {
				$thirdparty = $object->thirdparty;
			}

			$carac_client_name= pdfBuildThirdpartyName($thirdparty, $outputlangs);

			$carac_client=pdf_build_address($outputlangs,$this->emetteur,$object->thirdparty,($usecontact?$object->contact:''),$usecontact,'target',$object);

			// Show recipient
			$widthrecbox=100;
			if ($this->page_largeur < 210) $widthrecbox=84;	// To work with US executive format
			$posy=42;
			$posx=$this->page_largeur-$this->marge_droite-$widthrecbox;
			if (! empty($conf->global->MAIN_INVERT_SENDER_RECIPIENT)) $posx=$this->marge_gauche;
/*
			// Show recipient frame
			$pdf->SetTextColor(0,0,0);
			$pdf->SetFont('','', $default_font_size - 2);
			$pdf->SetXY($posx+2,$posy-5);
			$pdf->MultiCell($widthrecbox, 5, $outputlangs->transnoentities("BillTo").":",0,'L');
			$pdf->Rect($posx, $posy, $widthrecbox, $hautcadre);

			// Show recipient name
			$pdf->SetXY($posx+2,$posy+3);
			$pdf->SetFont('','B', $default_font_size);
			$pdf->MultiCell($widthrecbox, 4, $carac_client_name, 0, 'L');

			$posy = $pdf->getY();

			// Show recipient information
			$pdf->SetFont('','', $default_font_size - 1);
			$pdf->SetXY($posx+2,$posy);
			$pdf->MultiCell($widthrecbox, 4, $carac_client, 0, 'L');
*/
		}
	}


		/**
	 *   	Show footer of page. Need this->emetteur object
     *
	 *   	@param	PDF			$pdf     			PDF
	 * 		@param	CommandeFournisseur		$object				Object to show
	 *      @param	Translate	$outputlangs		Object lang for output
	 *      @param	int			$hidefreetext		1=Hide free text
	 *      @return	int								Return height of bottom margin including footer text
	 */
	function _pagefoot(&$pdf, $object, $outputlangs, $hidefreetext=0)
	{
		global $conf;
		$showdetails=$conf->global->MAIN_GENERATE_DOCUMENTS_SHOW_FOOT_DETAILS;
		//return pdf_pagefoot($pdf,$outputlangs,'',$this->emetteur,$this->marge_basse,$this->marge_gauche,$this->page_hauteur,$object,$showdetails,$hidefreetext);
	}

}
