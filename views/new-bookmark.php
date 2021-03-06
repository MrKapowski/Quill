  <div class="narrow">
    <?= partial('partials/header') ?>

      <div style="float: right; margin-top: 6px;">
        <button class="btn btn-success" id="btn_post">Save Bookmark</button>
      </div>

      <div style="clear: both;">
        <div class="alert alert-success hidden" id="test_success"><strong>Success! </strong><a href="" id="post_href">View your post</a></div>
        <div class="alert alert-danger hidden" id="test_error"><strong>Something went wrong!</strong><br>Your Micropub endpoint indicated that something went wrong creating the post.</div>
      </div>

      <form role="form" style="margin-top: 20px;" id="note_form">

        <div class="form-group">
          <label for="note_bookmark">Bookmark URL</label>
          <input type="text" id="note_bookmark" value="<?= $this->bookmark_url ?>" class="form-control">
        </div>

        <div class="form-group">
          <label for="note_name">Name</label>
          <input type="text" id="note_name" value="<?= $this->bookmark_name ?>" class="form-control">
        </div>

        <div class="form-group">
          <label for="note_content">Content</label>
          <textarea id="note_content" value="" class="form-control" style="height: 5em;"><?= $this->bookmark_content ?></textarea>
        </div>

        <div class="form-group">
          <label for="note_category">Tags</label>
          <input type="text" id="note_category" value="<?= $this->bookmark_tags ?>" class="form-control" placeholder="e.g. web, personal">
        </div>

        <?php if($this->syndication_targets): ?>
        <div class="form-group">
          <label for="note_syndicate-to">Syndicate <a href="javascript:reload_syndications()">refresh</a></label>
          <div id="syndication-container">
            <?php
              echo '<ul>';
              foreach($this->syndication_targets as $syn) {
                echo '<li>'
                 . '<button data-syndicate-to="'.(isset($syn['uid']) ? htmlspecialchars($syn['uid']) : htmlspecialchars($syn['target'])).'" class="btn btn-default btn-block">'
                   . ($syn['favicon'] ? '<img src="'.htmlspecialchars($syn['favicon']).'" width="16" height="16"> ' : '')
                   . htmlspecialchars($syn['target'])
                 . '</button>'
               . '</li>';
              }
              echo '</ul>';
            ?>
          </div>
        </div>
        <?php endif ?>

      </form>


      <hr>
      <div style="text-align: right;">
        Bookmarklet: <a href="javascript:<?= js_bookmarklet('partials/bookmark-bookmarklet', $this) ?>" class="btn btn-default btn-xs">bookmark</a>
      </div>

  </div>

<script>
$(function(){

  $("#note_category").tokenfield({
    createTokensOnBlur: true,
    beautify: true
  });

  $("#btn_post").click(function(){

    if($("#note_bookmark").val() == "") {
      return false;
    }

    var syndications = [];
    $("#syndication-container button.btn-info").each(function(i,btn){
      syndications.push($(btn).data('syndicate-to'));
    });

    $("#btn_post").addClass("loading disabled").text("Working...");

    $.post("/micropub/post", {
      'bookmark-of': $("#note_bookmark").val(),
      name: $("#note_name").val(),
      content: $("#note_content").val(),
      category: tokenfieldToArray("#note_category"),
      '<?= $this->user->micropub_syndicate_field ?>': syndications
    }, function(response){
      if(response.location != false) {

        $("#test_success").removeClass('hidden');
        $("#test_error").addClass('hidden');
        $("#post_href").attr("href", response.location);
        $("#note_form").addClass('hidden');

        // $("#note_bookmark").val("");
        // $("#note_content").val("");
        // $("#note_category").val("");

        window.location = response.location;
      } else {
        $("#test_success").addClass('hidden');
        $("#test_error").removeClass('hidden');
        $("#btn_post").removeClass("loading disabled").text("Post");
      }

    });
    return false;
  });

  bind_syndication_buttons();
});

<?= partial('partials/syndication-js') ?>

</script>
