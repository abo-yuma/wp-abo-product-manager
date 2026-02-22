jQuery(document).ready(function ($) {
  $("#aboyuma-product-category-table").DataTable({
    pageLength: 10,
    searching: true,
  });

  $("#aboyuma-product-category-table").on(
    "click",
    ".delete-category-button",
    function (e) {
      e.preventDefault();

      if (!confirm("Are you sure you want to delete this category?")) {
        return;
      }

      var button = $(this);
      var row = button.closest("tr");
      var id = button.data("id");

      $.post(
        productCategoryDelete.ajaxurl,
        {
          action: "aboyuma_product_category_delete",
          nonce: productCategoryDelete.nonce,
          id: id,
        },
        function (response) {
          if (response.success) {
            row.fadeOut(200, function () {
              $(this).remove();
            });
          } else {
            alert(
              response.data || "An error occurred while deleting the category.",
            );
          }
        },
      );
    },
  );
});
