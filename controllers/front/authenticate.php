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

class Kk_SocialloginAuthenticateModuleFrontController extends ModuleFrontController
{
    /**
     * This class should be use by your Instant Payment
     * Notification system to validate the order remotely
     */
    public function postProcess()
    {

        //Initialize
        $action = Tools::getValue('action', null);
        $mode = Tools::getValue('mode','request');

        $redirectUrl = $this->context->link->getModuleLink('kk_sociallogin','authenticate',array('action' => $action, 'mode' => 'response'));

        // print_r($redirectUrl);
        // die;


        //Switch between social
        switch ($action) {

            case 'facebook':

                if(Configuration::get('FACEBOOK_LOGIN_ENABLE')){

                    include_once(dirname(__FILE__)."/../../facebook/facebook.php");

                    $facebook = new Facebook(array(
                      'appId'  => Configuration::get('FACEBOOK_CLIENT_ID'),
                      'secret' => Configuration::get('FACEBOOK_CLIENT_SECRET')
                    ));

                    $fbUser = $facebook->getUser();
                    
                    switch ($mode) {
                        case 'request':
                          Tools::redirect($facebook->getLoginUrl(array('redirect_uri'=>$redirectUrl,'scope'=>"email")));
                            break;
                        
                        case 'response':
                            $userProfile = $facebook->api('/me?fields=id,first_name,last_name,email,link,gender,locale,picture');

                            $user['email'] = $userProfile['email'];
                            $user['lastname'] = $userProfile['last_name'];
                            $user['firstname'] = $userProfile['first_name'];
                            break;
                    }

                }
                else{

                    Tools::redirect($this->context->link->getPageLink('authentication',true));
                }
                break;

            case 'google':

                if(Configuration::get('GOOGLE_LOGIN_ENABLE')){

                    include_once(dirname(__FILE__)."/../../google/Google_Client.php");
                    include_once(dirname(__FILE__)."/../../google/contrib/Google_Oauth2Service.php");

                    $gClient = new Google_Client();
                    $gClient->setApplicationName('Login to amburshoe.co.in');
                    $gClient->setClientId(Configuration::get('GOOGLE_CLIENT_ID'));
                    $gClient->setClientSecret(Configuration::get('GOOGLE_CLIENT_SECRET'));
                    $gClient->setAccessType('online');
                    $gClient->setApprovalPrompt('auto');
                    $gClient->setRedirectUri($redirectUrl);
                    $google_oauthV2 = new Google_Oauth2Service($gClient);

                    switch ($mode) {
                        case 'request':
                            Tools::redirect($gClient->createAuthUrl());
                            break;
                        
                        case 'response':
                            
                            $gClient->authenticate();
                            $userProfile = $google_oauthV2->userinfo->get();

                            $user['email'] = $userProfile['email'];
                            $user['lastname'] = $userProfile['given_name'];
                            $user['firstname'] = $userProfile['family_name'];
                            break;
                    }

                }
                else{

                    Tools::redirect($this->context->link->getPageLink('authentication',true));
                }

                break;
            
            default:
                
                Tools::redirect($this->context->link->getPageLink('authentication',true));
                break;
        }
            
        //Check User Exist
        if($this->userExist($user['email'])){

            $this->login($user['email']);
        }
        else{

            $this->createUser($user['email'],$user['lastname'],$user['firstname']);
            $this->login($user['email']);
        }

        //Redirect after login
        Tools::redirect($this->context->link->getPageLink('my-account'));
    }

    public function userExist($email) {

        $customer = new Customer();
        $authentication = $customer->getByEmail($email);
        if (!$authentication)
            return false;
        return true;
    }

    public function createUser($email, $lastname, $firstname, $password = null) {

        if (\Customer::customerExists($email)) {
            return false;
        }

        $customer = new \Customer();
        $customer->active = 1;
        $customer->firstname = $firstname;
        $customer->lastname = $lastname;
        $customer->email = $email;
        $customer->active = 1;
        $customer->passwd  = $password ? $password : md5(bin2hex(openssl_random_pseudo_bytes(10)));

        if ($customer->add())
            return true;
        else
            return false;
    }

    public function login($email) {

        $customer = new Customer();
        $authentication = $customer->getByEmail($email);
        if (!$authentication) //user doesn't exist
            return false;

        $ctx = Context::getContext();
        // $ctx->cookie->id_compare = isset($ctx->cookie->id_compare) ? $ctx->cookie->id_compare: CompareProduct::getIdCompareByIdCustomer($customer->id);

        $ctx->cookie->id_customer = (int)($customer->id);
        $ctx->cookie->customer_lastname = $customer->lastname;
        $ctx->cookie->customer_firstname = $customer->firstname;
        $ctx->cookie->logged = 1;
        $customer->logged = 1;
        $ctx->cookie->is_guest = $customer->isGuest();
        $ctx->cookie->passwd = $customer->passwd;
        $ctx->cookie->email = $customer->email;
        // Add customer to the context
        $ctx->customer = $customer;

        // $id_cart = (int)\Cart::lastNoneOrderedCart($ctx->customer->id);

        // if ($id_cart) {
        //     $ctx->cart = new \Cart($id_cart);
        // } else {
        //     $ctx->cart = new \Cart();
        //     $ctx->cart->id_currency = \Currency::getDefaultCurrency()->id; //mandatory field
        // }
        // $ctx->cart->id_customer = (int)$customer->id;
        // $ctx->cart->secure_key = $customer->secure_key;
        // $ctx->cart->save();
        // $ctx->cookie->id_cart = (int)$ctx->cart->id;
        // \CartRule::autoRemoveFromCart($ctx);
        // \CartRule::autoAddToCart($ctx);

        $ctx->cookie->write();
        return true;
    }
}
