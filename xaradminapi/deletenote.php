<?php
/**
 * Delete a note
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
 * Delete a note
 *
 * @param $rnid ID
 *
 * Original Author of file: John Cox via phpMailer Team
 * @author Release module development team
 */
function release_adminapi_deletenote($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check
    if (!isset($rnid)) {
        throw new BadParameterException(null, xarML('Invalid Parameter Count'));
    }

    // The user API function is called
    $link = xarMod::apiFunc(
        'release',
        'user',
        'getnote',
        ['rnid' => $rnid]
    );

    if ($link == false) {
        throw new EmptyParameterException(null, xarML('No Such Release Note Present'));
    }

    // Security Check
    if (!xarSecurity::check('ManageRelease')) {
        return;
    }

    // Get datbase setup
    $dbconn =& xarDB::getConn();
    $xartable =& xarDB::getTables();

    $releasenotetable = $xartable['release_notes'];

    // Delete the item
    $query = "DELETE FROM $releasenotetable
            WHERE xar_rnid = ?";
    $result =& $dbconn->Execute($query, [$rnid]);
    if (!$result) {
        return;
    }

    // Let any hooks know that we have deleted a link
    xarModHooks::call('item', 'delete', $rnid, '');

    // Let the calling process know that we have finished successfully
    return true;
}
