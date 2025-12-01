jQuery(function ($) {
  $(document).on("click", ".cac-media-upload", function (e) {
    e.preventDefault();
    var target = $(this).data("target");
    var frame = wp.media({
      title: "Select or upload image",
      button: { text: "Use this image" },
      multiple: false,
    });
    frame.on("select", function () {
      var attachment = frame.state().get("selection").first().toJSON();
      $("#" + target)
        .val(attachment.url)
        .prev("img")
        .remove();
      $("#" + target).before(
        '<img src="' +
          attachment.url +
          '" style="max-width:80px;display:block;margin-bottom:6px;border-radius:50%;" />'
      );
    });
    frame.open();
  });

  $(document).on("click", ".cac-media-remove", function (e) {
    e.preventDefault();
    var target = $(this).data("target");
    $("#" + target)
      .val("")
      .prev("img")
      .remove();
  });
});
