<?php


/**
 * Skeleton subclass for performing query and update operations on the 'edt_calendrier' table.
 *
 * Liste des periodes datees de l'annee courante(pour definir par exemple les trimestres)
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.gepi
 */
class EdtCalendrierPeriodePeer extends BaseEdtCalendrierPeriodePeer {

 	/**
	 * Retrourne la periode actuelle, ou null si aucune periode n'est trouve pour le jours actuel
	 *
	 * @param      mixed $v string, integer (timestamp), or DateTime value.  Empty string will
	 *						be treated as NULL for temporal objects.
	 * @return     EdtCalendrierPeriode la periode actuelle
	 */
	public static function retrieveEdtCalendrierPeriodeActuelle($v = 'now') {
		// we treat '' as NULL for temporal objects because DateTime('') == DateTime('now')
		// -- which is unexpected, to say the least.
		//$dt = new DateTime();
		if ($v === null || $v === '') {
			$dt = null;
		} elseif ($v instanceof DateTime) {
			$dt = $v;
		} else {
			// some string/numeric value passed; we normalize that so that we can
			// validate it.
			try {
				if (is_numeric($v)) { // if it's a unix timestamp
					$dt = new DateTime('@'.$v, new DateTimeZone('UTC'));
					// We have to explicitly specify and then change the time zone because of a
					// DateTime bug: http://bugs.php.net/bug.php?id=43003
					$dt->setTimeZone(new DateTimeZone(date_default_timezone_get()));
				} else {
					$dt = new DateTime($v);
				}
			} catch (Exception $x) {
				throw new PropelException('Error parsing date/time value: ' . var_export($v, true), $x);
			}
		}

		return EdtCalendrierPeriodeQuery::create()->filterByDebutCalendrierTs($dt, Criteria::GREATER_EQUAL)
		    ->filterByFinCalendrierTs($dt, Criteria::LESS_THAN)->findOne();
	}


} // EdtCalendrierPeriodePeer