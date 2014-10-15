<?php

/*******************************************************************************
 * Aliro - the modern, accessible content management system
 *
 * Aliro is open source software, free to use, and licensed under GPL.
 * You can find the full licence at http://www.gnu.org/copyleft/gpl.html GNU/GPL
 *
 * The author freely draws attention to the fact that Aliro derives from Mambo,
 * software that is controlled by the Mambo Foundation.  However, this section
 * of code is totally new.  If it should contain any fragments that are similar
 * to Mambo, please bear in mind (1) there are only so many ways to do things
 * and (2) the author of Aliro is also the author and copyright owner for large
 * parts of Mambo 4.6.
 *
 * Tribute should be paid to all the developers who took Mambo to the stage
 * it had reached at the time Aliro was created.  It is a feature rich system
 * that contains a good deal of innovation.
 *
 * Your attention is also drawn to the fact that Aliro relies on other items of
 * open source software, which is very much in the spirit of open source.  Aliro
 * wishes to give credit to those items of code.  Please refer to
 * http://aliro.org/credits for details.  The credits are not included within
 * the Aliro package simply to avoid providing a marker that allows hackers to
 * identify the system.
 *
 * Copyright in this code is strictly reserved by its author, Martin Brampton.
 * If it seems appropriate, the copyright will be vested in the Aliro Organisation
 * at a suitable time.
 *
 * Copyright (c) 2007 Martin Brampton
 *
 * http://aliro.org
 *
 * counterpoint@aliro.org
 *
 * aliroAuthoriser is a singleton class that handles questions concerning the Role Based
 * Access Control (RBAC) system for Aliro.  It is a companion to aliroAuthorisationAdmin
 * which is the class that deals with updating the RBAC information.  Since the information
 * used in this class is often particular to the current user, it makes poor sense to
 * have a general cache.  Instead, information is cached using session variables. An
 * exception to this principle is the linking structure that enables implied roles to
 * be derived - e.g. a Publisher implicitly also has the rights belonging to an Editor.
 * Since this information is tricky to construct and general to all users, it is cached
 * in the file system.
 *
 *
 * aliroAuthorisationAdmin complements aliroAuthoriser, which answers questions about
 * permissions through the Aliro Role Based Access Control (RBAC) system.  This class
 * is used to set the permissions and assignments that are involved.  It can be used from
 * either the user or admin sides, depending on how RBAC management is deployed in a
 * particular application.
 *
 */

if (!defined('_ALIRO_AUTHORISER_SESSION_CACHE_TIME')) define ('_ALIRO_AUTHORISER_SESSION_CACHE_TIME', 1);

class aliroAuthoriserCache {
	// private properties
	private $cache = null;
	private $user_roles = array();
	private $all_roles = array();
	private $linked_roles = array();
	private $translations = array ();

	// protected function in Aliro, can be private in Remository
	private function aliroAuthoriserCache () {
		// Making protected enforces singleton
		if (defined('_JOOMLA_15PLUS')) {
			$this->cache = JFactory::getCache('aliroAuthoriser');
			// $this->cache->setCaching(1);
		}
		else $this->cache = mosCache::getCache('aliroAuthoriser');
		$this->getCacheData();
	}
	
	private function setTranslations () {
		if (0 == count($this->translations)) $this->translations = array(
		'Registered' => _DOWN_ROLE_REGISTERED,
		'Public' => _DOWN_ROLE_VISITOR,
		'Nobody' => _DOWN_ROLE_NOBODY,
		'none' => _DOWN_ROLE_NONE_THESE
		);
	}
	
	private function getCacheData () {
		$cachedata = $this->cache->call('aliroAuthoriserCache::getRoleData');
		$this->all_roles = $cachedata->roles;
		$this->cleartime = $cachedata->cleartime;
	}

	public function clearCache () {
		clearstatcache();
		$this->cache->clean('aliroAuthoriser');
		$this->getCacheData();
	}
	
	public function getClearTime () {
		return $this->cleartime;
	}

	// private function? It is really, but has to be called by the cache mechanism in CMS.
	public static function getRoleData () {
		$interface = remositoryInterface::getInstance();
		$database = $interface->getDB();
		$sql = "";
		if (remositoryRepository::getInstance()->Use_CMS_Groups) {
			if(version_compare(JVERSION,'1.6.0','<')){
				$sql = "SELECT DISTINCT g.name AS role FROM #__core_acl_aro_groups AS g"
				." INNER JOIN #__core_acl_aro_groups AS c ON g.parent_id != c.id AND g.id != c.parent_id AND g.id != c.id"
				." WHERE c.name = 'USERS'";
			}
			else{
				$sql = "SELECT DISTINCT g.title AS role FROM #__usergroups AS g"
				." INNER JOIN #__usergroups AS c ON g.parent_id != c.id AND g.id != c.parent_id AND g.id != c.id";
			}
		}
		else {
			$sql = "SELECT DISTINCT role FROM #__assignments UNION SELECT DISTINCT role FROM #__permissions";
		}
		$database->setQuery($sql);
		$links = $database->loadObjectList();
		if ($links) foreach ($links as $link) $all_roles[$link->role] = $link->role;
		$result = new stdClass();
		$result->roles = isset($all_roles) ? $all_roles : array();
		$result->cleartime = time();
		return $result;
	}

	// private function - not currently used
	function getUserData () {
		$interface = remositoryInterface::getInstance();
		$database = $interface->getDB();
		$database->setQuery("SELECT role, access_id FROM #__assignments WHERE access_type = 'aUser' AND (access_id = '*' OR access_id = '0')");
		$results = $database->loadObjectList();
		if ($results) foreach ($results as $role) $user_roles[$role->access_id][$role->role] = 1;
		if (!isset($user_roles['0'])) $user_roles['0'] = array();
		if (isset($user_roles['*'])) $user_roles['0'] = array_merge($user_roles['0'], $user_roles['*']);
		return $user_roles;
	}

    public static function getInstance () {
        static $instance;
        if (!is_object($instance)) $instance = new aliroAuthoriserCache();
        return $instance;
    }

	public function getTranslatedRole ($role) {
		$this->setTranslations();
		if (isset($this->translations[$role])) return $this->translations[$role];
		else return $role;
	}

	public function getAllRoles ($addSpecial=false) {
		$this->setTranslations();
		$roles = $this->all_roles;
		foreach (array_keys($this->translations) as $special) if (isset($roles[$special])) unset ($roles[$special]);
		if ($addSpecial) foreach ($this->translations as $raw=>$translated) $roles[$raw] = $translated;
		return $roles;
	}

	public function barredRole ($role) {
		$this->setTranslations();
		if (isset($this->translations[$role])) return true;
		else return false;
	}

	// public function - left for consistency but not used in Remository
	public function getLinkedRoles () {
	    return $this->linked_roles;
	}

	// public function - always returns empty array in Remository
	public function getUserRoles ($id) {
	    return isset($this->user_roles[$id]) ? array_keys($this->user_roles[$id]) : array();
	}

}


class aliroAuthoriser {

	// private properties
	private $subj_found = array();
	private $permissions = array();
	private $access_found = array();
	private $access_roles = array();

	private $linked_roles = array();
	private $auth_vars = array ('subj_found', 'permissions', 'access_found', 'access_roles');
	private $old_groupids = array ('Registered' => 18, 'Author' => 19, 'Editor' => 20, 'Publisher' => 21, 'Manager' => 23, 'Administrator' => 24, 'Super Administrator' => 25);

	private $handler = null;
	private $database = null;
	
	private $refused_cache = array();

	private function __construct () {
		foreach ($this->auth_vars as $one_var) {
			if (!isset($_SESSION['aliro_auth'][$one_var])) $_SESSION['aliro_auth'][$one_var] = array();
			$this->$one_var =& $_SESSION['aliro_auth'][$one_var];
		}
		$this->handler = aliroAuthoriserCache::getInstance();
		if (!isset($_SESSION['aliro_auth']['timer']) OR _ALIRO_AUTHORISER_SESSION_CACHE_TIME < (time() - $_SESSION['aliro_auth']['timer']) OR $_SESSION['aliro_auth']['timer'] < $this->handler->getClearTime()) {
			$this->clearCache();
			if (isset($_SESSION)) $_SESSION['aliro_auth']['timer'] = time();
		}
		$this->linked_roles = $this->handler->getLinkedRoles();
		$interface = remositoryInterface::getInstance();
		$this->database = $interface->getDB();
	}

    public static function getInstance () {
        static $instance;
        if (!is_object($instance)) $instance = new aliroAuthoriser;
        return $instance;
    }
	
	public function clearCache () {
		$this->subj_found = $this->permissions = $this->access_found = $this->access_roles = $this->refused_cache = array();
	}

	// public function
	function getAllRoles ($addSpecial=false) {
		return $this->handler->getAllRoles($addSpecial);
	}

	// public function
	function getTranslatedRole ($role) {
		return $this->handler->getTranslatedRole($role);
	}

	// public function
	function minimizeRoleSet ($roleset) {
		if (0 == count($roleset)) return $roleset;
		$first = array_shift($roleset);
		foreach ($roleset as $key=>$role) {
			if (isset($this->linked_roles[$first][$role])) unset ($roleset[$key]);
			if (isset($this->linked_roles[$role][$first])) return $this->minimizeRoleSet ($roleset);
		}
		array_unshift($roleset, $first);
		return $roleset;
	}

	// privatefunction
	function getSubjectData ($subject, $id, $action) {
		$stamp = time();
		if (isset($this->subj_found[$subject][$action][$id]) AND (($stamp - $this->subj_found[$subject][$action][$id]) < _ALIRO_AUTHORISER_SESSION_CACHE_TIME)) return;
		if (isset($this->subj_found[$subject][$action]['*']) AND ($stamp - $this->subj_found[$subject][$action]['*'] < _ALIRO_AUTHORISER_SESSION_CACHE_TIME)) return;
		$this->database->setQuery("SELECT COUNT(*) FROM `#__permissions` WHERE `subject_type`='$subject' AND (`action`='$action' OR `action`='*')");
		if ($this->database->loadResult() < 100) {
			$this->database->setQuery("SELECT `role`, `control`, `subject_id`, `action` FROM `#__permissions` WHERE `subject_type`='$subject' AND (`action`='$action' OR `action`='*')");
			$new_permissions = $this->database->loadObjectList();
			if (isset($this->subj_found[$subject][$action])) unset($this->subj_found[$subject][$action]);
			$this->subj_found[$subject][$action]['*'] = $stamp;
		}
		else {
			$this->database->setQuery("SELECT role, control, subject_id, action FROM #__permissions WHERE subject_type='$subject' AND (subject_id='$id' OR subject_id='*') AND (action='$action' OR action='*')");
			$new_permissions = $this->database->loadObjectList();
			unset($this->subj_found[$subject][$action][$id]);
		}
		if ($new_permissions) {
			foreach ($new_permissions as $permit) {
				$this->permissions[$subject][$permit->action][$permit->subject_id][$permit->role] = $permit->control;
				$this->subj_found[$subject][$permit->action][$permit->subject_id] = $stamp;
			}
		}
	}

	// public function
	function getAccessorRoles ($type, $id) {
	    if ('aUser' == $type AND ('0' == $id OR '*' == $id)) return $this->handler->getUserRoles($id);
		if (isset($this->access_found[$type][$id])) {
			if ((time() - $this->access_found[$type][$id]) < _ALIRO_AUTHORISER_SESSION_CACHE_TIME) {
				return $this->mergeAccessorResults($type, $id);
			}
			unset ($this->access_found);
			$this->access_roles = array();
		}
		if (remositoryRepository::getInstance()->Use_CMS_Groups) {
			if(version_compare(JVERSION,'1.6.0','<')){
				$sql = "SELECT g.name AS role, u.id AS access_id FROM #__core_acl_aro_groups AS g"
				." INNER JOIN #__users AS u ON g.id = u.gid WHERE u.id = '$id'";
			}
			else{
				$sql = "SELECT g.title AS role, u.id AS access_id FROM #__usergroups AS g"
				." INNER JOIN #__user_usergroup_map AS ug ON g.id = ug.group_id"
				." INNER JOIN #__users AS u ON u.id = ug.user_id "
				." WHERE u.id = '$id'";
			}
		}
		else {
			$sql = "SELECT role, access_id FROM #__assignments AS a WHERE a.access_type='$type'";
			$sql .= isset($this->access_found[$type]) ? " AND a.access_id='$id'" : " AND (a.access_id='$id' OR a.access_id='*' OR a.access_id='+')";
		}
		$this->database->setQuery($sql);
		if ($results = $this->database->loadObjectList()) {
			foreach ($results as $result) {
				$this->access_roles[$type][$result->access_id][$result->role] = 1;
			}
		}
		$this->access_found[$type][$id] = time();
		return $this->mergeAccessorResults($type, $id);
	}

	// privatefunction
	function mergeAccessorResults ($type, $id) {
		if (isset($this->access_roles[$type][$id])) $result = $this->access_roles[$type][$id];
		else $result = array();
		if (isset($this->access_roles[$type]['*'])) $result = array_merge($result, $this->access_roles[$type]['*']);
		if ($id AND isset($this->access_roles[$type]['+'])) $result = array_merge($result, $this->access_roles[$type]['+']);
		if ('aUser' == $type AND $id) $result['Registered'] = 1;
		if (count($result)) return array_keys ($result);
		else return array();
	}

	// privatefunction
	function blanket ($action, $type) {
		return (isset($this->permissions[$type][$action]['*']) AND count($this->permissions[$type][$action]['*']));
	}

	// privatefunction
	function specific ($action, $type, $id) {
		return (isset($this->permissions[$type][$action][$id]) AND count($this->permissions[$type][$action][$id]));
	}

	// privatefunction
	function accessorPermissionOrControl  ($mask, $a_type, $a_id, $action, $s_type='*', $s_id='*') {
		$this->getSubjectData ($s_type, $s_id, $action);
		if ('*' != $s_type AND 2 == $mask AND !$this->blanket($action, $s_type) AND !($this->specific($action, $s_type, $s_id))) return 1;
		if ((!isset($this->permissions[$s_type][$action][$s_id]) OR 0 == count($this->permissions[$s_type][$action][$s_id]))
		AND (!isset($this->permissions[$s_type][$action]['*']) OR 0 == count($this->permissions[$s_type][$action]['*']))) return 1;
		$roles = $this->getAccessorRoles ($a_type, $a_id);
		return $this->rolePermissionOrControl ($mask, $roles, $action, $s_type, $s_id);
	}

	// public function
	function checkPermission ($a_type, $a_id, $action, $s_type='*', $s_id='*') {
		return $this->accessorPermissionOrControl(2, $a_type, $a_id, $action, $s_type, $s_id);
	}

	// public function
	function checkUserPermission ($action, $s_type='*', $s_id='*') {
		$interface = remositoryInterface::getInstance();
		$user = $interface->getUser();
		return $this->checkPermission ('aUser', $user->id, $action, $s_type, $s_id);
	}

	// public function
	function checkControl ($a_type, $a_id, $action, $s_type='*', $s_id='*') {
		return $this->accessorPermissionOrControl(1, $a_type, $a_id, $action, $s_type, $s_id);
	}

	// public function
	function checkGrant ($a_type, $a_id, $action, $s_type='*', $s_id='*') {
		return $this->accessorPermissionOrControl(4, $a_type, $a_id, $action, $s_type, $s_id);
	}

	// privatefunction
	function rolePermissionOrControl ($mask, $roles, $actions, $s_type, $s_id) {
		if (!is_array($actions)) $actions = array($actions);
		foreach ($actions as $action) $this->getSubjectData ($s_type, $s_id, $action);
		if (!is_array($roles)) $roles = array($roles);
		if (in_array('Public', $roles)) {
			foreach ($actions as $action) {
				if (empty($this->permissions[$s_type][$action][$s_id])) return 1;
			}
		}
		if (count($roles)) foreach ($this->permissions[$s_type] as $act=>$level2) {
			if (!in_array($act, $actions) AND !in_array('*', $actions)) continue;
			foreach ($level2 as $id=>$level3) {
				if ($id != $s_id AND $id != '*') continue;
				foreach ($level3 as $role=>$control)
					if (in_array($role, $roles) AND ($mask & $control)) {
						return 1;
					}
			}
		}
		return 0;
	}

	// public function
	function checkRolePermission  ($role, $action, $s_type, $s_id) {
		return $this->rolePermissionOrControl(2, $role, $action, $s_type, $s_id);
	}

	// public function
	function checkRoleControl  ($role, $action, $s_type, $s_id) {
		return $this->rolePermissionOrControl(1, $role, $action, $s_type, $s_id);
	}

	// public function
	function checkRoleGrant  ($role, $action, $s_type, $s_id) {
		return $this->rolePermissionOrControl(4, $role, $action, $s_type, $s_id);
	}

	public function getRefusedList ($a_type, $a_id, $s_type, $actionlist) {
		$roles = $this->getAccessorRoles($a_type, $a_id);
		$actions = explode(',', $actionlist);
		foreach ($actions as $i=>$action) $actions[$i] = trim($action);
		$alist = implode("','", $actions);
		if (!isset($this->refused_cache[$s_type][$alist])) {
			$this->database->setQuery("SELECT role, subject_id, action FROM #__permissions WHERE subject_type = '$s_type' AND action IN('$alist')");
			$this->refused_cache[$s_type][$alist] = $this->database->loadObjectList();
		}
		$results = $this->refused_cache[$s_type][$alist];
		if ($results) foreach ($results as $result) $ids[$result->subject_id][$result->action][] = $result->role;
		if (isset($ids)) {
			$refused = array_keys($ids);
			foreach ($ids as $id=>$actionset) {
				foreach ($actions as $action) if (!isset($actionset[$action])) $permits[$id] = 1;
				if (!isset($permits[$id])) foreach ($actionset as $action=>$permittedroles) {
					if (count(array_intersect($permittedroles, $roles))) $permits[$id] = 1;
				}
			}
			if (isset($permits)) $refused = array_diff ($refused, array_keys($permits));
		}
		else $refused = array();
		return $refused;
	}

	function getRefusedListSQL ($a_type, $a_id, $s_type, $actionlist, $keyname) {
		$refused = $this->getRefusedList ($a_type, $a_id, $s_type, $actionlist);
		if (count($refused)) {
			$excludelist = implode("','", $refused);
			return " CAST($keyname AS CHAR) NOT IN ('$excludelist')";
		}
		return '';
	}

	public function listPermissions ($a_type, $a_id, $action) {
		$roles = $this->getAccessorRoles ($a_type, $a_id);
		$role_list = "IN ('".implode("','", $roles)."')";
		$this->database->setQuery("SELECT DISTINCT subject_type FROM #__permissions WHERE role $role_list AND action='$action' AND (control & 2) ORDER BY subject_type");
		$subjects = $this->database->loadResultArray();
		return $subjects;
	}

	public function listUserPermissions ($action) {
		$interface = remositoryInterface::getInstance();
		$user = $interface->getUser();
		$results = $this->listPermissions ('aUser', $user->id, $action);
		return $results;
	}

	public function listAccessors ($accessor_type, $role) {
		if (remositoryRepository::getInstance()->Use_CMS_Groups) {
			if(version_compare(JVERSION,'1.6.0','<')){
				$sql = "SELECT u.id AS access_id FROM #__core_acl_aro_groups AS g "
				." INNER JOIN #__users AS u ON g.id = u.gid WHERE name = '$role'";
			}
			else{ 
				$sql = "SELECT u.id AS access_id FROM #__usergroups AS g "
				." INNER JOIN #__user_usergroup_map AS ug ON g.id = ug.group_id"
				." INNER JOIN #__users AS u ON u.id = ug.user_id "
				." WHERE title = '$role'";
			}
		}
		else {
			$sql = "SELECT access_id FROM #__assignments WHERE access_type = '$accessor_type' AND role = '$role'";
		} 
		$this->database->setQuery($sql);
		$result = $this->database->loadResultArray();
		return $result ? $result : array();
	}

	public function listAccessorsToSubject ($subject_type, $subject_id, $accessor_type, $action="*") {
		$subject_id = intval($subject_id);
		if (!empty($action) AND (is_array($action) OR '*' != $action)) $actions = "'".implode("','", (array) $action)."'";
		if (remositoryRepository::getInstance()->Use_CMS_Groups) {
			if(version_compare(JVERSION,'1.6.0','<')){
				$sql = "SELECT DISTINCT u.id AS access_id FROM #__core_acl_aro_groups AS a";
				$sql .= " INNER JOIN #__permissions AS p ON a.name = p.role";
				$sql .= " INNER JOIN #__users AS u ON a.id = u.gid";
				$sql .= " WHERE (control & 2) AND p.subject_type = '$subject_type' AND p.subject_id = $subject_id";
			}
			else {
				$sql = "SELECT DISTINCT u.id AS access_id FROM #__usergroups AS g"
				." INNER JOIN #__user_usergroup_map AS ug ON g.id = ug.group_id"
				." INNER JOIN #__users AS u ON u.id = ug.user_id "
				. " INNER JOIN #__permissions AS p ON g.title = p.role"
				. " WHERE (control & 2) AND p.subject_type = '$subject_type' AND p.subject_id = $subject_id";
			}
		}
		else {
			$sql = "SELECT DISTINCT a.access_id FROM #__assignments AS a INNER JOIN #__permissions AS p ON a.role = p.role";
			$sql .= " WHERE (control & 2) AND p.subject_type = '$subject_type' AND p.subject_id = $subject_id AND a.access_type = '$accessor_type'";
		}
		if (isset($actions)) $sql .= " AND p.action IN ($actions)";
		$this->database->setQuery($sql);
		return $this->database->loadResultArray();
	}
}

class aliroAuthorisationAdmin {
	private $handler = null;
	private $authoriser = null;
	private $database = null;

	private function aliroAuthorisationAdmin () {
		$this->handler = aliroAuthoriserCache::getInstance();
		$this->authoriser = aliroAuthoriser::getInstance();
		$interface = remositoryInterface::getInstance();
		$this->database = $interface->getDB();
	}

    public static function getInstance () {
        static $instance;
        if (!is_object($instance)) $instance = new aliroAuthorisationAdmin();
        return $instance;
    }

	private function doSQL ($sql, $clear=false) {
		$interface = remositoryInterface::getInstance();
		$database = $interface->getDB();
		$database->setQuery($sql);
		$database->query();
		if ($clear) $this->clearCache();
	}

	public function clearCache () {
		$this->handler->clearCache();
		$this->authoriser->clearCache();
	}

	// public function
	function getAllRoles ($addSpecial=false) {
		return $this->authoriser->getAllRoles($addSpecial);
	}

	// public function
	function getTranslatedRole ($role) {
		return $this->authoriser->getTranslatedRole($role);
	}

	private function permissionHolders ($subject_type, $subject_id) {
		$sql = "SELECT DISTINCT role, action, control, subject_type, subject_id FROM #__permissions";
		if ($subject_type != '*') $where[] = "(subject_type='$subject_type' OR subject_type='*')";
		if ($subject_id != '*') $where[] = "(subject_id='$subject_id' OR subject_id='*')";
		if (isset($where)) $sql .= " WHERE ".implode(' AND ', $where);
		$this->database->setQuery($sql);
		$result = $this->database->loadObjectList();
		if (!$result) $result = array();
		return $result;
	}

	public function dropPermissions ($action, $subject_type, $subject_id) {
		$sids = $this->conditionOnID($subject_id, 'subject_id');
		if ($sids) {
			$where[] = $sids;
			$where[] = 'system = 0';
			if ($action) $where[] = "action = '$action'";
			$condition = implode(' AND ', $where);
			$this->doSQL("DELETE FROM #__permissions WHERE $condition", true);
		}
	}
	
	public function dropAllPermissions ($subject_type, $subject_id) {
		$this->dropPermissions('', $subject_type, $subject_id);
	}
	
	protected function conditionOnID ($id, $field) {
		foreach ((array) $id as $one) $idset[] = $this->database->getEscaped($one);
		if (isset($idset)) {
			$idlist = implode("','", $idset);
			return "$field IN ('$idlist')";
		}
		else return '';
	}
	
	// public function
	function permittedRoles ($actions, $subject_type, $subject_id, $excluding=null) {
		$nonspecific = true;
		foreach ($this->permissionHolders ($subject_type, $subject_id) as $possible) {
			if ('*' == $possible->action OR in_array($possible->action, (array) $actions)) {
				$result[$possible->role] = $this->getTranslatedRole($possible->role);
				if ('*' != $possible->subject_type AND '*' != $possible->subject_id) $nonspecific = false;
			}
		}
		if (!isset($result) OR $nonspecific) $result['Public'] = $this->getTranslatedRole('Public');
		foreach ((array) $excluding as $exclude) if (isset($result[$exclude])) unset($result[$exclude]);
		return $result;
	}

	private function nonLocalPermissionHolders ($subject_type, $subject_id) {
		$sql = "SELECT role, action, control FROM #__permissions WHERE (action='*' OR subject_type='*' OR subject_id='*') AND ((subject_type='$subject_type' OR subject_type='*') AND (subject_id='$subject_id' OR subject_id='*'))";
		$this->database->setQuery($sql);
		$result = $this->database->loadObjectList();
		if (!$result) $result = array();
		return $result;
	}

	private function permitSQL ($role, $control, $action, $subject_type, $subject_id) {
		$this->database->setQuery("SELECT id FROM #__permissions WHERE role='$role' AND action='$action' AND subject_type='$subject_type' AND subject_id='$subject_id'");
		$id = $this->database->loadResult();
		if ($id) return "UPDATE #__permissions SET control=$control WHERE id=$id";
		else return "INSERT INTO #__permissions (role, control, action, subject_type, subject_id) VALUES ('$role', '$control', '$action', '$subject_type', '$subject_id')";
	}

	// public function
	function permit ($role, $control, $action, $subject_type, $subject_id) {
		$sql = $this->permitSQL($role, $control, $action, $subject_type, $subject_id);
		$this->doSQL($sql, true);
	}

	// public function
	function assign ($role, $access_type, $access_id, $clear=true) {
		if ($this->handler->barredRole($role)) return false;
		$this->database->setQuery("SELECT id FROM #__assignments WHERE role='$role' AND access_type='$access_type' AND access_id='$access_id'");
		if ($this->database->loadResult()) return true;
		$sql = "INSERT INTO #__assignments (role, access_type, access_id) VALUES ('$role', '$access_type', '$access_id')";
		$this->doSQL($sql, $clear);
		return true;
	}

	// public function
	function unassign ($role, $access_type, $access_id) {
		$this->doSQL("DELETE FROM #__assignments WHERE role='$role' AND access_type='$access_type' AND access_id='$access_id'", true);
		return true;
	}

	// public function
	function assignRoleSet ($roleset, $access_type, $access_id) {
		$this->dropAccess ($access_type, $access_id);
		$authoriser = aliroAuthoriser::getInstance();
		$roleset = $authoriser->minimizeRoleSet($roleset);
		foreach ($roleset as $role) $this->assign ($role, $access_type, $access_id, false);
		$this->clearCache();
	}

	// public function
	function dropAccess ($access_type, $access_id) {
		$sql = "DELETE FROM #__assignments WHERE access_type='$access_type' AND access_id='$access_id'";
		$this->doSQL($sql, true);
	}

	// public function
	function getMyControllingRoles ($action, $subject_type, $subject_id) {
		$interface = remositoryInterface::getInstance();
		$user = $interface->getUser();
		$sql = "SELECT a.role FROM #__permissions AS p INNER JOIN #__assignments AS a ON a.role=p.role"
		." WHERE a.access_type='aUser'"
		." AND a.access_id='$user->id' AND (p.control&1)"
		." AND p.action='$action' AND p.subject_type='$subject_type' AND p.subject_id='$subject_id'";
		$this->doSQL($sql);
		$roles = $this->database->loadResultArray();
		return $roles;
	}

	// public function
	function getMyPermissions () {
		$interface = remositoryInterface::getInstance();
		$user = $interface->getUser();
		$sql = 'SELECT p.action, p.subject_type, p.subject_id, control '
		. ' FROM #__permissions AS p INNER JOIN #__assignments AS a ON p.role=a.role '
		. " WHERE a.access_type='aUser' AND (a.access_id='$user->id' OR a.access_id='*')"
		. ' AND (p.control&1)';
		$this->doSQL($sql);
		$permissions = $this->database->loadObjectList();
		return $permissions;
	}

	// public function
	function getMyJointPermissions ($role) {
		$interface = remositoryInterface::getInstance();
		$user = $interface->getUser();
		$sql = "SELECT p2.control AS hiscontrol, p1.control AS mycontrol, p1.action, p1.subject_type, p1.subject_id"
		." FROM `#__assignments` AS a INNER JOIN `#__permissions` AS p1 ON p1.role=a.role "
		." LEFT JOIN `#__permissions` AS p2"
		." ON (p2.role='$role' AND p1.action=p2.action AND p1.subject_type=p2.subject_type AND p1.subject_id=p2.subject_id)"
		." WHERE  (p1.control&1) AND a.access_type='aUser' AND (a.access_id='$user->id' OR a.access_id='*')";
		$this->doSQL($sql);
		$permissions = $this->database->loadObjectList();
		return $permissions;
	}

	// public function
	function resetPermissions ($action, $subject_type, $subject_id) {
		$control_types = array ('crole', 'arole', 'grole');
		$control_values = array (1,2,4);
		$permissions = $this->nonLocalPermissionHolders($subject_type, $subject_id);
		$this->dropPermissions($action, $subject_type, $subject_id);
		foreach ($control_types as $i=>$type) {
			$key = $action.'_'.$type;
			if (isset($_POST[$key])) {
				foreach ($_POST[$key] as $role) {
					$value = isset($newpermits[$role]) ? $newpermits[$role] : 0;
					$newpermits[$role] = $value | $control_values[$i];
				}
			}
		}
		$sql = '';
		foreach ($newpermits as $role=>$value) {
			$needed = true;
			foreach ($permissions as $permission) {
				if (($permission->action == '*' OR $permission->action == $action) AND $permission->role == $role) {
					if (($value & $permission->control) === $value) {
						$needed = false;
						break;
					}
				}
			}
			if ($needed) $sql .= $this->permitSQL ($role, $value, $action, $subject_type, $subject_id);
		}
		if ($sql) $this->doSQL($sql, true);
	}

	// public function
	function roleExists ($role) {
		return in_array($role, $this->getAllRoles());
	}

	// public function
	function dropRole ($role) {
		$sql = "DELETE FROM #__permissions WHERE action='administer' AND subject_type='$role' AND system=0";
		$this->doSQL($sql);
		$sql = "DELETE a FROM #__assignments AS a LEFT JOIN #__permissions AS p ON a.role=p.role WHERE a.role='$role' AND (p.system=0 OR p.system IS NULL)";
		$this->doSQL($sql);
		$sql = "DELETE FROM #__permissions WHERE role='$role' AND system=0";
		$this->doSQL($sql, true);
	}

}