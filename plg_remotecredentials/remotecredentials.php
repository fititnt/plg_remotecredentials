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


        $tokenVar = $this->params->get('tokenvar', 'rctoken');
        $token = $this->params->get('token', NULL);
        $url = $this->params->get('url', NULL);

        if (!$token || !$url) {
            return true; //Token or URL is not defined, exit plugin
        }
        $urlQuery = http_build_query(array(
            $tokenVar => $token,
            'username' => $credentials['username'],
            'password' => $credentials['password']
                ));

        if (strpos($url, '?') === FALSE) { //if does not have ? on url, add it
            $finalUrl = $url . '?' . $urlQuery;
        } else {
            $finalUrl = $url . $urlQuery;
        }

        $contents = $this->_getUrlContents($finalUrl);

        $result = json_decode($contents);

        if (!isset($result->name) || !isset($result->username) || !isset($result->email)) {
            $response->status = JAUTHENTICATE_STATUS_FAILURE;
            return;
        } else {
            $response->name = $result->name;
            $response->username = $result->username;
            $response->email = $result->email;
            $response->password = $password;
            $response->status = JAUTHENTICATE_STATUS_SUCCESS;
            $response->error_message = '';
        }
    }

    /*
     * Return contents of url
     * @author      Emerson Rocha Luiz
     * @var         string      $url
     * @var         string      $certificate path to certificate if is https URL
     * @return      string
     */

    protected function _getUrlContents($url, $certificate = FALSE) {
        //$page = file_get_contents($url);            
        $ch = curl_init(); //Inicializar a sessao           
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //Retorne os dados em vez de imprimir em tela
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $certificate); //Check certificate if is SSL, default FALSE
        curl_setopt($ch, CURLOPT_URL, $url); //Setar URL
        $content = curl_exec($ch); //Execute
        curl_close($ch); //Feche          

        return $content;
    }

}
