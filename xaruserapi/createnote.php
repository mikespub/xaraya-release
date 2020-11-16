<?php
/*
 * Create a release notification
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage release
 * @link http://xaraya.com/index.php/release/773.html
 */

function release_userapi_createnote($args)
{
    // Get arguments
    extract($args);

    // Argument check
    if ((!isset($rid)) ||
        (!isset($eid)) ||
        (!isset($exttype)) ||
        (!isset($version))) {
        throw new BadParameterException(null, xarML('Wrong arguments to release_userapi_create'));
    }

    // Get datbase setup
    $dbconn =& xarDB::getConn();
    $xartable =& xarDB::getTables();

    $releasetable = $xartable['release_notes'];

    $certified   = !empty($certified) ? $certified : '1';
    $approved    = !empty($approved)  ? $approved : '1';
    $priceterms  = !empty($priceterms)? $priceterms : '';
    $demolink    = !empty($demolink)? $demolink : '';
    $dllink      = !empty($dllink)? $dllink : '';
    $supportlink = !empty($supportlink)? $supportlink : '';
    $changelog   = !empty($changelog)? $changelog : '';
    $notes       = !empty($notes)? $notes : '';
    $exttype     = !empty($exttype)? $exttype : 1;
    $rstate      =  isset($rstate)? $rstate : 0;
    $usefeed     =  isset($usefeed)? $usefeed : 0;

    // Get next ID in table
    $nextId = $dbconn->GenId($releasetable);
    $time = time();
    $query = "INSERT INTO $releasetable (
                     xar_rnid,
                     xar_eid,
                     xar_rid,
                     xar_version,
                     xar_price,
                     xar_priceterms,
                     xar_demo,
                     xar_demolink,
                     xar_dllink,
                     xar_supported,
                     xar_supportlink,
                     xar_changelog,
                     xar_notes,
                     xar_time,
                     xar_certified,
                     xar_approved,
                     xar_rstate,
                     xar_usefeed,
                     xar_exttype
              )
            VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";

    $bindvars = array($nextId,(int)$eid,(int)$rid,$version,$price,$priceterms,$demo,$demolink,$dllink,$supported,
                      $supportlink,$changelog,$notes,$time,$certified,$approved,$rstate,(int)$usefeed,(int)$exttype);
    $result =&$dbconn->Execute($query, $bindvars);
    if (!$result) {
        return;
    }

    // Get the ID of the item that we inserted
    $rnid = $dbconn->PO_Insert_ID($releasetable, 'xar_rnid');

    // Let any hooks know that we have created a new user.
    xarModHooks::call('item', 'create', $rnid, 'rnid');

    // Return the id of the newly created user to the calling process
    return $rnid;
}
