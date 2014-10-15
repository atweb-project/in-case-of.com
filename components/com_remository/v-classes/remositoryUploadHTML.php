<?php

class remositoryUploadHTML extends remositoryCustomUserHTML {
	
	public function alreadyLogged () {
		$link = remositoryRepository::getInstance()->RemositoryBasicFunctionURL('upload');
		$check = aliroRequest::getInstance()->makeFormStamp();
		echo <<<ALREADY_LOGGED
		
		<div id="timeline">
			<p>
				Submit a case study in four easy steps!
			</p>
			<ul>
				<li class="radius orange current">1</li>
				<li class="radius">2</li>
				<li class="radius">3</li>
				<li class="radius end">4</li>
			</ul>
		</div>
		<div id="remositoryuploadregister">
			<h3 class="underline">Step One: Create / Assign an Account</h3>
			<div id="remositoryuploadregisterexist" class="radius green">
				<h2>Already logged in</h2>
				<p>
					As you are logged in, you can proceed immediately to step 2.
					<br /><br />
				</p>
				<form action="$link" method="post">
				<div class="fields short">
					<p>
						<input type="submit" value="Next" class="button" />
					</p>
					<input type="hidden" name="logorreg" value="already" />
					<input type="hidden" name="step" value="2" />
				</div>
				</form>
			</div>
		</div>
		
ALREADY_LOGGED;
		
	}
	
	public function register () {
		$link = remositoryRepository::getInstance()->RemositoryBasicFunctionURL('upload');
		$check = aliroRequest::getInstance()->makeFormStamp();
		echo <<<REGISTRATION
		
		<div id="timeline">
			<p>
				Submit a case study in four easy steps!
			</p>
			<ul>
				<li class="radius current">1</li>
				<li class="radius">2</li>
				<li class="radius">3</li>
				<li class="radius end">4</li>
			</ul>
		</div>
		<div id="remositoryuploadregister">
			<h3 class="underline">Step One: Create / Assign an Account</h3>
			<div id="remositoryuploadregisterexist" class="radius green">
				<h2>Existing users</h2>
				<p>
					Existing users can login to their account and easily upload a case study!
				</p>
				<form action="$link" method="post">
				<div class="fields short">
					<p>
						<label for="lemail">Email</label>
						<input type="text" id="lemail" name="email" size="30" class="inputbox" />
					</p>
					<p>
						<label for="password">Password</label>
						<input type="password" id="password" name="password" size="30" class="inputbox" />
					</p>
					<p>
						<input type="submit" value="Next" class="button" />
					</p>
					<input type="hidden" name="logorreg" value="login" />
					<input type="hidden" name="step" value="2" />
					$check
				</div>
				</form>
			</div>
			<div id="remositoryuploadregisternew" class="radius orange">
				<h2>Don't Have an Account?</h2>
				<p>
					Not to worry - we'll create one for you!  Enter in your name and email address
					and we'll create an account for you.
				</p>
				<form action="$link" method="post">
				<div class="fields short">
					<p>
						<label for="name">Name</label>
						<input type="text" id="name" name="name" size="30" class="inputbox" />
					</p>
					<p>
						<label for="remail">Email</label>
						<input type="text" id="remail" name="email" size="30" class="inputbox" />
					</p>
					<p>
						<input type="submit" value="Next" class="button" />
					</p>
					<input type="hidden" name="logorreg" value="register" />
					<input type="hidden" name="step" value="2" />
				</div>
				</form>
			</div>
		</div>
		
REGISTRATION;
		
	}
	
	public function upload ($classtypes, $classifications, $clist, $authors, $name='', $email='') {
		$formurl = remositoryRepository::getInstance()->RemositoryBasicFunctionURL('savefile');
		$reghtml = empty($email) ? '' : <<<REG_DONE
		
			<p>
				Thank you for registering, $name.  Your password has been sent to $email. 
				Please note that your account is not fully activated until you login with
				the new password.
			</p>
		
REG_DONE;

		echo <<<UPLOAD_STUDY
		
		<div id="timeline">
			<p>
				Submit a case study in four easy steps!
			</p>
			<ul>
				<li class="radius">1</li>
				<li class="radius current">2</li>
				<li class="radius">3</li>
				<li class="radius end">4</li>
			</ul>
		</div>
			<h3 class="underline">Step Two: Upload Case Study</h3>
			$reghtml		
			<form name='uploadForm' enctype='multipart/form-data' action='$formurl' method='post'>
				<div id="remositorystudyupload">
					<p>
						You can now upload your case study for review. To ensure that FindCaseStudies 
						users have access to quality, unbiased case studies, our staff personally 
						reviews each submission. 
					</p>
					<div class="fields wide">
						<p>
							<label for="userfile">File to upload:</label>
							<input class="text_area" type="file" id="userfile" name="userfile" />
						</p>
						<p>
							<label for="filetitle">Case Study Title: </label>
							<input type="text" class="inputbox" name="filetitle" id="filetitle" />
						</p>
						<p>	
							<label for="containerid">Category:</label>
							$clist
						</p>
						{$this->selectAuthor($authors)}
						{$this->tagChooser($classtypes, $classifications)}
						<p>
							<input type="submit" value="Next" class="button" />
						</p>
					</div>
					<input type="hidden" name="option" value="com_remository" />
					<input type="hidden" name="repnum" value="$this->repnum" />
					<input type="hidden" name="task" value="" />
				</div>
			</form>
					
UPLOAD_STUDY;

	}
	
	protected function selectAuthor ($authors) {
		if (empty($authors)) return;
		$optionhtml = '';
		foreach ($authors as $author) {
			$name = preg_replace('/[^a-zA-Z0-9]/', '-', $author);
			$optionhtml .= <<<ONE_AUTHOR
		
						<option value="$name">$author</option>
ONE_AUTHOR;

		}
		$title = _DOWN_FILE_AUTHOR;
		$other = _DOWN_SPECIFY_AUTHOR;
		return <<<CHOOSE_AUTHOR
		
						<p>
							<label for="fileauthor">$title </label>
							<select class="inputbox" name="fileauthor" id="fileauthor">
								$optionhtml
								<option value="999">$other</option>
							</select>
						</p>
						<p>
							<label for="otherauthor">$other: </label>
							<input type="text" class="inputbox" name="otherauthor" id="otherauthor" />
						</p>
		
CHOOSE_AUTHOR;

	}
	
	protected function tagChooser ($classtypes, $classifications) {
		$html = '';
		foreach ($classtypes as $type) {
			$fieldname = str_replace(' ', '-', 'type '.$type);
			$optionhtml = '';
			foreach ($classifications as $class) {
				if ($type != $class->type) continue;
				$optionhtml .= <<<AN_OPTION
				
						<option value="$class->id">$class->name</option>
AN_OPTION;

			}
			$html .= <<<TAG_SELECT
			
				<p>
					<label for="$fieldname">$type:</label>
					<select id="$fieldname" name="classification[]" class="inputbox">
						$optionhtml
					</select>
				</p>						
			
TAG_SELECT;

		}
		return $html;
	}
	
	public function chooseSubscription () {
		$link = remositoryRepository::getInstance()->RemositoryBasicFunctionURL('upload');
		echo <<<CHOOSE_SUBSCRIPTION
		<div id="timeline">
			<p>
				Submit a case study in four easy steps!
			</p>
			<ul>
				<li class="radius">1</li>
				<li class="radius">2</li>
				<li class="radius current">3</li>
				<li class="radius end">4</li>
			</ul>
		</div>
		<div id="remositoryuploadsubscribe">
			<h3 class="underline">Step Three: Select Subscription Plan</h3>
			<p>
				OK - your file has been received and will be reviewed by a FindCaseStudy analyst.
				Once the case study passes our review criteria, it will be made available to
				FindCaseStudy subscribers.
			</p>		
			<form name='subscribeForm' action='$link' method='post'>
				<div id="remositoryuploadsubchoice" class="fields short">
					<p>
						<input class="inputbox" type="radio" name="substype" value="1" />1 year - Case study is visible to subscribers for a 12 month duration - Free
					</p>
					<p>
						<input class="inputbox" type="radio" name="substype" value="2" />2 year - Case study is visible to subscribers for a 24 month duration - $600.00
					</p>
					<p>
						<input type="submit" value="Next" class="button" />
					</p>
					<input type="hidden" name="step" value="4" />
				</div>
			</form
		</div>
		
CHOOSE_SUBSCRIPTION;

	}
	
	public function choosePayment ($substype, $credits, $cost) {
		$link = remositoryRepository::getInstance()->RemositoryBasicFunctionURL('upload');
		if (0 == $cost) {
			echo <<<NO_COST
			
		<div id="timeline">
			<p>
				Submit a case study in four easy steps!
			</p>
			<ul>
				<li class="radius">1</li>
				<li class="radius">2</li>
				<li class="radius">3</li>
				<li class="radius current end">4</li>
			</ul>
		</div>
			<div id="remositoryuploadnocost">
				<h3 class="underline">Step Four: Define Payment Options</h3>
				<p>
					The selected Subscription requires no payment options
				</p>
				<form name="payForm" action="$link"" method="post">
					<div>
						<p>
							<input type="submit" value="Next" class="button" />
						</p>
						<input type="hidden" name="step" value="5" />
					</div>
				</form>
			</div>
			
NO_COST;

			return;
		}
		if (0 == $credits) $usecredit = '';
		else $usecredit = <<<USE_CREDIT
		
				<form name="payForm" action="$link"" method="post">
					<p>
						Apply one of my credits to this subscription 
						<input type="submit" class="button" value="Go" />
						<input type="hidden" name="reducecredits" value="1" />
					</p>
				</form>
		
USE_CREDIT;


		echo <<<CHOOSE_PLAN
		
		<div id="timeline">
			<p>
				Submit a case study in four easy steps!
			</p>
			<ul>
				<li class="radius">1</li>
				<li class="radius">2</li>
				<li class="radius">3</li>
				<li class="radius current end">4</li>
			</ul>
		</div>
			<div id="remositoryuploadcost">
				<h3 class="underline">Step Four: Define Payment Options</h3>
				<p>
					Please select a payment option:
				</p>
				<h3>Account Credit</h3>
				<p>
					Utilize your case study credits assigned to your account to this subscription.
				</p>
				<p>
					You currently have $credits case study credits associated with this account.
				</p>
				$usecredit
				<h3>PayPal</h3>
				<p>
					This will be filled in using the EasyPayPal module and will have a link that goes
					directly to PayPal.
				</p>
				<h3>Invoice company</h3>
				<p>
					Have FindCaseStudies invoice company for case study submission.
				</p>
				<form name='payForm' action='$link' method='post'>
					<div>
						<p>
							Name etc.
						</p>
						<p>
							<input type="submit" value="Go" class="button" />
						</p>
						<input type="hidden" name="step" value="5" />
					</div>
				</form>
			</div>
		
CHOOSE_PLAN;

	}
	
	public function completed () {
		if (aliroUser::getInstance()->id) $newaccount = '';
		else $newaccount = <<<NEW_ACCOUNT
		
			<p>
				Please note that a new account has been created for you and the password sent
				to the email address you specified.  You must log in to the account within 14
				days, and when you do so it will become permanently activated.  If you do not 
				do this, after 14 days your account and any related case studies will be deleted.
				You should in any case log in to your new account so that you can set your 
				preferences to suit yourself.
			</p>
		
NEW_ACCOUNT;

		echo <<<COMPLETED
		
		<div id="remositoryuploadcomplete">
			<h2>Case Study Submission Complete!</h2>
			<p>
				Your Subscription and Payment options have been successfully saved, and your case study 
				will now be reviewed by a FindCaseStudies analyst.  The Analyst will notify you at your 
				stored account email address if edits are necessary prior to subscription activation. 
				Upon approval, you will receive a notification and the case study will be visible to 
				FindCaseStudies memebers.
			</p>
			$newaccount
			<p>
				Thank you for your case study submission.  If you have any questions about this process, 
				feel free to contact us at 
				<a href="mailto:casestudyreview@findcasestudies.com">
					casestudyreview@findcasestudies.com
				</a>
			</p>
		</div>
		
COMPLETED;

	}
}