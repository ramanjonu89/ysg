$(function () {
  $('#view-more-categories').click(function () {
    $(this).find('i').toggleClass('fa-caret-up fa-caret-down');
    var container = $(this).closest('.box-content');
    var status = $(this).data('status');
    if (status === 'hide') {
      $(this).data('status', 'show');
      $(this).find('span').text("View less");
      categoryElements = container.find('.category-hidden-item:hidden').show(300);
    } else {
      $(this).data('status', 'hide');
      $(this).find('span').text("View more");
      categoryElements = container.find('.category-hidden-item').hide(300);
    }
  });

  $('#view-more-brands').click(function () {
    $(this).find('i').toggleClass('fa-caret-up fa-caret-down');
    var container = $(this).closest('.box-content');
    var status = $(this).data('status');
    if (status === 'hide') {
      $(this).data('status', 'show');
      $(this).find('span').text("View less");
      brandElements = container.find('.brand-hidden-item:hidden').show(300);
    } else {
      $(this).data('status', 'hide');
      $(this).find('span').text("View more");
      brandElements = container.find('.brand-hidden-item').hide(300);
    }
  });

  $('#view-more-sellers').click(function () {
    $(this).find('i').toggleClass('fa-caret-up fa-caret-down');
    var container = $(this).closest('.box-content');
    var status = $(this).data('status');
    if (status === 'hide') {
      $(this).data('status', 'show');
      $(this).find('span').text("View less");
      brandElements = container.find('.seller-hidden-item:hidden').show(300);
    } else {
      $(this).data('status', 'hide');
      $(this).find('span').text("View more");
      brandElements = container.find('.seller-hidden-item').hide(300);
    }
  });

  $('#view-more-subcategories').click(function () {
    $(this).find('i').toggleClass('fa-caret-up fa-caret-down');
    var subCategoryElements = $(this).closest('.box-content');
    var status = $(this).data('status');
    if (status === 'hide') {
      $(this).data('status', 'show');
      $(this).find('span').text("View less");
      subCategoryElements = subCategoryElements.find('.sub-category-hidden-item:hidden').show(300);
    } else {
      $(this).data('status', 'hide');
      $(this).find('span').text("View more");
      subCategoryElements = subCategoryElements.find('.sub-category-hidden-item').hide(300);
    }
  });
});
