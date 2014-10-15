<?php

/**************************************************************
* This file is part of Remository
* Copyright (c) 2006 Martin Brampton
* Issued as open source under GNU/GPL
* For support and other information, visit http://remository.com
* To contact Martin Brampton, write to martin@remository.com
*
* Remository started life as the psx-dude script by psx-dude@psx-dude.net
* It was enhanced by Matt Smith up to version 2.10
* Since then development has been primarily by Martin Brampton,
* with contributions from other people gratefully accepted
*/

class remositorySearchBoxHTML extends remositoryCustomUserHTML {

	public function searchBoxHTML($categories, $catselector, $search_words='', $dropdown=false) {
		$formurl = remositoryRepository::getInstance()->RemositoryBasicFunctionURL('search');
		echo $this->pathwayHTML(null);
		if ($dropdown) echo <<<DROP_DOWN

		<form method="post" action="$formurl">
			<div id="remositorysearch" class="ddpanel">
				<div id="remositorysearchcontent" class="ddpanelcontent">
					<h2>{$this->showHTML(_DOWN_SEARCH)}</h2>
					<p class="remositoryformentry">
						<label for="search_text">{$this->showHTML(_DOWN_SEARCH_TEXT)}</label>
						<input class="inputbox" type="text" name="search_text" id="search_text" value="{$this->show($search_words)}" />
						<input class="button" type="submit" name="submit" value="{$this->showHTML(_DOWN_SUB_BUTTON)}" />
					</p>
					{$this->simpleTickBox(_DOWN_SEARCH_FILETITLE, 'search_filetitle')}
					{$this->simpleTickBox(_DOWN_SEARCH_FILEDESC, 'search_filedesc')}
					{$this->showCategorySelector($categories, $catselector)}
				</div>
				<div id="remositorysearchtab" class="ddpaneltab">
					<a href="#"><span>{$this->showHTML(_DOWN_SEARCH)}</span></a>
				</div>
				<input type="hidden" name="submit" value="submit" />
			</div>
		</form>

		
DROP_DOWN;

		else echo <<<SEARCH_FORM

		<form method="post" action="$formurl">
			<div id="remositorysearch">
				<h2>{$this->showHTML(_DOWN_SEARCH)}</h2>
				<p class="remositoryformentry">
					<label for="search_text">{$this->showHTML(_DOWN_SEARCH_TEXT)}</label>
					<input class="inputbox" type="text" name="search_text" id="search_text" value="{$this->show($search_words)}" />
					<input class="button" type="submit" name="submit" value="{$this->showHTML(_DOWN_SUB_BUTTON)}" />
				</p>
				{$this->simpleTickBox(_DOWN_SEARCH_FILETITLE, 'search_filetitle')}
				{$this->simpleTickBox(_DOWN_SEARCH_FILEDESC, 'search_filedesc')}
				{$this->showCategorySelector($categories, $catselector)}
				<input type="hidden" name="submit" value="submit" />
			</div>
		</form>
		
SEARCH_FORM;

	}
	
	protected function showCategorySelector ($categories, $catselector) {
		$cathtml = '';
		foreach ($categories as $category) {
			$selected = (0 == count($catselector) OR isset($catselector[$category->id])) ? true : false;
			$cathtml .= $this->simpleTickBox($category->name, "catsearch[$category->id]", $selected);
		}
		if ($cathtml) return <<<CAT_SELECT
		
			<div>
				<h3>{$this->showHTML(_DOWN_SEARCH_CATEGORY_SELECT)}</h3>
				$cathtml
			</div>
		
CAT_SELECT;
		
	}
}