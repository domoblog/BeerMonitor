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

if (!isConnect('admin')) {
	throw new Exception('401 Unauthorized');
}
$eqLogics = beeriob::byType('beeriob');
?>

<table class="table table-condensed tablesorter" id="table_healthbeer">
	<thead>
		<tr>
			<th>{{Machine}}</th>
			<th>{{ID}}</th>
			<th>{{Age}}</th>
			<th>{{Température}}</th>
			<th>{{Etat bière}}</th>
			<th>{{Etat fût}}</th>
		</tr>
	</thead>
	<tbody>
	 <?php
foreach ($eqLogics as $eqLogic) {
	$temp = $eqLogic->getCmd(null, 'temp')->execCmd();
	$age = $eqLogic->getCmd(null, 'age')->execCmd();
	 if ($age == '') {
        $age = 0;
    }
	$etat = $eqLogic->getCmd(null, 'etat')->execCmd();
	$etatbeer = $eqLogic->getCmd(null, 'etatbeer')->execCmd();
	$labelbeer= 'info';
	$labelfut= 'info';
	switch ($etatbeer) {
		case 'fraîche':
			$labelbeer = 'success';
			break;
		case 'tiède':
			$labelbeer = 'warning';
			break;
		case 'chaude':
			$labelbeer = 'danger';
			break;
	}
	switch ($etat) {
		case 'frais':
			$labelfut = 'success';
			break;
		case 'bon':
			$labelfut = 'warning';
			break;
		case 'critique':
			$labelfut = 'danger';
			break;
	}
	echo '<tr><td><a href="' . $eqLogic->getLinkToConfiguration() . '" style="text-decoration: none;">' . $eqLogic->getHumanName(true) . '</a></td>';
	echo '<td><span class="label label-info" style="font-size : 1em;">' . $eqLogic->getId() . '</span></td>';
	echo '<td><span class="label label-info" style="font-size : 1em;">' . $age . ' jours</span></td>';
	echo '<td><span class="label label-info" style="font-size : 1em;">' . $temp . '°C</span></td>';
	echo '<td><span class="label label-'.$labelbeer.'" style="font-size : 1em;">' . $etatbeer . '</span></td>';
	echo '<td><span class="label label-'.$labelfut.'" style="font-size : 1em;">' . $etat . '</span></td></tr>';
}
?>
	</tbody>
</table>
