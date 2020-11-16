<?php
/**
 * Display a release
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
 * Display a release
 *
 * @param rid ID
 *
 * Original Author of file: John Cox via phpMailer Team
 * @author Release module development team
 */
function release_user_adddocs()
{
    // Security Check
    if (!xarSecurityCheck('OverviewRelease')) {
        return;
    }
    if (!xarVarFetch('rid', 'isset', $rid, null, XARVAR_NOT_REQUIRED)) {
        return;
    }
    if (!xarVarFetch('phase', 'str:1:', $phase, null, XARVAR_NOT_REQUIRED)) {
        return;
    }
    if (!xarVarFetch('exttype', 'isset', $exttype, null, XARVAR_NOT_REQUIRED)) {
        return;
    }
    if (!xarVarFetch('eid', 'isset', $eid, null, XARVAR_NOT_REQUIRED)) {
        return;
    }

    $data['items'] = array();
    $data['rid'] = $rid;
    $data['eid'] = $eid;
    if (empty($phase)) {
        $phase = 'getmodule';
    }

    switch (strtolower($phase)) {
        case 'getmodule':
        default:
            // First we need to get the module that we are adding the release note to.
            // This will be done in several stages so that the information is accurate.

            $authid = xarSecGenAuthKey();
            $data = xarTplModule('release', 'user', 'adddocs_getmodule', array('authid'    => $authid));

            xarTplSetPageTitle(xarVarPrepForDisplay(xarML('Documentation')));

            break;

        case 'start':
            // First we need to get the module that we are adding the release note to.
            // This will be done in several stages so that the information is accurate.

           if (!xarVarFetch('rid', 'isset', $rid, null, XARVAR_NOT_REQUIRED)) {
               return;
           }

            // The user API function is called.
            $data = xarMod::apiFunc(
                'release',
                'user',
                'getid',
                array('eid' => $eid)
            );

            
            $uid = xarUser::getVar('id');

            if (($data['uid'] == $uid) or (xarSecurityCheck('EditRelease', 0))) {
                $message = '';
            } else {
                $message = xarML('You are not allowed to add documentation to this module');
            }

            //TODO FIX ME!!!
            if (empty($data['name'])) {
                $message = xarML('There is no assigned ID for your extension.');
                $data['name']='';
            }

            xarTplSetPageTitle(xarVarPrepForDisplay($data['name']));

            $authid = xarSecGenAuthKey();
            $data = xarTplModule(
                'release',
                'user',
                'adddocs_start',
                array('rid'       => $data['rid'],
                        'eid'       => $data['eid'],
                        'name'      => $data['name'],
                        'desc'      => $data['desc'],
                        'exttype'      => $data['exttype'],
                        'message'   => $message,
                        'authid'    => $authid)
            );

            break;

        case 'module':

            $data['mtype'] = 'mgeneral';
            $data['return'] = 'module';
            // The user API function is called.

            $items = xarMod::apiFunc(
                'release',
                'user',
                'getdocs',
                array('eid' => $eid,
                                          'exttype'=> $data['mtype'])
            );

            if (empty($items)) {
                $data['message'] = xarML('There is no general module documentation defined');
            }


            xarTplSetPageTitle(xarVarPrepForDisplay(xarML('General Information')));


            // Check individual permissions for Edit / Delete
            for ($i = 0; $i < count($items); $i++) {
                $item = $items[$i];

                $uid = xarUser::getVar('id');
                $items[$i]['docsf'] = nl2br(xarVarPrepHTMLDisplay($item['docs']));
            }


            $data['items'] = $items;
            $data['authid'] = xarSecGenAuthKey();
        
            break;
        
        case 'theme':

            $data['mtype'] = 'tgeneral';
            $data['return'] = 'theme';
            // The user API function is called.

            $items = xarMod::apiFunc(
                'release',
                'user',
                'getdocs',
                array('eid' => $eid,
                                          'exttype'=> $data['mtype'])
            );

            if (empty($items)) {
                $data['message'] = xarML('There is no general theme documentation defined');
            }

            
            xarTplSetPageTitle(xarVarPrepForDisplay(xarML('General Information')));


            // Check individual permissions for Edit / Delete
            for ($i = 0; $i < count($items); $i++) {
                $item = $items[$i];

                $uid = xarUser::getVar('id');
                $items[$i]['docsf'] = nl2br(xarVarPrepHTMLDisplay($item['docs']));
            }


            $data['items'] = $items;
            $data['authid'] = xarSecGenAuthKey();

            break;

        case 'blockgroups':

            $data['mtype'] = 'bgroups';
            $data['return'] = 'blockgroups';
            // The user API function is called.

            $items = xarMod::apiFunc(
                'release',
                'user',
                'getdocs',
                array('eid' => $eid,
                                          'type'=> $data['mtype'])
            );

            if (empty($items)) {
                $data['message'] = xarML('There is no block groups documentation defined');
            }

            
            xarTplSetPageTitle(xarVarPrepForDisplay(xarML('General Information')));


            // Check individual permissions for Edit / Delete
            for ($i = 0; $i < count($items); $i++) {
                $item = $items[$i];

                $uid = xarUser::getVar('id');
                $items[$i]['docsf'] = nl2br(xarVarPrepHTMLDisplay($item['docs']));
            }


            $data['items'] = $items;
            $data['authid'] = xarSecGenAuthKey();

            break;
        
        case 'blocks':

            $data['mtype'] = 'mblocks';
            $data['return'] = 'blocks';
            // The user API function is called.

            $items = xarMod::apiFunc(
                'release',
                'user',
                'getdocs',
                array('eid' => $eid,
                                          'exttype'=> $data['mtype'])
            );

            if (empty($items)) {
                $data['message'] = xarML('There is no blocks documentation defined');
            }

            
            xarTplSetPageTitle(xarVarPrepForDisplay(xarML('General Information')));


            // Check individual permissions for Edit / Delete
            for ($i = 0; $i < count($items); $i++) {
                $item = $items[$i];

                $uid = xarUser::getVar('id');
                $items[$i]['docsf'] = nl2br(xarVarPrepHTMLDisplay($item['docs']));
            }


            $data['items'] = $items;
            $data['authid'] = xarSecGenAuthKey();


            break;

        case 'hooks':

            $data['mtype'] = 'mhooks';
            $data['return'] = 'hooks';
            // The user API function is called.

            $items = xarMod::apiFunc(
                'release',
                'user',
                'getdocs',
                array('eid' => $eid,
                                          'exttype'=> $data['mtype'])
            );

            if (empty($items)) {
                $data['message'] = xarML('There is no hook documentation defined');
            }

            
            xarTplSetPageTitle(xarVarPrepForDisplay(xarML('General Information')));


            // Check individual permissions for Edit / Delete
            for ($i = 0; $i < count($items); $i++) {
                $item = $items[$i];

                $uid = xarUser::getVar('id');
                $items[$i]['docsf'] = nl2br(xarVarPrepHTMLDisplay($item['docs']));
            }


            $data['items'] = $items;
            $data['authid'] = xarSecGenAuthKey();

            break;

        case 'update':
            if (!xarVarFetch('rid', 'isset', $rid, null, XARVAR_NOT_REQUIRED)) {
                return;
            }
            if (!xarVarFetch('mtype', 'isset', $mtype, null, XARVAR_NOT_REQUIRED)) {
                return;
            }
            if (!xarVarFetch('title', 'str:1:', $title, null, XARVAR_NOT_REQUIRED)) {
                return;
            }
            if (!xarVarFetch('return', 'isset', $return, null, XARVAR_NOT_REQUIRED)) {
                return;
            }
            if (!xarVarFetch('doc', 'isset', $doc, null, XARVAR_NOT_REQUIRED)) {
                return;
            }
            if (!xarVarFetch('eid', 'isset', $eid, null, XARVAR_NOT_REQUIRED)) {
                return;
            }

           if (!xarSecConfirmAuthKey()) {
               return;
           }

           if (!xarSecurityCheck('EditRelease', 0)) {
               $approved = 1;
           } else {
               $approved = 2;
           }

            // The user API function is called.
            if (!xarMod::apiFunc(
                'release',
                'user',
                'createdoc',
                array('eid'         => $eid,
                                      'rid'         => $rid,
                                      'type'        => $mtype,
                                      'title'       => $title,
                                      'doc'         => $doc,
                                      'approved'    => $approved)
            )) {
                return;
            }

            xarController::redirect(xarModURL('release', 'user', 'adddocs', array('phase' => $return,
                                                                              'eid' => $eid)));

           $data = '';
            break;
    }
    
    return $data;
}
