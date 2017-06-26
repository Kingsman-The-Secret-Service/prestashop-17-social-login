<?php
/**
* 2007-2015 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2015 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

class Kk_Sociallogin extends Module
{
    protected $config_form = false;

    public function __construct()
    {
        $this->name = 'kk_sociallogin';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'Kaviarasan K K';
        $this->need_instance = 1;

        $this->bootstrap = true;
        parent::__construct();

        $this->displayName = $this->l('Social Login Google+, Facebook');
        $this->description = $this->l('Customer Social Login Google+, Facebook');

        $this->confirmUninstall = $this->l('Are you sure you want to uninstall module?');

        $this->ps_versions_compliancy = array('min' => '1.7', 'max' => _PS_VERSION_);
    }

    /**
     * Don't forget to create update methods if needed:
     * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
     */
    public function install()
    {
        Configuration::updateValue('SOCIALLOGIN_LIVE_MODE', false);

        include(dirname(__FILE__).'/sql/install.php');

        return parent::install() &&
            $this->registerHook('header') &&
            $this->registerHook('displayCustomerLoginFormAfter');
    }

    public function uninstall()
    {
        Configuration::deleteByName('SOCIALLOGIN_LIVE_MODE');

        include(dirname(__FILE__).'/sql/uninstall.php');

        return parent::uninstall();
    }

    /**
     * Load the configuration form
     */
    public function getContent()
    {
        /**
         * If values have been submitted in the form, process.
         */
        if (((bool)Tools::isSubmit('submitSocialloginModule')) == true) {
            $this->postProcess();
        }

       return $this->renderForm();
    }

    /**
     * Create the form that will be displayed in the configuration of your module.
     */
    protected function renderForm()
    {
        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitSocialloginModule';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            .'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = array(
            'fields_value' => Configuration::getMultiple(array('GOOGLE_LOGIN_ENABLE','GOOGLE_CLIENT_ID','GOOGLE_CLIENT_SECRET','FACEBOOK_LOGIN_ENABLE','FACEBOOK_CLIENT_ID','FACEBOOK_CLIENT_SECRET')), /* Add values for your inputs */
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        return $helper->generateForm(array($this->getGoogleConfigForm(), $this->getFacebookConfigForm()));
    }

    /**
     * Create the structure of your form.
     */
    protected function getGoogleConfigForm()
    {
        return array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Google Login'),
                    'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Google Login'),
                        'name' => 'GOOGLE_LOGIN_ENABLE',
                        'is_bool' => true,
                        'desc' => $this->l('Use this module in live mode'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                    array(
                        'col' => 6,
                        'type' => 'text',
                        'desc' => $this->l('Enter google client ID'),
                        'name' => 'GOOGLE_CLIENT_ID',
                        'label' => $this->l('Client ID'),
                    ),
                    array(
                        'col' => 6,
                        'type' => 'text',
                        'desc' => $this->l('Enter google client secret'),
                        'name' => 'GOOGLE_CLIENT_SECRET',
                        'label' => $this->l('Client Secret'),
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
            )
        );
    }

    protected function getFacebookConfigForm()
    {
        return array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Facebook Login'),
                    'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Facebook Login'),
                        'name' => 'FACEBOOK_LOGIN_ENABLE',
                        'is_bool' => true,
                        'desc' => $this->l('Use this module in live mode'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                    array(
                        'col' => 6,
                        'type' => 'text',
                        'desc' => $this->l('Enter Facebook client ID'),
                        'name' => 'FACEBOOK_CLIENT_ID',
                        'label' => $this->l('Client ID'),
                    ),
                    array(
                        'col' => 6,
                        'type' => 'text',
                        'desc' => $this->l('Enter Facebook client secret'),
                        'name' => 'FACEBOOK_CLIENT_SECRET',
                        'label' => $this->l('Client Secret'),
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
            )
        );
    }

    /**
     * Set values for the inputs.
     */
    protected function getConfigFormValues()
    {
        return array(
            'GOOGLE_LOGIN_ENABLE' => $_POST['GOOGLE_LOGIN_ENABLE'],
            'GOOGLE_CLIENT_ID' => $_POST['GOOGLE_CLIENT_ID'],
            'GOOGLE_CLIENT_SECRET' => $_POST['GOOGLE_CLIENT_SECRET'],
            'FACEBOOK_LOGIN_ENABLE' => $_POST['FACEBOOK_LOGIN_ENABLE'],
            'FACEBOOK_CLIENT_ID' => $_POST['FACEBOOK_CLIENT_ID'],
            'FACEBOOK_CLIENT_SECRET'=> $_POST['GOOGLE_CLIENT_SECRET'],
        );
    }

    /**
     * Save form data.
     */
    protected function postProcess()
    {
        $form_values = $this->getConfigFormValues();

        foreach (array_keys($form_values) as $key) {
            Configuration::updateValue($key, Tools::getValue($key));
        }
    }

   public function hookHeader()
    {
        $this->context->controller->addCSS($this->_path.'/views/css/font-awesome.min.css');
    }

    public function hookDisplayCustomerLoginFormAfter(){

        if (!$this->active)
            return;

        $this->smarty->assign(array(
            'cart' => $this->context->cart,
            'cart_qties' => $this->context->cart->nbProducts(),
            'is_logged' => $this->context->customer->isLogged(),
            'customerName' => ($this->context->customer->logged ? $this->context->customer->firstname.' '.$this->context->customer->lastname : false),
            'firstName' => ($this->context->customer->logged ? $this->context->customer->firstname : false),
            'lastName' => ($this->context->customer->logged ? $this->context->customer->lastname : false),
            'order_process' => Configuration::get('PS_ORDER_PROCESS_TYPE') ? 'order-opc' : 'order'
        ));
        return $this->fetch( 'module:kk_sociallogin/views/templates/front/authentication.tpl');

    }
}
