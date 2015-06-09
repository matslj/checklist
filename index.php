<?php

require_once "template.php";

$intFilter = new CInterceptionFilter();
$intFilter->UserIsSignedInOrRecirectToSignIn();

// Set simple page variables that are used by template.php
$shell['title'] = "Kaninboets checklista";

$pagesPath = WS_SITELINK . "pages/ajax/";
$img = WS_IMAGES;
$javascript = WS_JAVASCRIPT . "checklist.js";

// ========================================================================== //
// HTML HEAD
// ========================================================================== //

ob_start();
?>
<script type="text/javascript" src="<?= $javascript ?>"></script>
<script type="text/javascript">
(function($) {
    $(document).ready(function() {
        init({
            colHeight : 20,
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
<h1>Kaninboets checklista</h1>
<div id="promotext">   
<p style='font-style: italic'>
    En reseplanerare för kaniner
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
    <span style="display: inline-block; margin-left: 20px; font-size: 0.8em;">(istället för att trycka på OK, kan du trycka på ENTER när du står i något av textfälten)</span>
</div>

<div id="noteList"></div>

<!-- Dialoger -->

<!-- Templates for the page -->
<script id="data-template" type="text/x-handlebars-template">
    {{! Templaten tar emot data som motsvaras av en CNote.php }}
    <div class="notedata">
        <h2>{{tag}}</h2>
        <table>
        {{#each data}}
            {{#with this}}
                <tr class="row">
                    <td><input id="{{id}}-chk" type="checkbox" name="checkedNote" {{chkBoxHelper checked}} value="true"></td>
                    <td class="text">{{text}}</td>
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
<?php

$shell['html_body'] = ob_get_contents();
ob_end_clean();

// ========================================================================== //
// DRAW SHELL
// ========================================================================== //

draw_shell();

?>