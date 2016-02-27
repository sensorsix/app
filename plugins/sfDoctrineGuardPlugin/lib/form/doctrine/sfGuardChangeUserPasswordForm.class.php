<?php

/**
 * sfGuardChangeUserPasswordForm for changing a users password
 *
 * @package    sfDoctrineGuardPlugin
 * @subpackage form
 * @author     Jonathan H. Wage <jonwage@gmail.com>
 * @version    SVN: $Id: sfGuardChangeUserPasswordForm.class.php 23536 2009-11-02 21:41:21Z Kris.Wallsmith $
 */
class sfGuardChangeUserPasswordForm extends BasesfGuardChangeUserPasswordForm
{
    protected $formatterName = 'bootstrap_horizontal';

    /**
     * @see sfForm
     */
    public function configure()
    {
    }
}