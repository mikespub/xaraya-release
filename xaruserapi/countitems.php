<?php
/**
 * Utility function counts number of items held by this module
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
function release_userapi_countitems($args)
{
    extract($args);

    $dbconn =& xarDB::getConn();
    $xartable =& xarDB::getTables();
    // It's good practice to name the table and column definitions you are
    // getting - $table and $column don't cut it in more complex modules
    $releasetable = $xartable['release_id'];
    //Joins on Catids
    if (!empty($catid)) {
        $categoriesdef = xarMod::apiFunc(
            'categories',
            'user',
            'leftjoin',
            ['modid'    => 773,
                                    'itemtype' => 0,
                                    'cids'     => [$catid],
                                    'andcids'  => 1, ]
        );
    }

    $query = "SELECT COUNT(1)
             FROM $releasetable";
    $bindvars = [];

    $from ='';
    $where = [];
    if (!empty($catid) && count([$catid]) > 0) {
        // add this for SQL compliance when there are multiple JOINs
        // Add the LEFT JOIN ... ON ... parts from categories
        $from .= ' LEFT JOIN ' . $categoriesdef['table'];
        $from .= ' ON ' . $categoriesdef['field'] . ' = ' . $releasetable.'.xar_eid';

        if (!empty($categoriesdef['more'])) {
            //$from = ' ( ' . $from . ' ) ';
            $from .= $categoriesdef['more'];
        }

        $where[] = $categoriesdef['where'];
        $query .= $from;
    }
    /*
        switch ($idtypes) {
        case 3: // module
            $where[] = "xar_type = '0'";
            break;
        case 2: // theme
            $where[] = "xar_type = '1'";
            break;
        }
        if (!empty($exttype)) {
            $where[] = " xar_exttype = ?";
            $bindvars[] = $exttype;
        }*/
    if (!empty($certified)) {
        $where[] = " xar_certified = ?";
        $bindvars[] = $certified;
    }

    if (count($where) > 0) {
        $query .= ' WHERE ' . join(' AND ', $where);
    }

    $query .= " ORDER BY xar_eid";

    $result =& $dbconn->Execute($query, $bindvars);
    if (!$result) {
        return;
    }

    // Check for an error with the database code, adodb has already raised
    // the exception so we just return
    if (!$result) {
        return;
    }
    // Obtain the number of items
    [$numitems] = $result->fields;
    // All successful database queries produce a result set, and that result
    // set should be closed when it has been finished with
    $result->Close();
    // Return the number of items
    return $numitems;
}
