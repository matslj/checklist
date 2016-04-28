<?php
// ===========================================================================================
//
// File: index.php
//
// Description: Main page of the application.
//
// Author: Mats Ljungquist
//

require_once "template.php";

$intFilter = new CInterceptionFilter();
$intFilter->UserIsSignedInOrRecirectToSignIn();

// Set simple page variables that are used by template.php
$shell['title'] = "Checklista översikt";

$pagesPath = WS_SITELINK . "pages/ajax/";
$img = WS_IMAGES;
$javascriptUtils = WS_JAVASCRIPT . "utils.js";
$javascript = WS_JAVASCRIPT . "checklist.js";
$javascript2 = WS_JAVASCRIPT . "chklist.lists.js";

// ========================================================================== //
// HTML HEAD
// ========================================================================== //

ob_start();
?>
<script type="text/javascript" src="<?= $javascriptUtils ?>"></script>
<script type="text/javascript" src="<?= $javascript ?>"></script>
<script type="text/javascript" src="<?= $javascript2 ?>"></script>
<script type="text/javascript">
var isMobile = false; //initiate as false
// device detection
if(/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|ipad|iris|kindle|Android|Silk|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i.test(navigator.userAgent) 
    || /1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(navigator.userAgent.substr(0,4))) 
    isMobile = true;

(function($) {
    $(document).ready(function() {
        
        init({
            colHeight : isMobile ? 600 : 30,
            pagesPath : '<?= $pagesPath ?>'
        });
    });
})(jQuery);
</script>
<?php
$shell['html_head'] = ob_get_contents();
ob_end_clean();

// ========================================================================== //
// HTML BODY
// ========================================================================== //

// ****************************************
// ** Sätt headern i html body
ob_start();
?>
<h1>Checklista - översikt</h1>
<div id="promotext">   
<p style='font-style: italic'>
    Översiktssida för dina checklistor
</p>
</div>
<?php
$shell['html_header'] = ob_get_contents();
ob_end_clean();
// ** Header i html body är nu satt
// *****************************************

ob_start();
?>

<div id="newPost">
    <input type="text" id="newText" placeholder="Notering" name="newText" />
    <input type="text" id="newCategory" name="newCategory" placeholder="Kategori" />
    <button id='newNoteButton'>OK</button>
    <span>(istället för att trycka på OK, kan du trycka på ENTER när du står i något av textfälten)</span>
</div>

<div id="noteList"></div>
<div id="noteListsWrapper">
    <a class="nl-command nl-create" href="#">Skapa lista</a>
    <div id="noteLists"></div>
</div>

<a href="#" class="panel">Show Panel</a>

<!-- Dialoger -->
<div id="tagDlg" title="Ändra kategori">
    <table>
        <tr>
            <td>Ändra från</td>
            <td class="tdOldVal"></td>
        </tr>
        <tr>
            <td>Till</td>
            <td><input type="text" id="tagDlgInput" name="tagDlgInput" placeholder="Ny kategori" /></td>
        </tr>
    </table>
</div>

<div id="noteListDlg" title="Ändra lista">
    <div class="input-form">
        <input type="hidden" id="dlgNoteListId" value="0" />
        <div class="errorMsg"></div>
        <div>
            <label for="dlgNoteListTitle">Titel*</label>
            <input type="text" id="dlgNoteListTitle"/>
        </div>
        <div>
            <label for="dlgNoteListDescription">Beskrivning</label>
            <textarea id="dlgNoteListDescription" rows="5" cols="50"></textarea>
        </div>
        <div class="ddOldLists">
        </div>
        <div class="last">
            <input type="checkbox" id="dlgNoteListDefault"/>
            <label for="dlgNoteListDefault">visas som standard</label>
        </div>
    </div>
</div>

<!-- Templates for the page -->
<script id="data-template" type="text/x-handlebars-template">
    {{! Templaten tar emot data som motsvaras av en CNote.php }}
    <div class="notedata">
        <h2><a href="#">{{tag}}</a> <span>({{size}})<span></h2><a class="editTag" href="?tag={{tag}}">[Ändra]</a>
        <div class="clear"></div>
        <table>
        {{#each data}}
            {{#with this}}
                <tr class="row">
                    <td><input id="{{id}}-chk" type="checkbox" name="checkedNote" {{chkBoxHelper checked}} value="true"></td>
                    <td class="text{{chkBoxHelper checked}}">{{text}}</td>
                    <td>
                        <a class="deletePost" href="?id={{id}}">
                            <img src="<?=$img?>close_16.png">
                        </a>
                    </td>
                </tr>
            {{/with}}
        {{/each}}
        </table>
    </div>
</script>

<!-- Templates for the page -->
<script id="list-template" type="text/x-handlebars-template">
    {{! Templaten tar emot data som motsvaras av en CNoteList.php }}
    <div class="notelistdata">
        <table>
        {{#each this}}
            {{#with this}}
                <tr class="row">
                    <td><a class="nl-command nl-change" href="?id={{id}}">{{title}}</a></td>
                    <td>
                        <a class="nl-command nl-edit" href="?id={{id}}">
                            <img src="<?=$img?>close_16.png">
                        </a>
                    </td>
                </tr>
            {{/with}}
        {{/each}}
        </table>
    </div>
</script>
<?php

$shell['html_body'] = ob_get_contents();
ob_end_clean();

// ========================================================================== //
// DRAW SHELL
// ========================================================================== //

draw_shell();

?>