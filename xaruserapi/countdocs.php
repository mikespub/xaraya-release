<?php
/**
 * Count the number of docs
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Release Module
 */
/**
 * count the number of docs per release
 * 
 * @param $rid ID
 * @returns integer
 * @returns number of docs for rid
 */
function release_userapi_countdocs($args)
{
    extract ($args);

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $releasetable = $xartable['release_docs'];

    $query = "SELECT COUNT(1)
            FROM $releasetable
            WHERE xar_rid = ?";
    $result =&$dbconn->Execute($query, array($rid));
    if (!$result) return;

    list($numitems) = $result->fields;

    $result->Close();

    return $numitems;
}

?>
