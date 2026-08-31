<?php
/* Copyright (C) 2019-2025	Pierre Ardoin	<developpeur@lesmetiersdubatiment.fr>
	*
	* This program is free software; you can redistribute it and/or modify
	* it under the terms of the GNU General Public License as published by
	* the Free Software Foundation; either version 2 of the License, or
	* (at your option) any later version.
	*
	* This program is distributed in the hope that it will be useful,
	* but WITHOUT ANY WARRANTY; without even the implied warranty of
	* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	* GNU General Public License for more details.
	*
	* You should have received a copy of the GNU General Public License
	* along with this program. If not, see <http://www.gnu.org/licenses/>.
	*/

/**		\class      DC2
	*		\brief      Class to manage DC2 data
	*		\brief      Classe de gestion du formulaire DC2
	*/

require_once(DOL_DOCUMENT_ROOT ."/core/class/commonobject.class.php");
require_once(DOL_DOCUMENT_ROOT ."/core/lib/functions.lib.php");
require_once(DOL_DOCUMENT_ROOT ."/core/lib/functions2.lib.php");
require_once(DOL_DOCUMENT_ROOT.'/core/lib/price.lib.php');
require_once(DOL_DOCUMENT_ROOT."/core/lib/files.lib.php");
require_once DOL_DOCUMENT_ROOT.'/comm/propal/class/propal.class.php';
require_once DOL_DOCUMENT_ROOT.'/product/class/product.class.php';
require_once DOL_DOCUMENT_ROOT.'/comm/action/class/actioncomm.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/modules/propale/modules_propale.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/propal.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/extrafields.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/doleditor.class.php';
require_once DOL_DOCUMENT_ROOT.'/fourn/class/fournisseur.commande.class.php';


/**
	*      \class      DC2
	*      \brief      Class to manage DC2
	*      \brief      Classe pour gerer le formulaire DC2
	*/
class DC2 extends CommonObject
{
	var $db;
	var $error;
	var $element = 'DC2';
	var $table_element = '';
	var $table_element_line = 'DC2';
	var $table_element_line2 = 'DC2_groupement';
	var $fk_element = '';
	var $ismultientitymanaged = 0;

	var $lines = array();
	var $line_dc1;
	var $line_dc2;

	function __construct($db)
	{
		$this->db = $db;
	}

	function call($action, $args)
	{
		if (empty($action)) return 0;
		if (method_exists($this, $action)) {
			$result = call_user_func_array(array($this, $action), $args);
			return $result;
		} else {
			return 0;
		}
	}

	/**
		* Fetch object lines from database
		*
		* @return  int     <0 if KO, >0 if OK
		*/
	function fetch()
	{
		global $conf, $langs, $object, $dc1_line;

		$this->lines = array();

		$sql = "SELECT *";
		$sql.= " FROM ".MAIN_DB_PREFIX."DC2 WHERE `fk_object` = ".((int)$object->id);

		dol_syslog("DC2::fetch sql=".$sql, LOG_DEBUG);

		$result = $this->db->query($sql);

		if ($result)
		{
			$num = $this->db->num_rows($result);
			$i = 0 ;

			if ($num == 1)
			{
				while ($i < $num)
				{
					$obj = $this->db->fetch_object($result);

					$this->lines[$i]            = $obj;
					$this->lines[$i]->AB_idem_DC1 = $obj->AB_idem_DC1 ? $obj->AB_idem_DC1 : 0;
					$this->lines[$i]->A = trim($obj->A);
					$this->lines[$i]->B = trim($obj->B);
					$this->lines[$i]->C1 = $obj->C1 ? $obj->C1 : 0;
					$this->lines[$i]->C2 = $obj->C2 ? $obj->C2 : 0;
					$this->lines[$i]->C2_Date = trim($obj->C2_Date);
					$this->lines[$i]->C2_idem = $obj->C2_idem ? $obj->C2_idem : 0;
					$this->lines[$i]->C2_adresse_internet = trim($obj->C2_adresse_internet);
					$this->lines[$i]->C2_renseignement_adresse = trim($obj->C2_renseignement_adresse);
					$this->lines[$i]->D1_liste =  trim($obj->D1_liste);
					$this->lines[$i]->D1_reference =  trim($obj->D1_reference);
					$this->lines[$i]->D1_idem = $obj->D1_idem ? $obj->D1_idem : 0;
					$this->lines[$i]->D1_adresse_internet =  trim($obj->D1_adresse_internet);
					$this->lines[$i]->D1_renseignement_adresse =  trim($obj->D1_renseignement_adresse);
					$this->lines[$i]->D2 = $obj->D2 ? $obj->D2 : 0;
					$this->lines[$i]->E1_registre_pro =  trim($obj->E1_registre_pro);
					$this->lines[$i]->E1_registre_spec =  trim($obj->E1_registre_spec);
					$this->lines[$i]->E3_idem = $obj->E3_idem ? $obj->E3_idem : 0;
					$this->lines[$i]->E3_adresse_internet =  trim($obj->E3_adresse_internet);
					$this->lines[$i]->E3_renseignement_adresse =  trim($obj->E3_renseignement_adresse);
					$this->lines[$i]->F_CA3_debut =  trim($obj->F_CA3_debut);
					$this->lines[$i]->F_CA3_fin =  trim($obj->F_CA3_fin);
					$this->lines[$i]->F_CA3_montant =  trim($obj->F_CA3_montant);
					$this->lines[$i]->F_CA2_debut =  trim($obj->F_CA2_debut);
					$this->lines[$i]->F_CA2_fin =  trim($obj->F_CA2_fin);
					$this->lines[$i]->F_CA2_montant =  trim($obj->F_CA2_montant);
					$this->lines[$i]->F_CA1_debut =  trim($obj->F_CA1_debut);
					$this->lines[$i]->F_CA1_fin =  trim($obj->F_CA1_fin);
					$this->lines[$i]->F_CA1_montant =  trim($obj->F_CA1_montant);
					$this->lines[$i]->F_date_creation =  trim($obj->F_date_creation);
					$this->lines[$i]->F2 =  trim($obj->F2);
					$this->lines[$i]->F3 = $obj->F3 ? $obj->F3 : 0;
					$this->lines[$i]->F4_idem = $obj->F4_idem ? $obj->F4_idem : 0;
					$this->lines[$i]->F4_adresse_internet =  trim($obj->F4_adresse_internet);
					$this->lines[$i]->F4_renseignement_adresse =  trim($obj->F4_renseignement_adresse);
					$this->lines[$i]->G1 =  trim($obj->G1);
					$this->lines[$i]->G2_idem = $obj->G2_idem ? $obj->G2_idem : 0;
					$this->lines[$i]->G2_adresse_internet =  trim($obj->G2_adresse_internet);
					$this->lines[$i]->G2_renseignement_adresse =  trim($obj->G2_renseignement_adresse);
					$this->lines[$i]->H =  trim($obj->H);
					$this->lines[$i]->I1 =  trim($obj->I1);
					$this->lines[$i]->I2 =  trim($obj->I2);

					$i++;
				}

			}
			else
			{

				$this->line  = new DC2Line($this->db);
			
				$this->line->fk_object = $object->id;
				$this->line->fk_element = $object->element; 
				$this->line->AB_idem_DC1 = "1";
				$this->line->A = $object->socid;
				$this->line->B = $dc1_line->objet_consultation;
				$this->line->C1 = "0";
				$this->line->C2 = "0";
				$this->line->C2_Date = "1970-01-01";
				$this->line->C2_idem = "0";
				$this->line->C2_adresse_internet = "";
				$this->line->C2_renseignement_adresse = "";
				$this->line->D1_liste = "";
				$this->line->D1_reference = "";
				$this->line->D1_idem = "0";
				$this->line->D1_adresse_internet = "";
				$this->line->D1_renseignement_adresse = "";
				$this->line->D2 = "0";
				$this->line->E1_registre_pro = "";
				$this->line->E1_registre_spec = "";
				$this->line->E3_idem = "0";
				$this->line->E3_adresse_internet = "";
				$this->line->E3_renseignement_adresse = "";
				$this->line->F_CA3_debut = "1970-01-01";
				$this->line->F_CA3_fin = "1970-01-01";
				$this->line->F_CA3_montant = "0";
				$this->line->F_CA2_debut = "1970-01-01";
				$this->line->F_CA2_fin = "1970-01-01";
				$this->line->F_CA2_montant = "0";
				$this->line->F_CA1_debut = "1970-01-01";
				$this->line->F_CA1_fin = "1970-01-01";
				$this->line->F_CA1_montant = "0";
				$this->line->F_date_creation = "1970-01-01";
				$this->line->F2 = "";
				$this->line->F3 = "0";
				$this->line->F4_idem = "0";
				$this->line->F4_adresse_internet = "";
				$this->line->F4_renseignement_adresse = "";
				$this->line->G1 = "";
				$this->line->G2_idem = "0";
				$this->line->G2_adresse_internet = "";
				$this->line->G2_renseignement_adresse = "";
				$this->line->H = "";
				$this->line->I1 = "";
				$this->line->I2 = "";
				
				$result = $this->line->insert();

				$this->lines = array();

				$sql = "SELECT *";
				$sql.= " FROM ".MAIN_DB_PREFIX."DC2 WHERE `fk_object` = ".((int)$object->id);

				dol_syslog("DC2::fetch sql=".$sql, LOG_DEBUG);

				$result = $this->db->query($sql);

				
				if ($result)
				{
					$num = $this->db->num_rows($result);
					$i = 0 ;

					if ($num == 1)
					{
						while ($i < $num)
						{
							$obj = $this->db->fetch_object($result);


							$this->lines[$i]            = $obj;
							$this->lines[$i]->AB_idem_DC1 = $obj->AB_idem_DC1 ? $obj->AB_idem_DC1 : 0;
							$this->lines[$i]->A = trim($obj->A);
							$this->lines[$i]->B = trim($obj->B);
							$this->lines[$i]->C1 = $obj->C1 ? $obj->C1 : 0;
							$this->lines[$i]->C2 = $obj->C2 ? $obj->C2 : 0;
							$this->lines[$i]->C2_Date = trim($obj->C2_Date);
							$this->lines[$i]->C2_idem = $obj->C2_idem ? $obj->C2_idem : 0;
							$this->lines[$i]->C2_adresse_internet = trim($obj->C2_adresse_internet);
							$this->lines[$i]->C2_renseignement_adresse = trim($obj->C2_renseignement_adresse);
							$this->lines[$i]->D1_liste =  trim($obj->D1_liste);
							$this->lines[$i]->D1_reference =  trim($obj->D1_reference);
							$this->lines[$i]->D1_idem = $obj->D1_idem ? $obj->D1_idem : 0;
							$this->lines[$i]->D1_adresse_internet =  trim($obj->D1_adresse_internet);
							$this->lines[$i]->D1_renseignement_adresse =  trim($obj->D1_renseignement_adresse);
							$this->lines[$i]->D2 = $obj->D2 ? $obj->D2 : 0;
							$this->lines[$i]->E1_registre_pro =  trim($obj->E1_registre_pro);
							$this->lines[$i]->E1_registre_spec =  trim($obj->E1_registre_spec);
							$this->lines[$i]->E3_idem = $obj->E3_idem ? $obj->E3_idem : 0;
							$this->lines[$i]->E3_adresse_internet =  trim($obj->E3_adresse_internet);
							$this->lines[$i]->E3_renseignement_adresse =  trim($obj->E3_renseignement_adresse);
							$this->lines[$i]->F_CA3_debut =  trim($obj->F_CA3_debut);
							$this->lines[$i]->F_CA3_fin =  trim($obj->F_CA3_fin);
							$this->lines[$i]->F_CA3_montant =  trim($obj->F_CA3_montant);
							$this->lines[$i]->F_CA2_debut =  trim($obj->F_CA2_debut);
							$this->lines[$i]->F_CA2_fin =  trim($obj->F_CA2_fin);
							$this->lines[$i]->F_CA2_montant =  trim($obj->F_CA2_montant);
							$this->lines[$i]->F_CA1_debut =  trim($obj->F_CA1_debut);
							$this->lines[$i]->F_CA1_fin =  trim($obj->F_CA1_fin);
							$this->lines[$i]->F_CA1_montant =  trim($obj->F_CA1_montant);
							$this->lines[$i]->F_date_creation =  trim($obj->F_date_creation);
							$this->lines[$i]->F2 =  trim($obj->F2);
							$this->lines[$i]->F3 = $obj->F3 ? $obj->F3 : 0;
							$this->lines[$i]->F4_idem = $obj->F4_idem ? $obj->F4_idem : 0;
							$this->lines[$i]->F4_adresse_internet =  trim($obj->F4_adresse_internet);
							$this->lines[$i]->F4_renseignement_adresse =  trim($obj->F4_renseignement_adresse);
							$this->lines[$i]->G1 =  trim($obj->G1);
							$this->lines[$i]->G2_idem = $obj->G2_idem ? $obj->G2_idem : 0;
							$this->lines[$i]->G2_adresse_internet =  trim($obj->G2_adresse_internet);
							$this->lines[$i]->G2_renseignement_adresse =  trim($obj->G2_renseignement_adresse);
							$this->lines[$i]->H =  trim($obj->H);
							$this->lines[$i]->I1 =  trim($obj->I1);
							$this->lines[$i]->I2 =  trim($obj->I2);

							$i++;
						}

					}
				}

			}  
			return 1;   
		}
		else
		{
			$this->error = $this->db->error()." sql=".$sql;
			return -1;
		}

	}

	/**
		*  \brief Update a line
		*
		*/
	function updateline($user)
	{
		global $langs, $conf, $object;

		$lineid = GETPOST('lineid') ? GETPOST('lineid') : 0;
		$field = GETPOST('field');

		$AB_idem_DC1 =  GETPOST('AB_idem_DC1');
		$A = GETPOST('A');
		$B = GETPOST('B');
		$C1 = GETPOST('C1');
		$C2 = GETPOST('C2');

		// Util utilitaire : construit une date SQL 'Y-m-d' à partir des champs POST {prefix}month / {prefix}day / {prefix}year
		$buildDateFromPost = function(string $prefix) {
			// utilisation de GETPOST(...,'int') pour éviter undefined index / valeurs non numériques
			$month = GETPOST($prefix . 'month','int');
			$day   = GETPOST($prefix . 'day','int');
			$year  = GETPOST($prefix . 'year','int');

			if (empty($month) || empty($day) || empty($year)) return ''; // chaîne vide = pas de date fournie

			// dol_mktime retourne timestamp ou '' -> on vérifie
			$ts = dol_mktime(12, 0, 0, $month, $day, $year);
			if (is_numeric($ts) && $ts > 0) {
				return date('Y-m-d', (int)$ts);
			}
			return '';
		};

		$C2_Date = $buildDateFromPost('C2_Date'); // correspond à C2_Datemonth, C2_Dateday, C2_Dateyear ?
		// Le code d'origine utilisait $_POST['C2_Datemonth'] etc. Certains prefix peuvent différer.
		// On essaye aussi une fallback avec 'C2_' (C2_Datemonth etc.) pour compatibilité avec ton form
		if (empty($C2_Date)) {
			$C2_Date = (function() use ($buildDateFromPost) {
				return $buildDateFromPost('C2_'); // C2_month, C2_day, C2_year
			})();
		}

		$C2_idem = GETPOST('C2_idem');
		$C2_adresse_internet = GETPOST('C2_adresse_internet');
		$C2_renseignement_adresse = GETPOST('C2_renseignement_adresse');
		$D1_liste =  GETPOST('D1_liste');
		$D1_reference =  GETPOST('D1_reference');
		$D1_idem = GETPOST('D1_idem');
		$D1_adresse_internet =  GETPOST('D1_adresse_internet');
		$D1_renseignement_adresse =  GETPOST('D1_renseignement_adresse');
		$D2 = GETPOST('D2');
		$E1_registre_pro =  GETPOST('E1_registre_pro');
		$E1_registre_spec =  GETPOST('E1_registre_spec');
		$E3_idem = GETPOST('E3_idem');
		$E3_adresse_internet =  GETPOST('E3_adresse_internet');
		$E3_renseignement_adresse =  GETPOST('E3_renseignement_adresse');

		$F_CA3_debut = '';
		$F_CA3_fin = '';
		$F_CA2_debut = '';
		$F_CA2_fin = '';
		$F_CA1_debut = '';
		$F_CA1_fin = '';
		$F_date_creation = '';

		// Construire correctement les dates à partir des POST
		// Compatibilité avec les noms de champs utilisés précédemment (F_CA3_debutmonth, etc.)
		$F_CA3_debut = (function() use ($buildDateFromPost) {
			$d = $buildDateFromPost('F_CA3_debut');
			if (!empty($d)) return $d;
			return $buildDateFromPost('F_CA3_debut'); // tentatives supplémentaires si nom différent
		})();
		$F_CA3_fin = $buildDateFromPost('F_CA3_fin');
		if (empty($F_CA3_fin)) $F_CA3_fin = $buildDateFromPost('F_CA3_fin'); // fallback

		$F_CA2_debut = $buildDateFromPost('F_CA2_debut');
		$F_CA2_fin = $buildDateFromPost('F_CA2_fin');

		$F_CA1_debut = $buildDateFromPost('F_CA1_debut');
		$F_CA1_fin = $buildDateFromPost('F_CA1_fin');

		$F_date_creation = $buildDateFromPost('F_date_creation');

		$F_CA3_montant =  GETPOST('F_CA3_montant');
		$F_CA2_montant =  GETPOST('F_CA2_montant');
		$F_CA1_montant =  GETPOST('F_CA1_montant');
		$F2 =  GETPOST('F2');
		$F3 = GETPOST('F3');
		$F4_idem = GETPOST('F4_idem');
		$F4_adresse_internet =  GETPOST('F4_adresse_internet');
		$F4_renseignement_adresse =  GETPOST('F4_renseignement_adresse');
		$G1 =  GETPOST('G1');
		$G2_idem = GETPOST('G2_idem');
		$G2_adresse_internet =  GETPOST('G2_adresse_internet');
		$G2_renseignement_adresse =  GETPOST('G2_renseignement_adresse');
		$H =  GETPOST('H');
		$I1 =  GETPOST('I1');
		$I2 =  GETPOST('I2');

		$line = new DC2Line($this->db);

		$result = $line->fetch($lineid);

		if ($result)
		{
			$line->field = $field;

			$line->AB_idem_DC1 = $AB_idem_DC1 ;
			$line->A = $A ;
			$line->B = $B ;
			$line->C1 = $C1 ;
			$line->C2 = $C2 ;
			$line->C2_Date = $C2_Date ;
			$line->C2_idem = $C2_idem ;
			$line->C2_adresse_internet = $C2_adresse_internet ;
			$line->C2_renseignement_adresse = $C2_renseignement_adresse ;
			$line->D1_liste = $D1_liste ;
			$line->D1_reference = $D1_reference ;
			$line->D1_idem = $D1_idem ;
			$line->D1_adresse_internet = $D1_adresse_internet ;
			$line->D1_renseignement_adresse = $D1_renseignement_adresse ;
			$line->D2 = $D2 ;
			$line->E1_registre_pro = $E1_registre_pro ;
			$line->E1_registre_spec = $E1_registre_spec ;
			$line->E3_idem = $E3_idem ;
			$line->E3_adresse_internet = $E3_adresse_internet ;
			$line->E3_renseignement_adresse = $E3_renseignement_adresse ;
			$line->F_CA3_debut = $F_CA3_debut ;
			$line->F_CA3_fin = $F_CA3_fin ;
			$line->F_CA3_montant = $F_CA3_montant ;
			$line->F_CA2_debut = $F_CA2_debut ;
			$line->F_CA2_fin = $F_CA2_fin ;
			$line->F_CA2_montant = $F_CA2_montant ;
			$line->F_CA1_debut = $F_CA1_debut ;
			$line->F_CA1_fin = $F_CA1_fin ;
			$line->F_CA1_montant = $F_CA1_montant ;
			$line->F_date_creation = $F_date_creation ;
			$line->F2 = $F2 ;
			$line->F3 = $F3 ;
			$line->F4_idem = $F4_idem ;
			$line->F4_adresse_internet = $F4_adresse_internet ;
			$line->F4_renseignement_adresse = $F4_renseignement_adresse ;
			$line->G1 = $G1 ;
			$line->G2_idem = $G2_idem ;
			$line->G2_adresse_internet = $G2_adresse_internet ;
			$line->G2_renseignement_adresse = $G2_renseignement_adresse ;
			$line->H = $H ;
			$line->I1 = $I1 ;
			$line->I2 = $I2 ;
	
			$result = $line->update();

			if ($result > 0)
			{       
				$this->fetch();
			
				$this->error = $langs->trans('DC2LineUpdated');     
				return $line->rowid;
			}
			else
			{
				$this->error = $this->line->error ?? $this->error;

				return -2;
			}
		}
		else
		{
			$this->error = $langs->trans('DC2LineDoesNotExist');
			return 0;
		}
			
	}
			
}

/**
	*  \class          DC2Line
	*  \brief          Class to manage DC2 lines
	*/
class DC2Line
{
	var $db;
	var $error;

	var $oldline;

	var $rowid;
	var $fk_object;
	var $fk_element;

	var $AB_idem_DC1 ;
	var $A ;
	var $B ;
	var $C1 ;
	var $C2 ;
	var $C2_Date ;
	var $C2_idem ;
	var $C2_adresse_internet;
	var $C2_renseignement_adresse ;
	var $D1_liste ;
	var $D1_reference ;
	var $D1_idem ;
	var $D1_adresse_internet ;
	var $D1_renseignement_adresse ;
	var $D2 ;
	var $E1_registre_pro ;
	var $E1_registre_spec ;
	var $E3_idem ;
	var $E3_adresse_internet ;
	var $E3_renseignement_adresse ;
	var $F_CA3_debut ;
	var $F_CA3_fin ;
	var $F_CA3_montant ;
	var $F_CA2_debut ;
	var $F_CA2_fin ;
	var $F_CA2_montant ;
	var $F_CA1_debut ;
	var $F_CA1_fin ;
	var $F_CA1_montant ;
	var $F_date_creation ;
	var $F2 ;
	var $F3 ;
	var $F4_idem ;
	var $F4_adresse_internet ;
	var $F4_renseignement_adresse ;
	var $G1 ;
	var $G2_idem ;
	var $G2_adresse_internet ;
	var $G2_renseignement_adresse ;
	var $H ;
	var $I1 ;
	var $I2 ;

	function __construct($DB)
	{
		$this->db = $DB;
	}

	function fetch($lineid = 0)
	{
		global $langs, $user, $conf;

		$sql = "SELECT *";
		$sql.= " FROM ".MAIN_DB_PREFIX."DC2";
		$sql.= " WHERE `rowid` = ".((int)$lineid);

		dol_syslog("DC2Line::fetch sql=".$sql);

		$result = $this->db->query($sql);

		if ($result)
		{
			$num = $this->db->num_rows($result);

			if ($num)
			{
				$obj = $this->db->fetch_object($result);

				$this->rowid        = $obj->rowid ? $obj->rowid : 0;
				$this->fk_object    = $obj->fk_object ? $obj->fk_object : 0;
				$this->fk_element   = $obj->fk_element ? $obj->fk_element : '';

				$this->line            = $obj;
				$this->line->AB_idem_DC1 = $obj->AB_idem_DC1 ? $obj->AB_idem_DC1 : 0;
				$this->line->A = trim($obj->A);
				$this->line->B = trim($obj->B);
				$this->line->C1 = $obj->C1 ? $obj->C1 : 0;
				$this->line->C2 = $obj->C2 ? $obj->C2 : 0;
				$this->line->C2_Date = trim($obj->C2_Date);
				$this->line->C2_idem = $obj->C2_idem ? $obj->C2_idem : 0;
				$this->line->C2_adresse_internet = trim($obj->C2_adresse_internet);
				$this->line->C2_renseignement_adresse = trim($obj->C2_renseignement_adresse);
				$this->line->D1_liste =  trim($obj->D1_liste);
				$this->line->D1_reference =  trim($obj->D1_reference);
				$this->line->D1_idem = $obj->D1_idem ? $obj->D1_idem : 0;
				$this->line->D1_adresse_internet =  trim($obj->D1_adresse_internet);
				$this->line->D1_renseignement_adresse =  trim($obj->D1_renseignement_adresse);
				$this->line->D2 = $obj->D2 ? $obj->D2 : 0;
				$this->line->E1_registre_pro =  trim($obj->E1_registre_pro);
				$this->line->E1_registre_spec =  trim($obj->E1_registre_spec);
				$this->line->E3_idem = $obj->E3_idem ? $obj->E3_idem : 0;
				$this->line->E3_adresse_internet =  trim($obj->E3_adresse_internet);
				$this->line->E3_renseignement_adresse =  trim($obj->E3_renseignement_adresse);
				$this->line->F_CA3_debut =  trim($obj->F_CA3_debut);
				$this->line->F_CA3_fin =  trim($obj->F_CA3_fin);
				$this->line->F_CA3_montant =  trim($obj->F_CA3_montant);
				$this->line->F_CA2_debut =  trim($obj->F_CA2_debut);
				$this->line->F_CA2_fin =  trim($obj->F_CA2_fin);
				$this->line->F_CA2_montant =  trim($obj->F_CA2_montant);
				$this->line->F_CA1_debut =  trim($obj->F_CA1_debut);
				$this->line->F_CA1_fin =  trim($obj->F_CA1_fin);
				$this->line->F_CA1_montant =  trim($obj->F_CA1_montant);
				$this->line->F_date_creation =  trim($obj->F_date_creation);
				$this->line->F2 =  trim($obj->F2);
				$this->line->F3 = $obj->F3 ? $obj->F3 : 0;
				$this->line->F4_idem = $obj->F4_idem ? $obj->F4_idem : 0;
				$this->line->F4_adresse_internet =  trim($obj->F4_adresse_internet);
				$this->line->F4_renseignement_adresse =  trim($obj->F4_renseignement_adresse);
				$this->line->G1 =  trim($obj->G1);
				$this->line->G2_idem = $obj->G2_idem ? $obj->G2_idem : 0;
				$this->line->G2_adresse_internet =  trim($obj->G2_adresse_internet);
				$this->line->G2_renseignement_adresse =  trim($obj->G2_renseignement_adresse);
				$this->line->H =  trim($obj->H);
				$this->line->I1 =  trim($obj->I1);
				$this->line->I2 =  trim($obj->I2);
				
				return $this->rowid;
			}
			else
			{
				$this->error = $langs->trans('DC2LineDoesNotExist');
				return -1;
			}

		}
		else
		{
			$this->error = $this->db->error()." sql=".$sql;

			return -1;
		}
	}

	function insert($notrigger = 0)
	{
		global $langs, $user, $conf;
		
		$this->db->begin();

		// escape values
		$fk_object = (int)$this->fk_object;
		$fk_element = $this->db->escape($this->fk_element);
		$AB_idem_DC1 = $this->db->escape($this->AB_idem_DC1);
		$A = $this->db->escape($this->A);
		$B = $this->db->escape($this->B);
		$C1 = $this->db->escape($this->C1);
		$C2 = $this->db->escape($this->C2);
		$C2_Date = $this->db->escape($this->C2_Date);
		$C2_idem = $this->db->escape($this->C2_idem);
		$C2_adresse_internet = $this->db->escape($this->C2_adresse_internet);
		$C2_renseignement_adresse = $this->db->escape($this->C2_renseignement_adresse);
		$D1_liste = $this->db->escape($this->D1_liste);
		$D1_reference = $this->db->escape($this->D1_reference);
		$D1_idem = $this->db->escape($this->D1_idem);
		$D1_adresse_internet = $this->db->escape($this->D1_adresse_internet);
		$D1_renseignement_adresse = $this->db->escape($this->D1_renseignement_adresse);
		$D2 = $this->db->escape($this->D2);
		$E1_registre_pro = $this->db->escape($this->E1_registre_pro);
		$E1_registre_spec = $this->db->escape($this->E1_registre_spec);
		$E3_idem = $this->db->escape($this->E3_idem);
		$E3_adresse_internet = $this->db->escape($this->E3_adresse_internet);
		$E3_renseignement_adresse = $this->db->escape($this->E3_renseignement_adresse);
		$F_CA3_debut = $this->db->escape($this->F_CA3_debut);
		$F_CA3_fin = $this->db->escape($this->F_CA3_fin);
		$F_CA3_montant = $this->db->escape($this->F_CA3_montant);
		$F_CA2_debut = $this->db->escape($this->F_CA2_debut);
		$F_CA2_fin = $this->db->escape($this->F_CA2_fin);
		$F_CA2_montant = $this->db->escape($this->F_CA2_montant);
		$F_CA1_debut = $this->db->escape($this->F_CA1_debut);
		$F_CA1_fin = $this->db->escape($this->F_CA1_fin);
		$F_CA1_montant = $this->db->escape($this->F_CA1_montant);
		$F_date_creation = $this->db->escape($this->F_date_creation);
		$F2 = $this->db->escape($this->F2);
		$F3 = $this->db->escape($this->F3);
		$F4_idem = $this->db->escape($this->F4_idem);
		$F4_adresse_internet = $this->db->escape($this->F4_adresse_internet);
		$F4_renseignement_adresse = $this->db->escape($this->F4_renseignement_adresse);
		$G1 = $this->db->escape($this->G1);
		$G2_idem = $this->db->escape($this->G2_idem);
		$G2_adresse_internet = $this->db->escape($this->G2_adresse_internet);
		$G2_renseignement_adresse = $this->db->escape($this->G2_renseignement_adresse);
		$H = $this->db->escape($this->H);
		$I1 = $this->db->escape($this->I1);
		$I2 = $this->db->escape($this->I2);

		$sql = "INSERT INTO ".MAIN_DB_PREFIX."DC2";
		$sql.= " (`fk_object`, `fk_element`, `AB_idem_DC1`, `A`, `B`, `C1` ,`C2`,`C2_Date`, `C2_idem`, `C2_adresse_internet`,`C2_renseignement_adresse`, `D1_liste`, `D1_reference`, `D1_idem`, `D1_adresse_internet`, `D1_renseignement_adresse`, `D2`, `E1_registre_pro`, `E1_registre_spec`, `E3_idem`, `E3_adresse_internet`, `E3_renseignement_adresse`, `F_CA3_debut`, `F_CA3_fin`, `F_CA3_montant`, `F_CA2_debut`, `F_CA2_fin`, `F_CA2_montant`, `F_CA1_debut`, `F_CA1_fin`, `F_CA1_montant`, `F_date_creation`, `F2`, `F3`, `F4_idem`, `F4_adresse_internet`, `F4_renseignement_adresse`, `G1`, `G2_idem`, `G2_adresse_internet`, `G2_renseignement_adresse`, `H`, `I1`, `I2`)";
		$sql.= " VALUES (".$fk_object.",";
		$sql.= " '".$fk_element."', ";
		$sql.= " '".$AB_idem_DC1."', ";
		$sql.= " '".$A."', ";
		$sql.= " '".$B."', ";
		$sql.= " '".$C1."', ";
		$sql.= " '".$C2."', ";
		$sql.= " '".$C2_Date."', ";
		$sql.= " '".$C2_idem."', ";
		$sql.= " '".$C2_adresse_internet."', ";
		$sql.= " '".$C2_renseignement_adresse."', ";
		$sql.= " '".$D1_liste."', ";
		$sql.= " '".$D1_reference."', ";
		$sql.= " '".$D1_idem."', ";
		$sql.= " '".$D1_adresse_internet."', ";
		$sql.= " '".$D1_renseignement_adresse."', ";
		$sql.= " '".$D2."', ";
		$sql.= " '".$E1_registre_pro."', ";
		$sql.= " '".$E1_registre_spec."', ";
		$sql.= " '".$E3_idem."', ";
		$sql.= " '".$E3_adresse_internet."', ";
		$sql.= " '".$E3_renseignement_adresse."', ";
		$sql.= " '".$F_CA3_debut."', ";
		$sql.= " '".$F_CA3_fin."', ";
		$sql.= " '".$F_CA3_montant."', ";
		$sql.= " '".$F_CA2_debut."', ";
		$sql.= " '".$F_CA2_fin."', ";
		$sql.= " '".$F_CA2_montant."', ";
		$sql.= " '".$F_CA1_debut."', ";
		$sql.= " '".$F_CA1_fin."', ";
		$sql.= " '".$F_CA1_montant."', ";
		$sql.= " '".$F_date_creation."', ";
		$sql.= " '".$F2."', ";
		$sql.= " '".$F3."', ";
		$sql.= " '".$F4_idem."', ";
		$sql.= " '".$F4_adresse_internet."', ";
		$sql.= " '".$F4_renseignement_adresse."', ";
		$sql.= " '".$G1."', ";
		$sql.= " '".$G2_idem."', ";
		$sql.= " '".$G2_adresse_internet."', ";
		$sql.= " '".$G2_renseignement_adresse."', ";
		$sql.= " '".$H."', ";
		$sql.= " '".$I1."', ";
		$sql.= " '".$I2."'";
		$sql.= ')';

		dol_syslog("DC2Line::insert sql=".$sql);

		$resql = $this->db->query($sql);
		if ($resql)
		{           
			if (! $notrigger)
			{
				include_once(DOL_DOCUMENT_ROOT . "/core/class/interfaces.class.php");
				$interface = new Interfaces($this->db);
				$result = $interface->run_triggers('LINEDC2_INSERT', $this, $user ,$langs, $conf);
				if ($result < 0) {
					$this->error = $langs->trans('ErrorCallingTrigger');
					$this->db->rollback();
					return -1;
				}
			}

			$this->db->commit();

			return 1;

		}
		else
		{
			$this->error = $this->db->error()." sql=".$sql;
			$this->db->rollback();

			return -2;
		}
	}

	function update($notrigger = 0)
	{
		global $langs, $user, $conf;

		// Clean parameters
		$this->AB_idem_DC1 = $this->AB_idem_DC1 ? $this->AB_idem_DC1 : 0;
		$this->A = trim($this->A);
		$this->B = trim($this->B);
		$this->C1 = $this->C1 ? $this->C1 : 0;
		$this->C2 = $this->C2 ? $this->C2 : 0;
		$this->C2_Date = trim($this->C2_Date);
		$this->C2_idem = $this->C2_idem ? $this->C2_idem : 0;
		$this->C2_adresse_internet = trim($this->C2_adresse_internet);
		$this->C2_renseignement_adresse = trim($this->C2_renseignement_adresse);
		$this->D1_liste =  trim($this->D1_liste);
		$this->D1_reference =  trim($this->D1_reference);
		$this->D1_idem = $this->D1_idem ? $this->D1_idem : 0;
		$this->D1_adresse_internet =  trim($this->D1_adresse_internet);
		$this->D1_renseignement_adresse =  trim($this->D1_renseignement_adresse);
		$this->D2 = $this->D2 ? $this->D2 : 0;
		$this->E1_registre_pro =  trim($this->E1_registre_pro);
		$this->E1_registre_spec =  trim($this->E1_registre_spec);
		$this->E3_idem = $this->E3_idem ? $this->E3_idem : 0;
		$this->E3_adresse_internet =  trim($this->E3_adresse_internet);
		$this->E3_renseignement_adresse =  trim($this->E3_renseignement_adresse);
		$this->F_CA3_debut =  trim($this->F_CA3_debut);
		$this->F_CA3_fin =  trim($this->F_CA3_fin);
		$this->F_CA3_montant =  trim($this->F_CA3_montant);
		$this->F_CA2_debut =  trim($this->F_CA2_debut);
		$this->F_CA2_fin =  trim($this->F_CA2_fin);
		$this->F_CA2_montant =  trim($this->F_CA2_montant);
		$this->F_CA1_debut =  trim($this->F_CA1_debut);
		$this->F_CA1_fin =  trim($this->F_CA1_fin);
		$this->F_CA1_montant =  trim($this->F_CA1_montant);
		$this->F_date_creation =  trim($this->F_date_creation);
		$this->F2 =  trim($this->F2);
		$this->F3 = $this->F3 ? $this->F3 : 0;
		$this->F4_idem = $this->F4_idem ? $this->F4_idem : 0;
		$this->F4_adresse_internet =  trim($this->F4_adresse_internet);
		$this->F4_renseignement_adresse =  trim($this->F4_renseignement_adresse);
		$this->G1 =  trim($this->G1);
		$this->G2_idem = $this->G2_idem ? $this->G2_idem : 0;
		$this->G2_adresse_internet =  trim($this->G2_adresse_internet);
		$this->G2_renseignement_adresse =  trim($this->G2_renseignement_adresse);
		$this->H =  trim($this->H);
		$this->I1 =  trim($this->I1);
		$this->I2 =  trim($this->I2);
				
		$this->db->begin();

		$sql = "UPDATE ".MAIN_DB_PREFIX."DC2";
		$sql.= " SET rowid = '".(int)$this->rowid."'";

		if ($this->field == "AB_idem_DC1") {
			$sql.= ", `AB_idem_DC1` = '".$this->db->escape($this->AB_idem_DC1)."'";
		}
		if ($this->field == "A") {
			$sql.= ", `A` = '".$this->db->escape($this->A)."'";
		}
		if ($this->field == "B") {
			$sql.= ", `B` = '".$this->db->escape($this->B)."'";
		}
		if ($this->field == "C1") {
			$sql.= ", `C1` = '".$this->db->escape($this->C1)."'";
		}
		if ($this->field == "C2") {
			$sql.= ", `C2` = '".$this->db->escape($this->C2)."'";
		}
		if ($this->field == "C2_Date") {
			$sql.= ", `C2_Date` = '".$this->db->escape($this->C2_Date)."'";
		}
		if ($this->field == "C2_idem") {
			$sql.= ", `C2_idem` = '".$this->db->escape($this->C2_idem)."'";
		}
		if ($this->field == "C2_adresse_internet") {
			$sql.= ", `C2_adresse_internet` = '".$this->db->escape($this->C2_adresse_internet)."'";
		}
		if ($this->field == "C2_renseignement_adresse") {
			$sql.= ", `C2_renseignement_adresse` = '".$this->db->escape($this->C2_renseignement_adresse)."'";
		}
		if ($this->field == "D1_liste") {
			$sql.= ", `D1_liste` = '".$this->db->escape($this->D1_liste)."'";
		}
		if ($this->field == "D1_reference") {
			$sql.= ", `D1_reference` = '".$this->db->escape($this->D1_reference)."'";
		}
		if ($this->field == "D1_idem") {
			$sql.= ", `D1_idem` = '".$this->db->escape($this->D1_idem)."'";
		}
		if ($this->field == "D1_adresse_internet") {
			$sql.= ", `D1_adresse_internet` = '".$this->db->escape($this->D1_adresse_internet)."'";
		}
		if ($this->field == "D1_renseignement_adresse") {
			$sql.= ", `D1_renseignement_adresse` = '".$this->db->escape($this->D1_renseignement_adresse)."'";
		}
		if ($this->field == "D2") {
			$sql.= ", `D2` = '".$this->db->escape($this->D2)."'";
		}
		if ($this->field == "E1_registre_pro") {
			$sql.= ", `E1_registre_pro` = '".$this->db->escape($this->E1_registre_pro)."'";
		}
		if ($this->field == "E1_registre_spec") {
			$sql.= ", `E1_registre_spec` = '".$this->db->escape($this->E1_registre_spec)."'";
		}
		if ($this->field == "E3_idem") {
			$sql.= ", `E3_idem` = '".$this->db->escape($this->E3_idem)."'";
		}
		if ($this->field == "E3_adresse_internet") {
			$sql.= ", `E3_adresse_internet` = '".$this->db->escape($this->E3_adresse_internet)."'";
		}
		if ($this->field == "E3_renseignement_adresse") {
			$sql.= ", `E3_renseignement_adresse` = '".$this->db->escape($this->E3_renseignement_adresse)."'";
		}
		if ($this->field == "F_CA3_montant") {
			$sql.= ", `F_CA3_debut` = '".$this->db->escape($this->F_CA3_debut)."'";
			$sql.= ", `F_CA3_fin` = '".$this->db->escape($this->F_CA3_fin)."'";
			$sql.= ", `F_CA3_montant` = '".$this->db->escape($this->F_CA3_montant)."'";
		}
		if ($this->field == "F_CA2_montant") {
			$sql.= ", `F_CA2_debut` = '".$this->db->escape($this->F_CA2_debut)."'";
			$sql.= ", `F_CA2_fin` = '".$this->db->escape($this->F_CA2_fin)."'";
			$sql.= ", `F_CA2_montant` = '".$this->db->escape($this->F_CA2_montant)."'";
		}
		if ($this->field == "F_CA1_montant") {
			$sql.= ", `F_CA1_debut` = '".$this->db->escape($this->F_CA1_debut)."'";
			$sql.= ", `F_CA1_fin` = '".$this->db->escape($this->F_CA1_fin)."'";
			$sql.= ", `F_CA1_montant` = '".$this->db->escape($this->F_CA1_montant)."'";
		}
		if ($this->field == "F_date_creation") {
			$sql.= ", `F_date_creation` = '".$this->db->escape($this->F_date_creation)."'";
		}
		if ($this->field == "F2") {
			$sql.= ", `F2` = '".$this->db->escape($this->F2)."'";
		}
		if ($this->field == "F3") {
			$sql.= ", `F3` = '".$this->db->escape($this->F3)."'";
		}
		if ($this->field == "F4_idem") {
			$sql.= ", `F4_idem` = '".$this->db->escape($this->F4_idem)."'";
		}
		if ($this->field == "F4_adresse_internet") {
			$sql.= ", `F4_adresse_internet` = '".$this->db->escape($this->F4_adresse_internet)."'";
		}
		if ($this->field == "F4_renseignement_adresse") {
			$sql.= ", `F4_renseignement_adresse` = '".$this->db->escape($this->F4_renseignement_adresse)."'";
		}
		if ($this->field == "G1") {
			$sql.= ", `G1` = '".$this->db->escape($this->G1)."'";
		}
		if ($this->field == "G2_idem") {
			$sql.= ", `G2_idem` = '".$this->db->escape($this->G2_idem)."'";
		}
		if ($this->field == "G2_adresse_internet") {
			$sql.= ", `G2_adresse_internet` = '".$this->db->escape($this->G2_adresse_internet)."'";
		}
		if ($this->field == "G2_renseignement_adresse") {
			$sql.= ", `G2_renseignement_adresse` = '".$this->db->escape($this->G2_renseignement_adresse)."'";
		}
		if ($this->field == "H") {
			$sql.= ", `H` = '".$this->db->escape($this->H)."'";
		}
		if ($this->field == "I1") {
			$sql.= ", `I1` = '".$this->db->escape($this->I1)."'";
		}
		if ($this->field == "I2") {
			$sql.= ", `I2` = '".$this->db->escape($this->I2)."'";
		}
		$sql.= " WHERE rowid = '".(int)$this->rowid."'";

		dol_syslog("DC2Line::update sql=".$sql);

		$resql = $this->db->query($sql);

		if ($resql)
		{

			if (! $notrigger)
			{
				include_once(DOL_DOCUMENT_ROOT . "/core/class/interfaces.class.php");
				$interface = new Interfaces($this->db);
				$result = $interface->run_triggers('LINEDC2_UPDATE', $this, $user ,$langs, $conf);
				if ($result < 0) {
					$this->error = $langs->trans('ErrorCallingTrigger');
					$this->db->rollback();
					return -1;
				}
			}

			$this->db->commit();

			return $this;

		}
		else
		{
			$this->error = $this->db->error()." sql=".$sql;
			$this->db->rollback();

			return -2;
		}
	} 
	
}
?>
