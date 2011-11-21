<?php

/*
 * @package         plg_remotecredentialsserver
 * @author          Emerson Rocha Luiz - emerson at webdesign.eng.br - @fititnt -  http://fititnt.org
 * @copyright       Copyright (C) 2005 - 2011 Webdesign Assessoria em Tecnologia da Informacao.
 * @license         GNU General Public License version 3. See license.txt
 */
defined('_JEXEC') or die('Restricted access');

jimport('joomla.plugin.plugin');

class plgSystemRemotecRedentialsserver extends JPlugin {

    /**
     * Get or id, or username, or email and check if exist one valid user with
     * this credentials, and return one JSON encoded JUser object, but with
     * hash of password null
     */
    function onAfterRender() {

        $app = JFactory::getApplication();
        if ($app->isAdmin()) {//if is admin, exit plugin
            return true;
        }

        $tokenVar = JRequest::getCmd('tokenvar', 'rctoken');
        $token = JRequest::getCmd($tokenVar, NULL);
        
        if (!$token) {
            return true; //Token is not defined, exit plugin
        }

        $ipsString = JRequest::getCmd('ips', NULL);
        
        if ($ipsString) {
            $ips = explode(',', $ipsString);
            if (!in_array($_SERVER['REMOTE_ADDR'], $ips)) {
                return true; //If is defined remote IPs, but remote ip is not listed, exit plugin
            }
        }
        //var_dump($tokenVar);die('breakpoint time :D!');
        $id = JRequest::getCmd('id', NULL);
        $username = JRequest::getCmd('username', NULL);
        $email = JRequest::getCmd('email', NULL);
        $password = JRequest::getCmd('password', NULL);

        if ( !$id && !$username && !$email) { //No username and password? error
            $result = array('error' => 'No id, username or password given. Please inform at least one');
            echo json_encode($result);
            
            $app->close();
            return true; //Just to be sure
        }
        if (!$password) { //No username and password? error
            $result = array('error' => 'No password given');
            echo json_encode($result);
            $app->close();
            return true; //Just to be sure
        }

        //What will be used to login? Lets define here
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('id, password');
        $query->from('#__users');
        if ($id != "") {
            $query->where('id = ' . (int) $id); //Cast or not cast, thats the question
        } else if ($username  != "") {
            $query->where('username = ' . $db->quote($username) );
        } else {
            $query->where('email = ' . $db->quote($email) );
        }
        
        $db->setQuery($query);
        $result = $db->loadObject();
        //die($query);
        if ($result) {
            $parts = explode(':', $result->password);
            $crypt = $parts[0];
            $salt = @$parts[1];
            jimport('joomla.user.helper');//JUserHelper
            $testcrypt = JUserHelper::getCryptedPassword($password, $salt);
            if ($crypt == $testcrypt) {
                $user = JUser::getInstance($result->id); // Bring this in line with the rest of the system
                $user->password = NULL; //this will make not show hashed password
                echo json_encode($user);
                $app->close();
                return true; //Just to be sure
            } else {
                $result = array('error' => 'Password does not mach');
                echo json_encode($result);
                $app->close();
                return true; //Just to be sure
            }
        } else {
            $result = array('error' => 'User not found');
            echo json_encode($result);
            $app->close();
            return true; //Just to be sure
        }
    }
}