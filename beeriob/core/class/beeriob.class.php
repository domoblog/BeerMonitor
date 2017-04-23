<?php

/* This file is part of Jeedom.
 *
 * Jeedom is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Jeedom is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Jeedom. If not, see <http://www.gnu.org/licenses/>.
 */

/* * ***************************Includes********************************* */
require_once dirname(__FILE__) . '/../../../../core/php/core.inc.php';

class beeriob extends eqLogic {
	/*     * *************************Attributs****************************** */

	public static $_widgetPossibility = array('custom' => true);

	/*     * ***********************Methode static*************************** */
	
	public static function tempchange($_option) {
		$beeriob = beeriob::byId($_option['beeriob_id']);
		$temp = $beeriob->getCmd(null, 'temp');
		$temp->event($_option['value']);
		$beeriob->calcstatus();
		
	}
	
	public static function cronDaily() {
		log::add('beeriob','info','Mise à jour des âges');
		$eqLogics = eqLogic::byType('beeriob');
		foreach($eqLogics as $beeriob) {
			$age = $beeriob->getCmd(null, 'age');
			$currentage = $age->execCmd();
			if ($currentage == ''){
				$currentage=0;
			}
			$age->event($currentage+1);
			//$changement = $beeriob->getCmd(null, 'changement');
			//$changement->event(date("d/m/y"));
			$beeriob->calcstatus();
		}
	}

	/*     * *********************Methode d'instance************************* */
	
	public function raz() {
		log::add('beeriob','info','Nouveau fût');
		$age = $this->getCmd(null, 'age');
		$age->event(0);
		$changement = $this->getCmd(null, 'changement');
		$changement->event(date("d/m/y"));
		$this->calcstatus();	
	}
	
	public function calcstatus() {
		//log::add('beeriob','info','Calcul');
		$age = $this->getCmd(null, 'age')->execCmd();
		if ($age == '') {
		}
        $temp = $this->getCmd(null, 'temp')->execCmd();
        $etatfutcmd = $this->getCmd(null, 'etat');
        $etatbeercmd = $this->getCmd(null, 'etatbeer');
        //test etat du fut
        if ($age < 8) {
            $etatfutcmd->event('frais');
        }
        else if ($age >= 9 && $age <= 20) {
            $etatfutcmd->event('bon');
        }
        else if ($age >= 21 && $age <= 30) {
            $etatfutcmd->event('critique');
        } 
        else {
        $etatfutcmd->event('inconnu');
        }
    	// test etat de la biere
        if ($temp < 5) {
            $etatbeercmd->event('fraîche');
        }
        else if ($temp >= 6 && $temp <= 12) {
            $etatbeercmd->event('tiède');
        }
        else if ($temp >= 13 && $temp <= 25) {
            $etatbeercmd->event('chaude');
        } 
        else {
            $etatbeercmd->event('inconnue');
        }

	}

	public function postSave() {
		$changement = $this->getCmd(null, 'changement');
		if (!is_object($changement)) {
			$changement = new beeriobCmd();
			$changement->setName(__('Changement le', __FILE__));
			$changement->setIsVisible(1);
			$changement->setOrder(1);
			$changement->setTemplate('dashboard','line');
			$changement->setTemplate('mobile','line');
		}
		$changement->setEqLogic_id($this->getId());
		$changement->setLogicalId('changement');
		$changement->setType('info');
		$changement->setSubType('string');
		$changement->save();
		if ($changement->execCmd() == ''){
			$changement->event(date("d/m/y"));
		}

		$age = $this->getCmd(null, 'age');
		if (!is_object($age)) {
			$age = new beeriobCmd();
			$age->setName(__('Age du fût', __FILE__));
			$age->setIsVisible(1);
			$age->setOrder(2);
			$age->setTemplate('dashboard','line');
			$age->setTemplate('mobile','line');
		}
		$age->setEqLogic_id($this->getId());
		$age->setLogicalId('age');
		$age->setType('info');
		$age->setSubType('numeric');
		$age->setUnite(' jours');
		$age->save();

		$etat = $this->getCmd(null, 'etat');
		if (!is_object($etat)) {
			$etat = new beeriobCmd();
			$etat->setName(__('Etat du fût', __FILE__));
			$etat->setIsVisible(1);
			$etat->setOrder(3);
			$etat->setTemplate('dashboard','line');
			$etat->setTemplate('mobile','line');
		}
		$etat->setEqLogic_id($this->getId());
		$etat->setLogicalId('etat');
		$etat->setType('info');
		$etat->setSubType('string');
		$etat->save();

		$temp = $this->getCmd(null, 'temp');
		if (!is_object($temp)) {
			$temp = new beeriobCmd();
			$temp->setName(__('T° bière', __FILE__));
			$temp->setIsVisible(1);
			$temp->setOrder(4);
			$temp->setTemplate('dashboard','line');
			$temp->setTemplate('mobile','line');
		}
		$temp->setEqLogic_id($this->getId());
		$temp->setLogicalId('temp');
		$temp->setType('info');
		$temp->setSubType('numeric');
		$temp->setUnite('°C');
		$temp->save();

		$etatbeer = $this->getCmd(null, 'etatbeer');
		if (!is_object($etatbeer)) {
			$etatbeer = new beeriobCmd();
			$etatbeer->setName(__('Etat bière', __FILE__));
			$etatbeer->setIsVisible(1);
			$etatbeer->setOrder(5);
			$etatbeer->setTemplate('dashboard','line');
			$etatbeer->setTemplate('mobile','line');
		}
		$etatbeer->setEqLogic_id($this->getId());
		$etatbeer->setLogicalId('etatbeer');
		$etatbeer->setType('info');
		$etatbeer->setSubType('string');
		$etatbeer->save();

		$raz = $this->getCmd(null, 'refresh');
		if (!is_object($raz)) {
			$raz = new beeriobCmd();
			$raz->setIsVisible(1);
			$raz->setOrder(6);
			$raz->setName(__('Nouveau fût', __FILE__));
		}
		$raz->setLogicalId('refresh');
		$raz->setEqLogic_id($this->getId());
		$raz->setType('action');
		$raz->setSubType('other');
		$raz->save();
		
		if ($this->getIsEnable() == 1) {
			$listener = listener::byClassAndFunction('beeriob', 'tempchange', array('beeriob_id' => intval($this->getId())));
			if (!is_object($listener)) {
				$listener = new listener();
			}
			$listener->setClass('beeriob');
			$listener->setFunction('tempchange');
			$listener->setOption(array('beeriob_id' => intval($this->getId())));
			$listener->emptyEvent();
			$tempcontrol = $this->getConfiguration('temperaturectrl');
			if ($tempcontrol == '') {
				return;
			}
			$cmd = cmd::byId(str_replace('#', '', $tempcontrol));
			if (!is_object($cmd)) {
				throw new Exception(__('Commande inconnue pour la température : ' . $tempcontrol, __FILE__));
			}
			$listener->addEvent($tempcontrol);
			$listener->save();
			$temp->event($cmd->execCmd());
			sleep(0.5);
		} else {
			$listener = listener::byClassAndFunction('beeriob', 'tempchange', array('beeriob_id' => intval($this->getId())));
			if (is_object($listener)) {
				$listener->remove();
			}
		}
		$this->calcstatus();
	}

	/*     * **********************Getteur Setteur*************************** */

}

class beeriobCmd extends cmd {
	/*     * *************************Attributs****************************** */

	public static $_widgetPossibility = array('custom' => true);

	/*     * ***********************Methode static*************************** */
	public function execute($_options =null) {
		if ($this->getType() == '') {
				return '';
			}
			$eqLogic = $this->getEqlogic();
			if ($this->getLogicalId()=='refresh') {
				$eqLogic->raz();
			}
	}

	/*     * *********************Methode d'instance************************* */

	/*     * **********************Getteur Setteur*************************** */
}

?>
