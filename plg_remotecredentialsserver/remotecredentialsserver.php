<?php

/*
 * @package         plg_remotecredentialsserver
 * @author          Emerson Rocha Luiz - emerson at webdesign.eng.br - @fititnt -  http://fititnt.org
 * @copyright       Copyright (C) 2005 - 2011 Webdesign Assessoria em Tecnologia da Informacao.
 * @license         GNU General Public License version 3. See license.txt
 */
defined('_JEXEC') or die('Restricted access');

jimport('joomla.plugin.plugin');

class plgSystemRequirelogin extends JPlugin {

    /**
     * Do something onAfterRender
     * 
     * @todo: is really on this System function the better way to do it? Hummm
     */
    function onAfterRender() {
        $user = JFactory::getuser();
        $app = JFactory::getApplication();
        $options = array();

        if ($app->isAdmin() && !$user->guest) {
            return true;
        }

        //@todo: do one check here if is Google, or any other type of visitor
        //Check if is desired component
        if ($this->params->get('option-include', NULL)) {
            $optionInclude = trim($this->params->get('option-include', NULL));
            if ($optionInclude != '*') {//Is *? so continue
                $options = explode(',', $this->params->get('option-include', NULL));
                if (isset($options) && !in_array(JRequest::getCmd('option'), $options)) {
                    return true;
                }
            }
        }
        //@todo: Maybe make one way to just exclude witch component must not be show?
        //@todo: do some check for if is one item of one desired group of category
        //@todo: ...

        if (JRequest::getCmd('option') != 'com_users') {
            $app->redirect(JRoute::_('index.php?option=com_users&view=login'), $this->params->get('message', NULL));
        }
        return true;
    }

}