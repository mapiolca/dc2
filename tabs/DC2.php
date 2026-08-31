<?php
/* Copyright (C) 2018-2025	Pierre Ardoin	<developpeur@lesmetiersdubatiment.fr>
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

/**		 \file       htdocs/delegation/tabs/DC2.php
	*			\ingroup    delegation
	*			\brief      DC2 module tabs view
	*			\brief      Affichage de l'onglet DC2 pour les propositions commerciales
	*/

$res=@include("../../main.inc.php");					// For root directory
if (! $res) $res=@include("../../../main.inc.php");	// For "custom" directory

require_once DOL_DOCUMENT_ROOT . '/core/modules/propale/modules_propale.php';
require_once DOL_DOCUMENT_ROOT . '/core/class/html.formfile.class.php';
require_once DOL_DOCUMENT_ROOT . '/core/class/html.formother.class.php';
require_once DOL_DOCUMENT_ROOT . '/core/lib/propal.lib.php';
require_once DOL_DOCUMENT_ROOT . '/core/lib/functions2.lib.php';
require_once DOL_DOCUMENT_ROOT . '/core/lib/date.lib.php';
if (! empty($conf->projet->enabled)) 
{
	require_once DOL_DOCUMENT_ROOT.'/projet/class/project.class.php';
	require_once DOL_DOCUMENT_ROOT.'/core/class/html.formprojet.class.php';
}

dol_include_once("/dc1/class/dc1.class.php");
dol_include_once("/dc2/class/dc2.class.php");

$langs->load('propal');
$langs->load('companies');
$langs->load('compta');
$langs->load('products');
$langs->load('banks');
$langs->load('main');
$langs->load("dc1@dc1");
$langs->load("dc2@dc2");
$langs->load('projects');


$id = GETPOST('id', 'int');
$lineid = GETPOST('lineid') ? GETPOST('lineid') : 0;
$field = GETPOST('field');

$action = GETPOST('action', 'alpha');
$cancel = GETPOST('cancel') ? true : false;
$error = false;
$message = '';
$formconfirm = null;

$form = new Form($db);
$object = new Propal($db);
$dc1 = new DC1($db);
$dc2 = new DC2($db);

$canAddLines = $user->admin || $user->hasRight('dc2','write');
$canReadLines = $user->admin || $user->hasRight('dc2','read');
$canDeleteLines = $user->admin || $user->hasRight('dc2','delete');

if (!$canReadLines)
{
	accessforbidden();
}

if ($id > 0)
{
	$result = $object->fetch($id);

	if ($result > 0)
	{
		
		$dc1_lines = array();
				
		if ($id > 0)
		{
			$result = $object->fetch($id);

			if ($result > 0)
			{
				$dc1->fetch();
				$dc1_lines = $dc1->lines;
				foreach ($dc1_lines as $dc1_line)
				{
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


		$dc2->fetch();
		
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

if (!$error && !$cancel)
{
	$result = $dc1->call($action, array($user));
	$result = $dc2->call($action, array($user));

	if ($result > 0)
	{
		$message = $dc2->error; //
	}
	else
	{
		$message = $dc2->error;
		$error = true;
	}
}

$head = propal_prepare_head($object);
$current_head = 'dc2';
	
$soc = new Societe($db);
$soc->fetch($object->socid);

// Security check
if (!empty($user->socid)) $socid = $user->socid;
$result = restrictedArea($user, 'propal', $id);

if (! empty($conf->projet->enabled)) 
{
	$project = new Project($db);
	$project->fetch($object->fk_project);
}

$numLines = sizeof($dc2->lines);
			
include '../tpl/dc2.default.tpl.php';

$db->close();
?>
