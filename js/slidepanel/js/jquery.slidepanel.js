;(function ( $, window, document, undefined ) {
    
    var defaults = {
        orientation: 'left'
    };

    // The actual plugin constructor
    function Slidepanel( $panelContent, options ) {
        this.$panelContent = $panelContent;
        this.options = $.extend( {}, defaults, options) ;
        this._defaults = defaults;
        this.init();
    }

    Slidepanel.prototype.init = function () {
        
        var base = this;

        if($('#slidepanel').length == 0){
            var panel_html = '<div id="slidepanel" class="cb_slide_panel"><div class="wrapper"><a href="#" class="close">Close</a><div class="inner"><div class="wrapper"></div></div></div></div>';
            $(panel_html).appendTo($('body'));   // .hide()
        }

        this.$panel = $('#slidepanel'); //.hide();
        this.$body = $('body');
        this.$body_position = this.$body.css('position');
        this.$innerStyle = $('.inner', this.$panel)[0].style;
        
        // Insert panel content and show it
        $('.inner .wrapper', this.$panel).append(this.$panelContent);
        $(this.$panelContent).show(); //.show();
        //$('.close', this.$panel);

        //hide the panel and set orientation class for display
        this.$panel.addClass('panel_' + this.options.orientation); // .hide()
        
        //set current trigger link to false for the current panel
        
        //reset any defined a positions
        this.$panel.css('left', '').css('right', '').css('top', '').css('bottom', '');

        //set a default top value for left and right orientations
        //and set the starting position based on element width
        if(this.options.orientation == 'left' || this.options.orientation == 'right') {
            var options = {};
            options['top'] = 40;
            options['width'] = 0;
            options[this.options.orientation] = 0;
            this.$panel.css(options);
        }
        
        //listen for a click on the close buttons for this panel
        $('.close', this.$panel).click(function(e) {
            e.preventDefault();
            base.expand();
        });
    };

    Slidepanel.prototype.expand = function() {
        var base = this;
        if (this.$panel.hasClass('expanded')) {
            this.collapse();
            return;
        }
        //set the css properties to animatate
        var panel_options = {};
        this.$innerStyle.display = 'block';
        panel_options['width'] = 278;
        
        //animate the panel into view
        this.$panel.animate(panel_options, 250, function() {
            base.$panel.addClass('expanded');
        });
    };

    Slidepanel.prototype.collapse = function() {
        var base = this;
        //set the css properties to animatate
        var panel_options = {};
        panel_options['width'] = 0;
        
        //animate the panel out of view
        this.$panel.animate(panel_options, 250, function () {
            base.$innerStyle.display = 'none';
            base.$panel.removeClass('expanded');
        });
    };

    $.fn['slidepanel'] = function ( options ) {
        return this.each(function () {       
            if (!$.data(this, 'plugin_slidepanel')) {
                $.data(this, 'plugin_slidepanel', new Slidepanel( this, options ));
            }
        });
    };

})(jQuery, window);