/**
 * Handles all the dynamic behavior of the index.php page.
 * <p>
 * Requires jquery, jquery-ui and handlebars.
 * 
 * @type type
 * @author Mats Ljungquist
 */

var init = null; // externalized handle to the init function (see below)

(function($) {
    
    /**
     * Initizlizes the javascript for this app. Should be called from an (document).ready() function.
     * 
     * @param {type} config app configuration parameters. There are two parameters that must be set: colHeight and pagesPath.
     *                      See below for explanation.
     * @returns {undefined}
     */
    init = function(config) {
        
        var template = null,                 // A handle to the handlebars template
            $htmlTarget = null,              // The html container where the notes will be presented
            entriesInCol = config.colHeight, // The minimum number of entries to be presented in a column
                                             // before it breaks into another column.
            pagesPath = config.pagesPath,    // Path to where all the ajax services resides.
            maxNrOfColumns = 4;              // The maximum number of columns which the notes will be displayed in

        /**
         * Extracts the value of a given (named) parameter from an url.
         * 
         * @param {type} name the name of the parameter whose value we are interested in
         * @param {type} href the url, with parameter, from wich we want to extract the parameter value
         * @returns {String} if parameter is present, return the parameter value. Otherwise return an empty string.
         */
        function getParameterByName(name, href) {
            name = name.replace(/[\[]/,"\\\[").replace(/[\]]/,"\\\]");
            var regexS = "[\\?&]"+name+"=([^&#]*)";
            var regex = new RegExp( regexS );
            var results = regex.exec( href );
            if( results == null ) {
                return "";
            } else {
                return decodeURIComponent(results[1].replace(/\+/g, " "));
            }
        }

        /**
         * Updates the $htmlTarget with all the notes in the database. This gets
         * done through an ajax call.
         * <p>
         * This method has some dynamic behaviour: the notes retrieved from the
         * database will be displayed in x columns where x = maxNrOfColumns and
         * each column will show y rows where y = entriesInCol. But the method will
         * not break a category in two, so if the entriesInCol is 30 and a category
         * contains 50 rows, they all will be displayed in a single column. Also if
         * a category contains 29 rows and then the next category contains 50 rows
         * they will also be displayed in the same (single) column (because the method
         * wont break the 50 row category).
         * <p>
         * Also, and this is the most dynamic part, if the total number of rows
         * is to great to fit into maxNrOfColumns x entriesInCol then the entriesInCol
         * will be automatically recalculated to fit the total number of rows.
         * 
         * @returns {undefined} returns nothing
         */
        function updateTemplateEntries() {
            $.getJSON(pagesPath + 'ajaxGetNotes.php', function( data ) {

                $htmlTarget.empty();
                // ********************************************************
                // * Display the retrieved data using a handlebars template.
                // * 
                var lastTag = null,
                    i = 0,
                    length = data.length,
                    entry = null,
                    tempMap = {};

                for (i; i < length; i++) {
                    entry = data[i];
                    if (lastTag == null || lastTag != entry.tag) {
                        lastTag = entry.tag;
                        tempMap[lastTag] = [];
                    }
                    tempMap[lastTag].push(entry);
                }
                
                var tempNrOfEntriesCol = Math.ceil(length / maxNrOfColumns);
                if (tempNrOfEntriesCol > entriesInCol) {
                    entriesInCol = tempNrOfEntriesCol;
                }

                var $col = $htmlTarget.append('<div class="col"/>').find(':last');
                i = 0;
                // Feed the template with the map created above.
                for (var key in tempMap) {
                    if (tempMap.hasOwnProperty(key)) {
                        if (i > entriesInCol) {
                            $col = $htmlTarget.append('<div class="col"/>').find(':last');
                            i = 0;
                        }
                        $col.append(template({tag: key, size: tempMap[key].length, data: tempMap[key]}));
                        i = i + tempMap[key].length;
                    }
                }
                $htmlTarget.append('<div class="clear"/>');
            });
        }

        /**
         * Reads the values from input fields #newText and #newCategory and
         * creates a new post in the database through an ajax call. On successful
         * return from ajax call #newText is reseted and focused and all content
         * data is re read (through updateTemplateEntries());
         * 
         * @returns {undefined} return nothing
         */
        function newPost() {
            // collect values
            var obj = {};
            obj.text = $("#newText").val();
            obj.tag = $("#newCategory").val();
            
            if (obj.text && obj.tag) {
                var json = JSON.stringify(obj);
                $.ajax({
                    url: pagesPath + 'ajaxCreateNote.php',
                    type:'POST',
                    dataType: "json",
                    data: {"payload": json},
                    success: function(data) {
                        $("#newText").val("");
                        // $("#newCategory").val("");
                        $("#newText").focus();
                        updateTemplateEntries();
                    }
                });
            }
        }
    
        // Initialization of templates and targets for templating
        var templateContent = $("#data-template").html();
        template = Handlebars.compile(templateContent);
        Handlebars.registerHelper('chkBoxHelper', function(checked) {
            return checked ? " CHECKED" : "";
        });
        $htmlTarget = $("#noteList");
        
        // Dialog for changing the category/tag of all notes within a category/tag.
        $("#tagDlg").dialog({
            autoOpen : false,
            buttons : {
                "OK" : function() {
                    var oldTag = $("#tagDlg td.tdOldVal").html(),
                        newTag = $("#tagDlg input").val();
                    if (oldTag && newTag) {
                        $.ajax({
                            url: pagesPath + 'ajaxChangeTag.php',
                            type:'POST',
                            dataType: "json",
                            data: "old=" + oldTag + "&new=" + newTag,
                            success: function(data) {
                                updateTemplateEntries();
                            }
                        });
                    }
                    $(this).dialog("close");
                }
            }
        });

        // The following two event handlers creates a new post by calling
        // the newPost()-method. The first eventhandler catches 'ENTER'-events
        // and the second catches click-events on the 'OK'-button.
        $('#newPost input').bind("keypress", function(e) {
            if (e.keyCode == 13) {
                newPost();
                e.preventDefault();
                return false; // prevent the button click from happening
            }
        });
        $("#newNoteButton").button().click(function(event) {
            newPost();
            event.preventDefault();
            return false;
        });

        // Handles all the events performed on the $htmlTarget:
        // - Delete post
        // - Check/uncheck post
        // - Select category into the #newCategory input field
        // - Open dialog to change category for all notes within a category.
        $htmlTarget.on("click", function(event) {
            var $eventTarget = $(event.target);

            if ($eventTarget.is('a.deletePost') || $eventTarget.is('a.deletePost img')) {
                var href = "", id = "";
                if ($eventTarget.is('a.deletePost')) {
                    href = $eventTarget.attr('href');
                } else {
                    href = $eventTarget.parent().attr('href');
                }
                id = getParameterByName("id", href);

                $.ajax({
                    url: pagesPath + 'ajaxDeleteNote.php',
                    type:'GET',
                    dataType: "json",
                    data: "id=" + id,
                    success: function(data) {
                        if (data.status === "OK") {
                            updateTemplateEntries();
                        }
                    }
                });
                event.preventDefault();
                return false;
            } else if ($eventTarget.is('input:checkbox')) {
                var elementId = $(event.target).attr('id');
                var index = elementId.indexOf('-');
                var noteId = elementId.substring(0, index);

                var checked = 0;
                if ($eventTarget.is(':checked')) {
                    checked = 1;
                }

                $.ajax({
                    url: pagesPath + 'ajaxCheckUncheckNote.php',
                    type:'GET',
                    dataType: "json",
                    data: "id=" + noteId + "&checked=" + checked,
                    success: function(data) {
                        updateTemplateEntries();
                    }
                });
            } else if ($eventTarget.is('h2 a')) {
                $("#newCategory").val($eventTarget.html());
                $("#newText").focus();
                event.preventDefault();
                return false;
            } else if ($eventTarget.is('a.editTag')) {
                var href = $eventTarget.attr('href');
                var tag = getParameterByName("tag", href);
                
                $("#tagDlg").dialog("open");
                $("#tagDlg td.tdOldVal").html(tag);
                $("#tagDlg input").val("").focus();
                event.preventDefault();
                return false;
            }
        });

        // Get all notes on startup.
        updateTemplateEntries();
    };
})(jQuery);