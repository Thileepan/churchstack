(function($){
    $.sidetabs = function(el, options){
        // To avoid scope issues, use 'base' instead of 'this'
        // to reference this class from internal events and functions.
        var base = this;
        
        // Access to jQuery and DOM versions of element
        base.$el = $(el);
        base.el = el;
        
        // Add a reverse reference to the DOM object
        base.$el.data("sidetabs", base);
        
        base.init = function(){
            if( typeof( radius ) === "undefined" || radius === null ) radius = "20px";
            
            base.radius = radius;

            
            base.options = $.extend({},$.sidetabs.defaultOptions, options);
            

                var $sidetabsContent = base.$el.find ('.sidetabs-content')
                , $allTabs = ''
                , hash = location.hash
                , activeTab = '';

                $allTabs = base.$el.find ('.tab');



                hash = hash.split ('#!')[1];
                activeTab = '#' + hash;

                if (!base.options.history || hash === undefined || hash === '') {
                    var tab = $allTabs.eq (0);
                    
                    activeTab = tab.attr ('data-fake-id');
                }



                base.setActiveTab (activeTab);
                base.setActiveContent (activeTab);

                $allTabs.click ( function (e) {
                    e.preventDefault ();
                    var $tab = $(this);
                    var href = $(this).attr ('data-fake-id');

                    base.setActiveTab (href);
                    base.setActiveContent (href);        

                    if (base.options.history) {
                        location.hash = '!' + href.split ('#')[1];
                    }

                    if ($tab.is ('.content-tab')) {
                        $('html, body').animate({
                             scrollTop: $tab.offset ().top - 25
                         }, 0);
                    }
                });

        };

        base.setActiveTab = function (tid) {
            $('.tab').removeClass ('active');
            $('a[data-fake-id=' + tid + ']').addClass ('active');
        }

        base.setActiveContent = function (cid) {
            $('.tab-pane').hide ();
            $(cid).show ();
        }
        
        // Sample Function, Uncomment to use
        // base.functionName = function(paramaters){
        // 
        // };
        
        // Run initializer
        base.init();
    };
    
    $.sidetabs.defaultOptions = {
        history: true
    };
    
    $.fn.sidetabs = function(options){
        return this.each(function(){
            (new $.sidetabs(this, options));

		   // HAVE YOUR PLUGIN DO STUFF HERE
			
	
		   // END DOING STUFF

        });
    };
    
})(jQuery);