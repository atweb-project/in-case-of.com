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

class remositoryClassifyListHTML extends remositoryCustomUserHTML {
	protected $tabcnt=0;

	public function classifyListHTML( $type, $cls ) {
		$repository = remositoryRepository::getInstance();
		foreach ($cls as $type=>$items) {
			echo <<<TYPE_HEAD

			<h2 class="contentheading">$type</h2>

TYPE_HEAD;

			foreach ($items as $item) {
				$name = $this->show($item->name);
				$description = $this->show($item->description);
				$link = $repository->RemositoryBasicFunctionURL('classify', $item->id);
				echo <<<CLASSIFY_ITEM

				<h3><a href="$link">$name</a></h3>
				<p>$description</p>

CLASSIFY_ITEM;

			}
		}
	}
}
