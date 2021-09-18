<?php
/**
 * Create a new id after validating the type and name
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
 * create a new ID
 *
 * @author Release module development team
 */
function release_userapi_allocateid($args)
{
    extract($args);

    /* Get rid of leading and trailing spaces in the name */

    if (!isset($regname) || !isset($exttype)) {
        $msg = xarML('Both a registration name and extension type were not provided and are needed for registration.');
        throw new Exception(null, $msg);
    }
    //check the name - common to all types?
    // Argument check
    if (!ereg("^[a-z0-9][a-z0-9_-]*[a-z0-9]$", $regname)) {
        $msg = xarML('Registered name may only contain alphanumeric characters, included underscore or hypen, and no spaces.');
        throw new Exception(null, $msg);
    }
    if (isset($ridno) && is_integer($ridno) && $ridno >0) { //could be a supplied ID, let's check if it's available.
        $checkrid = xarMod::apiFunc('release', 'user', 'getid', ['rid'=>$ridno, 'exttype'=>$exttype]);
        if (isset($checkrid['regname']) && !empty($checkrid['regname'])) { //the rid is take, try again
            xarErrorFree();
            xarErrorHandled();
            return;
        } else {
            //we are done here - use this rid as it seems ok
            return $ridno;
        }
    }

    // Get database setup
    $dbconn =& xarDB::getConn();
    $xartable =& xarDB::getTables();

    $releasetable = $xartable['release_id'];

    // Check if that regname exists for the given extension type - this part is common to all
    $query = "SELECT xar_rid FROM $releasetable
              WHERE xar_regname = ?
              AND xar_exttype = ?";

    $result =& $dbconn->Execute($query, [$regname,$exttype]);
    if (!$result) {
        return;
    }

    if ($result->RecordCount() > 0) {
        $msg = xarML('Sorry, the name you requested is already registered for that extension type, please choose another.');
        throw new Exception(null, $msg);
    }

    $bindvars=[];
    //for modules and themes the numbers are allocated as previously sharing and unique within the range


    //now get an array of all existing registrations for this itemtypes
    $allrids=[];
    // Get all IDs
    $query2 = "SELECT xar_rid FROM $releasetable ";
    if ($exttype ==1 or $exttype ==2) {//modules or themes
        $query2 .= " WHERE xar_exttype <= 2";
    } else {
        $query2 .= " WHERE xar_exttype = ?";
        $bindvars =[(int)$exttype];
    }

    $query2 .= " ORDER BY xar_rid";

    $result =& $dbconn->Execute($query2, $bindvars);
    if (!$result) {
        return;
    }
    for (; !$result->EOF; $result->MoveNext()) {
        [$rid] = $result->fields;
        $allrids[] = ['rid'=> $rid];
    }
    $result->Close();

    //jojodee - we want to get all the rids that exist and may not be sequential,
    // and allocate first free number to the next rid available for the extension type

    $totalrids=count($allrids);
    $i=0;
    $nextregid=1;  //We want to start from ID=1 not 0
    for ($i = 0; $i < $totalrids; $i++) {
        if ($nextregid == ($allrids[$i]['rid'])) {
            $nextregid++;
        }
    }
    if ($nextregid == 0) {
        return;
    }

    return $nextregid;
}
