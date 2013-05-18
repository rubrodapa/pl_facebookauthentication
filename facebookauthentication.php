<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  Authentication.facebook
 *
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Facebook Authentication Plugin
 *
 * @package     Joomla.Plugin
 * @subpackage  Authentication.facebook
 * @since       3.1
 */
class PlgAuthenticationFacebookauthentication extends JPlugin
{
	
	/**
	 * This method should handle the facebook authentication. It will try to authenticate the user using an oauth2 token.
	 * If it works, it will check if the user is registered and then will continue the login.
	 * If there is no token, the user will be redirected to facebook for login.
	 *
	 * @param   array   $credentials  Array holding the user credentials. Will not be used in this plugin since it will use the OAuth2 token.
	 * @param   array   $options      Array of extra options. Will not be used in this plugin since it will use the OAuth2 token.
	 * @param   object  $response     Authentication response object
	 *
	 * @return  boolean
	 *
	 * @since   3.1
	 */
	public function onUserAuthenticate($credentials, $options, &$response)
	{	
		
			$language = JFactory::getLanguaje()->load("plg_facebookauthentication");
		
			$client_id = $this->params->get("client_id","");
			$client_secret = $this->params->get("client_secret","");
			
			if($client_id == null || $client_id == "" || $client_secret == null || $client_secret == ""){
				
				throw new Exception(JText::_('PLG_FACEBOOK_ERROR_NO_KEYS'));
				
			}
		
			//Prepare the options for the OAuth2 login
			$OauthOptions = new JRegistry();
			$OauthOptions->def('redirecturi',JURI::current());
			$OauthOptions->def('clientid',$client_id);
			$OauthOptions->def('clientsecret',$client_secret);
			$OauthOptions->def('sendheaders',1);
			$array = array(
						"scope" => "email"
					);
			$OauthOptions->set('requestparams',$array);
		
			//Build the JFacebookOAuthobject
			$facebookOauth = new JFacebookOAuth($OauthOptions);
			
			//Authenticate. Will redirect to facebook if there is no correct code.
			try{
				
				$facebookOauth->authenticate();
				
			}catch(RuntimeException $e){
				
				$response->status		= JAuthentication::STATUS_FAILURE;
				$response->error_message = $e->getMessage();
				
				return false;
			}
			
		
			//If here, then we have a correct tokken. Proceed to create a JFacebook object.
			$facebook = new JFacebook($facebookOauth);
			
			//Take the user information from facebook
			$user = $facebook->__get("user");
			$json = $user->get("me");
			
			if($json){
				
				//Email from facebook user
				$email = $json->{'email'};
				
				//Checks if the user is already registered (by email)
				$db = JFactory::getDbo();
				$query	= $db->getQuery(true)
					->select('id')
					->from('#__users')
					->where('email=' . $db->quote($email));
				
				$db->setQuery($query);
				$result = $db->loadObject();
				
				if($result){
					
					//if it is registered, build the $response object using the info in the DB and return sucess
					$user = JUser::getInstance($result->id);
					$response->email = $user->email;
					$response->fullname = $user->name;
					$response->username = $user->username;
					
					if (JFactory::getApplication()->isAdmin())
					{
						$response->language = $user->getParam('admin_language');
					}
					else {
						$response->language = $user->getParam('language');
					}
					$response->status = JAuthentication::STATUS_SUCCESS;
					$response->error_message = '';
					
				}else{

					$response->status		= JAuthentication::STATUS_FAILURE;
					$response->error_message = JText::_('PLG_FACEBOOK_ERROR_NOT_REGISTERED');
					
				}
				
			}else{
				$response->status		= JAuthentication::STATUS_FAILURE;
				$response->error_message = JText::_('PLG_FACEBOOK_ERROR_COLLETING_DATA');
			}
			
			

	}
	
}
