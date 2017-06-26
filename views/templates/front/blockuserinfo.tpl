
<!-- Block user information module NAV  -->
<div id="_desktop_user_info">
	 <div class="user-info">
	<div class="btn-group">
	  <a class="blockcart" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
	    <i class="fa fa-2x fa-user"></i>
	  </a>
	  <div class="dropdown-menu dropdown-menu-right">

	    {if $is_logged}
				<li class="dropdown-item">
					<a href="{$link->getPageLink('my-account', true)|escape:'html':'UTF-8'}" title="{l s='View my customer account' mod='kk_sociallogin'}" class="account" rel="nofollow" style="white-space: nowrap;">
						<i class="fa fa-user"></i> 
						<span>{l s='Hello' mod='kk_sociallogin'}, {$customerName}</span></a>
				</li>
				<li class="dropdown-item">
					<a href="{$link->getPageLink('my-account', true)|escape:'html':'UTF-8'}" title="{l s='My account' mod='kk_sociallogin'}"><i class="fa fa-user"></i> {l s='My Account' mod='kk_sociallogin'}</a>
				</li class="dropdown-item">
				<li class="dropdown-item">
					<a id="wishlist-total" href="{$link->getModuleLink('blockwishlist', 'mywishlist', array(), true)|addslashes}" title="{l s='My wishlists' mod='kk_sociallogin'}"><i class="fa fa-heart"></i> {l s='Wish List' mod='kk_sociallogin'}</a>
				</li>
				<div class="dropdown-divider"></div>
				<li class="dropdown-item">
					<a class="logout" href="{$link->getPageLink('index', true, NULL, "mylogout")|escape:'html':'UTF-8'}" rel="nofollow" title="{l s='Log me out' mod='kk_sociallogin'}">
					<i class="fa fa-lock"></i> {l s='Sign out' mod='kk_sociallogin'}
					</a>
				</li>
			{else}
				{if (Configuration::get('GOOGLE_LOGIN_ENABLE'))}
					<li class="dropdown-item">
						<a href="{$link->getModuleLink('kk_sociallogin','authenticate',["action"=>"google","mode" => "request"])|escape:'html':'UTF-8'}" >
							<i class="fa fa-google-plus-square" aria-hidden="true"></i>
							{l s='Login with Google'}
						</a>
					</li>
				{/if}

				{if (Configuration::get('FACEBOOK_LOGIN_ENABLE'))}
					<li class="dropdown-item">
						<a href="{$link->getModuleLink('kk_sociallogin','authenticate',["action"=>"facebook","mode" => "request"])|escape:'html':'UTF-8'}" style="white-space: nowrap;">
							<i class="fa fa-facebook-official" aria-hidden="true"></i>
							{l s='Login with Facebook'}
						</a>
					</li>
				{/if}
					<li class="dropdown-item">
						<a class="login" href="{$link->getPageLink('my-account', true)|escape:'html':'UTF-8'}" rel="nofollow" title="{l s='Login to your customer account' mod='kk_sociallogin'}">
							<i class="fa fa-unlock-alt"></i> {l s='Sign in' mod='kk_sociallogin'}
						</a>
					</li>
			{/if}
	  </div>
	</div>
	</div>
</div>