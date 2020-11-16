<?php
/**
 * View notes
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
 * Display a release
 *
 * @param rid ID
 *
 * Original Author of file: John Cox via phpMailer Team
 * @author Release module development team
 */
function release_user_viewnotes()
{
    if (!xarVarFetch('startnum', 'str:1:', $startnum, '1', XARVAR_NOT_REQUIRED)) {
        return;
    }
    if (!xarVarFetch('phase', 'str:1:', $phase, 'all', XARVAR_NOT_REQUIRED)) {
        return;
    }
    if (!xarVarFetch('filter', 'str:1:', $filter, '', XARVAR_NOT_REQUIRED)) {
        return;
    }
    if (!xarVarFetch('exttype', 'str:1:', $exttype, '', XARVAR_NOT_REQUIRED)) {
        return;
    }
    // Security Check
    if (!xarSecurityCheck('OverviewRelease')) {
        return;
    }

    $uid = xarUser::getVar('id');
    $data['items'] = array();

    if (empty($phase)) {
        $phase = 'viewall';
    }
    $exttypes = xarMod::apiFunc('release', 'user', 'getexttypes');
    $data['exttypes'] = $exttypes;

    switch (strtolower($phase)) {

        case 'viewall':
        default:

            // The user API function is called.
            $items = xarMod::apiFunc(
                'release',
                'user',
                'getallnotes',
                array('startnum' => $startnum,
                                        'numitems' => xarModVars::get(
                                            'release',
                                            'itemsperpage'
                                        ),
                                        'approved' => 2,
                                        'usefeed'  => 1)
            );//only those that want to be on the feed
            if ($items == false) {
                $data['message'] = xarML('There are no releases based on your filters');
            }
            $phasedesc =xarML('All');
            break;

        case 'certified':

            // The user API function is called.
            $items = xarMod::apiFunc(
                'release',
                'user',
                'getallnotes',
                array('startnum' => $startnum,
                                        'numitems' => xarModVars::get(
                                            'release',
                                            'itemsperpage'
                                        ),
                                        'certified'=> 2)
            );

            if ($items == false) {
                $data['message'] = xarML('There are no releases based on your filters');
            }
            $phasedesc =xarML('Certified');
            break;

        case 'price':

            // The user API function is called.
            $items = xarMod::apiFunc(
                'release',
                'user',
                'getallnotes',
                array('startnum' => $startnum,
                                        'numitems' => xarModVars::get(
                                            'release',
                                            'itemsperpage'
                                        ),
                                        'price'    => 2)
            );
            
            if ($items == false) {
                $data['message'] = xarML('There are no releases based on your filters');
            }
            $phasedesc =xarML('Commercial');
            break;

        case 'free':

            // The user API function is called.
            $items = xarMod::apiFunc(
                'release',
                'user',
                'getallnotes',
                array('startnum' => $startnum,
                                        'numitems' => xarModVars::get(
                                            'release',
                                            'itemsperpage'
                                        ),
                                        'price'    => 1)
            );
            
            if ($items == false) {
                $data['message'] = xarML('There are no releases based on your filters');
            }
            $phasedesc =xarML('Free');
            break;

        case 'supported':

            // The user API function is called.
            $items = xarMod::apiFunc(
                'release',
                'user',
                'getallnotes',
                array('startnum' => $startnum,
                                        'numitems' => xarModVars::get(
                                            'release',
                                            'itemsperpage'
                                        ),
                                        'supported'=> 2)
            );
            
            if ($items == false) {
                $data['message'] = xarML('There are no releases based on your filters');
            }
            $phasedesc =xarML('Supported');
            break;
    }

    $numitems=count($items);
    // Check individual permissions for Edit / Delete
    for ($i = 0; $i < $numitems; $i++) {
        $item = $items[$i];

        // The user API function is called.
        $getid = xarMod::apiFunc(
            'release',
            'user',
            'getid',
            array('eid' => $items[$i]['eid'])
        );


        $items[$i]['displaylink'] =  xarModURL(
            'release',
            'user',
            'displaynote',
            array('rnid' => $item['rnid'])
        );

        $getuser = xarMod::apiFunc(
            'roles',
            'user',
            'get',
            array('uid' => $getid['uid'])
        );

        $items[$i]['contacturl'] = xarModURL(
            'roles',
            'user',
            'display',
            array('uid' => $getid['uid'])
        );

        $flipext = array_flip($exttypes);
        $items[$i]['exttype'] = xarVarPrepForDisplay($getid['exttype']);
        $item[$i]['exttypename']=array_search($getid['exttype'], $flipext);
        $items[$i]['class'] = xarVarPrepForDisplay($getid['class']);
        $items[$i]['regname'] = xarVarPrepForDisplay($getid['regname']);
        $items[$i]['rid'] = xarVarPrepForDisplay($getid['rid']);
        $items[$i]['displname'] = xarVarPrepForDisplay($getid['displname']);
        $items[$i]['realname'] = $getuser['name'];
        $items[$i]['desc'] = nl2br(xarVarPrepHTMLDisplay($getid['desc']));
        $items[$i]['notes'] = nl2br(xarVarPrepHTMLDisplay($item['notes']));


        //Add pager
        $data['pager'] = xarTplGetPager(
            $startnum,
            xarMod::apiFunc('release', 'user', 'countnotes', array('phase'=>$phase)),
            xarModURL('release', 'user', 'viewnotes', array('startnum' => '%%','phase'=>$phase,
                                                                           'filter'=>$filter,
                                                                            'exttype' =>$exttype)),
            xarModUserVars::get('release', 'itemsperpage', $uid)
        );
    }


    $phase=strtolower($phase);
    $data['phase'] = $phasedesc;
    // Add the array of items to the template variables
    $data['items'] = $items;

    // Return the template variables defined in this function
    return $data;
}
