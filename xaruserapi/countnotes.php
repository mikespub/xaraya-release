<?php
/**
 * Utility function counts number of noteitems held by this module
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
 * utility function to count the number of items held by this module
 *
 * @author jojodee
 * @return int number of items held by this module
 * @throws DATABASE_ERROR
 */
function release_userapi_countnotes($args)
{
    extract($args);

    if (empty($phase)) {
        $phase='viewall';
    }

    $dbconn =& xarDB::getConn();
    $xartable =& xarDB::getTables();

    $releasenotes = $xartable['release_notes'];
    $releaseids = $xartable['release_id'];

    $query = "SELECT COUNT(1)
            FROM $releasenotes";


    if ($phase=='viewall') {
        $query .= " WHERE xar_approved = 2";
    } elseif ($phase=='certified') {
        $query .= " WHERE xar_certified = 2
                    AND xar_approved = 2";
    } elseif ($phase=='fee') {
        $query .= " WHERE xar_price = 1
                    AND xar_approved = 2";
    } elseif ($phase=='price') {
        $query .= " WHERE xar_price = 2
                    AND xar_approved = 2";
    } elseif ($phase=='supported') {
        $query .= " WHERE xar_supported = 2
                    AND xar_approved = 2";
    }

    $result = &$dbconn->Execute($query);

    // Check for an error with the database code, adodb has already raised
    // the exception so we just return
    if (!$result) {
        return;
    }
    // Obtain the number of items
    list($numitems) = $result->fields;
    // All successful database queries produce a result set, and that result
    // set should be closed when it has been finished with
    $result->Close();

    // Return the number of items
    return $numitems;
}
