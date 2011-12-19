$(function() {
    var button = $('#login_button');
    var box = $('#login_box');
    var form = $('#login_form');
    button.removeAttr('href');
    button.mouseup(function(login) {
        box.toggle();
        button.toggleClass('Active');
    });
    form.mouseup(function() { 
        return false;
    });
    $(this).mouseup(function(login) {
        if(!($(login.target).parent('#login_button').length > 0)) {
            button.removeClass('Active');
            box.hide();
        }
    });
});

var _gaq = _gaq || [];
_gaq.push(['_setAccount', 'UA-26941179-2']);
_gaq.push(['_trackPageview']);

(function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
})();

window.___gcfg = {lang: 'es-419'};
(function() {
	var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
	po.src = 'https://apis.google.com/js/plusone.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
})();

(function() {
	var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
	po.src = 'https://platform.twitter.com/widgets.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
})();

window.fbAsyncInit = function() {
	FB.init({appId: '130545307059914', status: true, cookie: true,xfbml: true});
	FB.Event.subscribe('edge.create', function(url) {
  		_gaq.push(['_trackSocial', 'facebook', 'like', url]);
	});
	FB.Event.subscribe('edge.remove', function(url) {
  		_gaq.push(['_trackSocial', 'facebook', 'unlike', url]);
	});
};

(function() {
	var e = document.createElement('script'); e.async = true;
	e.src = document.location.protocol + '//connect.facebook.net/es_LA/all.js';
	document.getElementById('fb-root').appendChild(e);
}());

var _gaq = _gaq || [];
_gaq.push(['_setAccount', 'UA-27779927-1']);
_gaq.push(['_trackPageview']);

(function() {
	var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
	ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
	var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
})();
