<?php
/*
 * Release module get all registered release ids
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
 * get all users
 * @returns array
 * @return array of users, or false on failure
 * @TODO check if this is needed - use getallrids instead? or save this for quick alternative
 */
function release_userapi_getallids($args)
{
    extract($args);

    // Optional arguments.
    if (!isset($startnum)) {
        $startnum = 1;
    }
    if (!isset($numitems)) {
        $numitems = -1;
    }

    $releaseinfo = [];

    // Security Check
    if (!xarSecurity::check('OverviewRelease')) {
        return;
    }

    // Get database setup
    $dbconn =& xarDB::getConn();
    $xartable =& xarDB::getTables();

    $releasetable = $xartable['release_id'];
    $bindvars=[];
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
            ORDER BY xar_eid";
    if (!empty($certified)) {
        $query .= " WHERE xar_certified = ?";
        $bindvars[]=$certified;
    }
    if (isset($exttype) and !empty($exttype)) {
        if (!empty($certified)) {
            $query .= " AND xar_exttype = ?";
        } else {
            $query .= " WHERE xar_exttype = ?";
        }
        $bindvars[]= $exttype;
    }

    $result = $dbconn->SelectLimit($query, $numitems, $startnum-1, $bindvars);
    if (!$result) {
        return;
    }

    // Put users into result array
    for (; !$result->EOF; $result->MoveNext()) {
        [$eid,$rid, $uid, $regname, $displname, $desc, $class, $certified, $approved,
             $rstate, $regtime, $modified, $members, $scmlink, $openproj, $exttype] = $result->fields;
        if (xarSecurity::check('OverviewRelease', 0)) {
            $releaseinfo[] = ['eid'        => $eid,
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
                                   'exttype'    => $exttype, ];
        }
    }

    $result->Close();

    // Return the users
    return $releaseinfo;
}
