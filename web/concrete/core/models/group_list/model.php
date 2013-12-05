<?
defined('C5_EXECUTE') or die("Access Denied.");

/**
 * @package Users
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 * @access private
 */
 
/** 
 * @access private
 */

class Concrete5_Model_GroupSearch extends DatabaseItemList {
	
	
	protected $itemsPerPage = 10;
	protected $minimumGroupID = REGISTERED_GROUP_ID;
	
	protected $autoSortColumns = array('gName', 'gID');

	public function includeAllGroups() {
		$this->minimumGroupID = -1;
	}
	
	public function filterByKeywords($kw) {
		$db = Loader::db();
		$this->filter(false, "(Groups.gName like " . $db->qstr('%' . $kw . '%') . " or Groups.gDescription like " . $db->qstr('%' . $kw . '%') . ")");
	}
	
	public function filterByAllowedPermission($pk) {
		$assignment = $pk->getMyAssignment();
		$r = $assignment->getGroupsAllowedPermission();
		$gIDs = array('-1');
		if ($r == 'C') {
			$gIDs = array_merge($assignment->getGroupsAllowedArray(), $gIDs);
			$this->filter('gID', $gIDs, 'in');
		}
	}
	
	public function updateItemsPerPage( $num ) {
		$this->itemsPerPage = $num;
	}
	
	function __construct() {
		$this->setQuery("select Groups.gID, Groups.gName, Groups.gDescription from Groups");
	}

	public function get($itemsToGet = 100, $offset = 0) {
		$r = parent::get( $itemsToGet, intval($offset));
		$groups = array();
		foreach($r as $row) {
			$g = Group::getByID($row['gID']);			
			if (is_object($g)) {
				$groups[] = $g;
			}
		}
		return $groups;
	}
	
}