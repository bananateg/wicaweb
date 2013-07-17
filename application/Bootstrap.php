<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap {
	
	/*
	 * Initialize GlobalFunctions file 
	 */
	protected function _initGlobalFunctions(){
		require_once 'GlobalFunctions.php';
	}
	
	protected function _initPhpThumbHelper(){
		require_once 'PhpThumbHelper.php';
	}
		
	/*
	 * Initialize layouts
	 */
	protected function _initLayoutHelper() {
		$this->bootstrap ( 'frontController' );
		$layout = Zend_Controller_Action_HelperBroker::addHelper ( new ModuleLayoutLoader () );
	
	}
	
	/*
	 * Register Zend Framework Error Handler Pluging
	 */
	protected function _initErrorPlugin() {
		$front = Zend_Controller_Front::getInstance ();
		$front->registerPlugin ( new Zend_Controller_Plugin_ErrorHandler ( array (
				'module' => 'core',
				'controller' => 'error',
				'action' => 'error' 
		) ) );
		$front->throwExceptions ( false );
	
	}
	/*
	 * Get file with words to translate
	 */
	protected function _initTranslation() {
		
		
		Zend_Loader::loadClass('Zend_Translate');
		
		$translate = new Zend_Translate(
				'array',
				APPLICATION_PATH.'/configs/languages/',
				'es',
				array('scan' => Zend_Translate::LOCALE_FILENAME)
		);
		

		$id = New Zend_Session_Namespace('id');
		$website_language = $id->website_language;
		$locale = new Zend_Locale();
		
		if(isset($website_language)){
			$locale->setLocale($website_language);
		}else{
			$locale->setLocale(Zend_Locale::BROWSER);
		}
		
		
		// setting the right locale
		if ($translate->isAvailable($locale->getLanguage())) {
			$translate->setLocale($locale);
		} else {
			$translate->setLocale('es');
		}		
		
		Zend_Registry::set('Zend_Translate', $translate);
		
	}
	
	/*
	 * Get phpThumbs class
	 */
	protected function _initAutoloading() {
		$autoloader = Zend_Loader_Autoloader::getInstance();
		$autoloader->pushAutoloader(new phpThumb_Loader_Autoloader_phpThumbLoader());
		$autoloader->pushAutoloader(new phpThumb_Loader_Autoloader_phpThumbFunctionsLoader());
	}
	

}

class ModuleLayoutLoader extends Zend_Controller_Action_Helper_Abstract// looks up layout by module in application.ini
{
	public function preDispatch() {

		$bootstrap = $this->getActionController ()->getInvokeArg ( 'bootstrap' );
		$config = $bootstrap->getOptions ();
		$moduleName = $this->getRequest ()->getModuleName ();
		
		//check if layout module source exist
		if (isset ( $config [$moduleName] ['resources'] ['layout'] ['layout'] )) {
			$layoutScript = $config [$moduleName] ['resources'] ['layout'] ['layout'];
			Zend_Layout::getMvcInstance ()->setLayout ( $layoutScript );
			
			if (isset ( $config [$moduleName] ['resources'] ['layout'] ['layoutPath'] )) {
				$layoutPath = $config [$moduleName] ['resources'] ['layout'] ['layoutPath'];
				$moduleDir = Zend_Controller_Front::getInstance ()->getModuleDirectory ();
				Zend_Layout::getMvcInstance ()->setLayoutPath ( $layoutPath );
			}
		} else {
			$layoutScript = $config ['resources'] ['layout'] ['layout'];
			Zend_Layout::getMvcInstance ()->setLayout ( $layoutScript );
			
			if (isset ( $config ['resources'] ['layout'] ['layoutPath'] )) {
				$layoutPath = $config ['resources'] ['layout'] ['layoutPath'];
				$moduleDir = Zend_Controller_Front::getInstance ()->getModuleDirectory ();
				Zend_Layout::getMvcInstance ()->setLayoutPath ( $layoutPath );
			}
		}
		/*
		//Check session expiration
		if(!$this->checkIsLogin() && !$this->checkIsDefault()){
			//Check if session exist
			$id = New Zend_Session_Namespace('id');
			if($id->user_id=='' || $id->user_id==null){
				//Redirect to login
				header("Location: /core");
			}
		}*/
		
	
	}
	
	public function checkIsLogin()
	{
		$request = Zend_Controller_Front::getInstance()->getRequest();
		//Check if actual action is login
		$login = false;
		if($request->getActionName()=='index'){
			if($request->getControllerName()=='index'){
				if($request->getModuleName()=='core'){
					$login = true;
				}
			}
		}
		return $login;
	}
	
	public function checkIsDefault()
	{
		$request = Zend_Controller_Front::getInstance()->getRequest();
		//Check if actual action is login
		$default = false;
		if($request->getModuleName()=='default'){
			$default = true;
		}
		return $default;
	}
}