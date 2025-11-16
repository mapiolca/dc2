<?php

/* Copyright (C) 2019-2025      Pierre Ardoin        <developpeur@lesmetiersdubatiment.fr>
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

/**	    \file       htdocs/dc2/tpl/dc2.default.tpl.php
 *		\ingroup    dc2
 *		\brief      DC2 module default view
 */

llxHeader();

print ($message ? dol_htmloutput_mesg($message, '', ($error ? 'error' : 'ok'), 0) : '');

print dol_get_fiche_head($head, $current_head, $langs->trans('dc2'), -1, 'propal', 0, '', '', 0, '', 1);

$object->fetch_thirdparty();
// Proposal card

    $linkback = '<a href="'.DOL_URL_ROOT.'/comm/propal/list.php?restore_lastsearch_values=1'.(!empty($socid) ? '&socid='.$socid : '').'">'.$langs->trans("BackToList").'</a>';

    $morehtmlref = '<div class="refidno">';
    // Ref customer
    $usercancreate ="";
    $morehtmlref .= $form->editfieldkey("RefCustomer", 'ref_client', $object->ref_client, $object, $usercancreate, 'string', '', 0, 1);
    $morehtmlref .= $form->editfieldval("RefCustomer", 'ref_client', $object->ref_client, $object, $usercancreate, 'string'.(isset($conf->global->THIRDPARTY_REF_INPUT_SIZE) ? ':' . getDolGlobalString('THIRDPARTY_REF_INPUT_SIZE') : ''), '', null, null, '', 1);
    // Thirdparty
    $morehtmlref .= '<br>'.$soc->getNomUrl(1, 'customer');
    if (!getDolGlobalString('MAIN_DISABLE_OTHER_LINK') && $soc->id > 0) {
        $morehtmlref .= ' (<a href="'.DOL_URL_ROOT.'/comm/propal/list.php?socid='.$soc->id.'&search_societe='.urlencode($soc->name).'">'.$langs->trans("OtherProposals").'</a>)';
    }
    // Project
    if (isModEnabled('project')) {
        $langs->load("projects");
        $morehtmlref .= '<br>';
        if ($usercancreate) {
            $morehtmlref .= img_picto($langs->trans("Project"), 'project', 'class="pictofixedwidth"');
            if ($action != 'classify') {
                $morehtmlref .= '<a class="editfielda" href="'.$_SERVER['PHP_SELF'].'?action=classify&token='.newToken().'&id='.$object->id.'">'.img_edit($langs->transnoentitiesnoconv('SetProject')).'</a> ';
            }
            $morehtmlref .= $form->form_project($_SERVER['PHP_SELF'].'?id='.$object->id, $object->socid, (string) $object->fk_project, ($action == 'classify' ? 'projectid' : 'none'), 0, 0, 0, 1, '', 'maxwidth300');
        } else {
            if (!empty($object->fk_project)) {
                $proj = new Project($db);
                $proj->fetch($object->fk_project);
                $morehtmlref .= $proj->getNomUrl(1);
                if ($proj->title) {
                    $morehtmlref .= '<span class="opacitymedium"> - '.dol_escape_htmltag($proj->title).'</span>';
                }
            }
        }
    }
    $morehtmlref .= '</div>';

    dol_banner_tab($object, 'ref', $linkback, 0, 'ref', 'ref', $morehtmlref);
?>
  
<?php print $formconfirm ? $formconfirm : ''; ?>

<table class="border" width="100%">
    <tr>
        <td>
            <div class="info"><?php print $langs->trans('DC2_Consigne_entete'); ?>
                <br>
                <p>
                    <a href="<?php print DOL_URL_ROOT.'../core/modules/propale/doc/DC2_2023/notice-dc2-2023.pdf';?>"><?php print $langs->trans('DC2_Consigne_notice'); ?></a>
                </p>
            </div>
        </td>
    </tr>
</table>

<table id="tablelines" class="noborder" width="100%">
<?php if ($numLines > 0){ ?>
    <tr class="liste_titre nodrag nodrop">
        <td><?php echo $langs->trans('Label'); ?></td>
		<td width="350"><?php echo $langs->trans('Value'); ?></td>
		<td width="">&nbsp;</td>
	</tr>
    <?php 
    for($i = 0; $i < $numLines; $i++){
        $line_dc1 = $dc1->lines[$i];
        $line_dc2 = $dc2->lines[$i]; 

        if ($action == 'editline' && $lineid == $line_dc2->rowid){ ?>

        <form name="dc2" action="<?php echo $_SERVER["PHP_SELF"].'?id='.$object->id; ?>" method="POST">
        <input type="hidden" name="token" value="<?php  echo $_SESSION['newtoken']; ?>" />
        <input type="hidden" name="action" value="updateline" />
        <input type="hidden" name="id" value="<?php echo $object->id; ?>" />
        <input type="hidden" name="lineid" value="<?php echo $line_dc2->rowid; ?>"/>
        <input type="hidden" name="field" value="<?php echo $field ?>"/>

         <?php } ?>

        <tr class="<?php echo ($i%2==0 ? 'impair' : 'pair'); ?>">
            <td> 
                        
                <?php echo $langs->trans('DC2_A'); ?>
                <img src="/theme/md/img/info.png" alt="" title="<div class=&quot;centpercent&quot;><?php echo $langs->trans('DC2_A_Tooltype'); ?></div>" class="paddingright classfortooltip valigntextbottom">
                
            </td>

            <td>
                <?php if ($action == 'editline' && $lineid == $line_dc2->rowid && $field == "id_acheteur"){ ?>
                    <input type="text" size="8" id="id_acheteur" name="id_acheteur" value="<?php echo $line_dc2->A; ?>" />
                <?php }else{ 
                print $object->thirdparty->getNomUrl();
                } ?>
            </td>
            <td></td>
        </tr> 

        <tr class="<?php echo ($i%2==0 ? 'impair' : 'pair'); ?>">
            <td> 
                <?php echo $langs->trans('DC2_B'); ?>
                <img src="/theme/md/img/info.png" alt="" title="<div class=&quot;centpercent&quot;><?php echo $langs->trans('DC2_B_Tooltype'); ?></div>" class="paddingright classfortooltip valigntextbottom">
            </td>

            <td>
                <?php if ($action == 'editline' && $lineid == $line_dc2->rowid && $field == "B" ){ ?>
                    <textarea type="text" size="8" id="B" name="B" ><?php echo $line_dc2->B; ?></textarea>
                <?php } elseif ($line_dc1->objet_consultation == "") {
                   echo "Non Renseigné, remplissez le Formulaire DC1.";
                }else{ 
                    echo $line_dc1->objet_consultation ;
                } ?>
            </td>
            <td></td>
        </tr>

        <tr class="<?php echo ($i%2==0 ? 'impair' : 'pair'); ?>">
            <td> 
                <?php echo "&nbsp;&nbsp;&nbsp;B1 - ".$langs->trans('ref_consultation'); ?>
            </td>

            <td>
                <?php if ($line_dc1->ref_consultation == "") {
                   echo "Non Renseigné, remplissez le Formulaire DC1.";
                }else{ 
                    echo $line_dc1->ref_consultation ; 
                } ?>
            </td>
            <td></td>
        </tr>

        <tr class="<?php echo ($i%2==0 ? 'impair' : 'pair'); ?>">
            <td> 
                <?php echo $langs->trans('DC2_C'); ?>
            </td>
            <td></td>
            <td></td>
        </tr>
        
        <tr class="<?php echo ($i%2==0 ? 'impair' : 'pair'); ?>">
            <td> 
                <?php echo "&nbsp;&nbsp;&nbsp;".$langs->trans('DC2_C1'); ?>
            </td>
            <td></td>
            <td></td>
        </tr>

        <tr class="<?php echo ($i%2==0 ? 'impair' : 'pair'); ?>">
            <td> 
                <?php echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$langs->trans('DC2_C1a'); ?>
                <img src="/theme/md/img/info.png" alt="" title="<div class=&quot;centpercent&quot;><?php echo $langs->trans('DC2_C1a_Tooltype'); ?></div>" class="paddingright classfortooltip valigntextbottom">
            </td>
            <td>
                <?php
                    echo ''.$conf->global->MAIN_INFO_SOCIETE_NOM.'<br>
                    '.$conf->global->MAIN_INFO_SOCIETE_ADDRESS.'<br>
                    '.$conf->global->MAIN_INFO_SOCIETE_ZIP.' '.$conf->global->MAIN_INFO_SOCIETE_TOWN.'...';
                ?>
            </td>
            <td></td>
        </tr>
        <tr class="<?php echo ($i%2==0 ? 'impair' : 'pair'); ?>">
            <td> 
                <?php echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$langs->trans('DC2_C1b'); ?>
                <img src="/theme/md/img/info.png" alt="" title="<div class=&quot;centpercent&quot;><?php echo $langs->trans('DC2_C1b_Tooltype'); ?></div>" class="paddingright classfortooltip valigntextbottom">
            </td>
            <td>
                <?php

                $Forme_Juridique_Societe = getFormeJuridiqueLabel($conf->global->MAIN_INFO_SOCIETE_FORME_JURIDIQUE);

                    echo $Forme_Juridique_Societe;
                ?>
            </td>
            <td></td>
        </tr>

        <tr class="<?php echo ($i%2==0 ? 'impair' : 'pair'); ?>">
            <td>          
                <?php echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$langs->trans('DC2_C1c'); ?>
                <img src="/theme/md/img/info.png" alt="" title="<div class=&quot;centpercent&quot;><?php echo $langs->trans('DC2_C1c_Tooltype'); ?></div>" class="paddingright classfortooltip valigntextbottom">  
            </td>

            <td>
                <?php if ($action == 'editline' && $lineid == $line_dc2->rowid && $field == "C1" ){ ?>
                    
                    <select id="C1" name="C1" value="<?php echo $line->C1; ?>">
                        <option value="1" <?php if ($line_dc2->C1 == 1) { echo "selected" ; } ?>><?php echo $langs->trans('yes'); ?></option> 
                        <option value="2" <?php if ($line_dc2->C1 == 2) { echo "selected" ; } ?>><?php echo $langs->trans('no'); ?></option>
                    </select>

                <?php }else{ 

                if ($line_dc2->C1 == 1) {
                   echo $langs->trans('yes') ; 
                }elseif ($line_dc2->C1 == 2) {
                    echo $langs->trans('no') ; 
                }else{
                    echo $langs->trans('non_renseigne') ; 
                }
                
                } ?>
            </td>

            <?php if ($action == 'editline' && $lineid == $line_dc2->rowid && $field == "C1"){ ?>
                <td align="right">
                    <input type="submit" class="button" name="save" value="<?php echo $langs->trans("Save"); ?>" />&nbsp;
                    <input type="submit" class="button" name="cancel" value="<?php echo $langs->trans("Cancel"); ?>" />
                </td>
            <?php }else{ ?>
                <td align="right">
                    <?php if ($canAddLines) { ?>       
                        <a href="<?php echo $_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=editline&amp;lineid='.$line_dc2->rowid.'&amp;field=C1'; ?>">
                            <?php echo img_edit(); ?>
                        </a>
                    <?php } ?>
                </td>
            <?php } ?>
        </tr>

        <tr class="<?php echo ($i%2==0 ? 'impair' : 'pair'); ?>">
            <td> 
                <?php echo "&nbsp;&nbsp;&nbsp;".$langs->trans('DC2_C2'); ?>
                 <img src="/theme/md/img/info.png" alt="" title="<div class=&quot;centpercent&quot;><?php echo $langs->trans('DC2_C2_Tooltype'); ?></div>" class="paddingright classfortooltip valigntextbottom">  
            </td>
            <td></td>
            <td></td>
        </tr>

        <tr class="<?php echo ($i%2==0 ? 'impair' : 'pair'); ?>">
            <td> 
                <?php echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$langs->trans('DC2_C2_statut'); ?>
                 <img src="/theme/md/img/info.png" alt="" title="<div class=&quot;centpercent&quot;><?php echo $langs->trans('DC2_C2_Tooltype'); ?></div>" class="paddingright classfortooltip valigntextbottom">  
            </td>
           <td>
                <?php if ($action == 'editline' && $lineid == $line_dc2->rowid && $field == "C2" ){ ?>
                    
                    <input type="radio" name="C2" id="1" value="1" <?php if ($line_dc2->C2 == 1) { echo "checked" ; } ?> />
                    <label for="1">
                        <?php echo $langs->trans("DC2_C2_1"); ?>&nbsp;<img src="/theme/md/img/info.png" alt="" title="<div class=&quot;centpercent&quot;><?php echo $langs->trans('DC2_C2_1_Tooltype'); ?></div>" class="paddingright classfortooltip valigntextbottom">  
                    </label>
                    <br />
                    <input type="radio" name="C2" id="2" value="2" <?php if ($line_dc2->C2 == 2) { echo "checked" ; } ?> />
                    <label for="2">
                        <?php echo $langs->trans("DC2_C2_2"); ?>&nbsp;<img src="/theme/md/img/info.png" alt="" title="<div class=&quot;centpercent&quot;><?php echo $langs->trans('DC2_C2_2_Tooltype'); ?></div>" class="paddingright classfortooltip valigntextbottom">
                    </label>
                    <br/>
                    <input type="radio" name="C2" id="3" value="3" <?php if ($line_dc2->C2 == 3) { echo "checked" ; } ?> />
                    <label for="3">
                        <?php echo $langs->trans("DC2_C2_3"); ?>&nbsp;<img src="/theme/md/img/info.png" alt="" title="<div class=&quot;centpercent&quot;><?php echo $langs->trans('DC2_C2_3_Tooltype'); ?></div>" class="paddingright classfortooltip valigntextbottom">  
                    </label>
                    <br/>
                    <input type="radio" name="C2" id="4" value="4" <?php if ($line_dc2->C2 == 4) { echo "checked" ; } ?> />
                    <label for="4">
                        <?php echo $langs->trans("DC2_C2_4"); ?>&nbsp;<img src="/theme/md/img/info.png" alt="" title="<div class=&quot;centpercent&quot;><?php echo $langs->trans('DC2_C2_4_Tooltype'); ?></div>" class="paddingright classfortooltip valigntextbottom">  
                    </label>
                    <br/>
                    <input type="radio" name="C2" id="5" value="5" <?php if ($line_dc2->C2 == 5) { echo "checked" ; } ?> />
                    <label for="5">
                        <?php echo $langs->trans("DC2_C2_5"); ?>&nbsp;<img src="/theme/md/img/info.png" alt="" title="<div class=&quot;centpercent&quot;><?php echo $langs->trans('DC2_C2_5_Tooltype'); ?></div>" class="paddingright classfortooltip valigntextbottom">  
                    </label>

                <?php } else { 

                if ($line_dc2->C2 == 1) {
                   echo $langs->trans('DC2_C2_1') ;
                   echo '&nbsp;<img src="/theme/md/img/info.png" alt="" title="<div class=&quot;centpercent&quot;>'.$langs->trans("DC2_C2_1_Tooltype").'</div>" class="paddingright classfortooltip valigntextbottom">  '; 
                } elseif ($line_dc2->C2 == 2) {
                    echo $langs->trans('DC2_C2_2') ;
                    echo '&nbsp;<img src="/theme/md/img/info.png" alt="" title="<div class=&quot;centpercent&quot;>'.$langs->trans("DC2_C2_2_Tooltype").'</div>" class="paddingright classfortooltip valigntextbottom">  ';
                } elseif ($line_dc2->C2 == 3) {
                    echo $langs->trans('DC2_C2_3') ; 
                    echo '&nbsp;<img src="/theme/md/img/info.png" alt="" title="<div class=&quot;centpercent&quot;>'.$langs->trans("DC2_C2_3_Tooltype").'</div>" class="paddingright classfortooltip valigntextbottom">  ';
                } elseif ($line_dc2->C2 == 4) {
                    echo $langs->trans('DC2_C2_4') ;
                    echo '&nbsp;<img src="/theme/md/img/info.png" alt="" title="<div class=&quot;centpercent&quot;>'.$langs->trans("DC2_C2_4_Tooltype").'</div>" class="paddingright classfortooltip valigntextbottom">  '; 
                } elseif ($line_dc2->C2 == 5) {
                    echo $langs->trans('DC2_C2_5') ;
                    //echo '&nbsp;<img src="/theme/md/img/info.png" alt="" title="<div class=&quot;centpercent&quot;>'.$langs->trans("DC2_C2_5_Tooltype").'</div>" class="paddingright classfortooltip valigntextbottom">  '; 
                } else {
                    echo $langs->trans('non_renseigne') ; 
                }
                
                } ?>
            </td>

            <?php if ($action == 'editline' && $lineid == $line_dc2->rowid && $field == "C2"){ ?>
                <td align="right">
                    <input type="submit" class="button" name="save" value="<?php echo $langs->trans("Save"); ?>" />&nbsp;
                    <input type="submit" class="button" name="cancel" value="<?php echo $langs->trans("Cancel"); ?>" />
                </td>
            <?php } else { ?>
                <td align="right">
                    <?php if ($canAddLines) { ?>       
                        <a href="<?php echo $_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=editline&amp;lineid='.$line_dc2->rowid.'&amp;field=C2'; ?>">
                            <?php echo img_edit(); ?>
                        </a>
                    <?php } ?>
                </td>
            <?php } ?>
        </tr>

        <?php if ($line_dc2->C2 == 1) { ?>

            <tr class="<?php echo ($i%2==0 ? 'impair' : 'pair'); ?>">
                 <td>
                    <?php echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$langs->trans('DC2_C2_1_Consigne'); ?>
                </td>
                <td></td>
                <td></td>
            </tr>

        <?php }elseif ($line_dc2->C2 == 2 ) { ?>
            <tr class="<?php echo ($i%2==0 ? 'impair' : 'pair'); ?>">
                <td> 
                    <?php echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$langs->trans('DC2_C2_2_Consigne'); ?>
                    <img src="/theme/md/img/info.png" alt="" title="<div class=&quot;centpercent&quot;><?php echo $langs->trans('DC2_C2_2_Consigne_Tooltype'); ?></div>" class="paddingright classfortooltip valigntextbottom"> 
                </td>
               <td>
                    <?php if ($action == 'editline' && $lineid == $line_dc2->rowid && $field == "C2_Date" ){ ?>

                        
                        <?php  print $form->select_date($line_dc2->C2_Date,'C2_Date',0,0,0,"dc2"); ?>

                    <?php }elseif ($line_dc2->C2_Date == "1970-01-01") {
                        
                        echo $langs->trans('non_renseigne')." " ;

                        

                    }else{ 

                       echo dol_print_date($line_dc2->C2_Date);
                    
                    } ?>
                </td>


                <?php if ($action == 'editline' && $lineid == $line_dc2->rowid && $field == "C2_Date"){ ?>
                    <td align="right">
                        <input type="submit" class="button" name="save" value="<?php echo $langs->trans("Save"); ?>" />&nbsp;
                        <input type="submit" class="button" name="cancel" value="<?php echo $langs->trans("Cancel"); ?>" />
                    </td>
                <?php }else{ ?>
                    <td align="right">
                        <?php if ($canAddLines) { ?>       
                            <a href="<?php echo $_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=editline&amp;lineid='.$line_dc2->rowid.'&amp;field=C2_Date'; ?>">
                                <?php echo img_edit(); ?>
                            </a>
                        <?php } ?>
                    </td>
                <?php } ?>
            </tr>
        <?php }elseif ($line_dc2->C2 == 3 OR $line_dc2->C2 == 4 ) { ?>
            <tr class="<?php echo ($i%2==0 ? 'impair' : 'pair'); ?>">
                <td>
                    <?php echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$langs->trans('DC2_C2_Consigne'); ?>
                </td>
                <td></td>
                <td></td>
            </tr>
        <?php } ?>

        <tr class="<?php echo ($i%2==0 ? 'impair' : 'pair'); ?>">
            <td>          
                <?php echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$langs->trans('DC2_C2_idem'); ?>
            </td>

            <td>
                <?php if ($action == 'editline' && $lineid == $line_dc2->rowid && $field == "C2_idem" ){ ?>
                    
                    <select id="C2_idem" name="C2_idem" value="<?php echo $line->C2_idem; ?>">
                        <option value="1" <?php if ($line_dc2->C2_idem == 1) { echo "selected" ; } ?>><?php echo $langs->trans('yes'); ?></option> 
                        <option value="2" <?php if ($line_dc2->C2_idem == 2) { echo "selected" ; } ?>><?php echo $langs->trans('no'); ?></option>
                    </select>

                <?php }else{ 

                if ($line_dc2->C2_idem == 1) {
                   echo $langs->trans('yes') ; 
                }elseif ($line_dc2->C2_idem == 2) {
                    echo $langs->trans('no') ; 
                }else{
                    echo $langs->trans('non_renseigne') ; 
                }
                
                } ?>
            </td>

            <?php if ($action == 'editline' && $lineid == $line_dc2->rowid && $field == "C2_idem"){ ?>
                <td align="right">
                    <input type="submit" class="button" name="save" value="<?php echo $langs->trans("Save"); ?>" />&nbsp;
                    <input type="submit" class="button" name="cancel" value="<?php echo $langs->trans("Cancel"); ?>" />
                </td>
            <?php }else{ ?>
                <td align="right">
                    <?php if ($canAddLines) { ?>       
                        <a href="<?php echo $_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=editline&amp;lineid='.$line_dc2->rowid.'&amp;field=C2_idem'; ?>">
                            <?php echo img_edit(); ?>
                        </a>
                    <?php } ?>
                </td>
            <?php } ?>
        </tr>

        <?php if ($line_dc2->C2_idem == 2) { ?>
            <tr class="<?php echo ($i%2==0 ? 'impair' : 'pair'); ?>">
                <td> 
                    <?php echo $langs->trans('DC2_C2_adresse'); ?>  
                </td>

                <td>
                    <?php if ($action == 'editline' && $lineid == $line_dc2->rowid && $field == "C2_adresse_internet"){ ?>
                        <textarea type="text" id="C2_adresse_internet" name="C2_adresse_internet" ><?php echo $line_dc2->C2_adresse_internet; ?></textarea>
                    <?php }else{ 
                        echo $line_dc2->C2_adresse_internet;
                    } ?>
                </td>

                <?php if ($action == 'editline' && $lineid == $line_dc2->rowid && $field == "C2_adresse_internet"){ ?>
                    <td align="right">
                        <input type="submit" class="button" name="save" value="<?php echo $langs->trans("Save"); ?>" />&nbsp;<input type="submit" class="button" name="cancel" value="<?php echo $langs->trans("Cancel"); ?>" />
                    </td>
                <?php }else{ ?>
                    <td align="right">
                        <?php if ($canAddLines) { ?>       
                            <a href="<?php echo $_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=editline&amp;lineid='.$line_dc2->rowid.'&amp;field=C2_adresse_internet'; ?>">
                            <?php echo img_edit(); ?>
                        
                            </a>
                        <?php } ?>
                    </td>
                <?php } ?>
            </tr> 
            <tr class="<?php echo ($i%2==0 ? 'impair' : 'pair'); ?>">
                <td> 
                    <?php echo $langs->trans('DC2_C2_renseignements'); ?>
                </td>

                <td>
                    <?php if ($action == 'editline' && $lineid == $line_dc2->rowid && $field == "C2_renseignement_adresse"){ ?>
                        <textarea type="text" size="8" id="C2_renseignement_adresse" name="C2_renseignement_adresse" ><?php echo $line_dc2->C2_renseignement_adresse; ?></textarea>
                    <?php }else{ 
                        echo $line_dc2->C2_renseignement_adresse;
                    } ?>
                </td>

                <?php if ($action == 'editline' && $lineid == $line_dc2->rowid && $field == "C2_renseignement_adresse"){ ?>
                    <td align="right">
                        <input type="submit" class="button" name="save" value="<?php echo $langs->trans("Save"); ?>" />&nbsp;<input type="submit" class="button" name="cancel" value="<?php echo $langs->trans("Cancel"); ?>" />
                    </td>
                <?php }else{ ?>
                    <td align="right">
                        <?php if ($canAddLines) { ?>       
                            <a href="<?php echo $_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=editline&amp;lineid='.$line_dc2->rowid.'&amp;field=C2_renseignement_adresse'; ?>">
                                <?php echo img_edit(); ?>
                            </a>
                        <?php } ?>
                    </td>
                <?php } ?>
            </tr> 
        <?php } ?>

        <tr class="<?php echo ($i%2==0 ? 'impair' : 'pair'); ?>">
            <td> 
                <?php echo $langs->trans('DC2_C3'); //echo $langs->trans('DC2_D1'); ?>  
            </td>
            <td></td>
            <td></td>
        </tr>
        <tr class="<?php echo ($i%2==0 ? 'impair' : 'pair'); ?>">
            <td> 
                <?php echo "&nbsp;&nbsp;&nbsp;".$langs->trans('DC2_D1_1'); ?>  
            </td>
            <td></td>
            <td></td>
        </tr>

        <tr class="<?php echo ($i%2==0 ? 'impair' : 'pair'); ?>">
            <td> 
                <?php echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-".$langs->trans('DC2_D1_Indication'); ?>  
            </td>

            <td>
                <?php if ($action == 'editline' && $lineid == $line_dc2->rowid && $field == "D1_liste"){ ?>
                    <textarea type="text" id="D1_liste" name="D1_liste" ><?php echo $line_dc2->D1_liste; ?></textarea>
                <?php }else{ 
                    echo $line_dc2->D1_liste;
                } ?>
            </td>

            <?php if ($action == 'editline' && $lineid == $line_dc2->rowid && $field == "D1_liste"){ ?>
                <td align="right">
                    <input type="submit" class="button" name="save" value="<?php echo $langs->trans("Save"); ?>" />&nbsp;<input type="submit" class="button" name="cancel" value="<?php echo $langs->trans("Cancel"); ?>" />
                </td>
            <?php }else{ ?>
                <td align="right">
                    <?php if ($canAddLines) { ?>       
                        <a href="<?php echo $_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=editline&amp;lineid='.$line_dc2->rowid.'&amp;field=D1_liste'; ?>">
                        <?php echo img_edit(); ?>
                    
                        </a>
                    <?php } ?>
                </td>
            <?php } ?>
        </tr>

        <tr class="<?php echo ($i%2==0 ? 'impair' : 'pair'); ?>">
            <td> 
                <?php echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-".$langs->trans('DC2_D1_Reference'); ?>
                <img src="/theme/md/img/info.png" alt="" title="<div class=&quot;centpercent&quot;><?php echo $langs->trans('DC2_D1_Reference_Tooltip'); ?></div>" class="paddingright classfortooltip valigntextbottom"> 
            </td>

            <td>
                <?php if ($action == 'editline' && $lineid == $line_dc2->rowid && $field == "D1_reference"){ ?>
                    <textarea type="text" id="D1_reference" name="D1_reference" ><?php echo $line_dc2->D1_reference; ?></textarea>
                <?php }else{ 
                    echo $line_dc2->D1_reference;
                } ?>
            </td>

            <?php if ($action == 'editline' && $lineid == $line_dc2->rowid && $field == "D1_reference"){ ?>
                <td align="right">
                    <input type="submit" class="button" name="save" value="<?php echo $langs->trans("Save"); ?>" />&nbsp;<input type="submit" class="button" name="cancel" value="<?php echo $langs->trans("Cancel"); ?>" />
                </td>
            <?php }else{ ?>
                <td align="right">
                    <?php if ($canAddLines) { ?>       
                        <a href="<?php echo $_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=editline&amp;lineid='.$line_dc2->rowid.'&amp;field=D1_reference'; ?>">
                        <?php echo img_edit(); ?>
                    
                        </a>
                    <?php } ?>
                </td>
            <?php } ?>
        </tr>

        <tr class="<?php echo ($i%2==0 ? 'impair' : 'pair'); ?>">
            <td>          
                <?php echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$langs->trans('DC2_D1_idem'); ?>
            </td>
            <td>
                <?php if ($action == 'editline' && $lineid == $line_dc2->rowid && $field == "D1_idem" ){ ?>
                    
                    <select id="D1_idem" name="D1_idem" value="<?php echo $line->D1_idem; ?>">
                        <option value="1" <?php if ($line_dc2->D1_idem == 1) { echo "selected" ; } ?>><?php echo $langs->trans('yes'); ?></option> 
                        <option value="2" <?php if ($line_dc2->D1_idem == 2) { echo "selected" ; } ?>><?php echo $langs->trans('no'); ?></option>
                    </select>

                <?php }else{ 

                if ($line_dc2->D1_idem == 1) {
                   echo $langs->trans('yes') ; 
                }elseif ($line_dc2->D1_idem == 2) {
                    echo $langs->trans('no') ; 
                }else{
                    echo $langs->trans('non_renseigne') ; 
                }
                
                } ?>
            </td>

            <?php if ($action == 'editline' && $lineid == $line_dc2->rowid && $field == "D1_idem"){ ?>
                <td align="right">
                    <input type="submit" class="button" name="save" value="<?php echo $langs->trans("Save"); ?>" />&nbsp;
                    <input type="submit" class="button" name="cancel" value="<?php echo $langs->trans("Cancel"); ?>" />
                </td>
            <?php }else{ ?>
                <td align="right">
                    <?php if ($canAddLines) { ?>       
                        <a href="<?php echo $_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=editline&amp;lineid='.$line_dc2->rowid.'&amp;field=D1_idem'; ?>">
                            <?php echo img_edit(); ?>
                        </a>
                    <?php } ?>
                </td>
            <?php } ?>
        </tr>

        <?php if ($line_dc2->D1_idem == 2) { ?>
            <tr class="<?php echo ($i%2==0 ? 'impair' : 'pair'); ?>">
                <td> 
                    <?php echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-".$langs->trans('DC2_D1_adresse'); ?>  
                </td>

                <td>
                    <?php if ($action == 'editline' && $lineid == $line_dc2->rowid && $field == "D1_adresse_internet"){ ?>
                        <textarea type="text" id="D1_adresse_internet" name="D1_adresse_internet" ><?php echo $line_dc2->D1_adresse_internet; ?></textarea>
                    <?php }else{ 
                        echo $line_dc2->D1_adresse_internet;
                    } ?>
                </td>

                <?php if ($action == 'editline' && $lineid == $line_dc2->rowid && $field == "D1_adresse_internet"){ ?>
                    <td align="right">
                        <input type="submit" class="button" name="save" value="<?php echo $langs->trans("Save"); ?>" />&nbsp;<input type="submit" class="button" name="cancel" value="<?php echo $langs->trans("Cancel"); ?>" />
                    </td>
                <?php }else{ ?>
                    <td align="right">
                        <?php if ($canAddLines) { ?>       
                            <a href="<?php echo $_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=editline&amp;lineid='.$line_dc2->rowid.'&amp;field=D1_adresse_internet'; ?>">
                            <?php echo img_edit(); ?>
                        
                            </a>
                        <?php } ?>
                    </td>
                <?php } ?>
            </tr> 
            <tr class="<?php echo ($i%2==0 ? 'impair' : 'pair'); ?>">
                <td> 
                    <?php echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-".$langs->trans('DC2_D1_renseignements'); ?>
                </td>

                <td>
                    <?php if ($action == 'editline' && $lineid == $line_dc2->rowid && $field == "D1_renseignement_adresse"){ ?>
                        <textarea type="text" size="8" id="D1_renseignement_adresse" name="D1_renseignement_adresse" ><?php echo $line_dc2->D1_renseignement_adresse; ?></textarea>
                    <?php }else{ 
                        echo $line_dc2->D1_renseignement_adresse;
                    } ?>
                </td>

                <?php if ($action == 'editline' && $lineid == $line_dc2->rowid && $field == "D1_renseignement_adresse"){ ?>
                    <td align="right">
                        <input type="submit" class="button" name="save" value="<?php echo $langs->trans("Save"); ?>" />&nbsp;<input type="submit" class="button" name="cancel" value="<?php echo $langs->trans("Cancel"); ?>" />
                    </td>
                <?php }else{ ?>
                    <td align="right">
                        <?php if ($canAddLines) { ?>       
                            <a href="<?php echo $_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=editline&amp;lineid='.$line_dc2->rowid.'&amp;field=D1_renseignement_adresse'; ?>">
                                <?php echo img_edit(); ?>
                            </a>
                        <?php } ?>
                    </td>
                <?php } ?>
            </tr> 
        <?php } ?>

        <tr class="<?php echo ($i%2==0 ? 'impair' : 'pair'); ?>">
            <td> 
                <?php echo "&nbsp;&nbsp;&nbsp;".$langs->trans('DC2_D2'); ?>  
            </td>
            <td></td>
            <td></td>
        </tr>
        <tr class="<?php echo ($i%2==0 ? 'impair' : 'pair'); ?>">
            <td>          
                <?php echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$langs->trans('DC2_D2_attestation'); ?>
                <img src="/theme/md/img/info.png" alt="" title="<div class=&quot;centpercent&quot;><?php echo $langs->trans('DC2_D2_attestation_Tooltip'); ?></div>" class="paddingright classfortooltip valigntextbottom">  
            </td>
            <td>
                <?php if ($action == 'editline' && $lineid == $line_dc2->rowid && $field == "D2" ){ ?>
                    
                    <select id="D2" name="D2" value="<?php echo $line->D2; ?>">
                        <option value="1" <?php if ($line_dc2->D2 == 1) { echo "selected" ; } ?>><?php echo $langs->trans('yes'); ?></option> 
                        <option value="2" <?php if ($line_dc2->D2 == 2) { echo "selected" ; } ?>><?php echo $langs->trans('no'); ?></option>
                    </select>

                <?php }else{ 

                if ($line_dc2->D2 == 1) {
                   echo $langs->trans('yes') ; 
                }elseif ($line_dc2->D2 == 2) {
                    echo $langs->trans('no') ; 
                }else{
                    echo $langs->trans('non_renseigne') ; 
                }
                
                } ?>
            </td>

            <?php if ($action == 'editline' && $lineid == $line_dc2->rowid && $field == "D2"){ ?>
                <td align="right">
                    <input type="submit" class="button" name="save" value="<?php echo $langs->trans("Save"); ?>" />&nbsp;
                    <input type="submit" class="button" name="cancel" value="<?php echo $langs->trans("Cancel"); ?>" />
                </td>
            <?php }else{ ?>
                <td align="right">
                    <?php if ($canAddLines) { ?>       
                        <a href="<?php echo $_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=editline&amp;lineid='.$line_dc2->rowid.'&amp;field=D2'; ?>">
                            <?php echo img_edit(); ?>
                        </a>
                    <?php } ?>
                </td>
            <?php } ?>
        </tr>

        <?php if ($line_dc2->D2 == 2) { ?>
            <tr class="<?php echo ($i%2==0 ? 'impair' : 'pair'); ?>">
                <td> 
                    <?php echo $langs->trans('DC2_E'); ?>
                </td>
                <td></td>
                <td></td>
            </tr>
            <tr class="<?php echo ($i%2==0 ? 'impair' : 'pair'); ?>">
                <td> 
                    <?php echo "&nbsp;&nbsp;&nbsp;".$langs->trans('DC2_E_1'); ?>
                    <img src="/theme/md/img/info.png" alt="" title="<div class=&quot;centpercent&quot;><?php echo $langs->trans('DC2_E_1_Tooltip'); ?></div>" class="paddingright classfortooltip valigntextbottom">  
                </td>
                <td></td>
                <td></td>
            </tr>
            <tr class="<?php echo ($i%2==0 ? 'impair' : 'pair'); ?>">
                <td> 
                    <?php echo "&nbsp;&nbsp;&nbsp;".$langs->trans('DC2_E1'); ?>
                </td>

                <td>
                    <?php if ($action == 'editline' && $lineid == $line_dc2->rowid && $field == "E1_registre_pro"){ ?>
                        <textarea type="text" size="8" id="E1_registre_pro" name="E1_registre_pro" ><?php echo $line_dc2->E1_registre_pro; ?></textarea>
                    <?php }else{ 
                        echo $line_dc2->E1_registre_pro;
                    } ?>
                </td>

                <?php if ($action == 'editline' && $lineid == $line_dc2->rowid && $field == "E1_registre_pro"){ ?>
                    <td align="right">
                        <input type="submit" class="button" name="save" value="<?php echo $langs->trans("Save"); ?>" />&nbsp;<input type="submit" class="button" name="cancel" value="<?php echo $langs->trans("Cancel"); ?>" />
                    </td>
                <?php }else{ ?>
                    <td align="right">
                        <?php if ($canAddLines) { ?>       
                            <a href="<?php echo $_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=editline&amp;lineid='.$line_dc2->rowid.'&amp;field=E1_registre_pro'; ?>">
                                <?php echo img_edit(); ?>
                            </a>
                        <?php } ?>
                    </td>
                <?php } ?>
            </tr>
            <tr class="<?php echo ($i%2==0 ? 'impair' : 'pair'); ?>">
                <td> 
                    <?php echo "&nbsp;&nbsp;&nbsp;".$langs->trans('DC2_E2'); ?>
                </td>

                <td>
                    <?php if ($action == 'editline' && $lineid == $line_dc2->rowid && $field == "E1_registre_spec"){ ?>
                        <textarea type="text" size="8" id="E1_registre_spec" name="E1_registre_spec" ><?php echo $line_dc2->E1_registre_spec; ?></textarea>
                    <?php }else{ 
                        echo $line_dc2->E1_registre_spec;
                    } ?>
                </td>

                <?php if ($action == 'editline' && $lineid == $line_dc2->rowid && $field == "E1_registre_spec"){ ?>
                    <td align="right">
                        <input type="submit" class="button" name="save" value="<?php echo $langs->trans("Save"); ?>" />&nbsp;<input type="submit" class="button" name="cancel" value="<?php echo $langs->trans("Cancel"); ?>" />
                    </td>
                <?php }else{ ?>
                    <td align="right">
                        <?php if ($canAddLines) { ?>       
                            <a href="<?php echo $_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=editline&amp;lineid='.$line_dc2->rowid.'&amp;field=E1_registre_spec'; ?>">
                                <?php echo img_edit(); ?>
                            </a>
                        <?php } ?>
                    </td>
                <?php } ?>
            </tr>

            <tr class="<?php echo ($i%2==0 ? 'impair' : 'pair'); ?>">
                <td>          
                    <?php echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$langs->trans('DC2_E3_idem'); ?> 
                </td>
                <td>
                    <?php if ($action == 'editline' && $lineid == $line_dc2->rowid && $field == "E3_idem" ){ ?>
                        
                        <select id="E3_idem" name="E3_idem" value="<?php echo $line->E3_idem; ?>">
                            <option value="1" <?php if ($line_dc2->E3_idem == 1) { echo "selected" ; } ?>><?php echo $langs->trans('yes'); ?></option> 
                            <option value="2" <?php if ($line_dc2->E3_idem == 2) { echo "selected" ; } ?>><?php echo $langs->trans('no'); ?></option>
                        </select>

                    <?php }else{ 

                    if ($line_dc2->E3_idem == 1) {
                       echo $langs->trans('yes') ; 
                    }elseif ($line_dc2->E3_idem == 2) {
                        echo $langs->trans('no') ; 
                    }else{
                        echo $langs->trans('non_renseigne') ; 
                    }
                    
                    } ?>
                </td>

                <?php if ($action == 'editline' && $lineid == $line_dc2->rowid && $field == "E3_idem"){ ?>
                    <td align="right">
                        <input type="submit" class="button" name="save" value="<?php echo $langs->trans("Save"); ?>" />&nbsp;
                        <input type="submit" class="button" name="cancel" value="<?php echo $langs->trans("Cancel"); ?>" />
                    </td>
                <?php }else{ ?>
                    <td align="right">
                        <?php if ($canAddLines) { ?>       
                            <a href="<?php echo $_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=editline&amp;lineid='.$line_dc2->rowid.'&amp;field=E3_idem'; ?>">
                                <?php echo img_edit(); ?>
                            </a>
                        <?php } ?>
                    </td>
                <?php } ?>
            </tr>
            <?php if ($line_dc2->E3_idem == 2) { ?>
                <tr class="<?php echo ($i%2==0 ? 'impair' : 'pair'); ?>">
                    <td> 
                        <?php echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-".$langs->trans('DC2_E3_adresse'); ?>  
                    </td>

                    <td>
                        <?php if ($action == 'editline' && $lineid == $line_dc2->rowid && $field == "E3_adresse_internet"){ ?>
                            <textarea type="text" id="E3_adresse_internet" name="E3_adresse_internet" ><?php echo $line_dc2->E3_adresse_internet; ?></textarea>
                        <?php }else{ 
                            echo $line_dc2->E3_adresse_internet;
                        } ?>
                    </td>

                    <?php if ($action == 'editline' && $lineid == $line_dc2->rowid && $field == "E3_adresse_internet"){ ?>
                        <td align="right">
                            <input type="submit" class="button" name="save" value="<?php echo $langs->trans("Save"); ?>" />&nbsp;<input type="submit" class="button" name="cancel" value="<?php echo $langs->trans("Cancel"); ?>" />
                        </td>
                    <?php }else{ ?>
                        <td align="right">
                            <?php if ($canAddLines) { ?>       
                                <a href="<?php echo $_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=editline&amp;lineid='.$line_dc2->rowid.'&amp;field=E3_adresse_internet'; ?>">
                                <?php echo img_edit(); ?>
                            
                                </a>
                            <?php } ?>
                        </td>
                    <?php } ?>
                </tr> 
                <tr class="<?php echo ($i%2==0 ? 'impair' : 'pair'); ?>">
                    <td> 
                        <?php echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-".$langs->trans('DC2_E3_renseignements'); ?>
                    </td>

                    <td>
                        <?php if ($action == 'editline' && $lineid == $line_dc2->rowid && $field == "E3_renseignement_adresse"){ ?>
                            <textarea type="text" size="8" id="E3_renseignement_adresse" name="E3_renseignement_adresse" ><?php echo $line_dc2->E3_renseignement_adresse; ?></textarea>
                        <?php }else{ 
                            echo $line_dc2->E3_renseignement_adresse;
                        } ?>
                    </td>

                    <?php if ($action == 'editline' && $lineid == $line_dc2->rowid && $field == "E3_renseignement_adresse"){ ?>
                        <td align="right">
                            <input type="submit" class="button" name="save" value="<?php echo $langs->trans("Save"); ?>" />&nbsp;<input type="submit" class="button" name="cancel" value="<?php echo $langs->trans("Cancel"); ?>" />
                        </td>
                    <?php }else{ ?>
                        <td align="right">
                            <?php if ($canAddLines) { ?>       
                                <a href="<?php echo $_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=editline&amp;lineid='.$line_dc2->rowid.'&amp;field=E3_renseignement_adresse'; ?>">
                                    <?php echo img_edit(); ?>
                                </a>
                            <?php } ?>
                        </td>
                    <?php } ?>
                </tr> 
            <?php } ?>
            <tr class="<?php echo ($i%2==0 ? 'impair' : 'pair'); ?>">
                 <td>
                    <?php echo $langs->trans('DC2_F'); ?>
                </td>
                <td></td>
                <td></td>
            </tr>
            <tr class="<?php echo ($i%2==0 ? 'impair' : 'pair'); ?>">
                 <td>
                    <?php echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$langs->trans('DC2_F_Designation'); ?>
                    <img src="/theme/md/img/info.png" alt="" title="<div class=&quot;centpercent&quot;><?php echo $langs->trans('DC2_F_Designation_Tooltip'); ?></div>" class="paddingright classfortooltip valigntextbottom"> 
                </td>
                <td></td>
                <td></td>
            </tr>
            <tr class="<?php echo ($i%2==0 ? 'impair' : 'pair'); ?>">
                 <td>
                    <?php echo "&nbsp;&nbsp;&nbsp;".$langs->trans('DC2_F1'); ?>
                </td>
                <td></td>
                <td></td>
            </tr>
            <tr class="<?php echo ($i%2==0 ? 'impair' : 'pair'); ?>">
                 <td>
                    <?php echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-&nbsp;".$langs->trans('DC2_Exercice_du'); ?>
                    <?php if ($action == 'editline' && $lineid == $line_dc2->rowid && $field == "F_CA3_montant" ){ ?>

                            
                            <?php  print $form->select_date($line_dc2->F_CA3_debut,'F_CA3_debut',0,0,0,"dc2"); ?>

                        <?php }elseif ($line_dc2->F_CA3_debut == "1970-01-01") {
                            
                            echo $langs->trans('non_renseigne')." " ;     

                        }else{ 

                           echo dol_print_date($line_dc2->F_CA3_debut);
                        
                        } ?>
                    <?php echo "&nbsp;".$langs->trans('DC2_Exercice_au'); ?>
                    <?php if ($action == 'editline' && $lineid == $line_dc2->rowid && $field == "F_CA3_montant" ){ ?>

                            
                            <?php  print $form->select_date($line_dc2->F_CA3_fin,'F_CA3_fin',0,0,0,"dc2"); ?>

                        <?php }elseif ($line_dc2->F_CA3_fin == "1970-01-01") {
                            
                            echo $langs->trans('non_renseigne')." " ;  

                        }else{ 

                           echo dol_print_date($line_dc2->F_CA3_fin);
                        
                        } ?>
                </td>
                <td>
                    <?php echo "".$langs->trans('DC2_Chiffre'); ?>
                    <img src="/theme/md/img/info.png" alt="" title="<div class=&quot;centpercent&quot;><?php echo $langs->trans('DC2_Chiffre_Tooltip'); ?></div>" class="paddingright classfortooltip valigntextbottom">
                    <?php if ($action == 'editline' && $lineid == $line_dc2->rowid && $field == "F_CA3_montant"){ ?>
                            <input type="text" size="8" id="F_CA3_montant" name="F_CA3_montant" value="<?php echo $line_dc2->F_CA3_montant; ?>"/>
                        <?php }else{ 
                            echo price($line_dc2->F_CA3_montant,'',$langs,0,-1,-1,$conf->currency);
                        } ?>

                </td>
                <?php if ($action == 'editline' && $lineid == $line_dc2->rowid && $field == "F_CA3_montant"){ ?>
                    <td align="right">
                        <input type="submit" class="button" name="save" value="<?php echo $langs->trans("Save"); ?>" />&nbsp;
                        <input type="submit" class="button" name="cancel" value="<?php echo $langs->trans("Cancel"); ?>" />
                    </td>
                <?php }else{ ?>
                    <td align="right">
                        <?php if ($canAddLines) { ?>       
                            <a href="<?php echo $_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=editline&amp;lineid='.$line_dc2->rowid.'&amp;field=F_CA3_montant'; ?>">
                                <?php echo img_edit(); ?>
                            </a>
                        <?php } ?>
                    </td>
                <?php } ?>
            </tr>

            <tr class="<?php echo ($i%2==0 ? 'impair' : 'pair'); ?>">
                 <td>
                    <?php echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-&nbsp;".$langs->trans('DC2_Exercice_du'); ?>
                    <?php if ($action == 'editline' && $lineid == $line_dc2->rowid && $field == "F_CA2_montant" ){ ?>

                            
                            <?php  print $form->select_date($line_dc2->F_CA2_debut,'F_CA2_debut',0,0,0,"dc2"); ?>

                        <?php }elseif ($line_dc2->F_CA2_debut == "1970-01-01") {
                            
                            echo $langs->trans('non_renseigne')." " ;     

                        }else{ 

                           echo dol_print_date($line_dc2->F_CA2_debut);
                        
                        } ?>
                    <?php echo "&nbsp;".$langs->trans('DC2_Exercice_au'); ?>
                    <?php if ($action == 'editline' && $lineid == $line_dc2->rowid && $field == "F_CA2_montant" ){ ?>

                            
                            <?php  print $form->select_date($line_dc2->F_CA2_fin,'F_CA2_fin',0,0,0,"dc2"); ?>

                        <?php }elseif ($line_dc2->F_CA2_fin == "1970-01-01") {
                            
                            echo $langs->trans('non_renseigne')." " ;  

                        }else{ 

                           echo dol_print_date($line_dc2->F_CA2_fin);
                        
                        } ?>
                </td>
                <td>
                    <?php echo "".$langs->trans('DC2_Chiffre'); ?>
                    <img src="/theme/md/img/info.png" alt="" title="<div class=&quot;centpercent&quot;><?php echo $langs->trans('DC2_Chiffre_Tooltip'); ?></div>" class="paddingright classfortooltip valigntextbottom">
                    <?php if ($action == 'editline' && $lineid == $line_dc2->rowid && $field == "F_CA2_montant"){ ?>
                            <input type="text" size="8" id="F_CA2_montant" name="F_CA2_montant" value="<?php echo $line_dc2->F_CA2_montant; ?>"/>
                        <?php }else{ 
                            echo price($line_dc2->F_CA2_montant,'',$langs,0,-1,-1,$conf->currency);
                        } ?>

                </td>
                <?php if ($action == 'editline' && $lineid == $line_dc2->rowid && $field == "F_CA2_montant"){ ?>
                    <td align="right">
                        <input type="submit" class="button" name="save" value="<?php echo $langs->trans("Save"); ?>" />&nbsp;
                        <input type="submit" class="button" name="cancel" value="<?php echo $langs->trans("Cancel"); ?>" />
                    </td>
                <?php }else{ ?>
                    <td align="right">
                        <?php if ($canAddLines) { ?>       
                            <a href="<?php echo $_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=editline&amp;lineid='.$line_dc2->rowid.'&amp;field=F_CA2_montant'; ?>">
                                <?php echo img_edit(); ?>
                            </a>
                        <?php } ?>
                    </td>
                <?php } ?>
            </tr>

            <tr class="<?php echo ($i%2==0 ? 'impair' : 'pair'); ?>">
                 <td>
                    <?php echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-&nbsp;".$langs->trans('DC2_Exercice_du'); ?>
                    <?php if ($action == 'editline' && $lineid == $line_dc2->rowid && $field == "F_CA1_montant" ){ ?>

                            
                            <?php  print $form->select_date($line_dc2->F_CA1_debut,'F_CA1_debut',0,0,0,"dc2"); ?>

                        <?php }elseif ($line_dc2->F_CA1_debut == "1970-01-01") {
                            
                            echo $langs->trans('non_renseigne')." " ;     

                        }else{ 

                           echo dol_print_date($line_dc2->F_CA1_debut);
                        
                        } ?>
                    <?php echo "&nbsp;".$langs->trans('DC2_Exercice_au'); ?>
                    <?php if ($action == 'editline' && $lineid == $line_dc2->rowid && $field == "F_CA1_montant" ){ ?>

                            
                            <?php  print $form->select_date($line_dc2->F_CA1_fin,'F_CA1_fin',0,0,0,"dc2"); ?>

                        <?php }elseif ($line_dc2->F_CA1_fin == "1970-01-01") {
                            
                            echo $langs->trans('non_renseigne')." " ;  

                        }else{ 

                           echo dol_print_date($line_dc2->F_CA1_fin);
                        
                        } ?>
                </td>
                <td>
                    <?php echo "".$langs->trans('DC2_Chiffre'); ?>
                    <img src="/theme/md/img/info.png" alt="" title="<div class=&quot;centpercent&quot;><?php echo $langs->trans('DC2_Chiffre_Tooltip'); ?></div>" class="paddingright classfortooltip valigntextbottom">
                    <?php if ($action == 'editline' && $lineid == $line_dc2->rowid && $field == "F_CA1_montant"){ ?>
                            <input type="text" size="8" id="F_CA1_montant" name="F_CA1_montant" value="<?php echo $line_dc2->F_CA1_montant; ?>"/>
                        <?php }else{ 
                            echo price($line_dc2->F_CA1_montant,'',$langs,0,-1,-1,$conf->currency);
                        } ?>

                </td>
                <?php if ($action == 'editline' && $lineid == $line_dc2->rowid && $field == "F_CA1_montant"){ ?>
                    <td align="right">
                        <input type="submit" class="button" name="save" value="<?php echo $langs->trans("Save"); ?>" />&nbsp;
                        <input type="submit" class="button" name="cancel" value="<?php echo $langs->trans("Cancel"); ?>" />
                    </td>
                <?php }else{ ?>
                    <td align="right">
                        <?php if ($canAddLines) { ?>       
                            <a href="<?php echo $_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=editline&amp;lineid='.$line_dc2->rowid.'&amp;field=F_CA1_montant'; ?>">
                                <?php echo img_edit(); ?>
                            </a>
                        <?php } ?>
                    </td>
                <?php } ?>
            </tr>


            <tr class="<?php echo ($i%2==0 ? 'impair' : 'pair'); ?>">
                 <td>
                    <?php echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-&nbsp;".$langs->trans('DC2_F_Date_Creation'); ?>
                    
                </td>
                <td>
                    <?php if ($action == 'editline' && $lineid == $line_dc2->rowid && $field == "F_date_creation" ){ ?>

                            
                        <?php  print $form->select_date($line_dc2->F_date_creation,'F_date_creation',0,0,0,"dc2"); ?>

                        <?php }elseif ($line_dc2->F_date_creation == "1970-01-01") {
                            
                            echo $langs->trans('non_renseigne')." " ;  

                        }else{ 

                           echo dol_print_date($line_dc2->F_date_creation);
                        
                        } ?>

                </td>
                <?php if ($action == 'editline' && $lineid == $line_dc2->rowid && $field == "F_date_creation"){ ?>
                    <td align="right">
                        <input type="submit" class="button" name="save" value="<?php echo $langs->trans("Save"); ?>" />&nbsp;
                        <input type="submit" class="button" name="cancel" value="<?php echo $langs->trans("Cancel"); ?>" />
                    </td>
                <?php }else{ ?>
                    <td align="right">
                        <?php if ($canAddLines) { ?>       
                            <a href="<?php echo $_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=editline&amp;lineid='.$line_dc2->rowid.'&amp;field=F_date_creation'; ?>">
                                <?php echo img_edit(); ?>
                            </a>
                        <?php } ?>
                    </td>
                <?php } ?>
            </tr>

            <tr class="<?php echo ($i%2==0 ? 'impair' : 'pair'); ?>">
                 <td>
                    <?php echo "&nbsp;&nbsp;&nbsp;".$langs->trans('DC2_F2'); ?>
                    <img src="/theme/md/img/info.png" alt="" title="<div class=&quot;centpercent&quot;><?php echo $langs->trans('DC2_F2_Tooltip'); ?></div>" class="paddingright classfortooltip valigntextbottom">
                    
                </td>
                <td>
                    <?php if ($action == 'editline' && $lineid == $line_dc2->rowid && $field == "F2" ){ ?>

                            
                        <textarea name="F2" id="F2" ><?php echo $line_dc2->F2 ;?></textarea>

                        <?php }elseif ($line_dc2->F2 == "") {
                            
                            echo $langs->trans('non_renseigne')." " ;  

                        }else{ 

                           echo $line_dc2->F2;
                        
                        } ?>

                </td>
                <?php if ($action == 'editline' && $lineid == $line_dc2->rowid && $field == "F2"){ ?>
                    <td align="right">
                        <input type="submit" class="button" name="save" value="<?php echo $langs->trans("Save"); ?>" />&nbsp;
                        <input type="submit" class="button" name="cancel" value="<?php echo $langs->trans("Cancel"); ?>" />
                    </td>
                <?php }else{ ?>
                    <td align="right">
                        <?php if ($canAddLines) { ?>       
                            <a href="<?php echo $_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=editline&amp;lineid='.$line_dc2->rowid.'&amp;field=F2'; ?>">
                                <?php echo img_edit(); ?>
                            </a>
                        <?php } ?>
                    </td>
                <?php } ?>
            </tr>

            <tr class="<?php echo ($i%2==0 ? 'impair' : 'pair'); ?>">
                 <td>
                    <?php echo "&nbsp;&nbsp;&nbsp;".$langs->trans('DC2_F3'); ?>
                    
                </td>
                <td></td>
                <td></td>
            </tr>

            <tr class="<?php echo ($i%2==0 ? 'impair' : 'pair'); ?>">
                 <td>
                    <?php echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-&nbsp;".$langs->trans('DC2_F3_Texte'); ?>
                    <img src="/theme/md/img/info.png" alt="" title="<div class=&quot;centpercent&quot;><?php echo $langs->trans('DC2_F3_Texte_Tooltip'); ?></div>" class="paddingright classfortooltip valigntextbottom">
                    
                </td>
                <td>
                    <?php if ($action == 'editline' && $lineid == $line_dc2->rowid && $field == "F3" ){ ?>

                            
                        <select id="F3" name="F3" value="<?php echo $line->F3; ?>">
                            <option value="1" <?php if ($line_dc2->F3 == 1) { echo "selected" ; } ?>><?php echo $langs->trans('yes'); ?></option> 
                            <option value="2" <?php if ($line_dc2->F3 == 2) { echo "selected" ; } ?>><?php echo $langs->trans('no'); ?></option>
                        </select>

                    <?php }elseif ($line_dc2->F3 == 1) {
                       echo $langs->trans('yes') ; 
                    }elseif ($line_dc2->F3 == 2) {
                        echo $langs->trans('no') ; 
                    }else{
                        echo $langs->trans('non_renseigne') ; 
                    }?>

                </td>
                <?php if ($action == 'editline' && $lineid == $line_dc2->rowid && $field == "F3"){ ?>
                    <td align="right">
                        <input type="submit" class="button" name="save" value="<?php echo $langs->trans("Save"); ?>" />&nbsp;
                        <input type="submit" class="button" name="cancel" value="<?php echo $langs->trans("Cancel"); ?>" />
                    </td>
                <?php }else{ ?>
                    <td align="right">
                        <?php if ($canAddLines) { ?>       
                            <a href="<?php echo $_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=editline&amp;lineid='.$line_dc2->rowid.'&amp;field=F3'; ?>">
                                <?php echo img_edit(); ?>
                            </a>
                        <?php } ?>
                    </td>
                <?php } ?>
            </tr>

            <tr class="<?php echo ($i%2==0 ? 'impair' : 'pair'); ?>">
                 <td>
                    <?php echo "&nbsp;&nbsp;&nbsp;".$langs->trans('DC2_F4'); ?>
                    <img src="/theme/md/img/info.png" alt="" title="<div class=&quot;centpercent&quot;><?php echo $langs->trans('DC2_F4_Tooltip'); ?></div>" class="paddingright classfortooltip valigntextbottom">
                </td>
                <td></td>
                <td></td>
            </tr>

            <tr class="<?php echo ($i%2==0 ? 'impair' : 'pair'); ?>">
                <td>          
                    <?php echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$langs->trans('DC2_F4_idem'); ?> 
                </td>
                <td>
                    <?php if ($action == 'editline' && $lineid == $line_dc2->rowid && $field == "F4_idem" ){ ?>
                        
                        <select id="F4_idem" name="F4_idem" value="<?php echo $line->F4_idem; ?>">
                            <option value="1" <?php if ($line_dc2->F4_idem == 1) { echo "selected" ; } ?>><?php echo $langs->trans('yes'); ?></option> 
                            <option value="2" <?php if ($line_dc2->F4_idem == 2) { echo "selected" ; } ?>><?php echo $langs->trans('no'); ?></option>
                        </select>

                    <?php }else{ 

                    if ($line_dc2->F4_idem == 1) {
                       echo $langs->trans('yes') ; 
                    }elseif ($line_dc2->F4_idem == 2) {
                        echo $langs->trans('no') ; 
                    }else{
                        echo $langs->trans('non_renseigne') ; 
                    }
                    
                    } ?>
                </td>

                <?php if ($action == 'editline' && $lineid == $line_dc2->rowid && $field == "F4_idem"){ ?>
                    <td align="right">
                        <input type="submit" class="button" name="save" value="<?php echo $langs->trans("Save"); ?>" />&nbsp;
                        <input type="submit" class="button" name="cancel" value="<?php echo $langs->trans("Cancel"); ?>" />
                    </td>
                <?php }else{ ?>
                    <td align="right">
                        <?php if ($canAddLines) { ?>       
                            <a href="<?php echo $_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=editline&amp;lineid='.$line_dc2->rowid.'&amp;field=F4_idem'; ?>">
                                <?php echo img_edit(); ?>
                            </a>
                        <?php } ?>
                    </td>
                <?php } ?>
            </tr>
            <?php if ($line_dc2->F4_idem == 2) { ?>
                <tr class="<?php echo ($i%2==0 ? 'impair' : 'pair'); ?>">
                    <td> 
                        <?php echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-".$langs->trans('DC2_F4_adresse'); ?>  
                    </td>

                    <td>
                        <?php if ($action == 'editline' && $lineid == $line_dc2->rowid && $field == "F4_adresse_internet"){ ?>
                            <textarea type="text" id="F4_adresse_internet" name="F4_adresse_internet" ><?php echo $line_dc2->F4_adresse_internet; ?></textarea>
                        <?php }else{ 
                            echo $line_dc2->F4_adresse_internet;
                        } ?>
                    </td>

                    <?php if ($action == 'editline' && $lineid == $line_dc2->rowid && $field == "F4_adresse_internet"){ ?>
                        <td align="right">
                            <input type="submit" class="button" name="save" value="<?php echo $langs->trans("Save"); ?>" />&nbsp;<input type="submit" class="button" name="cancel" value="<?php echo $langs->trans("Cancel"); ?>" />
                        </td>
                    <?php }else{ ?>
                        <td align="right">
                            <?php if ($canAddLines) { ?>       
                                <a href="<?php echo $_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=editline&amp;lineid='.$line_dc2->rowid.'&amp;field=F4_adresse_internet'; ?>">
                                <?php echo img_edit(); ?>
                            
                                </a>
                            <?php } ?>
                        </td>
                    <?php } ?>
                </tr> 
                <tr class="<?php echo ($i%2==0 ? 'impair' : 'pair'); ?>">
                    <td> 
                        <?php echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-".$langs->trans('DC2_F4_renseignements'); ?>
                    </td>

                    <td>
                        <?php if ($action == 'editline' && $lineid == $line_dc2->rowid && $field == "F4_renseignement_adresse"){ ?>
                            <textarea type="text" size="8" id="F4_renseignement_adresse" name="F4_renseignement_adresse" ><?php echo $line_dc2->F4_renseignement_adresse; ?></textarea>
                        <?php }else{ 
                            echo $line_dc2->F4_renseignement_adresse;
                        } ?>
                    </td>

                    <?php if ($action == 'editline' && $lineid == $line_dc2->rowid && $field == "F4_renseignement_adresse"){ ?>
                        <td align="right">
                            <input type="submit" class="button" name="save" value="<?php echo $langs->trans("Save"); ?>" />&nbsp;<input type="submit" class="button" name="cancel" value="<?php echo $langs->trans("Cancel"); ?>" />
                        </td>
                    <?php }else{ ?>
                        <td align="right">
                            <?php if ($canAddLines) { ?>       
                                <a href="<?php echo $_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=editline&amp;lineid='.$line_dc2->rowid.'&amp;field=F4_renseignement_adresse'; ?>">
                                    <?php echo img_edit(); ?>
                                </a>
                            <?php } ?>
                        </td>
                    <?php } ?>
                </tr>
            <?php } ?>
            <tr class="<?php echo ($i%2==0 ? 'impair' : 'pair'); ?>">
                <td> 
                    <?php echo $langs->trans('DC2_G'); ?>
                </td>
                <td></td>
                <td></td>
            </tr>
            <tr class="<?php echo ($i%2==0 ? 'impair' : 'pair'); ?>">
                <td> 
                    <?php echo "&nbsp;&nbsp;&nbsp;".$langs->trans('DC2_G_Consigne'); ?>
                    <img src="/theme/md/img/info.png" alt="" title="<div class=&quot;centpercent&quot;><?php echo $langs->trans('DC2_G_Tooltip'); ?></div>" class="paddingright classfortooltip valigntextbottom">  
                </td>
                <td></td>
                <td></td>
            </tr>
            <tr class="<?php echo ($i%2==0 ? 'impair' : 'pair'); ?>">
                <td> 
                    <?php echo "&nbsp;&nbsp;&nbsp;".$langs->trans('DC2_G1'); ?>  
                </td>
                <td>
                    <?php if ($action == 'editline' && $lineid == $line_dc2->rowid && $field == "G1"){ ?>
                        <textarea type="text" id="G1" name="G1" ><?php echo $line_dc2->G1; ?></textarea>
                    <?php }else{ 
                        echo $line_dc2->G1;
                    } ?>
                </td>

                <?php if ($action == 'editline' && $lineid == $line_dc2->rowid && $field == "G1"){ ?>
                    <td align="right">
                        <input type="submit" class="button" name="save" value="<?php echo $langs->trans("Save"); ?>" />&nbsp;<input type="submit" class="button" name="cancel" value="<?php echo $langs->trans("Cancel"); ?>" />
                    </td>
                <?php }else{ ?>
                    <td align="right">
                        <?php if ($canAddLines) { ?>       
                            <a href="<?php echo $_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=editline&amp;lineid='.$line_dc2->rowid.'&amp;field=G1'; ?>">
                            <?php echo img_edit(); ?>
                        
                            </a>
                        <?php } ?>
                    </td>
                <?php } ?>
            </tr>

            <tr class="<?php echo ($i%2==0 ? 'impair' : 'pair'); ?>">
                <td> 
                    <?php echo "&nbsp;&nbsp;&nbsp;".$langs->trans('DC2_G2'); ?>
                    <img src="/theme/md/img/info.png" alt="" title="<div class=&quot;centpercent&quot;><?php echo $langs->trans('DC2_G2_Tooltip'); ?></div>" class="paddingright classfortooltip valigntextbottom">  
                </td>
                <td></td>
                <td></td>
            </tr>
            <tr class="<?php echo ($i%2==0 ? 'impair' : 'pair'); ?>">
                <td>          
                    <?php echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$langs->trans('DC2_G2_idem'); ?> 
                </td>
                <td>
                    <?php if ($action == 'editline' && $lineid == $line_dc2->rowid && $field == "G2_idem" ){ ?>
                        
                        <select id="G2_idem" name="G2_idem" value="<?php echo $line->G2_idem; ?>">
                            <option value="1" <?php if ($line_dc2->G2_idem == 1) { echo "selected" ; } ?>><?php echo $langs->trans('yes'); ?></option> 
                            <option value="2" <?php if ($line_dc2->G2_idem == 2) { echo "selected" ; } ?>><?php echo $langs->trans('no'); ?></option>
                        </select>

                    <?php }else{ 

                    if ($line_dc2->G2_idem == 1) {
                       echo $langs->trans('yes') ; 
                    }elseif ($line_dc2->G2_idem == 2) {
                        echo $langs->trans('no') ; 
                    }else{
                        echo $langs->trans('non_renseigne') ; 
                    }
                    
                    } ?>
                </td>

                <?php if ($action == 'editline' && $lineid == $line_dc2->rowid && $field == "G2_idem"){ ?>
                    <td align="right">
                        <input type="submit" class="button" name="save" value="<?php echo $langs->trans("Save"); ?>" />&nbsp;
                        <input type="submit" class="button" name="cancel" value="<?php echo $langs->trans("Cancel"); ?>" />
                    </td>
                <?php }else{ ?>
                    <td align="right">
                        <?php if ($canAddLines) { ?>       
                            <a href="<?php echo $_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=editline&amp;lineid='.$line_dc2->rowid.'&amp;field=G2_idem'; ?>">
                                <?php echo img_edit(); ?>
                            </a>
                        <?php } ?>
                    </td>
                <?php } ?>
            </tr>
            <?php if ($line_dc2->G2_idem == 2) { ?>
                <tr class="<?php echo ($i%2==0 ? 'impair' : 'pair'); ?>">
                    <td> 
                        <?php echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-".$langs->trans('DC2_G2_adresse'); ?>  
                    </td>

                    <td>
                        <?php if ($action == 'editline' && $lineid == $line_dc2->rowid && $field == "G2_adresse_internet"){ ?>
                            <textarea type="text" id="G2_adresse_internet" name="G2_adresse_internet" ><?php echo $line_dc2->G2_adresse_internet; ?></textarea>
                        <?php }else{ 
                            echo $line_dc2->G2_adresse_internet;
                        } ?>
                    </td>

                    <?php if ($action == 'editline' && $lineid == $line_dc2->rowid && $field == "G2_adresse_internet"){ ?>
                        <td align="right">
                            <input type="submit" class="button" name="save" value="<?php echo $langs->trans("Save"); ?>" />&nbsp;<input type="submit" class="button" name="cancel" value="<?php echo $langs->trans("Cancel"); ?>" />
                        </td>
                    <?php }else{ ?>
                        <td align="right">
                            <?php if ($canAddLines) { ?>       
                                <a href="<?php echo $_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=editline&amp;lineid='.$line_dc2->rowid.'&amp;field=G2_adresse_internet'; ?>">
                                <?php echo img_edit(); ?>
                            
                                </a>
                            <?php } ?>
                        </td>
                    <?php } ?>
                </tr> 
                <tr class="<?php echo ($i%2==0 ? 'impair' : 'pair'); ?>">
                    <td> 
                        <?php echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-".$langs->trans('DC2_G2_renseignements'); ?>
                    </td>

                    <td>
                        <?php if ($action == 'editline' && $lineid == $line_dc2->rowid && $field == "G2_renseignement_adresse"){ ?>
                            <textarea type="text" size="8" id="G2_renseignement_adresse" name="G2_renseignement_adresse" ><?php echo $line_dc2->G2_renseignement_adresse; ?></textarea>
                        <?php }else{ 
                            echo $line_dc2->G2_renseignement_adresse;
                        } ?>
                    </td>

                    <?php if ($action == 'editline' && $lineid == $line_dc2->rowid && $field == "G2_renseignement_adresse"){ ?>
                        <td align="right">
                            <input type="submit" class="button" name="save" value="<?php echo $langs->trans("Save"); ?>" />&nbsp;<input type="submit" class="button" name="cancel" value="<?php echo $langs->trans("Cancel"); ?>" />
                        </td>
                    <?php }else{ ?>
                        <td align="right">
                            <?php if ($canAddLines) { ?>       
                                <a href="<?php echo $_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=editline&amp;lineid='.$line_dc2->rowid.'&amp;field=G2_renseignement_adresse'; ?>">
                                    <?php echo img_edit(); ?>
                                </a>
                            <?php } ?>
                        </td>
                    <?php } ?>
                </tr>
            <?php } ?>
            <tr class="<?php echo ($i%2==0 ? 'impair' : 'pair'); ?>">
                <td> 
                    <?php echo "&nbsp;&nbsp;&nbsp;".$langs->trans('DC2_H'); ?>
                    <img src="/theme/md/img/info.png" alt="" title="<div class=&quot;centpercent&quot;><?php echo $langs->trans('DC2_H_Tooltip'); ?></div>" class="paddingright classfortooltip valigntextbottom">  
                </td>
                <td></td>
                <td></td>
            </tr>
            <tr class="<?php echo ($i%2==0 ? 'impair' : 'pair'); ?>">
                <td> 
                    <?php echo "&nbsp;&nbsp;&nbsp;".$langs->trans('DC2_H1'); ?>
                    <img src="/theme/md/img/info.png" alt="" title="<div class=&quot;centpercent&quot;><?php echo $langs->trans('DC2_H_designation_Tooltip'); ?></div>" class="paddingright classfortooltip valigntextbottom">
                </td>

                <td>
                    <?php if ($action == 'editline' && $lineid == $line_dc2->rowid && $field == "H"){ ?>
                        <textarea type="text" size="8" id="H" name="H" ><?php echo $line_dc2->H; ?></textarea>
                    <?php }else{ 
                        //echo $line_dc2->H;
                        print "<div class='warning'>Non pris en charge pour le moment...</div>";
                    } ?>
                </td>

                <?php if ($action == 'editline' && $lineid == $line_dc2->rowid && $field == "H"){ ?>
                    <td align="right">
                        <input type="submit" class="button" name="save" value="<?php echo $langs->trans("Save"); ?>" />&nbsp;<input type="submit" class="button" name="cancel" value="<?php echo $langs->trans("Cancel"); ?>" />
                    </td>
                <?php }else{ ?>
                    <td align="right">
                        <?php /* if ($canAddLines) { ?>       
                            <a href="<?php echo $_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=editline&amp;lineid='.$line_dc2->rowid.'&amp;field=H'; ?>">
                                <?php echo img_edit(); ?>
                            </a>
                        <?php } */?>
                    </td>
                <?php } ?>
            </tr>

            <tr class="<?php echo ($i%2==0 ? 'impair' : 'pair'); ?>">
                <td> 
                    <?php echo "".$langs->trans('DC2_I'); ?>
                </td>
                <td></td>
                <td></td>
            </tr>

            <tr class="<?php echo ($i%2==0 ? 'impair' : 'pair'); ?>">
                <td> 
                    <?php echo "&nbsp;&nbsp;&nbsp;".$langs->trans('DC2_I1'); ?>
                </td>

                <td>
                    <?php if ($action == 'editline' && $lineid == $line_dc2->rowid && $field == "I1"){ ?>
                        <textarea type="text" size="8" id="I1" name="I1" ><?php echo $line_dc2->I1; ?></textarea>
                    <?php }else{ 
                        echo $line_dc2->I1;
                    } ?>
                </td>

                <?php if ($action == 'editline' && $lineid == $line_dc2->rowid && $field == "I1"){ ?>
                    <td align="right">
                        <input type="submit" class="button" name="save" value="<?php echo $langs->trans("Save"); ?>" />&nbsp;<input type="submit" class="button" name="cancel" value="<?php echo $langs->trans("Cancel"); ?>" />
                    </td>
                <?php }else{ ?>
                    <td align="right">
                        <?php  if ($canAddLines) { ?>       
                            <a href="<?php echo $_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=editline&amp;lineid='.$line_dc2->rowid.'&amp;field=I1'; ?>">
                                <?php echo img_edit(); ?>
                            </a>
                        <?php } ?>
                    </td>
                <?php } ?>
            </tr>

            <tr class="<?php echo ($i%2==0 ? 'impair' : 'pair'); ?>">
                <td> 
                    <?php echo "&nbsp;&nbsp;&nbsp;".$langs->trans('DC2_I2'); ?>
                </td>

                <td>
                    <?php if ($action == 'editline' && $lineid == $line_dc2->rowid && $field == "I2"){ ?>
                        <textarea type="text" size="8" id="I2" name="I2" ><?php echo $line_dc2->I2; ?></textarea>
                    <?php }else{ 
                        echo $line_dc2->I2;
                    } ?>
                </td>

                <?php if ($action == 'editline' && $lineid == $line_dc2->rowid && $field == "I2"){ ?>
                    <td align="right">
                        <input type="submit" class="button" name="save" value="<?php echo $langs->trans("Save"); ?>" />&nbsp;<input type="submit" class="button" name="cancel" value="<?php echo $langs->trans("Cancel"); ?>" />
                    </td>
                <?php }else{ ?>
                    <td align="right">
                        <?php  if ($canAddLines) { ?>       
                            <a href="<?php echo $_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=editline&amp;lineid='.$line_dc2->rowid.'&amp;field=I2'; ?>">
                                <?php echo img_edit(); ?>
                            </a>
                        <?php } ?>
                    </td>
                <?php } ?>
            </tr>



        <?php } ?>
    <?php } ?>
	</form>
<?php } ?>

</table>
<table class="border" width="100%">
    <tr>
        <td>
            <div class="info"><?php print $langs->trans('DC2_generate'); ?></div>
        </td>
    </tr>
</table>

</div>

<br />

<?php dol_fiche_end(); ?>

<?php llxFooter(''); ?>

