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

/*******************************************************************************
**  The following are parameters for the ReMOSef Search Engine
**  Optimisation plug-in for Remository.  
**
**  The following are the parameters for the Remository specific
**  URL optimisation.
**
**  First, the definition of _REMOSITORY_SELECT_FROM_CONTAINER defines what
**  word will be used to identify the core functions of Remository - the
**  display of information about a category/folder or the detailed file
**  information page.  You can vary the word according to your requirements,
**  although it must not duplicate any of the other Remository function
**  codes.  (If in doubt ask martin@remository.com).
**
**  The following two lines define the translations that ReMOSef will perform
**  on names of folders and files when translating them for inclusion in a URL.
**  Each item in $remository_sef_name_chars is translated into the corresponding
**  element of $remository_sef_translate_chars.
**
**  NOTE it is important that space be the last translate character, since the
**  characters are processed in the order in which they appear.  Since earlier
**  translates may create new spaces, it is vital that the space translation is
**  done last.
**
**  You can extend these arrays as you wish, although it is obviously important
**  to make sure that the items of one match the items of the other exactly.
*******************************************************************************/
DEFINE('_REMOSITORY_SELECT_FROM_CONTAINER','Download');
global $remository_sef_name_chars, $remository_sef_translate_chars, $_SEF_SPACE;
$remository_sef_name_chars = array('&', '/', ' ');
$remository_sef_translate_chars = array('and', ' or ', $_SEF_SPACE);
