{if (Configuration::get('GOOGLE_LOGIN_ENABLE'))}
	<p class="text-lg-center text-md-center text-xs-center">
		<a href="{$link->getModuleLink('kk_sociallogin','authenticate',["action"=>"google","mode" => "request","page_type" => {$page.page_name}])|escape:'html':'UTF-8'}" class="btn btn-secondary">
			<span>
				<i class="fa fa-google-plus-square" aria-hidden="true"></i>
				{l s='Continue with Google'}
			</span>
		</a>
	</p>
{/if}

{if (Configuration::get('FACEBOOK_LOGIN_ENABLE'))}
	<p class="text-lg-center text-md-center text-xs-center">
		<a href="{$link->getModuleLink('kk_sociallogin','authenticate',["action"=>"facebook","mode" => "request","page_type" => {$page.page_name}])|escape:'html':'UTF-8'}" class="btn btn-secondary">
			<span>
				<i class="fa fa-facebook-official" aria-hidden="true"></i>
				{l s='Continue with Facebook'}
			</span>
		</a>
	</p>
{/if}
<hr/>