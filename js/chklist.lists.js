/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
var chklist = chklist || {};

/**
 * This class handles lists of notelists in the UI. The lists of notelists
 * are presented using a slide in panel (from the right). The slide in panel
 * originates from http://codebomber.com/jquery/slidepanel/
 * but is modified for this application.
 * <br>
 * The public methods are listed last (in the 'that'-object).
 * 
 * @author Mats Ljungquist
 * 
 * @param {type} pagesPath path to the location of the ajax (restlike) pages.
 * @returns {chklist.List.that} an object with a few public methods (see last)
 */
chklist.List = function(pagesPath) {
    var $listTemplate = $("#list-template").html(),
        $htmlTargetList = $("#noteLists"),
        $htmlTargetListsWrapper = $('#noteListsWrapper'),
        templateList = Handlebars.compile($listTemplate),
        $mainTitle = $("#main-title"),
        $promoText = $("p", "#promotext"),
        noteLists = null,
        currentNoteList = null,
        that = null;

    $htmlTargetListsWrapper.slidepanel({
        orientation: 'right'
    });
    
    /**
     * Utility function for checking if a variable is set and if not
     * give it a default value.
     * 
     * @param {type} variable the variable to be checked
     * @param {type} defaultValue the default value if variable not set
     * @returns {unresolved} either the unchanged value or the default value.
     */
    function isDefinedOrSet(variable, defaultValue) {
        return (typeof variable === 'undefined' ? defaultValue : variable);
    }
    
    function enableDisable() {
        if (!currentNoteList) {
            $('#newText').prop('disabled', true);
            $('#newCategory').prop('disabled', true);
            $('#newNoteButton').prop('disabled', true);
        } else {
            $('#newText').prop('disabled', false);
            $('#newCategory').prop('disabled', false);
            $('#newNoteButton').prop('disabled', false);
        }
    }
    
    /**
     * Creates a drop down list which will be used for copying all notes from an older list into a new one.
     * <br>
     * Affects UI.
     * 
     * @param {type} selector the selector of the element that should wrap the drop down list created by this method
     * @returns {undefined} nothing (affectes selector element directly)
     */
    function createListDD(selector) {
        var html = "<label for='dlgNoteListCopy'>Kopiera lista</label><select id='dlgNoteListCopy'><option value='-1'>---</option>",
            i = 0;
        if (noteLists) {
            for (; i < noteLists.length; i++) {
                html = html + "<option value='" + noteLists[i].id + "'>" + noteLists[i].title + "</option>";
            }
            html = html + "</select>";
            $(selector).html(html);
        }
    }
    
    /**
     * Retrieves note list by id.
     * 
     * @param {type} id of note list to retrieve
     * @returns {chklist.List.noteLists} the note list corresponding to the id in the parameter or null if no such exist
     */
    function getNoteListInternal(id) {
        for (var i = 0; i < noteLists.length; i++) {
            if (noteLists[i].id === id ) {
                return noteLists[i];
            }
        }
        return null;
    }
    
    /**
     * Sets the title and the subtitle (promo) of the page if currentNoteList is
     * set. The promo is truncated to 50 chars if longer.
     * 
     * @returns {undefined} no returns, modifies the dom.
     */
    function setTitleAndPromoFromCurrentNoteList() {
        if (currentNoteList && currentNoteList.title) {
            $mainTitle.empty();
            $mainTitle.html(currentNoteList.title);
            var d = currentNoteList.description,
                l = d.length;
            if (l > 50) {
                d = d.substring(0, 49) + "...";
            }
            $promoText.html(d);
        }
    }
    
    /**
     * 
     * @returns {undefined}
     */
    function renderNoteLists() {
        var $noteLists = $htmlTargetList.append('<div class="noteLists"/>').find(':last');
        $noteLists.append(templateList(noteLists));
    }
    
    function fetchNoteLists(renderNotes) {
        renderNotes = isDefinedOrSet(renderNotes, false);
        $.getJSON(pagesPath + 'ajaxGetNoteLists.php', function( data ) {
            if (data) {
                if(data.length > 0) {
                    $htmlTargetList.empty();
                    noteLists = data;
                    
                    // If no current note list is selected use the first
                    // from the result
                    if (!currentNoteList) {
                        currentNoteList = data[0];
                    } else {
                        // Refresh current note list
                        currentNoteList = getNoteListInternal(currentNoteList.id);
                    }
                    setTitleAndPromoFromCurrentNoteList();

                    enableDisable();

                    // Render all the note lists
                    renderNoteLists();

                    if (renderNotes) {
                        // Render notes for currents note list
                        updateEntries();
                    }
                } else {
                    enableDisable();
                }
            } else {
                enableDisable();
            }
        });
    }
        
    function upsertNoteList(title, description, def, id, copyId) {
        // console.log(title + " " + description + " "+ def + " "+ id + " copyId: " + copyId);
        // collect values
        var obj = {};
            //renderNotes = true;
        obj.title = title;
        obj.description = description;
        obj.def = def;
        obj.listId = id;
        obj.copyId = copyId;


//            if (id && lists.getCurrentNoteList() && id === lists.getCurrentNoteList().id) {
//                renderNotes = false;
//            }

        if (obj.title) {
            var json = JSON.stringify(obj);
            $.ajax({
                url: pagesPath + 'ajaxUpsertNoteList.php',
                type:'POST',
                dataType: "json",
                data: {"payload": json},
                success: function(data) {
                    // console.log(data.status);
                    if (data.status === "OK") {
                        $("#newText").val("");
                        $("#newText").focus();
                        fetchNoteLists(obj.listId);
                    }
                }
            });
        }
    }
    
    $("#noteListDlg").dialog({
        autoOpen : false,
        modal : true,
        open: function( event, ui ) {
            $(this).find('.errorMsg').empty();
            $(this).find('div.ddOldLists').empty();
        },
        buttons : [
            {
                id: "noteListDlg-btn-ok",
                text: "OK",
                click: function() {
                    var title = $("#dlgNoteListTitle").val(),
                        description = $("#dlgNoteListDescription").val(),
                        def = 0,
                        listId = $("#dlgNoteListId").val(),
                        copyId = -1,
                        $ddEl = null;

                    $ddEl = $(this).find("select");
                    if ($ddEl.length) {
                        copyId = $ddEl.find(":selected").val();
                    }

                    if ($("#dlgNoteListDefault").is(':checked')) {
                        def = 1;
                    }

                    upsertNoteList(title, description, def, listId, copyId);
                    $(this).dialog("close");
                }
            },
            {
                id: "noteListDlg-btn-delete",
                text: "Ta bort lista",
                click: function() {
                    var listId = $("#dlgNoteListId").val(),
                        self = this;
                    $.ajax({
                        url: pagesPath + 'ajaxDeleteNoteList.php',
                        type:'GET',
                        dataType: "json",
                        data: "id=" + listId,
                        success: function(data) {
                            if (data.status === "OK") {
                                fetchNoteLists(false);
                                $(self).dialog("close");
                            } else {
                                $(self).find('.errorMsg').html("Kan inte radera listan");
                            }
                        }
                    });

                }
            }
        ]
    });
    
    // Events on the list of notes - edit, change/switch and create note list.
    $htmlTargetListsWrapper.on("click", function(event) {
        var $eventTarget = $(event.target);

        if ($eventTarget.is('a.nl-edit') || $eventTarget.is('a.nl-edit img')) {
            var href = "", id = "", nlObj = null;
            if ($eventTarget.is('a.nl-edit')) {
                href = $eventTarget.attr('href');
            } else {
                href = $eventTarget.parent().attr('href');
            }
            id = utils.getParameterByName("id", href);

            nlObj = getNoteListInternal(id);
            $("#noteListDlg").dialog("open");
            $("#noteListDlg-btn-delete").show();
            $("#dlgNoteListTitle").val(nlObj.title).focus();
            $("#dlgNoteListDescription").val(nlObj.description);
            $("#dlgNoteListDefault").prop('checked', nlObj.def);
            $("#dlgNoteListId").val(nlObj.id);

            // Enable/disable delete button

//                $.ajax({
//                    url: pagesPath + 'ajaxDeleteNote.php',
//                    type:'GET',
//                    dataType: "json",
//                    data: "id=" + id,
//                    success: function(data) {
//                        if (data.status === "OK") {
//                            updateTemplateEntries();
//                        }
//                    }
//                });
            event.preventDefault();
            return false;
        } else if ($eventTarget.is('a.nl-change')) {
            // Handles changes from one nodeList to another
            // Fetch note list id from the query parameter associated with the clicked link
            var href = $eventTarget.attr('href'),
                id   = utils.getParameterByName("id", href);

            // Only perform change if a change has occurred (old id != new id)
            if (id !== currentNoteList.id) {
                currentNoteList = getNoteListInternal(id);
                setTitleAndPromoFromCurrentNoteList();
                updateEntries();
            }
            event.preventDefault();
            return false;
        } else if ($eventTarget.is('a.nl-create')) {
            $("#noteListDlg").dialog("open");
            $("#noteListDlg").dialog('option', 'title', 'Skapa lista');
            createListDD("#noteListDlg div.ddOldLists");
            $("#noteListDlg-btn-delete").hide();
            $("#dlgNoteListTitle").val("").focus();
            $("#dlgNoteListDescription").val("");
            $("#dlgNoteListDefault").prop('checked', false);
            $("#dlgNoteListId").val("");
            event.preventDefault();
            return false;
        }
    });
    
    // Public methods
    that = {
        /**
         * Returns the currently selected notelist or, if no such exist, the first notelist.
         * 
         * @returns {chklist.List.currentNoteList} the  current notelist or the first if no list is selected.
         */
        getCurrentNoteList: function () {
            if (currentNoteList === null) {
                return {id:0};
            } else {
                return currentNoteList;
            }
        },
        
        /**
         * Returns all the note lists.
         * 
         * @returns {chklist.List.noteLists}
         */
        getNoteLists: function() {
            return noteLists;
        },
        
        /**
         * Retrieves note list by id.
         * 
         * @param {type} id of note list to retrieve
         * @returns {chklist.List.noteLists} the note list corresponding to the id in the parameter or null if no such exist
         */
        getNoteList: function(id) {
            return getNoteListInternal(id);
        },
        
        /**
         * Populates the UI with lists and notes.
         * 
         * @returns {undefined} nothing - populates the UI directly
         */
        fetchListAndNotes: function() {
            fetchNoteLists(true);
        }
    };
    return that;
};

