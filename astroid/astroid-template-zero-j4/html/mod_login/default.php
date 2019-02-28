<?php
/**
 * @package   Astroid Framework
 * @author    JoomDev https://www.joomdev.com
 * @copyright Copyright (C) 2009 - 2019 JoomDev.
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

defined('_JEXEC') or die;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Router\Route;

HTMLHelper::_('behavior.core');
HTMLHelper::_('behavior.keepalive');
HTMLHelper::_('script', 'system/fields/passwordview.min.js', array('version' => 'auto', 'relative' => true));

Text::script('JSHOW');
Text::script('JHIDE');
?>
<form  id="login-form" class="form-signin"  action="<?php echo Route::_('index.php', true); ?>" method="post">
   <?php if ($params->get('pretext')) : ?>
      <div class="pretext">
         <p class="mb-3 text-muted"><?php echo $params->get('pretext'); ?></p>
      </div>
   <?php endif; ?>
   <div id="form-login-username">
      <?php if (!$params->get('usetext', 0)) : ?>
         <label for="modlgn-username" class="sr-only"><?php echo Text::_('MOD_LOGIN_VALUE_USERNAME'); ?></label>
         <div class="input-group mb-3">
            <div class="input-group-prepend">
               <span class="input-group-text p-2"><span class="fa fa-user hasTooltip" title="<?php echo Text::_('MOD_LOGIN_VALUE_USERNAME'); ?>"></span></span>
            </div>
            <input id="modlgn-username" type="text" name="username" class="form-control p-2" tabindex="0" size="18" placeholder="<?php echo Text::_('MOD_LOGIN_VALUE_USERNAME'); ?>" />
         </div>
      <?php else : ?>
         <label for="modlgn-username" class="sr-only"><?php echo Text::_('MOD_LOGIN_VALUE_USERNAME'); ?></label>
         <input id="modlgn-username" type="text" name="username" class="form-control" tabindex="0" size="18" placeholder="<?php echo Text::_('MOD_LOGIN_VALUE_USERNAME'); ?>" />
      <?php endif; ?>
   </div>

   <div id="form-login-password">
      <?php if (!$params->get('usetext')) : ?>
         <label for="modlgn-passwd" class="sr-only"><?php echo Text::_('JGLOBAL_PASSWORD'); ?></label>
         <div class="input-group mb-3">
            <div class="input-group-prepend">
               <span class="input-group-text p-2"><span class="fa fa-lock hasTooltip" title="<?php echo JText::_('JGLOBAL_PASSWORD'); ?>"></span></span>
            </div>
            <input id="modlgn-passwd" type="password" name="password" class="form-control p-2" tabindex="0" size="18" placeholder="<?php echo JText::_('JGLOBAL_PASSWORD'); ?>" />
         </div>
      <?php else : ?>
         <label for="modlgn-passwd" class="sr-only"><?php echo Text::_('JGLOBAL_PASSWORD'); ?></label>
         <input id="modlgn-passwd" type="password" name="password" class="form-control" tabindex="0" size="18" placeholder="<?php echo Text::_('JGLOBAL_PASSWORD'); ?>" />
      <?php endif; ?>
   </div>

   <?php if (count($twofactormethods) > 1) : ?>
      <div id="form-login-secretkey">
         <?php if (!$params->get('usetext')) : ?>
            <label for="modlgn-secretkey" class="sr-only"><?php echo Text::_('JGLOBAL_SECRETKEY'); ?></label>

            <div class="input-group mb-3">
               <div class="input-group-prepend">
                  <span class="icon-star" title="<?php echo Text::_('JGLOBAL_SECRETKEY'); ?>"></span>
               </div>
               <input id="modlgn-secretkey" autocomplete="off" type="text" name="secretkey" class="form-control" tabindex="0" size="18" placeholder="<?php echo Text::_('JGLOBAL_SECRETKEY'); ?>" />
            </div>
         <?php else : ?>
            <label for="modlgn-secretkey" class="sr-only"><?php echo Text::_('JGLOBAL_SECRETKEY'); ?></label>
            <input id="modlgn-secretkey" autocomplete="off" type="text" name="secretkey" class="form-control" tabindex="0" size="18" placeholder="<?php echo Text::_('JGLOBAL_SECRETKEY'); ?>" />
         <?php endif; ?>
         <span class="btn hasTooltip" title="<?php echo Text::_('JGLOBAL_SECRETKEY_HELP'); ?>">
            <span class="icon-help"></span>
         </span>
      </div>
   <?php endif; ?>

   <?php if (PluginHelper::isEnabled('system', 'remember')) : ?>
      <div class="custom-control custom-checkbox checkbox mb-3">
         <input type="checkbox" class="custom-control-input" id="modlgn-remember" name="remember" value="yes">
         <label class="custom-control-label" for="modlgn-remember"><?php echo Text::_('MOD_LOGIN_REMEMBER_ME'); ?></label>
      </div>
   <?php endif; ?>
   <button class="btn btn-lg btn-primary btn-block" type="submit"><?php echo Text::_('JLOGIN'); ?></button>
   <?php if ($params->get('posttext')) : ?>
      <div class="posttext">
         <p class="my-3 text-muted"><?php echo $params->get('posttext'); ?></p>
      </div>
   <?php endif; ?>
   <input type="hidden" name="option" value="com_users" />
   <input type="hidden" name="task" value="user.login" />
   <input type="hidden" name="return" value="<?php echo $return; ?>" />
   <?php echo HTMLHelper::_('form.token'); ?>

   <?php $usersConfig = ComponentHelper::getParams('com_users'); ?>
   <ul class="list-group mt-3">
      <?php if ($usersConfig->get('allowUserRegistration')) : ?>
         <li class="list-group-item">
            <a href="<?php echo Route::_('index.php?option=com_users&view=registration'); ?>">
               <?php echo Text::_('MOD_LOGIN_REGISTER'); ?> <span class="icon-arrow-right"></span></a>
         </li>
      <?php endif; ?>
      <li class="list-group-item">
         <a href="<?php echo Route::_('index.php?option=com_users&view=remind'); ?>">
            <?php echo Text::_('MOD_LOGIN_FORGOT_YOUR_USERNAME'); ?></a>
      </li>
      <li class="list-group-item">
         <a href="<?php echo Route::_('index.php?option=com_users&view=reset'); ?>">
            <?php echo Text::_('MOD_LOGIN_FORGOT_YOUR_PASSWORD'); ?></a>
      </li>
   </ul>
</form>