/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
var chklist = chklist || {};

chklist.List = function(pagesPath) {
    var $listTemplate = $("#list-template").html(),
        $htmlTargetList = $("#noteLists"),
        $htmlTargetListsWrapper = $("#noteListsWrapper"),
        templateList = Handlebars.compile($listTemplate),
        noteLists = null,
        currentNoteList = null,
        $panel = null,
        $panelDrawArea = null,
        that = null;

    $('.panel').slidepanel({
        orientation: 'right',
        mode: 'overlay',
        static: true
    });
    $panel = $('.panel').data('plugin_slidepanel').$panel;
    $panelDrawArea = $('.inner .wrapper', $panel);
    
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
    
    function getNoteListInternal(id) {
        for (var i = 0; i < noteLists.length; i++) {
            if (noteLists[i].id === id ) {
                return noteLists[i];
            }
        }
        return null;
    }
    
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
                    // If no current note list is selected use the first
                    // from the result
                    if (!currentNoteList) {
                        currentNoteList = data[0];
                    }

                    enableDisable();

                    noteLists = data;

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
        console.log(title + " " + description + " "+ def + " "+ id + " copyId: " + copyId);
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
                    console.log(data.status);
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
    
    that = {
        getCurrentNoteList: function () {
            if (currentNoteList === null) {
                return {id:0};
            } else {
                return currentNoteList;
            }
        },
        
        getNoteLists: function() {
            return noteLists;
        },
        
        getNoteList: function(id) {
            return getNoteListInternal(id);
        },
        
        fetchListAndNotes: function() {
            fetchNoteLists(true);
        }
    };
    return that;
};

