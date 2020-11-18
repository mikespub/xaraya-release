<?php
/**
 * Get module IDs
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
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
 * @author Release module development team
 * @TODO Review - do we really need this now? Let's amalgamate ...
 */
function release_userapi_getmoduleids($args)
{
    extract($args);

    // Optional arguments.
    if (!isset($startnum)) {
        $startnum = 1;
    }
    if (!isset($numitems)) {
        $numitems = -1;
    }
    $exttypes= xarMod::apiFunc('release', 'user', 'getexttypes');
    $text = xarML('Module');
    $extid = array_search($text, $exttypes);
    $releaseinfo = array();

    // Security Check
    if (!xarSecurity::check('OverviewRelease')) {
        return;
    }

    // Get database setup
    $dbconn =& xarDB::getConn();
    $xartable =& xarDB::getTables();

    $releasetable = $xartable['release_id'];

    $query = "SELECT xar_eid,
                     xar_rid,
                     xar_uid,
                     xar_regname,
                     xar_displname,
                     xar_desc,
                     xar_class,
                     xar_certified,
                     xar_approved,
                     xar_rstate,
                     xar_regtime,
                     xar_modified,
                     xar_members,
                     xar_scmlink,
                     xar_openproj,
                     xar_exttype
            FROM $releasetable
            WHERE xar_exttype = ?
            ORDER BY xar_eid";
    $bindvars = array($extid);
    $result = $dbconn->SelectLimit($query, $numitems, $startnum-1, $bindvars);
    if (!$result) {
        return;
    }

    // Put users into result array
    for (; !$result->EOF; $result->MoveNext()) {
        list($eid,$rid, $uid, $regname, $displname, $desc, $class, $certified, $approved,
             $rstate, $regtime, $modified, $members, $scmlink, $openproj, $exttype) = $result->fields;
        if (xarSecurity::check('OverviewRelease', 0)) {
            $releaseinfo[] = array('eid'        => $eid,
                                   'rid'        => $rid,
                                   'uid'        => $uid,
                                   'regname'    => $regname,
                                   'displname'  => $displname,
                                   'desc'       => $desc,
                                   'class'      => $class,
                                   'certified'  => $certified,
                                   'approved'   => $approved,
                                   'rstate'     => $rstate,
                                   'regtime'    => $regtime,
                                   'modified'   => $modified,
                                   'members'    => $members,
                                   'scmlink'    => $scmlink,
                                   'openproj'   => $openproj,
                                   'exttype'    => $exttype);
        }
    }

    $result->Close();

    // Return the users
    return $releaseinfo;
}
