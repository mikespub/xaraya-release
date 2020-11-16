<?php
/**
 * Modify a Note
 *
 * @package modules
 * @subpackage release
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @link http://xaraya.com/index.php/release/773.html
 */
/**
 * Modify a note
 *
 * @param $rnid ID
 *
 * Original Author of file: John Cox via phpMailer Team
 * @author Release module development team
 */
function release_admin_modifynote()
{
    // Security Check
    if (!xarSecurity::check('EditRelease')) {
        return;
    }

    if (!xarVar::fetch('rnid', 'id', $rnid)) {
        return;
    }
    if (!xarVar::fetch('phase', 'str:1:100', $phase, 'modify', xarVar::NOT_REQUIRED)) {
        return;
    }

    // The user API function is called.
    $data = xarMod::apiFunc(
        'release',
        'user',
        'getnote',
        array('rnid' => $rnid)
    );
    $eid = $data['eid'];

    if ($data == false) {
        return;
    }

    switch (strtolower($phase)) {

        case 'modify':
        default:

            // The user API function is called.
            $id = xarMod::apiFunc(
                'release',
                'user',
                'getid',
                array('eid' => $data['eid'])
            );

            if ($id == false) {
                return;
            }

            // The user API function is called.
            $user = xarMod::apiFunc(
                'roles',
                'user',
                'get',
                array('uid' => $id['uid'])
            );

            if ($id == false) {
                return;
            }

            $stateoptions=array();
            $stateoptions[0] = xarML('Planning');
            $stateoptions[1] = xarML('Alpha');
            $stateoptions[2] = xarML('Beta');
            $stateoptions[3] = xarML('Production/Stable');
            $stateoptions[4] = xarML('Mature');
            $stateoptions[5] = xarML('Inactive');

            foreach ($stateoptions as $key => $value) {
                if ($key==$data['rstate']) {
                    $rstatesel=$stateoptions[$key];
                }
            }
            $data['rid']=$id['rid'];
            $data['rstatesel']=$rstatesel;
            $data['stateoptions']=$stateoptions;
            $data['regname'] = $id['regname'];
            $data['exttype'] = $id['exttype'];
            $data['username'] = $user['name'];
            $data['changelogf'] = nl2br($data['changelog']);
            $data['notesf'] = nl2br($data['notes']);
            $data['authid'] = xarSec::genAuthKey();

            $exttypes = xarMod::apiFunc('release', 'user', 'getexttypes');
            $data['exttypes']=$exttypes;
            foreach ($exttypes as $k=>$v) {
                if ($data['exttype']==$k) {
                    $data['exttypename']=$v;
                }
            }

            break;

        case 'update':
          if (!xarVar::fetch('eid', 'int:1:', $eid, null, xarVar::NOT_REQUIRED)) {
              return;
          }
           if (!xarVar::fetch('rid', 'int:1:', $rid, null, xarVar::NOT_REQUIRED)) {
               return;
           }
           if (!xarVar::fetch('regname', 'str:1:', $regname, null, xarVar::NOT_REQUIRED)) {
               return;
           };
           if (!xarVar::fetch('version', 'str:1:', $version, null, xarVar::NOT_REQUIRED)) {
               return;
           }
           if (!xarVar::fetch('pricecheck', 'int:1:2', $pricecheck, null, xarVar::NOT_REQUIRED)) {
               return;
           }
           if (!xarVar::fetch('supportcheck', 'int:1:2', $supportcheck, null, xarVar::NOT_REQUIRED)) {
               return;
           }
           if (!xarVar::fetch('democheck', 'int:1:2', $democheck, null, xarVar::NOT_REQUIRED)) {
               return;
           }
           if (!xarVar::fetch('dllink', 'str:1:', $dllink, '', xarVar::NOT_REQUIRED)) {
               return;
           }
           if (!xarVar::fetch('price', 'float', $price, 0, xarVar::NOT_REQUIRED)) {
               return;
           }
           if (!xarVar::fetch('demolink', 'str:1:254', $demolink, '', xarVar::NOT_REQUIRED)) {
               return;
           }
           if (!xarVar::fetch('supportlink', 'str:1:254', $supportlink, '', xarVar::NOT_REQUIRED)) {
               return;
           }
           if (!xarVar::fetch('changelog', 'str:1:', $changelog, '', xarVar::NOT_REQUIRED)) {
               return;
           }
           if (!xarVar::fetch('notes', 'str:1:', $notes, '', xarVar::NOT_REQUIRED)) {
               return;
           }
           if (!xarVar::fetch('certified', 'int:1:2', $certified, 1, xarVar::NOT_REQUIRED)) {
               return;
           }
           if (!xarVar::fetch('approved', 'int:1:2', $approved, 1, xarVar::NOT_REQUIRED)) {
               return;
           }
           if (!xarVar::fetch('rstate', 'int:0:6', $rstate, 0, xarVar::NOT_REQUIRED)) {
               return;
           }
           if (!xarVar::fetch('usefeedchecked', 'checkbox', $usefeedchecked, false, xarVar::NOT_REQUIRED)) {
               return;
           }
           if (!xarVar::fetch('enotes', 'str:0:', $enotes, '', xarVar::NOT_REQUIRED)) {
               return;
           }
           if (!xarVar::fetch('newtime', 'isset', $newtime, '', xarVar::NOT_REQUIRED)) {
               return;
           }
            // Confirm authorisation code
            if (!xarSec::confirmAuthKey()) {
                return;
            }
            $usefeed = $usefeedchecked? 1: 0;
            $newtime = strtotime($newtime);
            if ($newtime >0) {
                $newtime= $newtime- xarMLS::userOffset($newtime) * 3600;
            } else {
                $newtime= $data['time'];
            }

            // The user API function is called.
            if (!xarMod::apiFunc(
                'release',
                'admin',
                'updatenote',
                array('eid'         => $eid,
                                      'rid'         => $rid,
                                      'rnid'        => $rnid,
                                      'version'     => $version,
                                      'time'        => $newtime,
                                      'price'       => $pricecheck,
                                      'supported'   => $supportcheck,
                                      'demo'        => $democheck,
                                      'dllink'      => $dllink,
                                      'priceterms'  => $price,
                                      'demolink'    => $demolink,
                                      'supportlink' => $supportlink,
                                      'changelog'   => $changelog,
                                      'notes'       => $notes,
                                      'exttype'     => $exttype,
                                      'enotes'      => $enotes,
                                      'certified'   => $certified,
                                      'approved'    => $approved,
                                      'rstate'      => $rstate,
                                      'usefeed'     => $usefeed)
            )) {
                return;
            }

            xarController::redirect(xarController::URL('release', 'user', 'displaynote', array('rnid'=>$rnid)));

            return true;

            break;
    }

    return $data;
}
