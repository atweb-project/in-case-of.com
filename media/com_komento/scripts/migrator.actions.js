Komento.module("migrator.actions",function(a){var b=this;Komento.require().script("admin.language","komento.common").done(function(){Komento.Controller("Migrator.Actions",{defaults:{"{migrateButton}":".migrateButton","{deleteButton}":".deleteButton"}},function(b){return{init:function(){b.progress=b.element.find(".migratorProgress"),b.migrator=b.element.find(".migratorTable")},"{migrateButton} click":function(a){a.checkClick()&&b.migrateStart()},migrateStart:function(){b.progress.controller().migratedComments().text("0"),b.progress.controller().progressBar().width("0%"),b.progress.controller().progressPercentage().text("0"),b.progress.controller().progressStatus().html('<img src="'+Komento.options.spinner+'" /> Migrating...'),b.migrator.controller().getStatistic()},migrateComplete:function(){b.progress.controller().progressStatus().text(a.language("COM_KOMENTO_MIGRATORS_PROGRESS_DONE")),b.progress.controller().log(a.language("COM_KOMENTO_MIGRATORS_LOG_COMPLETE")),b.migrateButton().enable()}}}),b.resolve()})});