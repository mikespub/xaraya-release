<?php

function release_user_display()
{
    // Security Check
    if(!xarSecurityCheck('OverviewRelease')) return;

    list($rid,
         $startnum,
         $phase) = xarVarCleanFromInput('rid',
                                        'startnum',
                                        'phase');

    // The user API function is called. 
    $id = xarModAPIFunc('release',
                         'user',
                         'getid',
                          array('rid' => $rid));


    $getuser = xarModAPIFunc('roles',
                             'user',
                             'get',
                              array('uid' => $id['uid']));

    if (empty($phase)){
        $phase = 'view';
    }

    $stateoptions=array();
    $stateoptions[0] = xarML('Planning');
    $stateoptions[1] = xarML('Alpha');
    $stateoptions[2] = xarML('Beta');
    $stateoptions[3] = xarML('Production/Stable');
    $stateoptions[4] = xarML('Mature');
    $stateoptions[5] = xarML('Inactive');

    switch(strtolower($phase)) {

        case 'view':
        default:

            $hooks = xarModCallHooks('item',
                                     'display',
                                     $rid,
                                     array('module' => 'release',
                                           'itemtype'  => '1',
                                           'returnurl' => xarModURL('release',
                                                                    'user',
                                                                    'display',
                                                                     array('rid' => $rid))
                                          ),
                                     'release'
                                     );

            if (empty($hooks)) {
                $data['hooks'] = '';
            } elseif (is_array($hooks)) {
                $data['hooks'] = join('',$hooks);
            } else {
                $data['hooks'] = $hooks;
            }
            $data['version'] = 0;
            $data['docs'] = 0;
            $data['general'] = 2;
            break;
        
        case 'version':
            // The user API function is called.
            $items = array();
            $items = xarModAPIFunc('release',
                                   'user',
                                   'getallnotes',
                                  array('startnum' => $startnum,
                                        'numitems' => xarModGetVar('release',
                                                                  'itemsperpage'),
                                        'rid' => $rid));

            if (empty($items)){
                $data['message'] = xarML('There is no version history on this module');
            }

            // Check individual permissions for Edit / Delete
            for ($i = 0; $i < count($items); $i++) {
                $item = $items[$i];

                // The user API function is called.
                $getid = xarModAPIFunc('release',
                                       'user',
                                       'getid',
                                       array('rid' => $items[$i]['rid']));

                $items[$i]['type'] = xarVarPrepForDisplay($getid['type']);
                $items[$i]['name'] = xarVarPrepForDisplay($getid['name']);
                $items[$i]['displaylink'] =  xarModURL('release',
                                                  'user',
                                                  'displaynote',
                                                   array('rnid' => $items[$i]['rnid']));

                $getuser = xarModAPIFunc('roles',
                                         'user',
                                         'get',
                                          array('uid' => $getid['uid']));

                $items[$i]['contacturl'] = xarModURL('roles',
                                                     'user',
                                                     'display',
                                                      array('uid' => $getid['uid']));


                $items[$i]['realname'] = $getuser['name'];
                $items[$i]['desc'] = xarVarPrepForDisplay($getid['desc']);

                if ($items[$i]['certified'] == 2){
                    $items[$i]['certifiedstatus'] = xarML('Yes');
                } else {
                   $items[$i]['certifiedstatus'] = xarML('No');
                }
                $items[$i]['changelog'] = nl2br($items[$i]['changelog']);
                $items[$i]['notes'] = nl2br($items[$i]['notes']);

                $items[$i]['comments'] = xarModAPIFunc('comments',
                                                       'user',
                                                       'get_count',
                                                       array('modid' => xarModGetIDFromName('release'),
                                                             'objectid' => $item['rnid']));
                
                if (!$items[$i]['comments']) {
                    $items[$i]['comments'] = '0';
                } elseif ($items[$i]['comments'] == 1) {
                    $items[$i]['comments'] .= ' ';
                } else {
                    $items[$i]['comments'] .= ' ';
                }

               $items[$i]['hitcount'] = xarModAPIFunc('hitcount',
                                                       'user',
                                                       'get',
                                                       array('modname' => 'release',
                                                             'objectid' => $item['rnid']));
                
                if (!$items[$i]['hitcount']) {
                    $items[$i]['hitcount'] = '0';
                } elseif ($items[$i]['hitcount'] == 1) {
                    $items[$i]['hitcount'] .= ' ';
                } else {
                    $items[$i]['hitcount'] .= ' ';
                }

                //Get the status update of each release
                foreach ($stateoptions as $key => $value) {
                    if ($key==$items[$i]['rstate']) {
                       $rstatesel=$stateoptions[$key];
                    }
                }
                $items[$i]['rstatesel']=$rstatesel;


            }


            $data['version'] = 2;
            $data['items'] = $items;
            $data['general'] = 2;
            break;


        case 'docsmodule':
            $data['mtype'] = 'mgeneral';
            // The user API function is called. 

            $items = xarModAPIFunc('release',
                                   'user',
                                   'getdocs',
                                    array('rid' => $rid,
                                          'type'=> $data['mtype']));

            if (empty($items)){
                $data['message'] = xarML('There is no general module documentation defined');
            }

            // Check individual permissions for Edit / Delete
            for ($i = 0; $i < count($items); $i++) {
                $item = $items[$i];

                $uid = xarUserGetVar('uid');
                $items[$i]['docsf'] = nl2br(xarVarPrepHTMLDisplay($item['docs']));
                $items[$i]['docurl'] = xarModURL('release',
                                                 'user',
                                                 'getdoc',
                                                 array('rdid' => $item['rdid']));
            }


            $data['items'] = $items;

            $data['version'] = 0;
            $data['docs'] = 2;
            $data['general'] = 0;
       
            break;
        
        case 'docstheme':

            $data['mtype'] = 'tgeneral';
            // The user API function is called. 

            $items = xarModAPIFunc('release',
                                   'user',
                                   'getdocs',
                                    array('rid' => $rid,
                                          'type'=> $data['mtype']));

            if (empty($items)){
                $data['message'] = xarML('There is no general theme documentation defined');
            }
             $numitems=count($items);
            // Check individual permissions for Edit / Delete
            for ($i = 0; $i < $numitems; $i++) {
                $item = $items[$i];

                $uid = xarUserGetVar('uid');
                $items[$i]['docsf'] = nl2br(xarVarPrepHTMLDisplay($item['docs']));
                $items[$i]['docurl'] = xarModURL('release',
                                                 'user',
                                                 'getdoc',
                                                 array('rdid' => $item['rdid']));
            }


            $data['items'] = $items;

            $data['version'] = 0;
            $data['docs'] = 2;
            $data['general'] = 0;
            break;

        case 'docsblockgroups':

            $data['mtype'] = 'bgroups';
            // The user API function is called. 

            $items = xarModAPIFunc('release',
                                   'user',
                                   'getdocs',
                                    array('rid' => $rid,
                                          'type'=> $data['mtype']));

            if (empty($items)){
                $data['message'] = xarML('There is no block groups documentation defined');
            }

            
            // Check individual permissions for Edit / Delete
            for ($i = 0; $i < count($items); $i++) {
                $item = $items[$i];

                $uid = xarUserGetVar('uid');
                $items[$i]['docsf'] = nl2br(xarVarPrepHTMLDisplay($item['docs']));
                $items[$i]['docurl'] = xarModURL('release',
                                                 'user',
                                                 'getdoc',
                                                 array('rdid' => $item['rdid']));
            }


            $data['items'] = $items;

            $data['version'] = 0;
            $data['docs'] = 2;
            $data['general'] = 0;

            break;
        
        case 'docsblocks':

            $data['mtype'] = 'mblocks';
            // The user API function is called. 

            $items = xarModAPIFunc('release',
                                   'user',
                                   'getdocs',
                                    array('rid' => $rid,
                                          'type'=> $data['mtype']));

            if (empty($items)){
                $data['message'] = xarML('There is no blocks documentation defined');
            }

            // Check individual permissions for Edit / Delete
            for ($i = 0; $i < count($items); $i++) {
                $item = $items[$i];

                $uid = xarUserGetVar('uid');
                $items[$i]['docsf'] = nl2br(xarVarPrepHTMLDisplay($item['docs']));
                $items[$i]['docurl'] = xarModURL('release',
                                                 'user',
                                                 'getdoc',
                                                 array('rdid' => $item['rdid']));
            }


            $data['items'] = $items;

            $data['version'] = 0;
            $data['docs'] = 2;
            $data['general'] = 0;
  return $data;
            break;

        case 'docshooks':

            $data['mtype'] = 'mhooks';
            // The user API function is called. 

            $items = xarModAPIFunc('release',
                                   'user',
                                   'getdocs',
                                    array('rid' => $rid,
                                          'type'=> $data['mtype']));

            if (empty($items)){
                $data['message'] = xarML('There is no hooks documentation defined');
            }

            // Check individual permissions for Edit / Delete
            for ($i = 0; $i < count($items); $i++) {
                $item = $items[$i];

                $uid = xarUserGetVar('uid');
                $items[$i]['docsf'] = nl2br(xarVarPrepHTMLDisplay($item['docs']));
                $items[$i]['docurl'] = xarModURL('release',
                                                 'user',
                                                 'getdoc',
                                                 array('rdid' => $item['rdid']));
            }


            $data['items'] = $items;

            $data['version'] = 0;
            $data['docs'] = 2;
            $data['general'] = 0;

            break;

    }
      foreach ($stateoptions as $key => $value) {
         if ($key==$id['rstate']) {
             $rstatesel=$stateoptions[$key];
         }
    }

     $data['rstatesel']=$rstatesel;
     $data['stateoptions']=$stateoptions;

// Version History
// View Docs
// Comment on docs
    $time=time();
    $data['time']=$time;
    $data['desc'] = nl2br($id['desc']);
    $data['name'] = $id['name'];
    $data['type'] = $id['type'];
    $data['contacturl'] = xarModUrl('roles', 'user', 'email', array('uid' => $id['uid']));
    $data['realname'] = $getuser['name'];
    $data['rid'] = $rid;
    $data['startnum']=$startnum;

return $data;
}

?>
