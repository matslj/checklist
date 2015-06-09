var init = null;

(function($) {
    
    init = function(config) {
        
        var templateContent = null,
            template = null,
            htmlTarget = null,
            entriesInCol = config.colHeight,
            pagesPath = config.pagesPath;

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

        function updateTemplateEntries() {
            $.getJSON(pagesPath + 'ajaxGetNotes.php', function( data ) {

                htmlTarget.empty();
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

                var $col = htmlTarget.append('<div class="col"/>').find(':last');
                i = 0;
                // Feed the template with the map created above.
                for (var key in tempMap) {
                    if (tempMap.hasOwnProperty(key)) {
                        if (i > entriesInCol) {
                            $col = htmlTarget.append('<div class="col"/>').find(':last');
                            i = 0;
                        }
                        $col.append(template({tag: key, data: tempMap[key]}));
                        i = i + tempMap[key].length;
                    }
                }
                htmlTarget.append('<div class="clear"/>');
            });
        }

        function newPost() {
            // collect values
            var obj = {};
            obj.text = $("#newText").val();
            obj.tag = $("#newCategory").val();

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

    
        // Initialization of templates and targets for templating
        templateContent = $("#data-template").html();
        template = Handlebars.compile(templateContent);
        Handlebars.registerHelper('chkBoxHelper', function(checked) {
            return checked ? "CHECKED" : "";
        });
        htmlTarget = $("#noteList");

        // Get all notes and show them using the handlebar template
        // updateTemplateEntries();

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

        htmlTarget.on("click", function(event) {
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
            }
        });

        updateTemplateEntries();
    };
})(jQuery);