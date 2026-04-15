jQuery(document).ready(function ($) {
  // Function to update the focal point
  console.log('Custom media is ready!');
  function updateFocalPoint(x, y) {
    $(".focal-point").css({
      left: `${x}%`,
      top: `${y}%`,
    });
    $("#focal-x").val(x);
    $("#focal-y").val(y);

    const focalPoint = {
      x: x,
      y: y,
    };

    $.post(ajaxurl, {
      action: "save_focal_point",
      focal_point: focalPoint,
      post_id: $("#focal-point-id").val(), // Include post ID in the request
    });
  }

  // Handle clicks on the image within .media-modal
  $(document).on("click", ".media-modal .details-image", function (e) {
    console.log("Image clicked");
    const $image = $(this);
    const offset = $image.offset();
    const x = e.pageX - offset.left;
    const y = e.pageY - offset.top;
    const width = $image.width();
    const height = $image.height();

    // Normalize coordinates to percentage
    const xPercent = (x / width) * 100;
    const yPercent = (y / height) * 100;

    updateFocalPoint(xPercent, yPercent);
  });

  // Handle input changes
  $(document).on("input", "#focal-x", function () {
    const x = $(this).val();
    const y = $("#focal-y").val();
    updateFocalPoint(x, y);
  });

  $(document).on("input", "#focal-y", function () {
    const x = $("#focal-x").val();
    const y = $(this).val();
    updateFocalPoint(x, y);
  });

  // Save focal point data
  $(document).on("click", "#save-post", function () {
    const focalPoint = {
      x: $("#focal-x").val(),
      y: $("#focal-y").val(),
    };
    $.post(ajaxurl, {
      action: "save_focal_point",
      focal_point: focalPoint,
      post_id: $("#post_ID").val(), // Include post ID in the request
    });
  });
});
