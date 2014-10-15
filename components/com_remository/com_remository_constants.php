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

// Affects number of files shown per page
DEFINE('_ITEMS_PER_PAGE','10');
// Default file listing order
// Possible values are 1 = File ID, 2 = File title, 3 = Download count (descending), 
// 4 = Submit date (descending), 5 = User Name (submitter), 6 = Author, 
// 7 = rating (descending)
DEFINE('_REM_DEFAULT_ORDERING', 2);

//Define how often timed events should be triggered
DEFINE('_REMOSITORY_CRON_INTERVAL', 600);

// Define whether descriptions should be processed by content plugins
DEFINE('_REMOSITORY_CONTENT_PLUGINS', 0);

// Define whether a summary of download counts should be shown
DEFINE ('_REMOSITORY_SHOW_DOWN_SUMMARY', 1);

// Definitions for paid download window
// First the number of time units for the window
DEFINE ('_REMOSITORY_DOWN_WINDOW_COUNT', 1);
// Then the units: must be one of MINUTE, HOUR, DAY, MONTH, YEAR
DEFINE ('_REMOSITORY_DOWN_WINDOW_UNIT', 'WEEK');

// Default size for icons
DEFINE ('_REMOSITORY_ICON_SIZE', 32);

// Definitions for the log file type
DEFINE('_REM_LOG_DOWNLOAD', 1);
DEFINE('_REM_LOG_UPLOAD', 2);
DEFINE('_REM_VOTE_USER_GENERAL',3);
DEFINE('_REM_LOG_UPDATE', 4);
DEFINE('_REM_LOG_DELETE', 5);
DEFINE('_REM_LOG_ADMIN_DOWNLOAD', 11);
DEFINE('_REM_LOG_FILE_SELECTION', 21);
DEFINE('_REM_LOG_BUY_CREDITS', 31);
DEFINE('_REM_LOG_BUY_FILE', 32);
DEFINE('_REM_LOG_DOWN_SUMMARY', 91);

// Determines how many pages will be shown around the current one
DEFINE('_PAGE_SPREAD','9');

// Determines whether email notifications will be sent
DEFINE('_REMOSITORY_EMAIL_ACCESSORS', 0);
DEFINE('_REMOSITORY_EMAIL_COMMENTS_ACCESSORS', 0);

// Determine whether search will be shown on every folder/file listing page
DEFINE ('_REMOSITORY_TOP_SEARCH', 1);

// Configure download restriction by credits
DEFINE ('_REMOSITORY_USE_CREDITS', 0);
DEFINE ('_REMOSITORY_REQUIRE_VODES_FOR_DOWNLOAD', 0);
DEFINE ('_REMOSITORY_REQUIRE_AUP_FOR_DOWNLOAD', 0);

// Whether visitors are allowed to vote
DEFINE ('_REMOSITORY_VISITORS_VOTE', 0);

// Options for the operation of SEF
DEFINE ('_REMOSITORY_SEF_LOWER_CASE', false);
DEFINE ('_REMOSITORY_SEF_UNIQUE_ID', false);

// Whether a new sub-folder can be created on upload
DEFINE ('_REMOSITORY_UPLOAD_CREATE_FOLDER', 0);

// Definitions for use with subscription management
// Resource types
DEFINE ('_REMOSITORY_SUBS_EVERYTHING', 1);
DEFINE ('_REMOSITORY_COUNT_DOWNLOAD', 41);
DEFINE ('_REMOSITORY_COUNT_UPLOAD', 42);
DEFINE ('_REMOSITORY_COUNT_EDIT', 43);
DEFINE ('_REMOSITORY_COUNT_DOWNLOAD_PLUS', 51);
DEFINE ('_REMOSITORY_COUNT_UPLOAD_PLUS', 52);
DEFINE ('_REMOSITORY_COUNT_EDIT_PLUS', 53);

// ID for file to be sent if check code is wrong
DEFINE ('_REMOSITORY_CHECKFAIL_FILE', 0);

// Names of fields to show in file details only if user is admin
define ('_REMOSITORY_SHOW_ADMIN_ONLY', '');

DEFINE('_THUMBNAILS_PER_COLUMN',2);
DEFINE('_REMOS_OPERATING_SYSTEMS','Linux,Windows,Mac,Palm,Other');
DEFINE('_REMOS_APP_BUGBEARS','no-ads,no-nags,no-spyware,no-limited-functionality');
DEFINE('_REMOS_LEGAL_TYPES','Free,Free for non-commercial use,GNU GPL');

// Used to check if a remote file has been specified
DEFINE('_REMOSITORY_REGEXP_URL','`^(https?|ftps?)\://[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(:[a-zA-Z0-9]*)?/?([a-zA-Z0-9\-\._\?\,\'/\\\+&%\$#\=~])*[^\.\,\)\(\s]`i');
DEFINE('_REMOSITORY_REGEXP_IP','`^(https?|ftps?)\://\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}(:[a-zA-Z0-9]*)?/?([a-zA-Z0-9\-\._\?\,\'/\\\+&%\$#\=~])*[^\.\,\)\(\s]`i');

// Block size to be used in handling file systems
DEFINE('_REMOSITORY_FILE_BLOCK_SIZE', 60000);
// Number of microseconds to sleep after sending each block
DEFINE('_REMOSITORY_DOWNLOAD_SLEEP', 250);


