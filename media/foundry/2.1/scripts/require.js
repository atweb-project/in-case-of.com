dispatch.to("Foundry/2.1 Core Plugins").at(function(e,t){e.require=function(){var t=function(t){return e.uri(t).setAnchor("").setQuery("").toPath("../")+""},n=function(e){var t=new n.batch(e);return n.batches[t.id]=t,t};return e.extend(n,{defaultOptions:{path:function(){var n=e.scriptPath||e("[require-path]").attr("require-path")||t(e("script:last").attr("src"))||t(window.location.href);return/^(\/|\.)/.test(n)&&(n=e.uri(window.location.href).toPath(n)+""),n}(),timeout:1e4,retry:3,verbose:e.environment=="development"},setup:function(t){e.extend(n.defaultOptions,t)},batches:{},batch:function(t){var r=this;r.id=e.uid(),r.manager=e.Deferred(),r.taskList=[],r.tasksFinalized=!1,r.options=e.extend({},n.defaultOptions,t)},status:function(){e.each(n.batches,function(t,n){console.info(n.id,n.state(),n),e.each(n.taskList,function(e,t){console.log("	 ["+t.name+"]",t.state())})})},loaders:{},addLoader:function(e,t){n[e]=t,n.batch.prototype[e]=function(){var e=this;return t.apply(e,arguments),e},n.loaders[e]=n[e]=t},removeLoader:function(e){delete n.batch.prototype[e],delete n[e]}}),e.extend(n.batch.prototype,{addTask:function(t){var n=this;if(!e.isDeferred(t))return;if(n.taskFinalized){n.options.verbose&&console.warn("$.require: "+t.name+" ignored because tasks of this batch are finalized.",t);return}t.batch=n,t.then(e.proxy(n.taskDone,t),e.proxy(n.taskFail,t),e.proxy(n.taskProgress,t)),n.taskList.push(t)},taskDone:function(){var e=this,t=e.batch;t.manager.notifyWith(t,[e])},taskFail:function(){var e=this,t=e.batch;t.options.verbose&&console.error("$.require: "+e.name+" failed to load.",e),t.manager.notifyWith(t,[e])},taskProgress:function(){var e=this,t=e.batch;t.manager.notifyWith(t,[e])},stat:function(){}}),e.each(["then","done","fail","always","pipe","progress"],function(t,r){n.batch.prototype[r]=function(){var t=this;return t.taskFinalized=!0,e.extend(t,t.manager.promise()),t.tasks=e.when.apply(null,t.taskList),t.tasks.done(function(){t.manager.resolve()}).fail(function(){t.options.verbose&&console.info("$.require: Batch "+t.id+" failed.",t),t.manager.reject()}),t[r].apply(t,arguments),t}}),n}(),e.require.addLoader("script",function(){var t=e.uri(e.indexUrl).host(),n=e.uri(document.location.href).host();t!==n&&t.match("xn--")&&(e.support.cors=!0);var r=document.createElement("script").async===!0||"MozAppearance"in document.documentElement.style||window.opera,i=function(){var t=this,n=e.makeArray(arguments),r,s;e.isPlainObject(n[0])?(r=n[0],s=n.slice(1)):s=n,r=e.extend({},i.defaultOptions,t.options,r,{batch:t});var o;e.each(s,function(e,n){var s=new i.task(n,r,o);t.addTask(s),r.serial&&o!==undefined?o.always(s.start):s.start(),o=s})};return e.extend(i,{defaultOptions:{path:"",extension:"js",serial:!1,async:!1,xhr:!1,prefetch:!0},setup:function(){e.extend(i.defaultOptions,options)},scripts:{},task:function(t,n,r){var i=e.extend(this,e.Deferred());i.name=t,i.options=n,i.taskBefore=r;if(e.isArray(t)){i.name=t[0]+"@"+t[1],i.moduleName=t[0];var s=t[2];s||(i.defineModule=!0,e.module.registry[i.moduleName]&&console.warn("$.require.script: "+i.moduleName+" exists! Using existing module instead."),i.options.xhr=!0),t=t[1],i.module=e.module(i.moduleName)}e.isUrl(t)?i.url=t:/^(\/|\.)/.test(t)?i.url=e.uri(i.options.path).toPath(t)+"":(i.url=e.uri(i.options.path).toPath("./"+t+"."+i.options.extension)+"",i.module=e.module(t))}}),e.extend(i.task.prototype,{start:function(){var t=this,n=t.taskBefore;if(t.module){var i=t.module.state();if(i=="resolved"){t.resolve();return}if(i=="rejected"){t.rejected();return}}if(r||t.options.xhr)t.load();else{if(!t.options.prefetch){t.load();return}t.script=e.script({url:t.url,type:"text/cache"}),t.script.done(n?function(){n.done(function(){t.reload()})}:function(){t.reload()})}},reload:function(){var e=this;e.script.remove(),e.load()},load:function(){var t=this,n=t.taskBefore,r={};t.script=i.scripts[t.url]||function(){var n=t.options.xhr?e.ajax({url:t.url,dataType:"text"}):e.script({url:t.url,type:"text/javascript",async:t.options.async,timeout:t.batch.options.timeout,retry:t.batch.options.retry,verbose:t.batch.options.verbose});return i.scripts[t.url]=n}(),t.script.done(function(r){var i=function(){t.module?t.module.done(t.resolve).fail(t.reject):t.resolve()};if(t.options.xhr){t.defineModule&&(t.module=e.module(t.moduleName,function(){var t=this;e.globalEval(r),t.resolveWith(r)}));if(!t.options.async||n){n.done(function(){e.globalEval(r),i()});return}}i()}).fail(function(){t.reject()})}}),i}()),e.require.addLoader("stylesheet",function(){var t=function(){var n=this,r=e.makeArray(arguments),i,s;e.isPlainObject(r[0])?(i=r[0],s=r.slice(1)):s=r,i=e.extend({},t.defaultOptions,n.options,i,{batch:n}),e.each(s,function(e,r){var s=new t.task(r,i),o=t.stylesheets[s.url];s=o||s,n.addTask(s),o||(t.stylesheets[s.url]=s,s.start())})};return e.extend(t,{defaultOptions:{path:"",extension:"css",xhr:!1},setup:function(){e.extend(t.defaultOptions,options)},stylesheets:{},task:function(t,n){var r=e.extend(this,e.Deferred());r.name=t,r.options=n,e.isUrl(t)?r.url=t:/^(\/|\.)/.test(t)?r.url=e.uri(r.options.path).toPath(t)+"":r.url=e.uri(r.options.path).toPath("./"+t+"."+r.options.extension)+"",r.options.url=r.url}}),e.extend(t.task.prototype,{start:function(){var t=this;e.stylesheet(t.options)?t.resolve():t.reject()}}),t}()),e.require.addLoader("template",function(){var t=function(){var n=this,r=e.makeArray(arguments),i,s;e.isPlainObject(r[0])?(i=r[0],s=r.slice(1)):s=r,i=e.extend({},t.defaultOptions,n.options,i,{batch:n}),e.each(s,function(e,r){var s=new t.task(r,i);n.addTask(s),s.start()})};return e.extend(t,{defaultOptions:{path:"",extension:"htm"},setup:function(){e.extend(t.defaultOptions,options)},loaders:{},task:function(t,n,r){var i=e.extend(this,e.Deferred());i.name=t,i.options=n,e.isArray(t)&&(i.name=t[0],t=t[1]),e.isUrl(t)?i.url=t:/^(\/|\.)/.test(t)?i.url=e.uri(i.options.path).toPath(t)+"":i.url=e.uri(i.options.path).toPath("./"+t+"."+i.options.extension)+""}}),e.extend(t.task.prototype,{start:function(){var n=this,r=n.taskBefore;n.loader=t.loaders[n.url]||function(){var r=e.ajax({url:n.url,dataType:"text"});return t.loaders[n.url]=r}(),n.loader.done(function(t){e.template(n.name,t),n.resolve()}).fail(function(){n.reject()})}}),t}()),e.require.addLoader("language",function(){var t=function(){var n=this,r=e.makeArray(arguments),i,s;e.isPlainObject(r[0])?(i=r[0],s=r.slice(1)):s=r,i=e.extend({},t.defaultOptions,n.options,i,{batch:n});var o=new t.task(s,i);n.addTask(o),setTimeout(function(){o.start()},1e3)};return e.extend(t,{defaultOptions:{path:""},setup:function(){e.extend(t.defaultOptions,options)},loaders:{},task:function(t,n){var r=e.extend(this,e.Deferred());r.name=t.join(","),r.options=n,r.url=n.path,r.languages=t}}),e.extend(t.task.prototype,{start:function(){var n=this,r=n.taskBefore;n.loader=t.loaders[n.name]||function(){var r=e.ajax({url:n.url,type:"POST",data:{languages:n.languages}});return t.loaders[n.name]=r}(),n.loader.done(function(t){e.language.add(t),n.resolve()}).fail(function(){n.reject()})}}),t}()),e.require.addLoader("library",function(){var t=this,n=e.makeArray(arguments),r={},i;return e.isPlainObject(n[0])?(r=n[0],i=n.slice(1)):i=n,e.extend(r,{path:e.scriptPath}),t.script.apply(t,[r].concat(i))}),e.require.addLoader("image",function(){var t=function(){var n=this,r=e.makeArray(arguments),i,s;e.isPlainObject(r[0])?(i=r[0],s=r.slice(1)):s=r,i=e.extend({},t.defaultOptions,n.options,i,{batch:n}),e.each(s,function(e,r){var s=new t.task(r,i),o=t.images[s.url];s=o||s,n.addTask(s),o||(t.images[s.url]=s,s.start())})};return e.extend(t,{defaultOptions:{path:""},setup:function(){e.extend(t.defaultOptions,options)},images:{},task:function(t,n){var r=e.extend(this,e.Deferred());r.name=t,r.options=n,e.isUrl(t)?r.url=t:/^(\/|\.)/.test(t)?r.url=e.uri(r.options.path).toPath(t)+"":r.url=e.uri(r.options.path).toPath("./"+t)+"",r.options.url=r.url}}),e.extend(t.task.prototype,{start:function(){var t=this;t.image=e(new Image).load(function(){t.resolve()}).error(function(){t.reject()}).attr("src",t.options.url)}}),t}())});