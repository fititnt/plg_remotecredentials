<?php

/**
 * @package     Remote Credentials
 * @author      Emerson Rocha Luiz - emerson at webdesign.eng.br - @fititnt -  http://fititnt.org
 * @copyright   Copyright (C) 2005 - 2011 Webdesign Assessoria em Tecnologia da Informacao.
 * @license     GNU General Public License version 3. See license.txt
 */
// No direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.plugin.plugin');

class plgAuthenticationRemotecredentials extends JPlugin {
    /*
     * For now, just enable login with just email
     */

    function onUserAuthenticate($credentials, $options, &$response) {

        jimport('joomla.user.helper');

        $response->type = 'remotecredentials';
        if (empty($credentials['password'])) {
            $response->status = JAUTHENTICATE_STATUS_FAILURE;
            $response->error_message = JText::_('JGLOBAL_AUTH_EMPTY_PASS_NOT_ALLOWED');
            return false;
        }

        //...

        
        // Success!
        $response->status = JAUTHENTICATE_STATUS_SUCCESS;
        $response->error_message = '';

        $response->status = JAUTHENTICATE_STATUS_SUCCESS;
        $response->error_message = '';
    }

}
