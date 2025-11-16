-- ============================================================================
-- Copyright (C) 2019 Pierre Ardoin
--
-- This program is free software; you can redistribute it and/or modify
-- it under the terms of the GNU General Public License as published by
-- the Free Software Foundation; either version 2 of the License, or
-- (at your option) any later version.
--
-- This program is distributed in the hope that it will be useful,
-- but WITHOUT ANY WARRANTY; without even the implied warranty of
-- MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
-- GNU General Public License for more details.
--
-- You should have received a copy of the GNU General Public License
-- along with this program. If not, see <http://www.gnu.org/licenses/>.
--
-- ============================================================================


CREATE TABLE IF NOT EXISTS `llx_DC2`( 
  	`rowid`				int(11) AUTO_INCREMENT,
  	`fk_object` 			int(11)  NOT NULL, 
  	`fk_element` 			VARCHAR(255) NOT NULL,
  	`AB_idem_DC1` 			int(11)  NOT NULL,   
  	`A`		   TEXT  NOT NULL,  
	`B`          TEXT NOT NULL,
	`C1` 			int(11)  NOT NULL,
	`C2` 			int(11)  NOT NULL,
	`C2_Date`          DATE NOT NULL,
	`C2_idem` 			int(11)  NOT NULL,
	`C2_adresse_internet`          VARCHAR(255) NOT NULL,
	`C2_renseignement_adresse`          VARCHAR(255) NOT NULL,
	`D1_liste`          VARCHAR(255) NOT NULL,
	`D1_reference`          VARCHAR(255) NOT NULL,
	`D1_idem` 			int(11)  NOT NULL,
	`D1_adresse_internet`          VARCHAR(255) NOT NULL,
	`D1_renseignement_adresse`          VARCHAR(255) NOT NULL,
	`D2` 			int(11)  NOT NULL,
	`E1_registre_pro`          VARCHAR(255) NOT NULL,
	`E1_registre_spec`          VARCHAR(255) NOT NULL,
	`E3_idem` 			int(11)  NOT NULL,
	`E3_adresse_internet`          VARCHAR(255) NOT NULL,
	`E3_renseignement_adresse`          VARCHAR(255) NOT NULL,
	`F_CA3_debut`          DATE NOT NULL,
	`F_CA3_fin`          DATE NOT NULL,
	`F_CA3_montant`          double(24,8) DEFAULT 0,
	`F_CA2_debut`          DATE NOT NULL,
	`F_CA2_fin`          DATE NOT NULL,
	`F_CA2_montant`          double(24,8) DEFAULT 0,
	`F_CA1_debut`          DATE NOT NULL,
	`F_CA1_fin`          DATE NOT NULL,
	`F_CA1_montant`          double(24,8) DEFAULT 0,
	`F_date_creation`          DATE NOT NULL,
	`F2`          VARCHAR(255) NOT NULL,
	`F3` 			int(11)  NOT NULL,
	`F4_idem` 			int(11)  NOT NULL,
	`F4_adresse_internet`          VARCHAR(255) NOT NULL,
	`F4_renseignement_adresse`          VARCHAR(255) NOT NULL,
	`G1`          VARCHAR(255) NOT NULL,
	`G2_idem` 			int(11)  NOT NULL,
	`G2_adresse_internet`          VARCHAR(255) NOT NULL,
	`G2_renseignement_adresse`          VARCHAR(255) NOT NULL,
	`H`          VARCHAR(255) NOT NULL,
	`I1`          VARCHAR(255) NOT NULL,
	`I2`          VARCHAR(255) NOT NULL,

	PRIMARY KEY (`rowid`) 
)ENGINE=innodb DEFAULT CHARSET=utf8 ;