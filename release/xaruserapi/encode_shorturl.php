<?php
/**
 * File: $Id:
 * 
 * Support for short URLs (user functions)
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Release
 * @author jojodee
 */

/**
 * return the path for a short URL to xarModURL for this module
 * 
 * @author jojodee
 * @param  $args the function and arguments passed to xarModURL
 * @returns string
 * @return path to be added to index.php for a short URL, or empty if failed
 */
function release_userapi_encode_shorturl($args)
{ 
    // Get arguments from argument array
    extract($args);
    // Check if we have something to work with
    if (!isset($func)) {
        return;
    }
    // default path is empty -> no short URL
    $path = '';
    // if we want to add some common arguments as URL parameters below
    $join = '?';
    // we can't rely on xarModGetName() here -> you must specify the modname !
    $module = 'release';

    if ($func == 'main') {
        $path = '/' . $module . '/';
    } elseif ($func == 'view') {
        $path = '/' . $module . '/view.html';
 
    } elseif ($func == 'display') {
        // check for required parameters
        if (isset($rid) && is_numeric($rid)) {
            $path = '/' . $module . '/' . $rid . '.html';
        } else {

        }
    } elseif ($func == 'viewnotes') {
        $path = '/' . $module . '/viewnotes.html';

    } elseif ($func == 'displaynote') {
        // check for required parameters
        if (isset($rnid) && is_numeric($rnid)) {
            $path = '/' . $module . '/displaynote/' . $rnid . '.html';
        } else {
        }

    } elseif ($func == 'addnotes') {
        // check for required parameters
        if (isset($phase) && ($phase=='start')) {
            $path = '/' . $module . '/addnotes/start/' . $rid . '.html';
        } else {
             $path = '/' . $module . '/addnotes.html';
        }
    } elseif ($func == 'addid') {
        $path = '/' . $module . '/addid.html';

    } elseif ($func == 'modifyid') {
        // check for required parameters
        if (isset($rid) && is_numeric($rid)) {
            $path = '/' . $module . '/modifyid/' . $rid . '.html';
        } else {
        }

    } else {
        // -> don't create a path here
    }
    // add some other module arguments as standard URL parameters
    if (!empty($path)) {
        if (isset($startnum)) {
            $path .= $join . 'startnum=' . $startnum;
            $join = '&';
        } 
        if (!empty($catid)) {
            $path .= $join . 'catid=' . $catid;
            $join = '&';
        } elseif (!empty($cids) && count($cids) > 0) {
            if (!empty($andcids)) {
                $catid = join('+', $cids);
            } else {
                $catid = join('-', $cids);
            } 
            $path .= $join . 'catid=' . $catid;
            $join = '&';
        } 
    } 

    return $path;
} 

?>
