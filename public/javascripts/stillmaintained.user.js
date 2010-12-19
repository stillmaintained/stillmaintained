// ==UserScript==
// @name        Still Maintained
// @description Adds status buttons to Github's search results page
// @include     https://github.com/search*
// @author      Jeff Kreeftmeijer
// ==/UserScript==

(function() {
  var script = document.createElement('script');
  script.src = 'http://stillmaintained.com/javascripts/userscript.js';
  script.type = 'text/javascript';
  document.getElementsByTagName('head')[0].appendChild(script);
})();

