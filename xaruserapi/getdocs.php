<?php
/**
 * Get documents
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
 * Get the docs
 *
 * @param $rnid $rid $approved
 *
 * Original Author of file: John Cox via phpMailer Team
 * @author Release module development team
 *
 */
function release_userapi_getdocs($args)
{
    extract($args);

    // Optional arguments.
    if (!isset($startnum)) {
        $startnum = 1;
    }
    if (!isset($numitems)) {
        $numitems = -1;
    }

    $releasedocs = [];

    // Security Check
    if (!xarSecurity::check('OverviewRelease')) {
        return;
    }

    // Get database setup
    $dbconn =& xarDB::getConn();
    $xartable =& xarDB::getTables();

    $releasedocstable = $xartable['release_docs'];

    $bindvars = [];
    $query = "SELECT xar_rdid,
                     xar_eid,
                     xar_rid,
                     xar_title,
                     xar_docs,
                     xar_exttype,
                     xar_time,
                     xar_approved
            FROM $releasedocstable

                     /*";
    if (!empty($approved)) {
        $query .= " WHERE xar_eid = ? AND xar_approved = ? AND xar_exttype = ?";
        array_push($bindvars, $eid, $approved, $exttype);
    } elseif (empty($exttype)) {
        $query .= " WHERE xar_approved = ?";
        array_push($bindvars, $approved);
    } else {
        $query .= "*/ WHERE xar_eid = ? AND xar_exttype = ?";
        array_push($bindvars, $eid, $exttype);
    }

    $query .= " ORDER BY xar_rdid";

    $result = $dbconn->SelectLimit($query, $numitems, $startnum-1, $bindvars);
    if (!$result) {
        return;
    }

    // Put users into result array
    for (; !$result->EOF; $result->MoveNext()) {
        [$rdid, $eid, $rid, $title, $docs, $exttype, $time, $approved] = $result->fields;
        if (xarSecurity::check('OverviewRelease', 0)) {
            $releasedocs[] = ['rdid'       => $rdid,
                                   'eid'        => $eid,
                                   'rid'        => $rid,
                                   'title'      => $title,
                                   'docs'       => $docs,
                                   'exttype'       => $exttype,
                                   'time'       => $time,
                                   'approved'   => $approved, ];
        }
    }

    $result->Close();

    // Return the users
    return $releasedocs;
}
