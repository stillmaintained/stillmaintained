var StillMaintained = {

  dance : function() {
    var self = this;
    $ = window.jQuery;

    $('div.results h2, td:first h2').each(function(count, title) {
      githubProjectUrl = $(title).find('a').attr('href');

      if(githubProjectUrl.split('/').length == 3) {
        $(title).append(
          '<a href="' + self.projectUrl(githubProjectUrl) + '">' +
           '<img src="' + self.projectStatusButton(githubProjectUrl) + '" />' +
          '</a>'
        );
      };

    });
  },

  projectUrl : function(githubProjectUrl) {
    return 'http://stillmaintained.com' + githubProjectUrl;
  },

  projectStatusButton : function(githubProjectUrl) {
    return this.projectUrl(githubProjectUrl) + '.png';
  }

}

StillMaintained.dance();

