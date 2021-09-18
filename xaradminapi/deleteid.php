<?php
/**
 * Delete an ID
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
 * Delete an ID
 *
 * @param $rid ID
 *
 * Original Author of file: John Cox via phpMailer Team
 * @author Release module development team
 */
function release_adminapi_deleteid($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check
    if (!isset($rid)) {
        throw new BadParameterException(null, xarML('Invalid Parameter Count'));
    }

    // The user API function is called
    $link = xarMod::apiFunc(
        'release',
        'user',
        'getid',
        ['eid' => $eid]
    );

    if ($link == false) {
        throw new EmptyParameterException(null, xarML('No Such Release ID Present'));
    }

    // Security Check
    if (!xarSecurity::check('ManageRelease')) {
        return;
    }

    // Get datbase setup
    $dbconn =& xarDB::getConn();
    $xartable =& xarDB::getTables();

    $releasetable = $xartable['release_id'];

    // Delete the item
    $query = "DELETE FROM $releasetable
            WHERE xar_eid = ?";
    $result =& $dbconn->Execute($query, [$eid]);
    if (!$result) {
        return;
    }

    // Let any hooks know that we have deleted a link
    xarModHooks::call('item', 'delete', $eid, '');

    // Let the calling process know that we have finished successfully
    return true;
}
