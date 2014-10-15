<?php

class remository_upload_Controller extends remositoryUserControllers {
	protected $view = null;
	protected $interface = null;
	
	public function upload ($func) {
		$this->view = remositoryUserHTML::viewMaker('UploadHTML', $this);
		$this->interface = remositoryInterface::getInstance();
		$step = remositoryRepository::getParam($_REQUEST, 'step', 1);
		$method = 'handleStep'.$step;
		if (method_exists($this, $method)) $this->$method();
		else $this->handleStep1();
	}
	
	private function handleStep1 () {
		if (aliroUser::getInstance()->id) $this->view->alreadyLogged();
		else $this->view->register();
	}
	
	private function handleStep2 () {
		$interface = remositoryInterface::getInstance();
		$database = $interface->getDB();
		$classtypes = array('ROI Financial Data', 'Organization size', 'Type of Deployment');
		$typelist = "'".implode("','", $classtypes)."'";
		$database->setQuery("SELECT id, type, name FROM #__downloads_classify WHERE published != 0 AND type IN ($typelist) ORDER BY type, name");
		$classifications = $database->loadObjectList();
		$logorreg = remositoryRepository::getParam($_REQUEST, 'logorreg');
		$email = remositoryRepository::getParam($_REQUEST, 'email');
		if ('login' == $logorreg) {
			$password = remositoryRepository::getParam($_REQUEST, 'password');
			$message = aliroUserAuthenticator::getInstance()->systemLogin($email, $password);
			if ($message) $this->interface->redirect('index.php?option=com_remository&func=upload', $message, _ALIRO_ERROR_WARN);
			$this->prepareForUpload($classtypes, $classifications); 
		}
		elseif ('register' == $logorreg) {
			$name = remositoryRepository::getParam($_REQUEST, 'name');
			$reg = new registrationAPI();
			if ($userid = $reg->register($email, $name)) {
				$_SESSION['remositoryUploadUserID'] = $userid;
				$this->prepareForUpload($classtypes, $classifications, $name, $email);
			}
			else $this->handleStep1();
		}
		elseif ('already' == $logorreg) $this->prepareForUpload($classtypes, $classifications);
		else $this->handleStep1();
	}
	
	private function prepareForUpload ($classtypes, $classifications, $name='', $email='') {
		$clist = $this->repository->getSelectList(false, 0, 'containerid', 'class="inputbox"', $this->remUser, true);
		$this->view->upload($classtypes, $classifications, $clist, remositoryFile::getPopularAuthors(), $name, $email);
	}
	
	private function handleStep3 ($file) {
		$_SESSION['remositoryUploadFileID'] = $file->id;
		$this->view->chooseSubscription();
	}
	
	private function handleStep4 () {
		$substype = remositoryRepository::getParam($_REQUEST, 'substype');
		if (1 != $substype) {
			$this->interface = remositoryInterface::getInstance();
			$credits = array_sum($this->interface->triggerMambots('subscription', array()));
			$cost = 600;
		}
		else {
			$credits = $cost = 0;
			$fileid = remositoryRepository::getParam($_SESSION, 'remositoryUploadFileID', 0);
			if ($fileid) aliroDatabase::getInstance()->doSQL("UPDATE #__downloads_files SET publish_from = NOW(), publish_to = DATE_ADD(NOW(), INTERVAL 1 YEAR)");
		}
		$this->view->choosePayment($substype, $credits, $cost);
	}
	
	public function handleStep5 () {
		$this->view->completed();
	}
	
	public function remositoryUploadSaved ($file) {
		// Need to be very careful here - class has been invoked by Mambot handler, not by Remository
		// The manager object is the mambot handler, not the Remository manager object
		$this->view = remositoryUserHTML::viewMaker('UploadHTML', $this);
		$this->handleStep3($file);
		return true;
	}
	
}