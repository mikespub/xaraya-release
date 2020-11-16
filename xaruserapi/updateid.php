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
 * @TODO
 */
function release_userapi_updateid($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check
    if ((!isset($eid)) ||
        (!isset($uid)) ||
        (!isset($regname)) ||
        (!isset($displname)) ||
        (!isset($exttype)) ||
        (!isset($class))) {
        throw new BadParameterException(null, xarML('Invalid Parameter Count'));
    }

    // The user API function is called
    $link = xarMod::apiFunc(
        'release',
        'user',
        'getid',
        array('eid' => $eid)
    );

    if ($link == false) {
        throw new BadParameterException(null, xarML('No Such Release ID Present'));
    }

    //this should not change once registered
    if (!isset($regtime)) {
        $regtime=$link['regtime'];
    }
    $modified = time();

    // Security Check
    if (!xarSecurity::check('OverviewRelease')) {
        return;
    }

    // Get database setup
    $dbconn =& xarDB::getConn();
    $xartable =& xarDB::getTables();

    if (empty($approved)) {
        $approved = '1';
    }

    $releasetable = $xartable['release_id'];

    // Update the link
    $query = "UPDATE $releasetable
            SET xar_uid       = ?,
                xar_regname   = ?,
                xar_displname = ?,
                xar_class     = ?,
                xar_desc      = ?,
                xar_certified = ?,
                xar_approved  = ?,
                xar_rstate    = ?,
                xar_regtime   = ?,
                xar_modified  = ?,
                xar_members   = ?,
                xar_scmlink   = ?,
                xar_openproj  = ?,
                xar_exttype   = ?
            WHERE xar_eid     = ?";
    $bindvars = array((int)$uid,$regname,$displname,$class,$desc,$certified,$approved,$rstate,
                      $regtime, $modified, $members, $scmlink, $openproj, (int)$exttype,(int)$eid);
    $result =& $dbconn->Execute($query, $bindvars);
    if (!$result) {
        return;
    }

    if (empty($cids)) {
        $cids = array();
    }
    $args['module'] = 'release';
    $args['cids'] = $cids;

    // Let the calling process know that we have finished successfully

    xarModHooks::call('item', 'update', $eid, $args);

    // Return the id of the newly created user to the calling process
    return $eid;
}
