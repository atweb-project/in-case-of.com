<?php
// These settings are dummy values to suppress errors when the language file is used in a context where the real values have not been set

if (!isset($Large_Text_Len)) $Large_Text_Len = 500;

if (!isset($Small_Text_Len)) $Small_Text_Len = 150;

if (!isset($mosConfig_sitename)) $mosConfig_sitename = 'Site Name';



// Add your own definitions here, just using plain DEFINE

// e.g. define ('_DOWNLOADS_TITLE', 'The name of my Repository');

// Your definitions will override the standard ones if placed ahead of them



// Prototype for standard definitions: if (!defined('')) DEFINE ('', '');



// Additions in 3.53

if (!defined('_DOWN_ALL_CATEGORIES')) DEFINE ('_DOWN_ALL_CATEGORIES', 'Tutte le categorie');

if (!defined('_DOWN_THANKS_FOR_VOTING')) DEFINE ('_DOWN_THANKS_FOR_VOTING', 'Grazie per il tuo voto!');

if (!defined('_DOWN_CAST_YOUR_VOTE')) DEFINE ('_DOWN_CAST_YOUR_VOTE', 'Perch� non valutare anche tu?');

if (!defined('_DOWN_LOGIN_TO_VOTE')) DEFINE ('_DOWN_LOGIN_TO_VOTE', 'Autenticati per votare');

if (!defined('_DOWN_CONFIG_EXPLAIN_5')) DEFINE ('_DOWN_CONFIG_EXPLAIN_5', 'E = seleziona i campi per caricare o modificare files');

if (!defined('_DOWN_SEARCH_ALL_FOLDERS')) DEFINE ('_DOWN_SEARCH_ALL_FOLDERS', 'Cerca in tutte le cartelle');

if (!defined('_DOWN_UPLOAD_BLOCKED_GROUPS')) DEFINE ('_DOWN_UPLOAD_BLOCKED_GROUPS', 'Mi dispiace, non hai ancora garantito strutture per caricare al momento.');

if (!defined('_DOWN_NO_REMOSITORY_GROUPS')) DEFINE ('_DOWN_NO_REMOSITORY_GROUPS', 'I gruppi di remository non sono disponibili - Le configurazioni indicano di usare i gruppi del CMS');

if (!defined('_DOWN_POSSIBLE_DUPLICATE_NAMES')) DEFINE ('_DOWN_POSSIBLE_DUPLICATE_NAMES', 'Configurazione salvata, ma il file ID &egrave duplicato nel filename');

if (!defined('_DOWN_CONFIG105')) DEFINE ('_DOWN_CONFIG105', 'Download_Browser_Save: <br /><em>(Disabilita la azione del broswer alla aperura del file da scaricare.)</em>');

if (!defined('_DOWN_CONFIG104')) DEFINE ('_DOWN_CONFIG104', 'Use_CMS_Groups: <br /><em>(Usa i gruppi del CMS al posto di quelli di remository per gestire gli accessi.)</em>');

if (!defined('_DOWN_CONFIG103')) DEFINE ('_DOWN_CONFIG103', 'Immediate_Download: <br /><em>(Salta le informazioni sul file, vai subito al download.)</em>');

if (!defined('_DOWN_CONFIG72')) DEFINE ('_DOWN_CONFIG72', 'Real_With_ID: <br /><em>(Nomi attuali dei file sul disco da fare diventare unici usando un File ID Number.)</em>');

if (!defined('_DOWN_DETAILS')) DEFINE('_DOWN_DETAILS','Details');

if (!defined('_DOWN_THIS_FILE_TODAY')) DEFINE ('_DOWN_THIS_FILE_TODAY', 'Hai scaricato questo file %d volte nelle ultime 24 ore, il limite &egrave %d.');

if (!defined('_DOWN_ALL_FILES_TODAY')) DEFINE ('_DOWN_ALL_FILES_TODAY', 'Il totale dei file scaricati &egrave %d nelle ultime 24 ore, il limite &egrave %d.');

if (!defined('_DOWN_YOUR_CREDITS')) DEFINE ('_DOWN_YOUR_CREDITS', 'Al momento hai %d crediti disponibili.');

if (!defined('_DOWN_TOO_FEW_CREDITS')) DEFINE ('_DOWN_TOO_FEW_CREDITS', 'Troppi pochi crediti per scaricare.');

if (!defined('_DOWN_CONFIG71')) DEFINE ('_DOWN_CONFIG71', 'Show_SubCategories: <br /><em>(Mostra le categorie sotto nel sottocategorie.)</em>');

if (!defined('_DOWN_PRICE_LABEL')) DEFINE ('_DOWN_PRICE_LABEL', 'Prezzo:');



// Additions in 3.52

if (!defined('_DOWN_MANY_CONTAINERS')) DEFINE('_DOWN_MANY_CONTAINERS', 'Troppi container per lista - per favore navigare fino a link dei container');

if (!defined('_DOWN_DELETE_SURE')) DEFINE ('_DOWN_DELETE_SURE', 'Sei sicuro di voler cancellare questo file?');

if (!defined('_DOWN_SEARCH_CATEGORY_SELECT')) DEFINE ('_DOWN_SEARCH_CATEGORY_SELECT', 'Dicidi quali categorie includere');

if (!defined('_DOWN_SEARCH_FILES')) DEFINE ('_DOWN_SEARCH_FILES', 'Cerca files');



// Additions in 3.51

if (!defined('_DOWN_NOT_ABSOLUTE')) DEFINE ('_DOWN_NOT_ABSOLUTE', 'Path assoluta non valida');

if (!defined('_DOWN_NEW_OR_UPDATED')) DEFINE ('_DOWN_NEW_OR_UPDATED', 'File nuovo o aggiornato');

if (!defined('_DOWN_NEW_FILE_COMMENT')) DEFINE ('_DOWN_NEW_FILE_COMMENT', 'Nuovo commento al file');

if (!defined('_DOWN_MAIL_MESSAGE_PREFIX')) DEFINE ('_DOWN_MAIL_MESSAGE_PREFIX', "Salve %s\n\n");

if (!defined('_DOWN_NEW_UPDATED_MSG')) DEFINE ('_DOWN_NEW_UPDATED_MSG',

					"Su %s un file � stato aggiunto o modificato.  Il titolo del file &egrave %s."

					."\n\nper favore non rispondere al messaggio, � stato creato automaticamente al solo scopo di informare\n"

					);

if (!defined('_DOWN_NEW_COMMENT_MSG')) DEFINE ('_DOWN_NEW_COMMENT_MSG',

					"Su %s Un nuovo commento � stato aggiunto al file.  il titolo del file &egrave %s.  "

					."Il commento &egrave stato fatto da %s e &egrave:\n\n%s"

					."\n\nper favore non rispondere al messaggio, � stato creato automaticamente al solo scopo di informare\n"

					);



// Modifications in 3.51

if (!defined('_DOWN_MAIL_MSG')) DEFINE('_DOWN_MAIL_MSG','Salve un nuovo file &egrave stato inviato da %s'

                   ." per il sito %s.\n"

                   ."Per favore loggarsi come amministratore per vedere e pubblicare il file.\n\n"

                   ."per favore non rispondere al messaggio, � stato creato automaticamente al solo scopo di informare\n");

if (!defined('_DOWN_MAIL_MSG_APP')) DEFINE('_DOWN_MAIL_MSG_APP','Salve un nuovo file &egrave stato inviato da %s'

                   ." Per il sito %s.\n"

                   ."Come da configurato, il file &egrave stato approvato automaticamente.\n\n"

                   ."per favore non rispondere al messaggio, � stato creato automaticamente al solo scopo di informare\n");

if (!defined('_DOWN_LINK_TO_FILE')) DEFINE ('_DOWN_LINK_TO_FILE', "\n\nPuoi trovare il file su %s");

if (!defined('_DOWN_LINK_TO_CONTAINER')) DEFINE ('_DOWN_LINK_TO_CONTAINER', "\n\nPuoi trovare il container su %s");



// Additions in 3.50

if (!defined('_DOWN_APPROVE_ROLES')) DEFINE ('_DOWN_APPROVE_ROLES', 'Regole auto-approva');

if (!defined('_DOWN_ACCESS_REFUSED')) DEFINE ('_DOWN_ACCESS_REFUSED', 'Mi dispiace, richiesta rifiutata');

if (!defined('_DOWN_MAYBE_LOGIN')) DEFINE ('_DOWN_MAYBE_LOGIN', 'Forse dovresti autenticarti, o registrare un account.');

if (!defined('_DOWN_CONFIG70')) DEFINE ('_DOWN_CONFIG70', 'Show_all_containers: <br /><em>(Nei risultati di ricerca, visualizza completamente la path del container)</em>');

if (!defined('_DOWN_CONFIG69')) DEFINE ('_DOWN_CONFIG69', 'Set_date_locale: <br /><em>(Scegli un formato locale per gestire la data)</em>');

if (!defined('_DOWN_CONFIG68')) DEFINE ('_DOWN_CONFIG68', 'Force_Language: <br /><em>(forza ad usare questo valore lingua, ignora il CMS)</em>');



// Additions in 3.47 to fix missed items in earlier versions

if (!defined('_DOWN_NO_FILE_RECEIVED')) DEFINE ('_DOWN_NO_FILE_RECEIVED', 'Nonostante tu abbia inviato un nuovo oggetto, nessun file &egrave stato ricevuto.');

if (!defined('_DOWN_SAMPLE')) DEFINE ('_DOWN_SAMPLE', 'Campione');

if (!defined('_DOWN_SAMPLE_DESCRIPTION')) DEFINE ('_DOWN_SAMPLE_DESCRIPTION', 'Rimpiazza o modifica questo container di dimostrazione come richiesto');

if (!defined('_DOWN_INSTALL_DONE1')) DEFINE ('_DOWN_INSTALL_DONE1', 'Sei invitato a leggere le ultime note sulla release <a href="/administrator/components/com_remository/read_me.txt">read_me.txt</a>');

if (!defined('_DOWN_INSTALL_DONE2')) DEFINE ('_DOWN_INSTALL_DONE2', 'Trovi maggiori informazioni su <a href="http://remository.com">il sito ufficiale di Remository</a>.');

if (!defined('_DOWN_INSTALL_DONE3')) DEFINE ('_DOWN_INSTALL_DONE3', 'Aiuta Remository');

if (!defined('_DOWN_INSTALL_DONE4')) DEFINE ('_DOWN_INSTALL_DONE4', 'Lo svliluppo e il mantenimento di Remository ruba del tempo e'

		.' denaro per lo sviluppo.  Se invitato a donare qualcosa al progetto'

		.' per il tuo uso al software, Considera di fare una donazione a supporto del progetto.'

		.' Clicca sul pulsante qui sotto per fare una donazione:');

if (!defined('_DOWN_LOCAL_OR_REMOTE')) DEFINE ('_DOWN_LOCAL_OR_REMOTE', 'Locale o remoto');

if (!defined('_DOWN_IS_LOCAL')) DEFINE ('_DOWN_IS_LOCAL', 'Locale');

if (!defined('_DOWN_IS_REMOTE')) DEFINE ('_DOWN_IS_REMOTE', 'Remoto');

if (!defined('_DOWN_LOCALISE_REMOTE_FILE')) DEFINE ('_DOWN_LOCALISE_REMOTE_FILE', 'Fallo diventare locale');

if (!defined('_DOWN_CANCEL_UPLOAD')) DEFINE ('_DOWN_CANCEL_UPLOAD', 'Cancella');

if (!defined('_DOWN_NOT_YET_IMPLEMENTED')) DEFINE ('_DOWN_NOT_YET_IMPLEMENTED', 'THIS FEATURE NOT YET IMPLEMENTED');

if (!defined('_DOWN_ADMIN_CPANEL_CACHEPATH')) DEFINE ('_DOWN_ADMIN_CPANEL_CACHEPATH', 'Percorso Cache del CMS - importante per RSS e il controllo degli accessi:');

if (!defined('_DOWN_CONFIG_EXPLAIN_4')) DEFINE ('_DOWN_CONFIG_EXPLAIN_4', 'D = seleziona i campi per la pagina dei dettagli');

if (!defined('_DOWN_CONFIG67')) DEFINE ('_DOWN_CONFIG67', 'Profile_URI: <br /><em>(Relative non-SEF URI al profilo con %u dove il user ID deve essere)</em>');

if (!defined('_DOWN_CONFIG66')) DEFINE ('_DOWN_CONFIG66', 'Activate_AEC: <br /><em>(Attiva itegrazione AEC per controllare gli accessi)</em>');

if (!defined('_DOWN_AEC_OPTION_A')) DEFINE ('_DOWN_AEC_OPTION_A', 'Giusto seleziona il gruppo(i) sotto.');

if (!defined('_DOWN_AEC_OPTION_B')) DEFINE ('_DOWN_AEC_OPTION_B', 'Cancella tutto, quindi applica al gruppo(i) sotto.');

if (!defined('_DOWN_AEC_OPTION_C')) DEFINE ('_DOWN_AEC_OPTION_C', 'Cancella set al gruppo nella applicazione, quindi applica al gruppo(i) sotto.');

if (!defined('_DOWN_CONFIG65')) DEFINE ('_DOWN_CONFIG65', 'Home_Page_Title:<br /><em>(La barra di titolo nel broswer per la pagina principale di remository)</em>');

if (!defined('_DOWN_NEXT')) DEFINE ('_DOWN_NEXT', 'Prossimo');

if (!defined('_DOWN_PREVIOUS')) DEFINE ('_DOWN_PREVIOUS', 'Precendente');

if (!defined('_DOWN_DOWNLOAD_LC')) DEFINE ('_DOWN_DOWNLOAD_LC', 'Scarica');

if (!defined('_DOWN_EMPTY_REPOSITORY')) DEFINE ('_DOWN_EMPTY_REPOSITORY', 'Cartella Vuota');

if (!defined('_DOWN_OVERVIEW_LATEST')) DEFINE ('_DOWN_OVERVIEW_LATEST', 'Ultimi file nella cartella');

if (!defined('_DOWN_SPECIFY_AUTHOR')) DEFINE ('_DOWN_SPECIFY_AUTHOR', 'Un altro autore');

if (!defined('_DOWN_CONFIG64')) DEFINE ('_DOWN_CONFIG64', 'Author_Threshold:<br /><em>(Minime lise di autori da includere nella lista drop down)</em>');

if (!defined('_DOWN_CONFIG63')) DEFINE ('_DOWN_CONFIG63', 'Main_Authors:<br /><em>(Lista degli autori per la selezione, Separata da virgole)</em>');

if (!defined('_DOWN_CONFIG62')) DEFINE ('_DOWN_CONFIG62', 'Min_Comment_length:<br /><em>(Minima lunghezza del commento ad un file, o ignora)</em>');

if (!defined('_DOWN_CONFIG61')) DEFINE ('_DOWN_CONFIG61', 'Video_Download:<br /><em>(Permetti di scaricare video)</em>');

if (!defined('_DOWN_CONFIG60')) DEFINE ('_DOWN_CONFIG60', 'Audio_Download:<br /><em>(Permetti di scaricare audio)</em>');

if (!defined('_DOWN_CONFIG59')) DEFINE ('_DOWN_CONFIG59', 'Alias:<br /><em>(Repository alias)</em>');

if (!defined('_DOWN_CONFIG58')) DEFINE ('_DOWN_CONFIG58', 'Name:<br /><em>(Repository nome)</em>');

if (!defined('_DOWN_PLAY')) DEFINE ('_DOWN_PLAY', 'Riproduci');

if (!defined('_DOWN_PLAY_THANK_YOU')) DEFINE ('_DOWN_PLAY_THANK_YOU', 'Grazie per la riproduzione del file');

if (!defined('_DOWN_CONFIG57')) DEFINE ('_DOWN_CONFIG57', 'Video File Extensions:<br /><em>(Video file da essere guardati, non scaricati)</em>');

if (!defined('_DOWN_CONFIG56')) DEFINE ('_DOWN_CONFIG56', 'Audio File Extensions:<br /><em>(Audio file da essere ascoltati, non scaricati)</em>');

if (!defined('_DOWN_FEATURED')) DEFINE ('_DOWN_FEATURED', 'Consigliato');

if (!defined('_DOWN_IS_FEATURED')) DEFINE ('_DOWN_IS_FEATURED', 'File consigliato:');

if (!defined('_DOWN_FEATURE_START')) DEFINE ('_DOWN_FEATURE_START', 'Data di inizio:');

if (!defined('_DOWN_FEATURE_END')) DEFINE ('_DOWN_FEATURE_END', 'Data di fine (vuoto = per sempre)');

if (!defined('_DOWN_PUBLISH_FROM')) DEFINE ('_DOWN_PUBLISH_FROM', 'Pubblica da:');

if (!defined('_DOWN_PUBLISH_TO')) DEFINE ('_DOWN_PUBLISH_TO', 'Pubblica fino al (vuoto = per sempre):');

if (!defined('_DOWN_FIELD_NAME')) DEFINE ('_DOWN_FIELD_NAME', 'Nome campo');

if (!defined('_DOWN_FIELD_TITLE')) DEFINE ('_DOWN_FIELD_TITLE', 'Titolo campo');

if (!defined('_DOWN_CUSTOM_UPLOAD')) DEFINE ('_DOWN_CUSTOM_UPLOAD', 'In caricamento');

if (!defined('_DOWN_CUSTOM_LIST')) DEFINE ('_DOWN_CUSTOM_LIST', 'In lista file');

if (!defined('_DOWN_CUSTOM_INFO_PAGE')) DEFINE ('_DOWN_CUSTOM_INFO_PAGE', 'Nella pagina di informazione');

if (!defined('_DOWN_FIELD_OPTIONS')) DEFINE ('_DOWN_FIELD_OPTIONS', 'limita i valori a questi inseriti tra virgole');

if (!defined('_DOWN_CUSTOM_INFO')) DEFINE ('_DOWN_CUSTOM_INFO', 'i nomi dei campi da usare nel DB e devono essere unici per xxx_downloads_files tabella.  I titolo dei campi saranno usati per chiamare i campi quando usati.');

if (!defined('_DOWN_CUSTOM_FIELDS_TAB')) DEFINE ('_DOWN_CUSTOM_FIELDS_TAB', 'Aggiungi campi');

if (!defined('_DOWN_CUSTOM_FIELDS_HEAD')) DEFINE ('_DOWN_CUSTOM_FIELDS_HEAD', 'Campi personalizzati');

if (!defined('_DOWN_CONFIG55')) DEFINE ('_DOWN_CONFIG55', 'Featured_Number:<br /><em>(Numero di file da consigliare)</em>');

if (!defined('_DOWN_IS_VISIBLE')) DEFINE ('_DOWN_IS_VISIBLE', 'Visibile?');

if (!defined('_DOWN_FREQUENCY')) DEFINE ('_DOWN_FREQUENCY', 'Frequenza');

if (!defined('_DOWN_TYPE')) DEFINE ('_DOWN_TYPE', 'Tipo');

if (!defined('_DOWN_DESCRIPTION')) DEFINE ('_DOWN_DESCRIPTION', 'Descrizione');

if (!defined('_DOWN_CLASSIFICATIONS')) DEFINE ('_DOWN_CLASSIFICATIONS', 'Classificazione');

if (!defined('_DOWN_SAVE')) DEFINE ('_DOWN_SAVE', 'Salva');

if (!defined('_DOWN_FILE_STRUCTURE')) DEFINE ('_DOWN_FILE_STRUCTURE', 'stuttura del file');

if (!defined('_DOWN_PRUNE')) DEFINE ('_DOWN_PRUNE', '- prune');

if (!defined('_DOWN_DELETE_MISSING')) DEFINE ('_DOWN_DELETE_MISSING', 'cancella file mancanti');

if (!defined('_DOWN_APPLY')) DEFINE ('_DOWN_APPLY', 'Applica');

if (!defined('_DOWN_CLASSIFICATION_NAME')) DEFINE ('_DOWN_CLASSIFICATION_NAME', 'Nome classificazione');

if (!defined('_DOWN_DISPLAY_LISTS')) DEFINE ('_DOWN_DISPLAY_LISTS', 'visualizza file in lista:');

if (!defined('_DOWN_NOT_PUBLISHED')) DEFINE ('_DOWN_NOT_PUBLISHED', 'Non pubblicati');

if (!defined('_DOWN_PUBLISH')) DEFINE ('_DOWN_PUBLISH', 'Pubblica');

if (!defined('_DOWN_UNPUBLISH')) DEFINE ('_DOWN_UNPUBLISH', 'Non pubblicare');

if (!defined('_DOWN_FILE')) DEFINE ('_DOWN_FILE', 'File');

if (!defined('_DOWN_ADD')) DEFINE ('_DOWN_ADD', 'Aggiungi');

if (!defined('_DOWN_CLASSIFN')) DEFINE ('_DOWN_CLASSIFN', 'Classif\'n');

if (!defined('_DOWN_ROLES')) DEFINE ('_DOWN_ROLES', 'regole:');

if (!defined('_DOWN_DELETE')) DEFINE ('_DOWN_DELETE', 'cancella');

if (!defined('_DOWN_ADD_USERS_NEW_ROLE')) DEFINE ('_DOWN_ADD_USERS_NEW_ROLE', 'Aggiungi utenti alla nuova regola');

if (!defined('_DOWN_ADD_MEMBERS')) DEFINE ('_DOWN_ADD_MEMBERS', 'Aggiungi membri');

if (!defined('_DOWN_ADD_LOCAL')) DEFINE ('_DOWN_ADD_LOCAL', 'Aggiungi locale');

if (!defined('_DOWN_ADD_REMOTE')) DEFINE ('_DOWN_ADD_REMOTE', 'Aggiungi remoto');

if (!defined('_DOWN_EDIT_APPROVAL')) DEFINE ('_DOWN_EDIT_APPROVAL', 'modifica approvazione');

if (!defined('_DOWN_DELETE_SUBMISSION')) DEFINE ('_DOWN_DELETE_SUBMISSION', 'Cancella invio');

if (!defined('_DOWN_CONTAINER')) DEFINE ('_DOWN_CONTAINER', 'Cartella');

if (!defined('_DOWN_PUBLISH_FILES')) DEFINE ('_DOWN_PUBLISH_FILES', 'Pubblica files');

if (!defined('_DOWN_DELETE_ORPHANS')) DEFINE ('_DOWN_DELETE_ORPHANS', 'Cancella orfani');

if (!defined('_DOWN_ORPHAN')) DEFINE ('_DOWN_ORPHAN', 'Orfani');

if (!defined('_DOWN_MISSING')) DEFINE ('_DOWN_MISSING', 'Persi');

if (!defined('_DOWN_ADD_USERS_ROLE')) DEFINE ('_DOWN_ADD_USERS_ROLE', 'Aggiungi utenti alla regola');

if (!defined('_DOWN_REMOVE_USERS_ROLE')) DEFINE ('_DOWN_REMOVE_USERS_ROLE', 'Rimuovi utenti dalla regola');

if (!defined('_DOWN_PUBLISHING')) DEFINE ('_DOWN_PUBLISHING', 'Pubblicando');

if (!defined('_DOWN_EDIT_CONTAINER')) DEFINE ('_DOWN_EDIT_CONTAINER', 'Modifica cartella');

if (!defined('_DOWN_REMOSITORY')) DEFINE ('_DOWN_REMOSITORY', 'Remository');

if (!defined('_DOWN_PHYSICAL_FILE')) DEFINE ('_DOWN_PHYSICAL_FILE', 'File fisico');

if (!defined('_DOWN_METADATA')) DEFINE ('_DOWN_METADATA', 'Metadata');

if (!defined('_DOWN_SHORT_DESCRIPTION')) DEFINE ('_DOWN_SHORT_DESCRIPTION', 'Breve descrizione');

if (!defined('_DOWN_LICENSE_HEADING')) DEFINE ('_DOWN_LICENSE_HEADING', 'Licenza');

if (!defined('_DOWN_ABOUT_FILE')) DEFINE ('_DOWN_ABOUT_FILE', 'Su il file');

if (!defined('_DOWN_COMMENTS_HEADING')) DEFINE ('_DOWN_COMMENTS_HEADING', 'Commenti');

if (!defined('_DOWN_STORAGE')) DEFINE ('_DOWN_STORAGE', 'Archivio');

if (!defined('_DOWN_ACCESS_CONTROL')) DEFINE ('_DOWN_ACCESS_CONTROL', 'Controllo accesso');

if (!defined('_DOWN_DOWNLOAD_ROLES')) DEFINE ('_DOWN_DOWNLOAD_ROLES', 'Regole download');

if (!defined('_DOWN_UPLOAD_ROLES')) DEFINE ('_DOWN_UPLOAD_ROLES', 'Regole upload');

if (!defined('_DOWN_EDIT_ROLES')) DEFINE ('_DOWN_EDIT_ROLES', 'Modifica regole');

if (!defined('_DOWN_ADD_NEW_ROLE')) DEFINE ('_DOWN_ADD_NEW_ROLE', 'Aggiungi una nuova regola:');

// Recent additions 3.46+

if (!defined('_DOWN_DOWNLOAD_FROM')) DEFINE ('_DOWN_DOWNLOAD_FROM', 'Scarica da');

if (!defined('_DOWN_UPLOAD_TO')) DEFINE ('_DOWN_UPLOAD_TO', 'Carica su');

if (!defined('_DOWN_EDIT_IN')) DEFINE ('_DOWN_EDIT_IN', 'Modifica in');

if (!defined('_DOWN_SUBS_EVERYTHING')) DEFINE ('_DOWN_SUBS_EVERYTHING', 'Conta tutti i downloads ai file');

if (!defined('_DOWN_SUBS_COUNT_DOWNLOAD')) DEFINE ('_DOWN_SUBS_COUNT_DOWNLOAD', 'Contat i downloads di specifiche categorie');

if (!defined('_DOWN_SUBS_COUNT_UPLOAD')) DEFINE ('_DOWN_SUBS_COUNT_UPLOAD', 'Conta uploads di specifiche categorie');

if (!defined('_DOWN_SUBS_COUNT_DOWNLOAD_PLUS')) DEFINE ('_DOWN_SUBS_COUNT_DOWNLOAD_PLUS', 'Conta contenitori e downloads discendenti');

if (!defined('_DOWN_SUBS_COUNT_UPLOAD_PLUS')) DEFINE ('_DOWN_SUBS_COUNT_UPLOAD_PLUS', 'Conta contenitori e uploads discendenti');

if (!defined('_DOWN_COUNT_DOWN')) DEFINE ('_DOWN_COUNT_DOWN', 'Manger di sottoscrizioni per contare i downloads');

if (!defined('_DOWN_COUNT_DOWN_CHILD')) DEFINE ('_DOWN_COUNT_DOWN_CHILD', 'Includi i discendenti nel conto dei downloads');

if (!defined('_DOWN_COUNT_UP')) DEFINE ('_DOWN_COUNT_UP', 'Manager di sottoscrizione per contare gli uploads');

if (!defined('_DOWN_COUNT_UP_CHILD')) DEFINE ('_DOWN_COUNT_UP_CHILD', 'Includi i discendenti nel conto degli uploads');

if (!defined('_DOWN_CONFIG54')) DEFINE ('_DOWN_CONFIG54', 'Count_Down:<br /><em>(Restringi tutti i downloads usando un manager di sottoscrizioni)</em>');

if (!defined('_DOWN_CONFIG53')) DEFINE ('_DOWN_CONFIG53', 'Show_File_Folder_Counts:<br /><em>(Visualizza conti cartelle e files)</em>');

if (!defined('_DOWN_CONFIG52')) DEFINE ('_DOWN_CONFIG52', 'Show_Footer:<br /><em>(Visualizza il footer di remository (Cerca/Invia/Crediti))</em>');

if (!defined('_DOWN_CONFIG51')) DEFINE ('_DOWN_CONFIG51', 'Allow_File_Info:<br /><em>(Link alle informazioni dettagliate dalla lista dei file)</em>');

if (!defined('_DOWN_IS_ACTIVE')) DEFINE ('_DOWN_IS_ACTIVE', 'Attivo?');

if (!defined('_DOWN_NEW_REPOSITORY')) DEFINE ('_DOWN_NEW_REPOSITORY', 'Crea un nuovo repository come quello in corso');

if (!defined('_DOWN_REPOSITORY_NAME')) DEFINE ('_DOWN_REPOSITORY_NAME', 'Nome archivio');

if (!defined('_DOWN_REPOSITORY_NUMBER')) DEFINE ('_DOWN_REPOSITORY_NUMBER', 'Numero archivio');

if (!defined('_DOWN_REPOSITORY_ALIAS')) DEFINE ('_DOWN_REPOSITORY_ALIAS', 'Repository alias');

// Recent additions 3.46

if (!defined('_DOWN_SUBTITLE')) DEFINE ('_DOWN_SUBTITLE', 'Sottotitlo file:');

if (!defined('_DOWN_PUBLISHER_ID')) DEFINE ('_DOWN_PUBLISHER_ID', 'ID del pubblicatore:');

if (!defined('_DOWN_PUBLISHED_DATE')) DEFINE ('_DOWN_PUBLISHED_DATE', 'Pubblicato in data:');

if (!defined('_DOWN_ALIAS')) DEFINE ('_DOWN_ALIAS', 'Alias');

if (!defined('_DOWN_BOTH')) DEFINE ('_DOWN_BOTH', 'Entrambi');

if (!defined('_DOWN_CONFIG50')) DEFINE ('_DOWN_CONFIG50', 'Remository_Pathway:<br /><em>(usa il bread crumb di remository (pathway))</em>');

if (!defined('_DOWN_SEARCHING_BY')) DEFINE ('_DOWN_SEARCHING_BY', 'Cerca tra');

if (!defined('_DOWN_FILTERING_BY')) DEFINE ('_DOWN_FILTERING_BY', 'Filtra tra');

if (!defined('_DOWN_REMOVE_TERM')) DEFINE ('_DOWN_REMOVE_TERM', 'Rimuovi termine');

if (!defined('_DOWN_NO_FILTERS')) DEFINE ('_DOWN_NO_FILTERS', 'Nessun filtro in effetti - Tutti gli oggenti trovati');

if (!defined('_DOWN_REVIEW_TAGS')) DEFINE ('_DOWN_REVIEW_TAGS', 'Modifica tags nel gruppo');

if (!defined('_DOWN_NO_RESULTS_1')) DEFINE ('_DOWN_NO_RESULTS_1', 'Mi dispiace, la tua ricerca non ha avuto risultati.');

if (!defined('_DOWN_NO_RESULTS_2')) DEFINE ('_DOWN_NO_RESULTS_2', 'non trovato che cosa cerchi?');

if (!defined('_DOWN_CONTACT_US')) DEFINE ('_DOWN_CONTACT_US', 'Usa il nostro forum per discutere su cosa stai cercando.');

if (!defined('_DOWN_ALL_CATS_RETRY')) DEFINE ('_DOWN_ALL_CATS_RETRY', 'Riprovare la ricerca con tutte le categorie?');

if (!defined('_DOWN_ADD_ALL_CATS')) DEFINE ('_DOWN_ADD_ALL_CATS', 'Aggiungi tutte le categorie');

if (!defined('_DOWN_CONFIG49')) DEFINE ('_DOWN_CONFIG49', 'Classification_Types:<br /><em>(Classification types (comma separated))</em>');



// Recent additions 3.45

if (!defined('_DOWN_GROUP_NO_NAME')) DEFINE ('_DOWN_GROUP_NO_NAME', 'Gruppo non salvato - nessun nome');

if (!defined('_DOWN_GROUP_SAVED')) DEFINE ('_DOWN_GROUP_SAVED', 'Il gruppo &egrave stato salvato');

if (!defined('_DOWN_CONFIG48')) DEFINE ('_DOWN_CONFIG48', 'Show_RSS_feeds:<br /><em>(Visualizza RSS feeds)</em>');

if (!defined('_DOWN_CONFIG46')) DEFINE ('_DOWN_CONFIG46', 'ExtsDisplay:<br /><em>(Estensioni da visualizzare online (Seprate da virgole))</em>');

if (!defined('_DOWN_DISPLAY_NOW')) DEFINE ('_DOWN_DISPLAY_NOW', 'Visualizza il file ora');

if (!defined('_DOWN_CONFIG47')) DEFINE ('_DOWN_CONFIG47', 'Scribd:<br /><em>(Scribd publishing key)</em>');

if (!defined('')) DEFINE ('', '');

// Recent addition 3.44.1

if (!defined('_DOWN_AEC_REFUSED')) DEFINE ('_DOWN_AEC_REFUSED', 'Spiacente, hai esaurito il tuo limite di download');

// Recent additions 3.44

if (!defined('_DOWN_FIELD')) DEFINE ('_DOWN_FIELD', 'Campo');

if (!defined('_DOWN_SEQUENCE')) DEFINE ('_DOWN_SEQUENCE', 'Sequenza');

if (!defined('_DOWN_CONFIG_EXPLAIN_1')) DEFINE ('_DOWN_CONFIG_EXPLAIN_1', 'A = Selezionare i campi per la lista dei file nel contenitore');

if (!defined('_DOWN_CONFIG_EXPLAIN_2')) DEFINE ('_DOWN_CONFIG_EXPLAIN_2', 'B = Selezionare i campi per i risultati della ricerca');

if (!defined('_DOWN_CONFIG_EXPLAIN_3')) DEFINE ('_DOWN_CONFIG_EXPLAIN_3', 'Selezionare i campi per i risultati di caricamento multiplo di file');

// Recent additions 3.43:

if (!defined('_DOWN_ADDFILE_THUMBNAIL')) DEFINE ('_DOWN_ADDFILE_THUMBNAIL', 'Miniatura %u (facoltativo)');

if (!defined('_DOWN_CONFIG45')) DEFINE("_DOWN_CONFIG45","Default_Licence:<br/><i>(Facoltativo - se presente sar&agrave; usata per ogni oggetto che non abbia un licenza specifica)</i>");

if (!defined('_DOWN_CONFIG_TITLE_LICENCE')) DEFINE('_DOWN_CONFIG_TITLE_LICENCE', 'Licenza');

if (!defined('_DOWN_VISIT')) DEFINE('_DOWN_VISIT','Visita');

if (!defined('_DOWN_EDIT')) DEFINE('_DOWN_EDIT','Modifica');

if (!defined('_DOWN_STRUCT_RECURSE_ALL')) DEFINE('_DOWN_STRUCT_RECURSE_ALL','Includi tutte le sottocartelle e i file contenuti');

if (!defined('_DOWN_STRUCT_RECURSE_DIR')) DEFINE('_DOWN_STRUCT_RECURSE_DIR','Includi solo i file di questo livello e le cartelle');

if (!defined('_DOWN_STRUCT_RECURSE_NONE')) DEFINE('_DOWN_STRUCT_RECURSE_NONE','Includi solo i file di questo livello');

if (!defined('_DOWN_STRUCT_NO_DIR')) DEFINE ('_DOWN_STRUCT_NO_DIR', 'La cartella specificata non esiste');

if (!defined('_DOWN_ADMINISTRATOR')) DEFINE ('_DOWN_ADMINISTRATOR', 'Amministratore');

// Recent modifications 3.43:

if (!defined('_DOWN_ADMIN_ACT_ADDSTRUCTURE')) DEFINE('_DOWN_ADMIN_ACT_ADDSTRUCTURE','Aggiungi i file gi&agrave; presenti nel server');

if (!defined('_DOWN_STRUCTURE_ADDED')) DEFINE('_DOWN_STRUCTURE_ADDED','File aggiunti al deposito');

if (!defined('_DOWN_ADDSTRUCTURE_TITLE')) DEFINE('_DOWN_ADDSTRUCTURE_TITLE','aggiungi file gi&agrave; presenti nel server');

//

if (!defined('_REM_INTERNAL')) DEFINE('_REM_INTERNAL','Errore interno di Remository');

if (!defined('_DOWNLOADS_TITLE')) DEFINE("_DOWNLOADS_TITLE","$mosConfig_sitename :: Deposito dei file");

if (!defined('_UP_FILE')) DEFINE('_UP_FILE','Carica un file');

if (!defined('_ADD_FILE_BUTTON')) DEFINE('_ADD_FILE_BUTTON','Aggiungi un file');

if (!defined('_SUBMIT_FILE_BUTTON')) DEFINE('_SUBMIT_FILE_BUTTON','Invia un file');

if (!defined('_DOWN_ROLE_REGISTERED')) DEFINE('_DOWN_ROLE_REGISTERED','Registrato');

if (!defined('_DOWN_ROLE_VISITOR')) DEFINE('_DOWN_ROLE_VISITOR','Visitatore');

if (!defined('_DOWN_ROLE_NOBODY')) DEFINE('_DOWN_ROLE_NOBODY','Nessuno');

if (!defined('_DOWN_ROLE_NONE_THESE')) DEFINE('_DOWN_ROLE_NONE_THESE','Nessuno di questi');

if (!defined('_DOWN_PAGE_TEXT')) DEFINE ('_DOWN_PAGE_TEXT', 'Pagina');

if (!defined('_DOWN_PAGE_SHOW_RESULTS')) DEFINE('_DOWN_PAGE_SHOW_RESULTS','Mostra risultati ');

if (!defined('_DOWN_PAGE_SHOW_RANGE')) DEFINE('_DOWN_PAGE_SHOW_RANGE','%s a %s di %s');

if (!defined('_SUBMIT_FILE_NOLOG')) DEFINE('_SUBMIT_FILE_NOLOG', 'Invio negato - non hai effettuato l\'accesso');

if (!defined('_SUBMIT_FILE_NOUSER')) DEFINE('_SUBMIT_FILE_NOUSER', 'Invio negato - solo per amministratori');

if (!defined('_SUBMIT_FILE_NOLIMIT')) DEFINE('_SUBMIT_FILE_NOLIMIT', 'Invio negato - limite raggiunto');

if (!defined('_SUBMIT_FILE_NOSPACE')) DEFINE('_SUBMIT_FILE_NOSPACE', 'Invio negato - mancanza di spazio');

if (!defined('_SUBMIT_NO_DDIR')) DEFINE('_SUBMIT_NO_DDIR', 'Invio negato - nessuna cartella per scaricare');

if (!defined('_SUBMIT_NO_UDDIR')) DEFINE('_SUBMIT_NO_UDDIR', 'Submit denied - no down-up dir');

if (!defined('_SUBMIT_HEADING')) DEFINE('_SUBMIT_HEADING', 'Carica file nel deposito');

if (!defined('_SUBMIT_INSTRUCT1')) DEFINE('_SUBMIT_INSTRUCT1', 'Esamina il tuo computer e seleziona un file che vorresti caricare per condividerlo.');

if (!defined('_SUBMIT_INSTRUCT2')) DEFINE('_SUBMIT_INSTRUCT2', 'O, se il file esiste da qualche parte in internet, immetti lo URL e altri dettagli per quel file');

if (!defined('_SUBMIT_INSTRUCT3')) DEFINE('_SUBMIT_INSTRUCT3', 'Per favore, seleziona il file da inviare e compila i dettagli richiesti');

if (!defined('_DOWN_FILE_SUBMIT_NOCHOICES')) DEFINE('_DOWN_FILE_SUBMIT_NOCHOICES','Non hai categorie di invio concesse - Questa categoria potrebbe non essere pubblica');

if (!defined('_SUBMIT_NEW_FILE')) DEFINE('_SUBMIT_NEW_FILE', 'Nuovo file');

if (!defined('_SUBMIT_UPLOAD_BUTTON')) DEFINE('_SUBMIT_UPLOAD_BUTTON', 'Carica file &amp; magazzino');

if (!defined('_MAIN_DOWNLOADS')) DEFINE('_MAIN_DOWNLOADS','Pagina principale del deposito');

if (!defined('_BACK_CAT')) DEFINE('_BACK_CAT','Ritorna alla categoria madre');

if (!defined('_BACK_FOLDER')) DEFINE('_BACK_FOLDER','Ritorna alla cartella madre');

if (!defined('_DOWN_START')) DEFINE('_DOWN_START','Comincerai a scaricare tra 2 secondi');

if (!defined('_DOWN_CLICK')) DEFINE('_DOWN_CLICK','Clicca qui se non parte');

if (!defined('_INVALID_ID')) DEFINE('_INVALID_ID','ID non valido');

if (!defined('_DOWN_CATEGORY')) DEFINE('_DOWN_CATEGORY','Categoria');

if (!defined('_DOWN_NO_PARENT')) DEFINE('_DOWN_NO_PARENT','Nessuna madre - livello massimo **');

if (!defined('_DOWN_FOLDER')) DEFINE('_DOWN_FOLDER','Cartella');

if (!defined('_DOWN_FOLDERS')) DEFINE('_DOWN_FOLDERS','Cartelle');

if (!defined('_DOWN_FILES')) DEFINE('_DOWN_FILES','File');

if (!defined('_DOWN_FOLDERS_FILES')) DEFINE('_DOWN_FOLDERS_FILES','Cartelle/File');

if (!defined('_DOWN_NO_CATS')) DEFINE('_DOWN_NO_CATS',"Il deposito non &egrave; stato ancora fondato per <i>$mosConfig_sitename</i>.<br/>&nbsp;<br/>Nessuna categoria definita.");

if (!defined('_DOWN_NO_VISITOR_CATS')) DEFINE('_DOWN_NO_VISITOR_CATS','Spiacente, il deposito non &egrave; disponibile ai visitatori casuali - effettuare l\'accesso, per cortesia');

if (!defined('_DOWN_ADMIN_FUNC')) DEFINE('_DOWN_ADMIN_FUNC','Funzioni dell\'amministratore:');

if (!defined('_DOWN_ADD_CAT')) DEFINE('_DOWN_ADD_CAT','Aggiungi categoria');

if (!defined('_DOWN_DEL_CAT')) DEFINE('_DOWN_DEL_CAT','Cancella categoria');

if (!defined('_DOWN_EDIT_CAT')) DEFINE('_DOWN_EDIT_CAT','Modifica categoria');

if (!defined('_DOWN_UP_NEITHER')) DEFINE('_DOWN_UP_NEITHER','Nessuno dei due');

if (!defined('_DOWN_UP_DOWNLOAD_ONLY')) DEFINE('_DOWN_UP_DOWNLOAD_ONLY','Scarica soltanto');

if (!defined('_DOWN_UP_UPLOAD_ONLY')) DEFINE('_DOWN_UP_UPLOAD_ONLY','Carica soltanto');

if (!defined('_DOWN_UP_BOTH')) DEFINE('_DOWN_UP_BOTH','Entrambi');

if (!defined('_DOWN_USERS_PERMITTED')) DEFINE('_DOWN_USERS_PERMITTED','Agli utenti &egrave; permesso:');

if (!defined('_DOWN_VISITORS_PERMITTED')) DEFINE('_DOWN_VISITORS_PERMITTED','Ai visitatori &egrave; permesso:');

if (!defined('_DOWN_UP_ABSOLUTE_PATH')) DEFINE('_DOWN_UP_ABSOLUTE_PATH','Percorso assoluto (facoltativo)');

if (!defined('_DOWN_ADD_FOLDER')) DEFINE('_DOWN_ADD_FOLDER','Aggiungi cartella');

if (!defined('_DOWN_DEL_FOLDER')) DEFINE('_DOWN_DEL_FOLDER','Cancella cartella');

if (!defined('_DOWN_EDIT_FOLDER')) DEFINE('_DOWN_EDIT_FOLDER','Modifica cartella');

if (!defined('_DOWN_ADD_FILE')) DEFINE('_DOWN_ADD_FILE','Aggiungi file');

if (!defined('_DOWN_DEL_FILE')) DEFINE('_DOWN_DEL_FILE','Cancella file');

if (!defined('_DOWN_EDIT_FILE')) DEFINE('_DOWN_EDIT_FILE','Modifica file');

if (!defined('_DOWN_PUB')) DEFINE('_DOWN_PUB','Pubblicati:');

if (!defined('_DOWN_SUBMIT_ANOTHER')) DEFINE('_DOWN_SUBMIT_ANOTHER','Inviare adesso un altro file?');

if (!defined('_DOWN_SUBMIT_INSPECT')) DEFINE('_DOWN_SUBMIT_INSPECT','Vedere il file appena inviato?');

if (!defined('_YES')) DEFINE('_YES','s&igrave');

if (!defined('_NO')) DEFINE('_NO','No');

if (!defined('_GLOBAL')) DEFINE('_GLOBAL','Globale');

if (!defined('_DOWN_DESC')) DEFINE('_DOWN_DESC','Descrizione:');

if (!defined('_DOWN_DOWNLOADS')) DEFINE('_DOWN_DOWNLOADS','Scaricati:');

if (!defined('_DOWN_THUMBNAILS')) DEFINE('_DOWN_THUMBNAILS','Miniature:');

if (!defined('_DOWN_RATING')) DEFINE('_DOWN_RATING','Voto:');

if (!defined('_DOWN_VOTES')) DEFINE('_DOWN_VOTES','Voti totali:');

if (!defined('_DOWN_YOUR_VOTE')) DEFINE('_DOWN_YOUR_VOTE','Il tuo voto:');

if (!defined('_DOWN_RATE_BUTTON')) DEFINE('_DOWN_RATE_BUTTON','Vota');

if (!defined('_DOWN_ALREADY_VOTE')) DEFINE('_DOWN_ALREADY_VOTE','Hai gi&agrave; votato per questo download, grazie!');

if (!defined('_DOWN_FILE_TITLE')) DEFINE('_DOWN_FILE_TITLE','Titolo del file:');

if (!defined('_DOWN_FILE_TITLE_SORT')) DEFINE('_DOWN_FILE_TITLE_SORT','Titolo file');

if (!defined('_DOWN_REAL_NAME')) DEFINE('_DOWN_REAL_NAME','Nome fisico del file:');

if (!defined('_DOWNLOAD')) DEFINE('_DOWNLOAD','Scarica');

if (!defined('_DOWN_DOWNLOADS_SORT')) DEFINE('_DOWN_DOWNLOADS_SORT','Download');

if (!defined('_DOWN_SUB_BY')) DEFINE('_DOWN_SUB_BY','Inviato da:');

if (!defined('_DOWN_FILE_DATE')) DEFINE('_DOWN_FILE_DATE','Data del file:');

if (!defined('_DOWN_FILE_AUTHOR')) DEFINE('_DOWN_FILE_AUTHOR','Autore del file:');

if (!defined('_DOWN_FILE_VER')) DEFINE('_DOWN_FILE_VER','Versione del:');

if (!defined('_DOWN_FILE_SIZE')) DEFINE('_DOWN_FILE_SIZE','Dimensioni del file:');

if (!defined('_DOWN_FILE_TYPE')) DEFINE('_DOWN_FILE_TYPE','Tipo di file:');

if (!defined('_DOWN_FILE_COMPANY')) DEFINE('_DOWN_FILE_COMPANY','Compagnia:');

if (!defined('_DOWN_FILE_COMPANY_URL')) DEFINE('_DOWN_FILE_COMPANY_URL','URL del sito della compagnia:');

if (!defined('_DOWN_FILE_AUTHOR_URL')) DEFINE('_DOWN_FILE_AUTHOR_URL','URL del sito dell\'autore:');

if (!defined('_DOWN_FILE_RELEASE_DATE')) DEFINE('_DOWN_FILE_RELEASE_DATE','Data di rilascio (AAAA-MM-GG):');

if (!defined('_DOWN_FILE_STATUS')) DEFINE('_DOWN_FILE_STATUS','Stato:');

if (!defined('_DOWN_FILE_LANGUAGES')) DEFINE('_DOWN_FILE_LANGUAGES','Lingue supportate:');

if (!defined('_DOWN_FILE_REQUIREMENTS')) DEFINE('_DOWN_FILE_REQUIREMENTS','Requisiti:');

if (!defined('_DOWN_FILE_OPERATING_SYSTEM')) DEFINE('_DOWN_FILE_OPERATING_SYSTEM','Sistema operativo:');

if (!defined('_DOWN_SCREEN')) DEFINE('_DOWN_SCREEN','Immagini:');

if (!defined('_DOWN_SCREEN_CLICK')) DEFINE('_DOWN_SCREEN_CLICK','Clicca per vedere');

if (!defined('_DOWN_NA')) DEFINE('_DOWN_NA','N/D');

if (!defined('_DOWN_CAT_NAME')) DEFINE('_DOWN_CAT_NAME','Nome della categoria:');

if (!defined('_DOWN_SUB_BUTTON')) DEFINE('_DOWN_SUB_BUTTON','Vai');

if (!defined('_DOWN_ALL_DONE')) DEFINE('_DOWN_ALL_DONE','Finito!');

if (!defined('_DOWN_NOT_AUTH')) DEFINE('_DOWN_NOT_AUTH','Non autorizzato!');

if (!defined('_DOWN_FOLDER_NAME')) DEFINE('_DOWN_FOLDER_NAME','Nome della cartella:');

if (!defined('_DOWN_FOLDER_ADD_BUT')) DEFINE('_DOWN_FOLDER_ADD_BUT','Aggiungi cartella');

if (!defined('_DOWN_UP_WAIT')) DEFINE('_DOWN_UP_WAIT','N.B.: tutti gli invii saranno vagliati prima della pubblicazione.');

if (!defined('_DOWN_AUTOAPP')) DEFINE('_DOWN_AUTOAPP','Il tuo file &egrave; stato approvato e pubblicato automaticamente.');

if (!defined('_DOWN_APPROVE_PUB')) DEFINE('_DOWN_APPROVE_PUB','Approva + Pubblica');

if (!defined('_DOWN_SUGGEST_LOC')) DEFINE('_DOWN_SUGGEST_LOC','Suggerisci luogo:');

if (!defined('_DOWNLOAD_URL')) DEFINE('_DOWNLOAD_URL','URL del download:');

if (!defined('_DOWN_ICON')) DEFINE('_DOWN_ICON','Icona:');

if (!defined('_DOWN_MOVE_FILE')) DEFINE('_DOWN_MOVE_FILE','Sposta il file:');

if (!defined('_DOWN_MOVE_FILE_FAILED')) DEFINE('_DOWN_MOVE_FILE_FAILED','Spostamento del file fallito!');

if (!defined('_DOWN_FILE_NEW_LOC')) DEFINE('_DOWN_FILE_NEW_LOC','Nuova posizione per il file:');

if (!defined('_DOWN_AWAIT_APPROVE')) DEFINE('_DOWN_AWAIT_APPROVE','Attesa dell\'approvazione per l\'invio del file:');

if (!defined('_DOWN_ADMIN_APPROVE')) DEFINE('_DOWN_ADMIN_APPROVE','Approva invii');

if (!defined('_DOWN_ID')) DEFINE('_DOWN_ID','ID');

if (!defined('_DOWN_SUBMIT_DATE')) DEFINE('_DOWN_SUBMIT_DATE','Inviato/i in data:');

if (!defined('_DOWN_APP_SUB_BUTTON')) DEFINE('_DOWN_APP_SUB_BUTTON','Approva invio');

if (!defined('_DOWN_DEL_SUB_BUTTON')) DEFINE('_DOWN_DEL_SUB_BUTTON','Cancella invio');

if (!defined('_DOWN_SUB_APPROVE')) DEFINE('_DOWN_SUB_APPROVE','L\'invio &egrave; stato approvato.');

if (!defined('_DOWN_SUB_DEL')) DEFINE('_DOWN_SUB_DEL','L\'invio &egrave; stato cancellato.');

if (!defined('_DOWN_NO_SUB')) DEFINE('_DOWN_NO_SUB','Nessun altro invio da approvare.');

if (!defined('_DOWN_REV_SUB')) DEFINE('_DOWN_REV_SUB','Esamina ulteriori invii');

if (!defined('_DOWN_SEARCH')) DEFINE('_DOWN_SEARCH','Cerca nel deposito');

if (!defined('_DOWN_SEARCH_TEXT')) DEFINE('_DOWN_SEARCH_TEXT','Cerca per:');

if (!defined('_DOWN_SEARCH_FILETITLE')) DEFINE('_DOWN_SEARCH_FILETITLE','Carca titoli dei file:');

if (!defined('_DOWN_SEARCH_FILEDESC')) DEFINE('_DOWN_SEARCH_FILEDESC','cerca descrizione dei file:');

if (!defined('_DOWN_SEARCH_ERR')) DEFINE('_DOWN_SEARCH_ERR','Devi specificare almeno un campo di ricerca, insieme al testo da cercare.');

if (!defined('_DOWN_SEARCH_NORES')) DEFINE('_DOWN_SEARCH_NORES','Nessun file trovato.');

if (!defined('_DOWN_FILE_HOMEPAGE')) DEFINE('_DOWN_FILE_HOMEPAGE','Homepage del file:');

if (!defined('_DOWN_UPDATE_SUB')) DEFINE('_DOWN_UPDATE_SUB','Aggiorna invio');

if (!defined('_DOWN_UP_EDIT_ID')) DEFINE('_DOWN_UP_EDIT_ID','FileID:');

if (!defined('_DOWN_FILE_DEL_NOTE')) DEFINE('_DOWN_FILE_DEL_NOTE','Nota: la vecchia lista dei file &egrave; stata rimossa dal database, ma il file fisico <b>potrebbe</b> esistere ancora!');

if (!defined('_DOWN_SUB_DATE')) DEFINE('_DOWN_SUB_DATE','Inviato il ');

if (!defined('_DOWN_SUB_DATE_SORT')) DEFINE('_DOWN_SUB_DATE_SORT','Data invio');

if (!defined('_DOWN_SUB_ID_SORT')) DEFINE ('_DOWN_SUB_ID_SORT', 'Inviato da');

if (!defined('_DOWN_COMMENTS')) DEFINE('_DOWN_COMMENTS','Commenti:');

if (!defined('_DOWN_YOUR_COMM')) DEFINE('_DOWN_YOUR_COMM','Il tuo commento:');

if (!defined('_DOWN_LEAVE_COMM')) DEFINE('_DOWN_LEAVE_COMM','Lascia un commento');

if (!defined('_DOWN_FIRST_COMMENT')) DEFINE('_DOWN_FIRST_COMMENT','Commenta questo file per primo!');

if (!defined('_DOWN_FIRST_COMMENT_NL')) DEFINE('_DOWN_FIRST_COMMENT_NL','Commenta per primo! Accedi o registrati, per favore.');

if (!defined('_DOWN_ALREADY_COMM')) DEFINE('_DOWN_ALREADY_COMM','Hai gi&agrave; commentato questo file.');

if (!defined('_DOWN_MAX_COMM')) DEFINE("_DOWN_MAX_COMM","Max: $Small_Text_Len caratteri");

if (!defined('_DOWN_DESC_MAX')) DEFINE("_DOWN_DESC_MAX","Max: $Large_Text_Len caratteri");

if (!defined('_DOWN_MAIL_SUB')) DEFINE('_DOWN_MAIL_SUB','Nuovo invio di download in Mambo');

if (!defined('_DOWN_MAIL_MSG')) DEFINE('_DOWN_MAIL_MSG','Salve, l\'utente $user_full ha inviato un nuovo download'

                   .' per il sito $mosConfig_sitename.\n'

                   .'Per cortesia, vai a $mosConfig_live_site/administrator/index.php come amministratore per esaminare ed approvare questo invio.\n\n'

                   .'Non rispondere a questo messaggio, perch&egrave; &egrave; generato automaticamente a solo scopo informativo.\n');

if (!defined('_DOWN_MAIL_MSG_APP')) DEFINE('_DOWN_MAIL_MSG_APP','Salve, l\'utente $user_full ha inviato un nuovo download'

                   .' per il sito $mosConfig_sitename.\n'

                   .'Secondo quanto disposto nelle opzioni di configurazione, l\'invio &egrave; stato approvato automaticamente.\n\n'

                   .'Non rispondere a questo messaggio, perch&egrave; &egrave; generato automaticamente a solo scopo informativo.\n');

if (!defined('_DOWN_ORDER_BY')) DEFINE('_DOWN_ORDER_BY','Ordinato per :');

if (!defined('_DOWN_RESET')) DEFINE('_DOWN_RESET','Ricalcola conteggio file');

if (!defined('_DOWN_RESET_GO')) DEFINE('_DOWN_RESET_GO','Sto ricalcolando il conteggio dei file...');

if (!defined('_DOWN_RESET_DONE')) DEFINE('_DOWN_RESET_DONE','Ricalcolo del conteggio dei file completato!');

if (!defined('_DOWN_FIND_ORPHANS')) DEFINE('_DOWN_FIND_ORPHANS','Trova file orfani');

if (!defined('_DOWN_DEL_ORPHANS')) DEFINE('_DOWN_DEL_ORPHANS','Cancella file orfani');

if (!defined('_DOWN_ORPHAN_SELECT')) DEFINE('_DOWN_ORPHAN_SELECT','Seleziona');

if (!defined('_DOWN_ORPHAN_FILE_DEL')) DEFINE('_DOWN_ORPHAN_FILE_DEL','File da cancellare');

if (!defined('_DOWN_ORPHAN_NODEL')) DEFINE('_DOWN_ORPHAN_NODEL','Nessun file da cancellare');

if (!defined('_DOWN_ORPHAN_DONE')) DEFINE('_DOWN_ORPHAN_DONE','File orfani cancellati');

if (!defined('_DOWN_BAD_POST')) DEFINE('_DOWN_BAD_POST','Le impostazioni non sono state inviate correttamente dal modulo.');

if (!defined('_DOWN_SUB_WAIT')) DEFINE('_DOWN_SUB_WAIT','Un invio aggiornato sta gi&agrave; attendendo un\'approvazione per questo file.');

if (!defined('_DOWN_REG_ONLY')) DEFINE('_DOWN_REG_ONLY','Solo utenti registrati:');

if (!defined('_DOWN_RESTRICTED_WARN')) DEFINE('_DOWN_RESTRICTED_WARN','Spiacente, questa area non esiste o non &egrave; disponibile');

if (!defined('_DOWN_MEMBER_ONLY_WARN')) DEFINE('_DOWN_MEMBER_ONLY_WARN',"Questa area &egrave; riservata ai memebri del gruppo.<BR />"

                             ."Non puoi accedere a questi files senza autorizzazione");

if (!defined('_DOWN_REG_ONLY_WARN')) DEFINE("_DOWN_REG_ONLY_WARN","Questa area &egrave; accessibile solo agli utenti registrati.<BR />"

                             ."Per favore, accedi o <a href='http://www.samples-share.com/component/comprofiler/registers.html'>registrati</a>.");

if (!defined('_DOWN_COUNT_EXCEEDED')) DEFINE('_DOWN_COUNT_EXCEEDED',"Spiacente il tuo limite di download &egrave; stato raggiunto");

if (!defined('_DOWN_COUNT_EXCEEDED_FILE')) DEFINE('_DOWN_COUNT_EXCEEDED_FILE',"Spiacente il tuo limite di download per questo file &egrave; stato raggiunto");

if (!defined('_DOWN_NO_FILEN')) DEFINE('_DOWN_NO_FILEN','Per cortesia, immetti un nome per il file');

if (!defined('_DOWN_MINI_SCREEN_PROMPT')) DEFINE('_DOWN_MINI_SCREEN_PROMPT','Mostra un\'immagine ridotta nella lista dei file:');

if (!defined('_DOWN_SEL_LOC_PROMPT')) DEFINE('_DOWN_SEL_LOC_PROMPT','Seleziona un\'area');

if (!defined('_DOWN_ALL_LOC_PROMPT')) DEFINE('_DOWN_ALL_LOC_PROMPT','Tutte le aree');

if (!defined('_DOWN_SEL_CAT_DEL')) DEFINE('_DOWN_SEL_CAT_DEL','Seleziona una categoria da cancellare');

if (!defined('_DOWN_NO_CAT_DEF')) DEFINE('_DOWN_NO_CAT_DEF','Nessuna categoria &egrave; stata definita');

if (!defined('_DOWN_PUB_PROMPT')) DEFINE('_DOWN_PUB_PROMPT','Seleziona una categoria per ');

if (!defined('_DOWN_SEL_FILE_DEL')) DEFINE('_DOWN_SEL_FILE_DEL','Seleziona un file da cancellare');

if (!defined('_DOWN_CONFIG_COMP')) DEFINE('_DOWN_CONFIG_COMP','I dettagli di configurazione sono stati aggiornati!');

if (!defined('_DOWN_CONFIG_ERR')) DEFINE("_DOWN_CONFIG_ERR","Si &egrave; verifcato un errore!\nNon posso aprire il file config per scriverlo!");

if (!defined('_DOWN_CATS')) DEFINE('_DOWN_CATS','Categorie');

if (!defined('_DOWN_PARENT_CAT')) DEFINE('_DOWN_PARENT_CAT','Categoria madre');

if (!defined('_DOWN_PARENT_FOLDER')) DEFINE('_DOWN_PARENT_FOLDER','cartella madre');

if (!defined('_DOWN_PUB1')) DEFINE('_DOWN_PUB1','Pubblicato');

if (!defined('_DOWN_RECORDS')) DEFINE('_DOWN_RECORDS','Registri');

if (!defined('_DOWN_ACCESS')) DEFINE('_DOWN_ACCESS','Accesso');

if (!defined('_DOWN_GROUP')) DEFINE('_DOWN_GROUP','Gruppo');

if (!defined('_DOWN_FILE_SYSTEM')) DEFINE('_DOWN_FILE_SYSTEM','File System');

if (!defined('_DOWN_FILE_SYSTEM_OK')) DEFINE('_DOWN_FILE_SYSTEM_OK','File System - OK');

if (!defined('_DOWN_DIRECTORY_NON_EXISTENT')) DEFINE('_DOWN_DIRECTORY_NON_EXISTENT','Cartella inesistente');

if (!defined('_DOWN_NOT_WRITEABLE')) DEFINE('_DOWN_NOT_WRITEABLE','Cartella non scrivibile');

if (!defined('_DOWN_WRITEABLE')) DEFINE('_DOWN_WRITEABLE','Cartella scrivibile');

if (!defined('_DOWN_DATABASE')) DEFINE('_DOWN_DATABASE','Database');

if (!defined('_DOWN_ADMIN_CPANEL_STORE')) DEFINE ('_DOWN_ADMIN_CPANEL_STORE','Memorizzazione di default per i nuovi contenitori:');

if (!defined('_DOWN_ADMIN_CPANEL_FILESTORE')) DEFINE ('_DOWN_ADMIN_CPANEL_FILESTORE','Magazzino di default per il file system:');

if (!defined('_DOWN_ADMIN_CPANEL_UPLOADS')) DEFINE ('_DOWN_ADMIN_CPANEL_UPLOADS','Area di attesa per gli invii:');

if (!defined('_DOWN_CPANEL_RETURN')) DEFINE ('_DOWN_CPANEL_RETURN','Pannello di controllo');

if (!defined('_DOWN_CPANEL_SUB_BASIC')) DEFINE ('_DOWN_CPANEL_SUB_BASIC','Gestione di base');

if (!defined('_DOWN_CPANEL_SUB_FILES')) DEFINE ('_DOWN_CPANEL_SUB_FILES','Trattamento dei file');

if (!defined('_DOWN_CPANEL_SUB_HKEEP')) DEFINE ('_DOWN_CPANEL_SUB_HKEEP','Operazioni ausiliarie');

if (!defined('_DOWN_CPANEL_SUB_UTILS')) DEFINE ('_DOWN_CPANEL_SUB_UTILS','Utilit&agrave;');

if (!defined('_DOWN_CPANEL_SUB_INFO')) DEFINE ('_DOWN_CPANEL_SUB_INFO','Informazioni');

if (!defined('_DOWN_ADMIN_ACT_CONTAINERS')) DEFINE('_DOWN_ADMIN_ACT_CONTAINERS','Gestione contenitori');

if (!defined('_DOWN_ADMIN_ACT_FILES')) DEFINE('_DOWN_ADMIN_ACT_FILES','Gestione file');

if (!defined('_DOWN_ADMIN_ACT_GROUPS')) DEFINE('_DOWN_ADMIN_ACT_GROUPS','Gestione gruppi');

if (!defined('_DOWN_ADMIN_ACT_UPLOADS')) DEFINE('_DOWN_ADMIN_ACT_UPLOADS','Approva invii');

if (!defined('_DOWN_ADMIN_ACT_CONFIG')) DEFINE('_DOWN_ADMIN_ACT_CONFIG','Configurazione');

if (!defined('_DOWN_ADMIN_ACT_UNLINKED')) DEFINE('_DOWN_ADMIN_ACT_UNLINKED','Trattamento file orfani/slegati');

if (!defined('_DOWN_ADMIN_ACT_FTP')) DEFINE('_DOWN_ADMIN_ACT_FTP','Invio di massa dei file dal server');

if (!defined('_DOWN_ADMIN_ACT_MISSING')) DEFINE('_DOWN_ADMIN_ACT_MISSING','Elenca file mancanti');

if (!defined('_DOWN_ADMIN_ACT_COUNTS')) DEFINE('_DOWN_ADMIN_ACT_COUNTS','Ricalcola conteggio file');

if (!defined('_DOWN_ADMIN_ACT_DOWNLOADS')) DEFINE('_DOWN_ADMIN_ACT_DOWNLOADS','Imposta tutti i contatori dei download a zero');

if (!defined('_DOWN_ADMIN_ACT_PRUNE')) DEFINE('_DOWN_ADMIN_ACT_PRUNE','Rimuovi tutte le vecchie voci di log del file');

if (!defined('_DOWN_ADMIN_ACT_THUMBS')) DEFINE('_DOWN_ADMIN_ACT_THUMBS','Verifica l\'integrit&agrave; delle miniature');

if (!defined('_DOWN_ADMIN_ACT_DBCONVERT')) DEFINE('_DOWN_ADMIN_ACT_DBCONVERT','Converti database pre 3.20');

if (!defined('_DOWN_ADMIN_ACT_DBCONVERT2')) DEFINE('_DOWN_ADMIN_ACT_DBCONVERT2','Converti databse pre 3.40');

if (!defined('_DOWN_ADMIN_ACT_STATS')) DEFINE('_DOWN_ADMIN_ACT_STATS','Statistiche');

if (!defined('_DOWN_ADMIN_ACT_ABOUT')) DEFINE('_DOWN_ADMIN_ACT_ABOUT','Riguardo Remository');

if (!defined('_DOWN_ADMIN_ACT_SUPPORT')) DEFINE('_DOWN_ADMIN_ACT_SUPPORT','Servizi di supporto e sviluppo');

if (!defined('_DOWN_MOST_DOWNLOADED')) DEFINE('_DOWN_MOST_DOWNLOADED','I più scaricati');

if (!defined('_DOWN_MOST_DOWNLOADED_LONG')) DEFINE('_DOWN_MOST_DOWNLOADED_LONG','File con il più alto numero di download');

if (!defined('_DOWN_POPULAR')) DEFINE ('_DOWN_POPULAR','File popolari');

if (!defined('_DOWN_POPULAR_LONG')) DEFINE ('_DOWN_POPULAR_LONG','I file pi&ugrave; scaricati negli ultimi %s giorni');

if (!defined('_DOWN_NEWEST')) DEFINE ('_DOWN_NEWEST','File recenti');

if (!defined('_DOWN_NEWEST_LONG')) DEFINE ('_DOWN_NEWEST_LONG','L\'ultimo dal nostro deposito di file');

if (!defined('_DOWN_VISITORS')) DEFINE('_DOWN_VISITORS','Visitatori');

if (!defined('_DOWN_REG_USERS')) DEFINE ('_DOWN_REG_USERS','Utenti registrati');

if (!defined('_DOWN_OTHER_USERS')) DEFINE ('_DOWN_OTHER_USERS', 'Altri utenti');

if (!defined('_DOWN_STORAGE_STATUS')) DEFINE ('_DOWN_STORAGE_STATUS','Stato della memorizzazione');

if (!defined('_DOWN_ALL_REGISTERED')) DEFINE('_DOWN_ALL_REGISTERED','Tutti gli utenti registrati');

if (!defined('_DOWN_REG_ONLY_TITLE')) DEFINE('_DOWN_REG_ONLY_TITLE','Solo registrati');

if (!defined('_DOWN_PUBLIC_TITLE')) DEFINE('_DOWN_PUBLIC_TITLE','Pubblico');

if (!defined('_DOWN_APPROVE_TITLE')) DEFINE('_DOWN_APPROVE_TITLE','File da approvare');

if (!defined('_DOWN_DATE')) DEFINE('_DOWN_DATE','Data');

if (!defined('_DOWN_NAME_TITLE')) DEFINE('_DOWN_NAME_TITLE','Nome');

if (!defined('_DOWN_CONFIG_TITLE')) DEFINE('_DOWN_CONFIG_TITLE','Config');

if (!defined('_DOWN_CONFIG_TITLE1')) DEFINE('_DOWN_CONFIG_TITLE1','Percorsi/varie');

if (!defined('_DOWN_CONFIG_TITLE2')) DEFINE('_DOWN_CONFIG_TITLE2','Scelte');

if (!defined('_DOWN_CONFIG_TITLE3')) DEFINE('_DOWN_CONFIG_TITLE3','D/L text');

if (!defined('_DOWN_CONFIG_TITLE4')) DEFINE('_DOWN_CONFIG_TITLE4','Personalizza');

if (!defined('_DOWN_CONFIG_TITLE_PREAMBLE')) DEFINE('_DOWN_CONFIG_TITLE_PREAMBLE','Intro');

if (!defined('_DOWN_CONFIG1')) DEFINE("_DOWN_CONFIG1","TabClass:<br/><i>(MOS CSS Colori della colonna alternati (due valori separati da una virgola)</i>");

if (!defined('_DOWN_CONFIG2')) DEFINE("_DOWN_CONFIG2","TabHeader:<br/><i>(MOS CSS Inestazione della pagina e sfondo del pannello amministratore)</i>");

if (!defined('_DOWN_CONFIG3')) DEFINE("_DOWN_CONFIG3","Web_Down_Path:<br/><i>(Percorso di memorizzazione dei download - Web - NO Trailing Slash)</i>");

if (!defined('_DOWN_CONFIG4')) DEFINE("_DOWN_CONFIG4","Down_Path:<br/><i>(Percorso di memorizzazione dei download - File - NO Trailing Slash)</i>");

if (!defined('_DOWN_CONFIG5')) DEFINE("_DOWN_CONFIG5","Up_Path:<br/><i>(Percorso di memorizzazione degli upload - NO Trailing Slash)</i>");

if (!defined('_DOWN_CONFIG6')) DEFINE("_DOWN_CONFIG6","MaxSize:<br/><i>(Dimensione massima consentita dei file da inviare, in kB)</i>");

if (!defined('_DOWN_CONFIG7')) DEFINE("_DOWN_CONFIG7","Max_Up_Per_Day:<br/><i>(Numero massimo di upload consentiti per utente al giorno (l\'amministratore non ha limiti))</i>");

if (!defined('_DOWN_CONFIG8')) DEFINE("_DOWN_CONFIG8","Max_Up_Dir_Space:<br/><i>(Spazio massimo disponibile nella cartella di upload in kB)</i>");

if (!defined('_DOWN_CONFIG9')) DEFINE("_DOWN_CONFIG9","ExtsOk:<br/><i>(Estensioni consentite per gli upload (lista separata da virgole))</i>");

if (!defined('_DOWN_CONFIG10')) DEFINE("_DOWN_CONFIG10","Allowed_Desc_Tags:<br/><i>(Tag HTML consentiti per la descrizione dei file (lista separata da virgole))</i>");

if (!defined('_DOWN_CONFIG11')) DEFINE("_DOWN_CONFIG11","Allow_Up_Overwrite:<br/><i>(Consenti ai nnuovi invii di sovrascrivere file esistenti)</i>");

if (!defined('_DOWN_CONFIG12')) DEFINE("_DOWN_CONFIG12","Allow_User_Sub:<br/><i>(Consenti agli utenti di inviare file - I file dovranno essere approvati dall'amministratore)</i>");

if (!defined('_DOWN_CONFIG13')) DEFINE("_DOWN_CONFIG13","Allow_User_Edit:<br/><i>(Consenti agli utenti di modificare i file inviati - dovranno essere approvati nuovamente dall\'amministratore)</i>");

if (!defined('_DOWN_CONFIG14')) DEFINE("_DOWN_CONFIG14","Allow_User_Up:<br/><i>(Consenti agli utenti di inviare file - I file dovranno essere approvati dall'amministratore)</i>");

if (!defined('_DOWN_CONFIG15')) DEFINE("_DOWN_CONFIG15","Allow_Comments:<br/><i>(Consenti agli utenti di lasciare commenti sui file)</i>");

if (!defined('_DOWN_CONFIG16')) DEFINE("_DOWN_CONFIG16","Send_Sub_Mail:<br/><i>(Invia un\'e-mail all\'amministratore (o sostituto) quando un utente invia un file)</i>");

if (!defined('_DOWN_CONFIG17')) DEFINE("_DOWN_CONFIG17","Sub_Mail_Alt_Addr:<br/><i>(Indirizzo e-mail alternativo a cui inviare gli avvisi di invio (altrimenti verr&agrave usata l\'e-mail dell\'amministratore)</i>");

if (!defined('_DOWN_CONFIG18')) DEFINE("_DOWN_CONFIG18","Sub_Mail_Alt_Name:<br/><i>(Nome alternativo per chi riceve gli avvisi di invio)</i>");

if (!defined('_DOWN_CONFIG19')) DEFINE("_DOWN_CONFIG19","HeaderPic:<br/><i>(Grafica dell\'intestazione)</i>");

if (!defined('_DOWN_CONFIG20')) DEFINE("_DOWN_CONFIG20","Anti-Leach:<br/><i>(Nascondi i file per prevenire collegamenti diretti)</i>");

if (!defined('_DOWN_CONFIG21')) DEFINE("_DOWN_CONFIG21","Large_Text_Len:<br/><i>(Massima lunghezza memorizzata dei campi di testo larghi (Desc/Licenza))</i>");

if (!defined('_DOWN_CONFIG22')) DEFINE("_DOWN_CONFIG22","Small_Text_Len:<br/><i>(Massima Lunghezza memorizzata Stored Length of Small Input/Text Fields)</i>");

if (!defined('_DOWN_CONFIG23')) DEFINE("_DOWN_CONFIG23","Small_Image_Width:<br/><i>(Larghezza delle videate piccole)</i>");

if (!defined('_DOWN_CONFIG24')) DEFINE("_DOWN_CONFIG24","Small_Image_Height:<br/><i>(Altezza delle videate piccole)</i>");

if (!defined('_DOWN_CONFIG25')) DEFINE("_DOWN_CONFIG25","Allow_Votes:<br/><i>(Consenti agli utenti di assegnare voti ai file</i>");

if (!defined('_DOWN_CONFIG26')) DEFINE("_DOWN_CONFIG26","Enable_Admin_Autoapp:<br/><i>(Invii da parte dell\'amministratore approvati e pubblicati automaticamente)</i>");

if (!defined('_DOWN_CONFIG27')) DEFINE("_DOWN_CONFIG27","Enable_User_Autoapp:<br/><i>(Invii da parte degli utenti approvati e pubblicati automaticamente)</i>");

if (!defined('_DOWN_CONFIG28')) DEFINE("_DOWN_CONFIG28","Enable_List_Downloads:<br/><i>(Consenti di scaricare da liste di file nelle categorie/cartelle)</i>");

if (!defined('_DOWN_CONFIG29')) DEFINE("_DOWN_CONFIG29","Allow users to submit remote files:<br/><i>(File che sono ospitati altrove)</i>");

if (!defined('_DOWN_CONFIG30')) DEFINE("_DOWN_CONFIG30","Favourites_Max:<br/><i>(Allow Users to record this many favourite files)</i>");

if (!defined('_DOWN_CONFIG31')) DEFINE("_DOWN_CONFIG31","Date_Format:<br/><i>(Formato della data per Remository - parametro della funzione data di PHP 1)</i>");

if (!defined('_DOWN_CONFIG32')) DEFINE("_DOWN_CONFIG32","Default_Version:<br/><i>(Valore di default da assegnare alla versione del file in occasione di un nuovo invio)</i>");

if (!defined('_DOWN_CONFIG33')) DEFINE('_DOWN_CONFIG33','See_Containers_no_download:<br/><i>(Consenti agli utenti di vedere categorie/cartelle nelle aree dove non possono scaricare)</i>');

if (!defined('_DOWN_CONFIG34')) DEFINE('_DOWN_CONFIG34','See_Files_no_download:<br/><i>(Consenti agli utenti di vedere i file che non possono scaricare)</i>');

if (!defined('_DOWN_CONFIG35')) DEFINE('_DOWN_CONFIG35','Max_Thumbnails:<br/><i>(Massimo numero di miniature da memorizzare; 0 per memorizzare solo lo URL)</i>');

if (!defined('_DOWN_CONFIG36')) DEFINE("_DOWN_CONFIG36","Large_Image_Width:<br/><i>(Larghezza videata grande)</i>");

if (!defined('_DOWN_CONFIG37')) DEFINE("_DOWN_CONFIG37","Large_Image_Height:<br/><i>(Altezza videata grande)</i>");

if (!defined('_DOWN_CONFIG38')) DEFINE("_DOWN_CONFIG38","Allow Large Image Display:<br/><i>(Finestra di pop-up per la videata grande)</i>");

if (!defined('_DOWN_CONFIG39')) DEFINE("_DOWN_CONFIG39","Memorizza file nel database, di default");

if (!defined('_DOWN_CONFIG40')) DEFINE("_DOWN_CONFIG40","Max_Down_Per_Day:<br/><i>(Massimo numero di download al giorno consentiti ad un utente (l'\amministratore non ha limiti))</i>");

if (!defined('_DOWN_CONFIG41')) DEFINE("_DOWN_CONFIG41","Max_Down_File_Day:<br/><i>(Massimo numero di download al giorno per un file consentiti ad un utente (l\'amministratore non ha limiti))</i>");

if (!defined('_DOWN_CONFIG42')) DEFINE("_DOWN_CONFIG42","Allow_User_Delete:<br/><i>(Il mittente &grave autorizzato a cancellare il file?)</i>");

if (!defined('_DOWN_CONFIG43')) DEFINE('_DOWN_CONFIG43','Make_Auto_Thumbnail:<br/><i>(Crea automaticamente le miniature per i file di immagini)</i>');

if (!defined('_DOWN_CONFIG44')) DEFINE("_DOWN_CONFIG44","Max_Down_Reg_Day:<br/><i>(Massimo numero di download al giorno consentiti ad un utente registrato (l'\amministratore non ha limiti))</i>");

if (!defined('_DOWN_STATS_TITLE')) DEFINE('_DOWN_STATS_TITLE','Statistiche');

if (!defined('_DOWN_TOP_TITLE')) DEFINE('_DOWN_TOP_TITLE','Massimo');

if (!defined('_DOWN_RATED_TITLE')) DEFINE('_DOWN_RATED_TITLE','Valutato');

if (!defined('_DOWN_VOTED_ON')) DEFINE('_DOWN_VOTED_ON','Votato');

if (!defined('_DOWN_VOTES_TITLE')) DEFINE('_DOWN_VOTES_TITLE','Voti');

if (!defined('_DOWN_RATING_TITLE')) DEFINE('_DOWN_RATING_TITLE','Valutazione');

if (!defined('_DOWN_ABOUT')) DEFINE('_DOWN_ABOUT','Al riguardo');

if (!defined('_DOWN_SUPPORT')) DEFINE('_DOWN_SUPPORT','Supporto');

if (!defined('_DOWN_ABOUT_DESCRIBE')) DEFINE('_DOWN_ABOUT_DESCRIBE','Remository - Un deposito di file per Joomla/MamboOpenSource 4.5+');

if (!defined('_DOWN_TITLE_ABOUT')) DEFINE('_DOWN_TITLE_ABOUT','Titolo');

if (!defined('_DOWN_VERSION_ABOUT')) DEFINE('_DOWN_VERSION_ABOUT','Versione');

if (!defined('_DOWN_AUTHOR_ABOUT')) DEFINE('_DOWN_AUTHOR_ABOUT','Autore');

if (!defined('_DOWN_WEBSITE_ABOUT')) DEFINE('_DOWN_WEBSITE_ABOUT','WebSite');

if (!defined('_DOWN_EMAIL_ABOUT')) DEFINE('_DOWN_EMAIL_ABOUT','EMail');

if (!defined('_DOWN_SEL_FILE_APPROVE')) DEFINE('_DOWN_SEL_FILE_APPROVE','Seleziona un file da approvare');

if (!defined('_DOWN_DESC_SMALL')) DEFINE('_DOWN_DESC_SMALL','Breve descrizione:');

if (!defined('_DOWN_DESC_SMALL_MAX')) DEFINE('_DOWN_DESC_SMALL_MAX','Max: 150 caratteri');

if (!defined('_DOWN_AUTO_SHORT')) DEFINE('_DOWN_AUTO_SHORT','Genera automaticamente una breve descrizione:');

if (!defined('_DOWN_LICENSE')) DEFINE('_DOWN_LICENSE','Licenza:');

if (!defined('_DOWN_LICENSE_AGREE')) DEFINE('_DOWN_LICENSE_AGREE','Deve sottoscrivere la licenza:');

if (!defined('_DOWN_LEECH_WARN')) DEFINE('_DOWN_LEECH_WARN','La tua sessione non &egrave; stata validata e le misure anti-leach sono entrate in funzione.');

if (!defined('_DOWN_LICENSE_WARN')) DEFINE('_DOWN_LICENSE_WARN','Per cortesia, leggi/accetta la licenza, per scaricare.');

if (!defined('_DOWN_LICENSE_CHECKBOX')) DEFINE('_DOWN_LICENSE_CHECKBOX','Aderisco ai termini di cui sopra.');

if (!defined('_DOWN_DATE_FORMAT')) DEFINE("_DOWN_DATE_FORMAT","%d %B %Y"); //Uses PHP's strftime Command Format

if (!defined('_DOWN_FILE_NOTFOUND')) DEFINE('_DOWN_FILE_NOTFOUND','File non trovato');

if (!defined('_DOWN_ACCESS_GROUP')) DEFINE('_DOWN_ACCESS_GROUP','Gruppo autorizzato:');

if (!defined('_DOWN_THUMB_WRONG_TYPE')) DEFINE('_DOWN_THUMB_WRONG_TYPE','<h3>Le immagini per le miniature devono essere in formato png, jpg o jpeg</h3>');

if (!defined('_DOWN_EXTENSION_IN_TITLE')) DEFINE('_DOWN_EXTENSION_IN_TITLE','Estensione nel titolo');

if (!defined('_DOWN_INHERIT')) DEFINE('_DOWN_INHERIT','I contenitori che ne discendono sono da ereditare?');

if (!defined('_DOWN_FORCE_INHERIT')) DEFINE('_DOWN_FORCE_INHERIT','Forza i figli ad ereditare gli elementi segnati');

if (!defined('_DOWN_EXT_IN_TITLE')) DEFINE('_DOWN_EXT_IN_TITLE','Estensione nel titolo');

if (!defined('_DOWN_BULK_ADD_FILES')) DEFINE('_DOWN_BULK_ADD_FILES','Aggiungi file in massa dalla cartella di download');

if (!defined('_DOWN_NO_AVAILABLE_FILES')) DEFINE('_DOWN_NO_AVAILABLE_FILES','Nesusn file disponibile');

if (!defined('_DOWN_ABS_PATH_TO_FILES')) DEFINE('_DOWN_ABS_PATH_TO_FILES','Percorso assoluto al file:');

if (!defined('_DOWN_ACCEPTABLE_EXTENSIONS')) DEFINE('_DOWN_ACCEPTABLE_EXTENSIONS','Estensioni consentite (separate da una virgola):');

if (!defined('_DOWN_EXTENSION_IN_TITLE')) DEFINE('_DOWN_EXTENSION_IN_TITLE','Estensione inclusa nel titolo:');

if (!defined('_DOWN_DOWNLOAD_TEXT_BOX')) DEFINE('_DOWN_DOWNLOAD_TEXT_BOX','Finestra di testo del download');

if (!defined('_DOWN_MAIN_PREAMBLE')) DEFINE('_DOWN_MAIN_PREAMBLE', 'Testo per la pagina principale');

if (!defined('_DOWN_PRUNE_LOG')) DEFINE('_DOWN_PRUNE_LOG','Snellisci il file di log');

if (!defined('_DOWN_LOGFILE_CUTOFF_DATE')) DEFINE('_DOWN_LOGFILE_CUTOFF_DATE','Per favore, inserire la data prima della quale tutte le voci registrate nel file di log dovranno essere cancellate:');

if (!defined('_DOWN_PRESS_SAVE_ACTIVATE')) DEFINE('_DOWN_PRESS_SAVE_ACTIVATE','poi premere "Salva" per attivare.');

if (!defined('_DOWN_METHOD_NOT_PRESENT')) DEFINE('_DOWN_METHOD_NOT_PRESENT','Errore del componente %s: tentativo di usare il metodo inesistente %s in %s');

if (!defined('_DOWN_CLASS_NOT_PRESENT')) DEFINE('_DOWN_CLASS_NOT_PRESENT','Errore del componente %s: tentativo di usare la classe inesistente %s in %s');

if (!defined('_DOWN_NO_UPLOAD_TO_FILES')) DEFINE('_DOWN_NO_UPLOAD_TO_FILES','Non &grave possibile eseguire un invio di massa ad un contenitore con un percorso file');

if (!defined('_DOWN_COUNTS_RECALCULATED')) DEFINE('_DOWN_COUNTS_RECALCULATED','Conetggio dei file e delle cartelle ricalcolato');

if (!defined('_DOWN_COUNTS_RESET')) DEFINE('_DOWN_COUNTS_RESET','Conteggio degli scaricamenti del file azzerato');

if (!defined('_DOWN_OLD_LOG_REMOVED')) DEFINE('_DOWN_OLD_LOG_REMOVED','Voci del file di log registrate prima del %s rimosse.');

if (!defined('_DOWN_NONE_MISSING')) DEFINE('_DOWN_NONE_MISSING','nessun file mancante');

if (!defined('_DOWN_BLOB_NOCHUNKS')) DEFINE('_DOWN_BLOB_NOCHUNKS',' - BLOB TABLE HAS 0 CHUNKS');

if (!defined('_DOWN_CHUNKS_DISCREPANCY')) DEFINE('_DOWN_CHUNKS_DISCREPANCY',' - BLOB TABLE HAS %s CHUNKS, FILE SAYS %s');

if (!defined('_DOWN_PLAINTEXT_DISCREPANCY')) DEFINE('_DOWN_PLAINTEXT_DISCREPANCY',' - PLAIN TEXT TABLE HAS %s ENTRIES, SHOULD BE EXACTLY 1');

if (!defined('_DOWN_NOT_FOUND_HERE')) DEFINE('_DOWN_NOT_FOUND_HERE',' - NON TROVATO IN QUESTO POSTO<br/>');

if (!defined('_DOWN_LOCAL_NO_URL')) DEFINE('_DOWN_LOCAL_NO_URL','FILE NON LOCALE, MA NESSUN URL SPECIFICATO');

if (!defined('_DOWN_NOT_VALID_BR')) DEFINE('_DOWN_NOT_VALID_BR',' - NON VALIDATO<br/>');

if (!defined('_DOWN_NONE_MISSING')) DEFINE('_DOWN_NONE_MISSING','Nessun file mancante');

if (!defined('_DOWN_NO_RELEVANT_THUMB')) DEFINE('_DOWN_NO_RELEVANT_THUMB',' - nessun file rilevante - rimozione tentata.<br/>');

if (!defined('_DOWN_THUMB_NOT_BELONG')) DEFINE('_DOWN_THUMB_NOT_BELONG','%s nella cartella %s cui il file non appartiene - rimozione tentata.<br/>');

if (!defined('_DOWN_THUMB_NOT_IN_DB')) DEFINE('_DOWN_THUMB_NOT_IN_DB',' - nessun riscontro nel database - rimozione tentata.<br/>');

if (!defined('_DOWN_THUMB_OK')) DEFINE('_DOWN_THUMB_OK','Nessun problema rilevato con i file delle miniature');

if (!defined('_DOWN_DB_CONVERT_OK')) DEFINE('_DOWN_DB_CONVERT_OK','Conversione del database completata. Qualunque file messo in lista qui sopra era non era valido.');

if (!defined('_DOWN_ADD_NUMBER_FILES')) DEFINE('_DOWN_ADD_NUMBER_FILES','Aggiungere un numero di file');

if (!defined('_DOWN_DISPLAY_NUMBER')) DEFINE('_DOWN_DISPLAY_NUMBER','Mostra #');

if (!defined('_DOWN_SEARCH_COLON')) DEFINE('_DOWN_SEARCH_COLON','Cerca:');

if (!defined('_DOWN_SHOW_DESCENDANTS')) DEFINE('_DOWN_SHOW_DESCENDANTS','Mostra discendenti: ');

if (!defined('_DOWN_CLICK_TO_VISIT')) DEFINE('_DOWN_CLICK_TO_VISIT','Clicca per visitare il sito');

if (!defined('_DOWN_CONTAINERS')) DEFINE('_DOWN_CONTAINERS','Contenitori');

if (!defined('_DOWN_UP_PLAIN_TEXT')) DEFINE('_DOWN_UP_PLAIN_TEXT','I file possono essere memorizzati come testo semplice?');

if (!defined('_GLOBAL')) DEFINE('_GLOBAL','Globale');

if (!defined('_DOWN_KEYWORDS')) DEFINE('_DOWN_KEYWORDS', 'Parole chiave:');

if (!defined('_DOWN_WINDOW_TITLE')) DEFINE('_DOWN_WINDOW_TITLE','Titolo finestra:');

if (!defined('_DOWN_EDITOR_GROUP')) DEFINE('_DOWN_EDITOR_GROUP','gruppo modificatori:');

if (!defined('_DOWN_AUTO_FOR_ADMIN')) DEFINE('_DOWN_AUTO_FOR_ADMIN','Approva automaticamente per l\'amministratore:');

if (!defined('_DOWN_AUTO_FOR_USERS')) DEFINE('_DOWN_AUTO_FOR_USERS','Approva automaticamente per l\'utente:');

if (!defined('_DOWN_AUTO_USER_GROUP')) DEFINE('_DOWN_AUTO_USER_GROUP','Approva automaticamente per il gruppo utenti:');

if (!defined('_DOWN_CONTAINER_CASCADE')) DEFINE('_DOWN_CONTAINER_CASCADE','Applica a tutte le cartelle figlie:');

if (!defined('_DOWN_THANK_YOU')) DEFINE('_DOWN_THANK_YOU','Grazie per aver scaricato ');

if (!defined('_DOWN_WAIT_OR_CLICK')) DEFINE('_DOWN_WAIT_OR_CLICK','Se il tuo download non parte automaticamente dopo lacuni secondi, per favore, clicca sul colegamento al download qui sopra');

if (!defined('_DOWN_UPDATE_THUMBNAILS')) DEFINE('_DOWN_UPDATE_THUMBNAILS','Aggiorna miniature');

if (!defined('_DOWN_DELETE_THUMBNAIL')) DEFINE('_DOWN_DELETE_THUMBNAIL','Cancella miniature');

if (!defined('_DOWN_SUBMIT_NEW_THUMBNAIL')) DEFINE('_DOWN_SUBMIT_NEW_THUMBNAIL','Invia nuove miniature');

if (!defined('_DOWN_NOT_LOGGED_UPLOAD')) DEFINE('_DOWN_NOT_LOGGED_UPLOAD','Spiacente, non sei autorizzato ad inviare.  Per cortesia, accedi o registrati.');

if (!defined('_DOWN_NOT_LOGGED_COMMENT')) DEFINE('_DOWN_NOT_LOGGED_COMMENT','Spiacente, non sei autorizzato a commentare.  Per cortesia, accedi o registrati.');

if (!defined('_DOWN_NOT_LOGGED_VOTE')) DEFINE('_DOWN_NOT_LOGGED_VOTE','Spiacente, non sei autorizzato a votare.  Per cortesia, accedi o registrati.');

if (!defined('_DOWN_COMMENT_NL')) DEFINE('_DOWN_COMMENT_NL','Per cortesia, accedi o registrati per commentare');



// Define some summaries for tables

if (!defined('_TABLE_SUMMARY_GENHEAD')) DEFINE ('_TABLE_SUMMARY_GENHEAD', 'E\' disponibile la lista della categorie di downalod e del numero di file in ogni categoria.');

if (!defined('_TABLE_SUMMARY_ADDMANYFILES')) DEFINE ('_TABLE_SUMMARY_ADDMANYFILES', 'Questa tabella ti consente di selezionare file mutipli da inviare.');

// /components/com_remository/com_remository_upload.php

if (!defined('_DOWNLOAD_UPLOAD_TITLE')) DEFINE('_DOWNLOAD_UPLOAD_TITLE','Invia un file');

if (!defined('_HEAD')) DEFINE('_HEAD','Invia un file');

if (!defined('_FILE')) DEFINE('_FILE','Invia file:');

if (!defined('_CLOSE')) DEFINE('_CLOSE','Chiudi');

if (!defined('_SENDFILE')) DEFINE('_SENDFILE','Invia file');

if (!defined('_ERR1')) DEFINE('_ERR1','Questo file non era specificato.');

if (!defined('_ERR2')) DEFINE('_ERR2','Attacco invio file');

if (!defined('_ERR3')) DEFINE('_ERR3','Il file che hai cercato di inviare &egrave; di lunghezza zero!');

if (!defined('_ERR4')) DEFINE('_ERR4','Hai tentato di inviare un file con un\'estensione disabilitata!');

if (!defined('_ERR5')) DEFINE('_ERR5','Il file che hai cercato di inviare eccede la dimensione massima di ');

if (!defined('_ERR6')) DEFINE('_ERR6','Il file che hai cercato di inviare esiste gi&agrave;. Per piacere, specifica un nuovo nome per il file.');

if (!defined('_ERR7')) DEFINE('_ERR7','La cartella di invio &grave satura al momento.');

if (!defined('_ERR8')) DEFINE('_ERR8','Devi essere un amministratore o un utente registrato per inviare.');

if (!defined('_ERR9')) DEFINE('_ERR9','Hai raggiunto il tuo limite giornaliero di invio.');

if (!defined('_ERR10')) DEFINE('_ERR10','Gli invii da parte di utenti non &egrave; permesso.');

if (!defined('_ERR11')) DEFINE('_ERR11','L\'invio del file &egrave; fallito, con codice d\'errore ');

if (!defined('_UP_SUCCESS')) DEFINE('_UP_SUCCESS','Invio del file eseguito!');

if (!defined('_UPLOAD_URL_LOCK')) DEFINE('_UPLOAD_URL_LOCK','-File inviato-');

if (!defined('_WARNING1')) DEFINE('_WARNING1','Il percorso per i file &egrave; cambiato - ATTENZIONE file non spostati');

if (!defined('_WARNING2')) DEFINE('_WARNING2','Il percorso specificato non esiste - Crearne uno nuovo : ');

if (!defined('_APPROVE')) DEFINE('_APPROVE','Approva');

if (!defined('_CANCEL')) DEFINE('_CANCEL','Cancella');

if (!defined('_DOWN_MISSING_TITLE')) DEFINE('_DOWN_MISSING_TITLE','File mancanti');

if (!defined('_MBT_GROUP_MANAGER')) DEFINE('_MBT_GROUP_MANAGER','Gestore di gruppo');

if (!defined('_MBT_GROUP_FILTER')) DEFINE('_MBT_GROUP_FILTER','Filtra per nome');

if (!defined('_MBT_GROUP_GROUP')) DEFINE('_MBT_GROUP_GROUP','Gruppo');

if (!defined('_MBT_GROUP_DESCRIPTION')) DEFINE('_MBT_GROUP_DESCRIPTION','Descrizione');

if (!defined('_MBT_GROUP_EMAIL')) DEFINE('_MBT_GROUP_EMAIL','Email al gruppo');

if (!defined('_MBT_GROUP_ADD')) DEFINE('_MBT_GROUP_ADD','Aggiungi gruppo');

if (!defined('_MBT_GROUP_EDIT')) DEFINE('_MBT_GROUP_EDIT','Modifica gruppo');

if (!defined('_MBT_GROUP_EDIT')) DEFINE('_MBT_GROUP_EDIT','Modifica gruppo');

if (!defined('_MBT_GROUP_NAME')) DEFINE('_MBT_GROUP_NAME','Nome');

if (!defined('_MBT_GROUP_AVAI_USER')) DEFINE('_MBT_GROUP_AVAI_USER','Utenti disponibili');

if (!defined('_MBT_GROUP_SEL_USER')) DEFINE('_MBT_GROUP_SEL_USER','membri di questo gruppo');

if (!defined('_MBT_GROUP_MEMBERS')) DEFINE('_MBT_GROUP_MEMBERS','Membri');

if (!defined('_MBT_GROUP_SUBJECT')) DEFINE('_MBT_GROUP_SUBJECT','Oggetto');

if (!defined('_MBT_GROUP_MESSAGE')) DEFINE('_MBT_GROUP_MESSAGE','Messaggio');

if (!defined('_MBT_GROUP_SEND')) DEFINE('_MBT_GROUP_SEND','invia');

if (!defined('_MBT_GROUP_MISS_SUB')) DEFINE('_MBT_GROUP_MISS_SUB','Per favore, inserisci un oggetto');

if (!defined('_MBT_GROUP_MISS_MSG')) DEFINE('_MBT_GROUP_MISS_MSG','per favore, inserisci un messaggio');

if (!defined('_MBT_GROUP_MISS_GROUP')) DEFINE('_MBT_GROUP_MISS_GROUP','Per favore, inserisci il nome di un gruppo');

if (!defined('_MBT_GROUP_SEND_ADMIN')) DEFINE('_MBT_GROUP_SEND_ADMIN','Solo gli amministratori posson inviare e-mail!');

if (!defined('_MBT_GROUP_SEND_NOTARGET')) DEFINE('_MBT_GROUP_SEND_NOTARGET','Non c\'&grave un\'e-mail di destinazione nel gruppo');

if (!defined('_MBT_GROUP_SEND_OK')) DEFINE('_MBT_GROUP_SEND_OK','Mail inviata a ');

if (!defined('_MBT_GROUP_SEND_USERS')) DEFINE('_MBT_GROUP_SEND_USERS','Utenti');
