<?php

function release_adminapi_deletedoc($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check
    if (!isset($rdid)) {
        $msg = xarML('Invalid Parameter Count');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }

    // The user API function is called
    $link = xarModAPIFunc('release',
                          'user',
                          'getdoc',
                         array('rdid' => $rdid));

    if ($link == false) {
        $msg = xarML('No Such Release Doc Present');
        xarErrorSet(XAR_USER_EXCEPTION, 
                    'MISSING_DATA',
                     new DefaultUserException($msg));
        return; 
    }

    // Security Check
    if(!xarSecurityCheck('DeleteRelease')) return;

    // Get datbase setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $releasetable = $xartable['release_docs'];

    // Delete the item
    $query = "DELETE FROM $releasetable
            WHERE xar_rnid = ?";
    $result =& $dbconn->Execute($query,array($rdid));
    if (!$result) return;

    // Let any hooks know that we have deleted a link
    xarModCallHooks('item', 'delete', $rdid, '');

    // Let the calling process know that we have finished successfully
    return true;
}

?>
