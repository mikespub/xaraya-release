<?php
/**
 * Latest Projects
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Release Module
 * @link http://xaraya.com/index.php/release/773.html
 */

/**
 * initialise block
 * @return array
 */
function release_latestprojectsblock_init()
{
    return [
        'numitems' => 5,
        'showonlists'=>0,
        'nocache' => 0, // cache by default
        'pageshared' => 1,
        'usershared' => 1, // share across group members
        'cacheexpire' => null,
    ];
}

/**
 * get information on block
 * @return array
 */
function release_latestprojectsblock_info()
{
    // Values
    return ['text_type' => 'Latest Extensions',
        'module' => 'release',
        'text_type_long' => 'Show latest registered extensions',
        'allow_multiple' => true,
        'form_content' => false,
        'form_refresh' => false,
        'show_preview' => true, ];
}

/**
 * display block
 * @return array
 */
function release_latestprojectsblock_display($blockinfo)
{
    // Security check
    if (!xarSecurity::check('ReadReleaseBlock', 1, 'Block', $blockinfo['title'])) {
        return;
    }

    // Get variables from content block.

    if (!is_array($blockinfo['content'])) {
        $vars = @unserialize($blockinfo['content']);
    } else {
        $vars = $blockinfo['content'];
    }

    // Defaults
    if (empty($vars['numitems'])) {
        $vars['numitems'] = 5;
    }
    if (!isset($vars['showonlists']) || empty($vars['showonlists'])) {
        $vars['showonlists'] = 1;
    }
    $usefeed = ($vars['showonlists'] == 1) ? 1 : null; //null - no selection on usefeed, 1 select for lists
    // The API function is called to get all notes
    $items = xarMod::apiFunc(
        'release',
        'user',
        'getallrids',
        ['numitems' => $vars['numitems'],
                           'openproj' => $vars['showonlists'],
                           'sort'     => 'regtime', ]
    );

    if (!isset($items) && xarCurrentErrorType() != XAR_NO_EXCEPTION) {
        return;
    } // throw back

    // TODO: check for conflicts between transformation hook output and xarVar::prepForDisplay
    // Loop through each item and display it.
    $data['items'] = [];
    if (is_array($items)) {
        foreach ($items as $item) {
            if (xarSecurity::check('OverviewRelease', 0)) {
                $item['link'] = xarController::URL(
                    'release',
                    'user',
                    'display',
                    ['eid' => $item['eid']]
                );

            // Security check 2 - else only display the item name (or whatever is
                // appropriate for your module)
            } else {
                $item['link'] = '';
            }
            $exttypes = xarMod::apiFunc('release', 'user', 'getexttypes');
            foreach ($exttypes as $k=>$v) {
                if ($item['exttype'] == $k) {
                    $item['exttypename'] = $v;
                }
            }

            $roles = new xarRoles();
            $role = $roles->getRole($item['uid']);
            $item['author']= $role->getName();
            $item['authorlink']=xarController::URL('roles', 'user', 'display', ['uid'=>$item['uid']]);
            // Add this item to the list of items to be displayed
            $data['items'][] = $item;
        }
    }
    $data['blockid'] = $blockinfo['bid'];
    // Now we need to send our output to the template.
    // Just return the template data.
    $blockinfo['content'] = $data;

    return $blockinfo;
}
