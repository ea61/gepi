<?php


/**
 * Base class that represents a query for the 'edt_cours' table.
 *
 * Liste de tous les creneaux de tous les emplois du temps
 *
 * @method     EdtEmplacementCoursQuery orderByIdCours($order = Criteria::ASC) Order by the id_cours column
 * @method     EdtEmplacementCoursQuery orderByIdGroupe($order = Criteria::ASC) Order by the id_groupe column
 * @method     EdtEmplacementCoursQuery orderByIdAid($order = Criteria::ASC) Order by the id_aid column
 * @method     EdtEmplacementCoursQuery orderByIdSalle($order = Criteria::ASC) Order by the id_salle column
 * @method     EdtEmplacementCoursQuery orderByJourSemaine($order = Criteria::ASC) Order by the jour_semaine column
 * @method     EdtEmplacementCoursQuery orderByIdDefiniePeriode($order = Criteria::ASC) Order by the id_definie_periode column
 * @method     EdtEmplacementCoursQuery orderByDuree($order = Criteria::ASC) Order by the duree column
 * @method     EdtEmplacementCoursQuery orderByHeuredebDec($order = Criteria::ASC) Order by the heuredeb_dec column
 * @method     EdtEmplacementCoursQuery orderByTypeSemaine($order = Criteria::ASC) Order by the id_semaine column
 * @method     EdtEmplacementCoursQuery orderByIdCalendrier($order = Criteria::ASC) Order by the id_calendrier column
 * @method     EdtEmplacementCoursQuery orderByModifEdt($order = Criteria::ASC) Order by the modif_edt column
 * @method     EdtEmplacementCoursQuery orderByLoginProf($order = Criteria::ASC) Order by the login_prof column
 *
 * @method     EdtEmplacementCoursQuery groupByIdCours() Group by the id_cours column
 * @method     EdtEmplacementCoursQuery groupByIdGroupe() Group by the id_groupe column
 * @method     EdtEmplacementCoursQuery groupByIdAid() Group by the id_aid column
 * @method     EdtEmplacementCoursQuery groupByIdSalle() Group by the id_salle column
 * @method     EdtEmplacementCoursQuery groupByJourSemaine() Group by the jour_semaine column
 * @method     EdtEmplacementCoursQuery groupByIdDefiniePeriode() Group by the id_definie_periode column
 * @method     EdtEmplacementCoursQuery groupByDuree() Group by the duree column
 * @method     EdtEmplacementCoursQuery groupByHeuredebDec() Group by the heuredeb_dec column
 * @method     EdtEmplacementCoursQuery groupByTypeSemaine() Group by the id_semaine column
 * @method     EdtEmplacementCoursQuery groupByIdCalendrier() Group by the id_calendrier column
 * @method     EdtEmplacementCoursQuery groupByModifEdt() Group by the modif_edt column
 * @method     EdtEmplacementCoursQuery groupByLoginProf() Group by the login_prof column
 *
 * @method     EdtEmplacementCoursQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     EdtEmplacementCoursQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     EdtEmplacementCoursQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     EdtEmplacementCoursQuery leftJoinGroupe($relationAlias = '') Adds a LEFT JOIN clause to the query using the Groupe relation
 * @method     EdtEmplacementCoursQuery rightJoinGroupe($relationAlias = '') Adds a RIGHT JOIN clause to the query using the Groupe relation
 * @method     EdtEmplacementCoursQuery innerJoinGroupe($relationAlias = '') Adds a INNER JOIN clause to the query using the Groupe relation
 *
 * @method     EdtEmplacementCoursQuery leftJoinAidDetails($relationAlias = '') Adds a LEFT JOIN clause to the query using the AidDetails relation
 * @method     EdtEmplacementCoursQuery rightJoinAidDetails($relationAlias = '') Adds a RIGHT JOIN clause to the query using the AidDetails relation
 * @method     EdtEmplacementCoursQuery innerJoinAidDetails($relationAlias = '') Adds a INNER JOIN clause to the query using the AidDetails relation
 *
 * @method     EdtEmplacementCoursQuery leftJoinEdtSalle($relationAlias = '') Adds a LEFT JOIN clause to the query using the EdtSalle relation
 * @method     EdtEmplacementCoursQuery rightJoinEdtSalle($relationAlias = '') Adds a RIGHT JOIN clause to the query using the EdtSalle relation
 * @method     EdtEmplacementCoursQuery innerJoinEdtSalle($relationAlias = '') Adds a INNER JOIN clause to the query using the EdtSalle relation
 *
 * @method     EdtEmplacementCoursQuery leftJoinEdtCreneau($relationAlias = '') Adds a LEFT JOIN clause to the query using the EdtCreneau relation
 * @method     EdtEmplacementCoursQuery rightJoinEdtCreneau($relationAlias = '') Adds a RIGHT JOIN clause to the query using the EdtCreneau relation
 * @method     EdtEmplacementCoursQuery innerJoinEdtCreneau($relationAlias = '') Adds a INNER JOIN clause to the query using the EdtCreneau relation
 *
 * @method     EdtEmplacementCoursQuery leftJoinEdtCalendrierPeriode($relationAlias = '') Adds a LEFT JOIN clause to the query using the EdtCalendrierPeriode relation
 * @method     EdtEmplacementCoursQuery rightJoinEdtCalendrierPeriode($relationAlias = '') Adds a RIGHT JOIN clause to the query using the EdtCalendrierPeriode relation
 * @method     EdtEmplacementCoursQuery innerJoinEdtCalendrierPeriode($relationAlias = '') Adds a INNER JOIN clause to the query using the EdtCalendrierPeriode relation
 *
 * @method     EdtEmplacementCoursQuery leftJoinUtilisateurProfessionnel($relationAlias = '') Adds a LEFT JOIN clause to the query using the UtilisateurProfessionnel relation
 * @method     EdtEmplacementCoursQuery rightJoinUtilisateurProfessionnel($relationAlias = '') Adds a RIGHT JOIN clause to the query using the UtilisateurProfessionnel relation
 * @method     EdtEmplacementCoursQuery innerJoinUtilisateurProfessionnel($relationAlias = '') Adds a INNER JOIN clause to the query using the UtilisateurProfessionnel relation
 *
 * @method     EdtEmplacementCoursQuery leftJoinAbsenceEleveSaisie($relationAlias = '') Adds a LEFT JOIN clause to the query using the AbsenceEleveSaisie relation
 * @method     EdtEmplacementCoursQuery rightJoinAbsenceEleveSaisie($relationAlias = '') Adds a RIGHT JOIN clause to the query using the AbsenceEleveSaisie relation
 * @method     EdtEmplacementCoursQuery innerJoinAbsenceEleveSaisie($relationAlias = '') Adds a INNER JOIN clause to the query using the AbsenceEleveSaisie relation
 *
 * @method     EdtEmplacementCours findOne(PropelPDO $con = null) Return the first EdtEmplacementCours matching the query
 * @method     EdtEmplacementCours findOneByIdCours(int $id_cours) Return the first EdtEmplacementCours filtered by the id_cours column
 * @method     EdtEmplacementCours findOneByIdGroupe(string $id_groupe) Return the first EdtEmplacementCours filtered by the id_groupe column
 * @method     EdtEmplacementCours findOneByIdAid(string $id_aid) Return the first EdtEmplacementCours filtered by the id_aid column
 * @method     EdtEmplacementCours findOneByIdSalle(string $id_salle) Return the first EdtEmplacementCours filtered by the id_salle column
 * @method     EdtEmplacementCours findOneByJourSemaine(string $jour_semaine) Return the first EdtEmplacementCours filtered by the jour_semaine column
 * @method     EdtEmplacementCours findOneByIdDefiniePeriode(string $id_definie_periode) Return the first EdtEmplacementCours filtered by the id_definie_periode column
 * @method     EdtEmplacementCours findOneByDuree(string $duree) Return the first EdtEmplacementCours filtered by the duree column
 * @method     EdtEmplacementCours findOneByHeuredebDec(string $heuredeb_dec) Return the first EdtEmplacementCours filtered by the heuredeb_dec column
 * @method     EdtEmplacementCours findOneByTypeSemaine(string $id_semaine) Return the first EdtEmplacementCours filtered by the id_semaine column
 * @method     EdtEmplacementCours findOneByIdCalendrier(string $id_calendrier) Return the first EdtEmplacementCours filtered by the id_calendrier column
 * @method     EdtEmplacementCours findOneByModifEdt(string $modif_edt) Return the first EdtEmplacementCours filtered by the modif_edt column
 * @method     EdtEmplacementCours findOneByLoginProf(string $login_prof) Return the first EdtEmplacementCours filtered by the login_prof column
 *
 * @method     array findByIdCours(int $id_cours) Return EdtEmplacementCours objects filtered by the id_cours column
 * @method     array findByIdGroupe(string $id_groupe) Return EdtEmplacementCours objects filtered by the id_groupe column
 * @method     array findByIdAid(string $id_aid) Return EdtEmplacementCours objects filtered by the id_aid column
 * @method     array findByIdSalle(string $id_salle) Return EdtEmplacementCours objects filtered by the id_salle column
 * @method     array findByJourSemaine(string $jour_semaine) Return EdtEmplacementCours objects filtered by the jour_semaine column
 * @method     array findByIdDefiniePeriode(string $id_definie_periode) Return EdtEmplacementCours objects filtered by the id_definie_periode column
 * @method     array findByDuree(string $duree) Return EdtEmplacementCours objects filtered by the duree column
 * @method     array findByHeuredebDec(string $heuredeb_dec) Return EdtEmplacementCours objects filtered by the heuredeb_dec column
 * @method     array findByTypeSemaine(string $id_semaine) Return EdtEmplacementCours objects filtered by the id_semaine column
 * @method     array findByIdCalendrier(string $id_calendrier) Return EdtEmplacementCours objects filtered by the id_calendrier column
 * @method     array findByModifEdt(string $modif_edt) Return EdtEmplacementCours objects filtered by the modif_edt column
 * @method     array findByLoginProf(string $login_prof) Return EdtEmplacementCours objects filtered by the login_prof column
 *
 * @package    propel.generator.gepi.om
 */
abstract class BaseEdtEmplacementCoursQuery extends ModelCriteria
{

	/**
	 * Initializes internal state of BaseEdtEmplacementCoursQuery object.
	 *
	 * @param     string $dbName The dabase name
	 * @param     string $modelName The phpName of a model, e.g. 'Book'
	 * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
	 */
	public function __construct($dbName = 'gepi', $modelName = 'EdtEmplacementCours', $modelAlias = null)
	{
		parent::__construct($dbName, $modelName, $modelAlias);
	}

	/**
	 * Returns a new EdtEmplacementCoursQuery object.
	 *
	 * @param     string $modelAlias The alias of a model in the query
	 * @param     Criteria $criteria Optional Criteria to build the query from
	 *
	 * @return    EdtEmplacementCoursQuery
	 */
	public static function create($modelAlias = null, $criteria = null)
	{
		if ($criteria instanceof EdtEmplacementCoursQuery) {
			return $criteria;
		}
		$query = new EdtEmplacementCoursQuery();
		if (null !== $modelAlias) {
			$query->setModelAlias($modelAlias);
		}
		if ($criteria instanceof Criteria) {
			$query->mergeWith($criteria);
		}
		return $query;
	}

	/**
	 * Find object by primary key
	 * Use instance pooling to avoid a database query if the object exists
	 * <code>
	 * $obj  = $c->findPk(12, $con);
	 * </code>
	 * @param     mixed $key Primary key to use for the query
	 * @param     PropelPDO $con an optional connection object
	 *
	 * @return    EdtEmplacementCours|array|mixed the result, formatted by the current formatter
	 */
	public function findPk($key, $con = null)
	{
		if ((null !== ($obj = EdtEmplacementCoursPeer::getInstanceFromPool((string) $key))) && $this->getFormatter()->isObjectFormatter()) {
			// the object is alredy in the instance pool
			return $obj;
		} else {
			// the object has not been requested yet, or the formatter is not an object formatter
			$stmt = $this
				->filterByPrimaryKey($key)
				->getSelectStatement($con);
			return $this->getFormatter()->formatOne($stmt);
		}
	}

	/**
	 * Find objects by primary key
	 * <code>
	 * $objs = $c->findPks(array(12, 56, 832), $con);
	 * </code>
	 * @param     array $keys Primary keys to use for the query
	 * @param     PropelPDO $con an optional connection object
	 *
	 * @return    PropelObjectCollection|array|mixed the list of results, formatted by the current formatter
	 */
	public function findPks($keys, $con = null)
	{	
		return $this
			->filterByPrimaryKeys($keys)
			->find($con);
	}

	/**
	 * Filter the query by primary key
	 *
	 * @param     mixed $key Primary key to use for the query
	 *
	 * @return    EdtEmplacementCoursQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKey($key)
	{
		return $this->addUsingAlias(EdtEmplacementCoursPeer::ID_COURS, $key, Criteria::EQUAL);
	}

	/**
	 * Filter the query by a list of primary keys
	 *
	 * @param     array $keys The list of primary key to use for the query
	 *
	 * @return    EdtEmplacementCoursQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKeys($keys)
	{
		return $this->addUsingAlias(EdtEmplacementCoursPeer::ID_COURS, $keys, Criteria::IN);
	}

	/**
	 * Filter the query on the id_cours column
	 * 
	 * @param     int|array $idCours The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    EdtEmplacementCoursQuery The current query, for fluid interface
	 */
	public function filterByIdCours($idCours = null, $comparison = Criteria::EQUAL)
	{
		if (is_array($idCours)) {
			return $this->addUsingAlias(EdtEmplacementCoursPeer::ID_COURS, $idCours, Criteria::IN);
		} else {
			return $this->addUsingAlias(EdtEmplacementCoursPeer::ID_COURS, $idCours, $comparison);
		}
	}

	/**
	 * Filter the query on the id_groupe column
	 * 
	 * @param     string $idGroupe The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    EdtEmplacementCoursQuery The current query, for fluid interface
	 */
	public function filterByIdGroupe($idGroupe = null, $comparison = Criteria::EQUAL)
	{
		if (is_array($idGroupe)) {
			return $this->addUsingAlias(EdtEmplacementCoursPeer::ID_GROUPE, $idGroupe, Criteria::IN);
		} elseif(preg_match('/[\%\*]/', $idGroupe)) {
			return $this->addUsingAlias(EdtEmplacementCoursPeer::ID_GROUPE, str_replace('*', '%', $idGroupe), Criteria::LIKE);
		} else {
			return $this->addUsingAlias(EdtEmplacementCoursPeer::ID_GROUPE, $idGroupe, $comparison);
		}
	}

	/**
	 * Filter the query on the id_aid column
	 * 
	 * @param     string $idAid The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    EdtEmplacementCoursQuery The current query, for fluid interface
	 */
	public function filterByIdAid($idAid = null, $comparison = Criteria::EQUAL)
	{
		if (is_array($idAid)) {
			return $this->addUsingAlias(EdtEmplacementCoursPeer::ID_AID, $idAid, Criteria::IN);
		} elseif(preg_match('/[\%\*]/', $idAid)) {
			return $this->addUsingAlias(EdtEmplacementCoursPeer::ID_AID, str_replace('*', '%', $idAid), Criteria::LIKE);
		} else {
			return $this->addUsingAlias(EdtEmplacementCoursPeer::ID_AID, $idAid, $comparison);
		}
	}

	/**
	 * Filter the query on the id_salle column
	 * 
	 * @param     string $idSalle The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    EdtEmplacementCoursQuery The current query, for fluid interface
	 */
	public function filterByIdSalle($idSalle = null, $comparison = Criteria::EQUAL)
	{
		if (is_array($idSalle)) {
			return $this->addUsingAlias(EdtEmplacementCoursPeer::ID_SALLE, $idSalle, Criteria::IN);
		} elseif(preg_match('/[\%\*]/', $idSalle)) {
			return $this->addUsingAlias(EdtEmplacementCoursPeer::ID_SALLE, str_replace('*', '%', $idSalle), Criteria::LIKE);
		} else {
			return $this->addUsingAlias(EdtEmplacementCoursPeer::ID_SALLE, $idSalle, $comparison);
		}
	}

	/**
	 * Filter the query on the jour_semaine column
	 * 
	 * @param     string $jourSemaine The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    EdtEmplacementCoursQuery The current query, for fluid interface
	 */
	public function filterByJourSemaine($jourSemaine = null, $comparison = Criteria::EQUAL)
	{
		if (is_array($jourSemaine)) {
			return $this->addUsingAlias(EdtEmplacementCoursPeer::JOUR_SEMAINE, $jourSemaine, Criteria::IN);
		} elseif(preg_match('/[\%\*]/', $jourSemaine)) {
			return $this->addUsingAlias(EdtEmplacementCoursPeer::JOUR_SEMAINE, str_replace('*', '%', $jourSemaine), Criteria::LIKE);
		} else {
			return $this->addUsingAlias(EdtEmplacementCoursPeer::JOUR_SEMAINE, $jourSemaine, $comparison);
		}
	}

	/**
	 * Filter the query on the id_definie_periode column
	 * 
	 * @param     string $idDefiniePeriode The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    EdtEmplacementCoursQuery The current query, for fluid interface
	 */
	public function filterByIdDefiniePeriode($idDefiniePeriode = null, $comparison = Criteria::EQUAL)
	{
		if (is_array($idDefiniePeriode)) {
			return $this->addUsingAlias(EdtEmplacementCoursPeer::ID_DEFINIE_PERIODE, $idDefiniePeriode, Criteria::IN);
		} elseif(preg_match('/[\%\*]/', $idDefiniePeriode)) {
			return $this->addUsingAlias(EdtEmplacementCoursPeer::ID_DEFINIE_PERIODE, str_replace('*', '%', $idDefiniePeriode), Criteria::LIKE);
		} else {
			return $this->addUsingAlias(EdtEmplacementCoursPeer::ID_DEFINIE_PERIODE, $idDefiniePeriode, $comparison);
		}
	}

	/**
	 * Filter the query on the duree column
	 * 
	 * @param     string $duree The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    EdtEmplacementCoursQuery The current query, for fluid interface
	 */
	public function filterByDuree($duree = null, $comparison = Criteria::EQUAL)
	{
		if (is_array($duree)) {
			return $this->addUsingAlias(EdtEmplacementCoursPeer::DUREE, $duree, Criteria::IN);
		} elseif(preg_match('/[\%\*]/', $duree)) {
			return $this->addUsingAlias(EdtEmplacementCoursPeer::DUREE, str_replace('*', '%', $duree), Criteria::LIKE);
		} else {
			return $this->addUsingAlias(EdtEmplacementCoursPeer::DUREE, $duree, $comparison);
		}
	}

	/**
	 * Filter the query on the heuredeb_dec column
	 * 
	 * @param     string $heuredebDec The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    EdtEmplacementCoursQuery The current query, for fluid interface
	 */
	public function filterByHeuredebDec($heuredebDec = null, $comparison = Criteria::EQUAL)
	{
		if (is_array($heuredebDec)) {
			return $this->addUsingAlias(EdtEmplacementCoursPeer::HEUREDEB_DEC, $heuredebDec, Criteria::IN);
		} elseif(preg_match('/[\%\*]/', $heuredebDec)) {
			return $this->addUsingAlias(EdtEmplacementCoursPeer::HEUREDEB_DEC, str_replace('*', '%', $heuredebDec), Criteria::LIKE);
		} else {
			return $this->addUsingAlias(EdtEmplacementCoursPeer::HEUREDEB_DEC, $heuredebDec, $comparison);
		}
	}

	/**
	 * Filter the query on the id_semaine column
	 * 
	 * @param     string $typeSemaine The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    EdtEmplacementCoursQuery The current query, for fluid interface
	 */
	public function filterByTypeSemaine($typeSemaine = null, $comparison = Criteria::EQUAL)
	{
		if (is_array($typeSemaine)) {
			return $this->addUsingAlias(EdtEmplacementCoursPeer::ID_SEMAINE, $typeSemaine, Criteria::IN);
		} elseif(preg_match('/[\%\*]/', $typeSemaine)) {
			return $this->addUsingAlias(EdtEmplacementCoursPeer::ID_SEMAINE, str_replace('*', '%', $typeSemaine), Criteria::LIKE);
		} else {
			return $this->addUsingAlias(EdtEmplacementCoursPeer::ID_SEMAINE, $typeSemaine, $comparison);
		}
	}

	/**
	 * Filter the query on the id_calendrier column
	 * 
	 * @param     string $idCalendrier The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    EdtEmplacementCoursQuery The current query, for fluid interface
	 */
	public function filterByIdCalendrier($idCalendrier = null, $comparison = Criteria::EQUAL)
	{
		if (is_array($idCalendrier)) {
			return $this->addUsingAlias(EdtEmplacementCoursPeer::ID_CALENDRIER, $idCalendrier, Criteria::IN);
		} elseif(preg_match('/[\%\*]/', $idCalendrier)) {
			return $this->addUsingAlias(EdtEmplacementCoursPeer::ID_CALENDRIER, str_replace('*', '%', $idCalendrier), Criteria::LIKE);
		} else {
			return $this->addUsingAlias(EdtEmplacementCoursPeer::ID_CALENDRIER, $idCalendrier, $comparison);
		}
	}

	/**
	 * Filter the query on the modif_edt column
	 * 
	 * @param     string $modifEdt The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    EdtEmplacementCoursQuery The current query, for fluid interface
	 */
	public function filterByModifEdt($modifEdt = null, $comparison = Criteria::EQUAL)
	{
		if (is_array($modifEdt)) {
			return $this->addUsingAlias(EdtEmplacementCoursPeer::MODIF_EDT, $modifEdt, Criteria::IN);
		} elseif(preg_match('/[\%\*]/', $modifEdt)) {
			return $this->addUsingAlias(EdtEmplacementCoursPeer::MODIF_EDT, str_replace('*', '%', $modifEdt), Criteria::LIKE);
		} else {
			return $this->addUsingAlias(EdtEmplacementCoursPeer::MODIF_EDT, $modifEdt, $comparison);
		}
	}

	/**
	 * Filter the query on the login_prof column
	 * 
	 * @param     string $loginProf The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    EdtEmplacementCoursQuery The current query, for fluid interface
	 */
	public function filterByLoginProf($loginProf = null, $comparison = Criteria::EQUAL)
	{
		if (is_array($loginProf)) {
			return $this->addUsingAlias(EdtEmplacementCoursPeer::LOGIN_PROF, $loginProf, Criteria::IN);
		} elseif(preg_match('/[\%\*]/', $loginProf)) {
			return $this->addUsingAlias(EdtEmplacementCoursPeer::LOGIN_PROF, str_replace('*', '%', $loginProf), Criteria::LIKE);
		} else {
			return $this->addUsingAlias(EdtEmplacementCoursPeer::LOGIN_PROF, $loginProf, $comparison);
		}
	}

	/**
	 * Filter the query by a related Groupe object
	 *
	 * @param     Groupe $groupe  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    EdtEmplacementCoursQuery The current query, for fluid interface
	 */
	public function filterByGroupe($groupe, $comparison = Criteria::EQUAL)
	{
		return $this
			->addUsingAlias(EdtEmplacementCoursPeer::ID_GROUPE, $groupe->getId(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the Groupe relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    EdtEmplacementCoursQuery The current query, for fluid interface
	 */
	public function joinGroupe($relationAlias = '', $joinType = Criteria::LEFT_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('Groupe');
		
		// create a ModelJoin object for this join
		$join = new ModelJoin();
		$join->setJoinType($joinType);
		$join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
		
		// add the ModelJoin to the current object
		if($relationAlias) {
			$this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
			$this->addJoinObject($join, $relationAlias);
		} else {
			$this->addJoinObject($join, 'Groupe');
		}
		
		return $this;
	}

	/**
	 * Use the Groupe relation Groupe object
	 *
	 * @see       useQuery()
	 * 
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    GroupeQuery A secondary query class using the current class as primary query
	 */
	public function useGroupeQuery($relationAlias = '', $joinType = Criteria::LEFT_JOIN)
	{
		return $this
			->joinGroupe($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'Groupe', 'GroupeQuery');
	}

	/**
	 * Filter the query by a related AidDetails object
	 *
	 * @param     AidDetails $aidDetails  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    EdtEmplacementCoursQuery The current query, for fluid interface
	 */
	public function filterByAidDetails($aidDetails, $comparison = Criteria::EQUAL)
	{
		return $this
			->addUsingAlias(EdtEmplacementCoursPeer::ID_AID, $aidDetails->getId(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the AidDetails relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    EdtEmplacementCoursQuery The current query, for fluid interface
	 */
	public function joinAidDetails($relationAlias = '', $joinType = Criteria::LEFT_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('AidDetails');
		
		// create a ModelJoin object for this join
		$join = new ModelJoin();
		$join->setJoinType($joinType);
		$join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
		
		// add the ModelJoin to the current object
		if($relationAlias) {
			$this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
			$this->addJoinObject($join, $relationAlias);
		} else {
			$this->addJoinObject($join, 'AidDetails');
		}
		
		return $this;
	}

	/**
	 * Use the AidDetails relation AidDetails object
	 *
	 * @see       useQuery()
	 * 
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    AidDetailsQuery A secondary query class using the current class as primary query
	 */
	public function useAidDetailsQuery($relationAlias = '', $joinType = Criteria::LEFT_JOIN)
	{
		return $this
			->joinAidDetails($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'AidDetails', 'AidDetailsQuery');
	}

	/**
	 * Filter the query by a related EdtSalle object
	 *
	 * @param     EdtSalle $edtSalle  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    EdtEmplacementCoursQuery The current query, for fluid interface
	 */
	public function filterByEdtSalle($edtSalle, $comparison = Criteria::EQUAL)
	{
		return $this
			->addUsingAlias(EdtEmplacementCoursPeer::ID_SALLE, $edtSalle->getIdSalle(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the EdtSalle relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    EdtEmplacementCoursQuery The current query, for fluid interface
	 */
	public function joinEdtSalle($relationAlias = '', $joinType = Criteria::LEFT_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('EdtSalle');
		
		// create a ModelJoin object for this join
		$join = new ModelJoin();
		$join->setJoinType($joinType);
		$join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
		
		// add the ModelJoin to the current object
		if($relationAlias) {
			$this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
			$this->addJoinObject($join, $relationAlias);
		} else {
			$this->addJoinObject($join, 'EdtSalle');
		}
		
		return $this;
	}

	/**
	 * Use the EdtSalle relation EdtSalle object
	 *
	 * @see       useQuery()
	 * 
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    EdtSalleQuery A secondary query class using the current class as primary query
	 */
	public function useEdtSalleQuery($relationAlias = '', $joinType = Criteria::LEFT_JOIN)
	{
		return $this
			->joinEdtSalle($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'EdtSalle', 'EdtSalleQuery');
	}

	/**
	 * Filter the query by a related EdtCreneau object
	 *
	 * @param     EdtCreneau $edtCreneau  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    EdtEmplacementCoursQuery The current query, for fluid interface
	 */
	public function filterByEdtCreneau($edtCreneau, $comparison = Criteria::EQUAL)
	{
		return $this
			->addUsingAlias(EdtEmplacementCoursPeer::ID_DEFINIE_PERIODE, $edtCreneau->getIdDefiniePeriode(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the EdtCreneau relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    EdtEmplacementCoursQuery The current query, for fluid interface
	 */
	public function joinEdtCreneau($relationAlias = '', $joinType = Criteria::INNER_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('EdtCreneau');
		
		// create a ModelJoin object for this join
		$join = new ModelJoin();
		$join->setJoinType($joinType);
		$join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
		
		// add the ModelJoin to the current object
		if($relationAlias) {
			$this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
			$this->addJoinObject($join, $relationAlias);
		} else {
			$this->addJoinObject($join, 'EdtCreneau');
		}
		
		return $this;
	}

	/**
	 * Use the EdtCreneau relation EdtCreneau object
	 *
	 * @see       useQuery()
	 * 
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    EdtCreneauQuery A secondary query class using the current class as primary query
	 */
	public function useEdtCreneauQuery($relationAlias = '', $joinType = Criteria::INNER_JOIN)
	{
		return $this
			->joinEdtCreneau($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'EdtCreneau', 'EdtCreneauQuery');
	}

	/**
	 * Filter the query by a related EdtCalendrierPeriode object
	 *
	 * @param     EdtCalendrierPeriode $edtCalendrierPeriode  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    EdtEmplacementCoursQuery The current query, for fluid interface
	 */
	public function filterByEdtCalendrierPeriode($edtCalendrierPeriode, $comparison = Criteria::EQUAL)
	{
		return $this
			->addUsingAlias(EdtEmplacementCoursPeer::ID_CALENDRIER, $edtCalendrierPeriode->getIdCalendrier(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the EdtCalendrierPeriode relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    EdtEmplacementCoursQuery The current query, for fluid interface
	 */
	public function joinEdtCalendrierPeriode($relationAlias = '', $joinType = Criteria::LEFT_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('EdtCalendrierPeriode');
		
		// create a ModelJoin object for this join
		$join = new ModelJoin();
		$join->setJoinType($joinType);
		$join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
		
		// add the ModelJoin to the current object
		if($relationAlias) {
			$this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
			$this->addJoinObject($join, $relationAlias);
		} else {
			$this->addJoinObject($join, 'EdtCalendrierPeriode');
		}
		
		return $this;
	}

	/**
	 * Use the EdtCalendrierPeriode relation EdtCalendrierPeriode object
	 *
	 * @see       useQuery()
	 * 
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    EdtCalendrierPeriodeQuery A secondary query class using the current class as primary query
	 */
	public function useEdtCalendrierPeriodeQuery($relationAlias = '', $joinType = Criteria::LEFT_JOIN)
	{
		return $this
			->joinEdtCalendrierPeriode($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'EdtCalendrierPeriode', 'EdtCalendrierPeriodeQuery');
	}

	/**
	 * Filter the query by a related UtilisateurProfessionnel object
	 *
	 * @param     UtilisateurProfessionnel $utilisateurProfessionnel  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    EdtEmplacementCoursQuery The current query, for fluid interface
	 */
	public function filterByUtilisateurProfessionnel($utilisateurProfessionnel, $comparison = Criteria::EQUAL)
	{
		return $this
			->addUsingAlias(EdtEmplacementCoursPeer::LOGIN_PROF, $utilisateurProfessionnel->getLogin(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the UtilisateurProfessionnel relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    EdtEmplacementCoursQuery The current query, for fluid interface
	 */
	public function joinUtilisateurProfessionnel($relationAlias = '', $joinType = Criteria::LEFT_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('UtilisateurProfessionnel');
		
		// create a ModelJoin object for this join
		$join = new ModelJoin();
		$join->setJoinType($joinType);
		$join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
		
		// add the ModelJoin to the current object
		if($relationAlias) {
			$this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
			$this->addJoinObject($join, $relationAlias);
		} else {
			$this->addJoinObject($join, 'UtilisateurProfessionnel');
		}
		
		return $this;
	}

	/**
	 * Use the UtilisateurProfessionnel relation UtilisateurProfessionnel object
	 *
	 * @see       useQuery()
	 * 
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    UtilisateurProfessionnelQuery A secondary query class using the current class as primary query
	 */
	public function useUtilisateurProfessionnelQuery($relationAlias = '', $joinType = Criteria::LEFT_JOIN)
	{
		return $this
			->joinUtilisateurProfessionnel($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'UtilisateurProfessionnel', 'UtilisateurProfessionnelQuery');
	}

	/**
	 * Filter the query by a related AbsenceEleveSaisie object
	 *
	 * @param     AbsenceEleveSaisie $absenceEleveSaisie  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    EdtEmplacementCoursQuery The current query, for fluid interface
	 */
	public function filterByAbsenceEleveSaisie($absenceEleveSaisie, $comparison = Criteria::EQUAL)
	{
		return $this
			->addUsingAlias(EdtEmplacementCoursPeer::ID_COURS, $absenceEleveSaisie->getIdEdtEmplacementCours(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the AbsenceEleveSaisie relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    EdtEmplacementCoursQuery The current query, for fluid interface
	 */
	public function joinAbsenceEleveSaisie($relationAlias = '', $joinType = Criteria::LEFT_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('AbsenceEleveSaisie');
		
		// create a ModelJoin object for this join
		$join = new ModelJoin();
		$join->setJoinType($joinType);
		$join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
		
		// add the ModelJoin to the current object
		if($relationAlias) {
			$this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
			$this->addJoinObject($join, $relationAlias);
		} else {
			$this->addJoinObject($join, 'AbsenceEleveSaisie');
		}
		
		return $this;
	}

	/**
	 * Use the AbsenceEleveSaisie relation AbsenceEleveSaisie object
	 *
	 * @see       useQuery()
	 * 
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    AbsenceEleveSaisieQuery A secondary query class using the current class as primary query
	 */
	public function useAbsenceEleveSaisieQuery($relationAlias = '', $joinType = Criteria::LEFT_JOIN)
	{
		return $this
			->joinAbsenceEleveSaisie($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'AbsenceEleveSaisie', 'AbsenceEleveSaisieQuery');
	}

	/**
	 * Exclude object from result
	 *
	 * @param     EdtEmplacementCours $edtEmplacementCours Object to remove from the list of results
	 *
	 * @return    EdtEmplacementCoursQuery The current query, for fluid interface
	 */
	public function prune($edtEmplacementCours = null)
	{
		if ($edtEmplacementCours) {
			$this->addUsingAlias(EdtEmplacementCoursPeer::ID_COURS, $edtEmplacementCours->getIdCours(), Criteria::NOT_EQUAL);
	  }
	  
		return $this;
	}

	/**
	 * Code to execute before every SELECT statement
	 * 
	 * @param     PropelPDO $con The connection object used by the query
	 */
	protected function basePreSelect(PropelPDO $con)
	{
		return $this->preSelect($con);
	}

	/**
	 * Code to execute before every DELETE statement
	 * 
	 * @param     PropelPDO $con The connection object used by the query
	 */
	protected function basePreDelete(PropelPDO $con)
	{
		return $this->preDelete($con);
	}

	/**
	 * Code to execute before every UPDATE statement
	 * 
	 * @param     array $values The associatiove array of columns and values for the update
	 * @param     PropelPDO $con The connection object used by the query
	 */
	protected function basePreUpdate(&$values, PropelPDO $con)
	{
		return $this->preUpdate($values, $con);
	}

} // BaseEdtEmplacementCoursQuery