<?php
/**
 * Get all the extension notes for RSS feed
 *
 * @package modules
 * @copyright (C) 2005-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage release
 * @link http://xaraya.com/index.php/release/773.html
 */
/**
 * Get module IDs
 *
 * Original Author of file: John Cox via phpMailer Team
 * @author jojodee
 * @author Release module development team
 * @param integer $releaseno
 */
function release_userapi_getallrssextnotes($args)
{
    extract($args);
    //Make provision to pass in $releaseno to set defined number of items to get

    $releaseinfo = array();

    // Security Check
    if (!xarSecurity::check('OverviewRelease')) {
        return;
    }

    // Get database setup
    $dbconn =& xarDB::getConn();
    $xartable =& xarDB::getTables();

    $releasenotes = $xartable['release_notes'];
    //Need to only get the last x release notes for efficiency
    $query = "SELECT xar_rnid,
                     xar_eid,
                     xar_rid,
                     xar_version,
                     xar_exttype
            FROM $releasenotes
            WHERE xar_approved = 2 and xar_usefeed = 1
            ORDER by xar_time DESC";

    if (isset($releaseno) && is_numeric($releaseno)) { //unlimited if not set??
        $result =& $dbconn->SelectLimit($query, $releaseno, 0);
    } else {
        $result =& $dbconn->Execute($query);
    }
    if (!$result) {
        return;
    }

    // Put users into result array
    for (; !$result->EOF; $result->MoveNext()) {
        list($rnid, $eid, $rid, $version, $exttype) = $result->fields;
        if (xarSecurity::check('OverviewRelease', 0)) {
            $releaseinfo[] = array('rnid'       => $rnid,
                                   'eid'        => $eid,
                                   'rid'        => $rid,
                                   'version'    => $version,
                                   'exttype'    => $exttype);
        }
    }

    $result->Close();

    // Return the users
    return $releaseinfo;
}
