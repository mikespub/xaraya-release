<?php
/*
 * Get all module release notes for the rss feed
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage release
 * @link http://xaraya.com/index.php/release/773.html
 */

function release_userapi_getallrssmodsnotes($args)
{
    extract($args);

    $releaseinfo = array();

    // Security Check
    if (!xarSecurity::check('OverviewRelease')) {
        return;
    }

    // Get database setup
    $dbconn =& xarDB::getConn();
    $xartable =& xarDB::getTables();

    $releasenotes = $xartable['release_notes'];

    $query = "SELECT xar_rnid,
                     xar_eid,
                     xar_rid,
                     xar_version
            FROM $releasenotes
            WHERE xar_certified = ? and xar_userfeed = ?
            AND xar_exttype = ?
            ORDER by xar_time DESC";

    $bindvars = array(2, 1, $exttype);
    $result =& $dbconn->Execute($query, $bindvars);
    if (!$result) {
        return;
    }

    // Put users into result array
    for (; !$result->EOF; $result->MoveNext()) {
        list($rnid, $eid, $rid, $version) = $result->fields;
        if (xarSecurity::check('OverviewRelease', 0)) {
            $releaseinfo[] = array('rnid'       => $rnid,
                                   'eid'        => $eid,
                                   'rid'        => $rid,
                                   'version'    => $version);
        }
    }

    $result->Close();

    // Return the users
    return $releaseinfo;
}
