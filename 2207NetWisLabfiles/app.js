// Expandable Naration
$('.expandable_naration').find('h4').addClass('expanded_content');
$('.expandable_naration').find('h4')
  .click( function(event) {
      if (this == event.target) {
          $(this).toggleClass('expanded_content_active');
          $(this).next('div').toggle('medium');
      }
      return false;
  })
  .next('div').hide();

$('.collapse-button').click( function(event) {
  $(this).parent('div').toggle('medium');
  $(this).parent('div').prev('h4').toggleClass('expanded_content_active');
});

function initDatasetAccordions() {
  var $datasetRows = $('#download-overview .download-table__row').not('.download-table__row--header');

  if (!$datasetRows.length) {
    return;
  }

  $datasetRows.each(function(index) {
    var $row = $(this);
    var $title = $row.find('.download-table__title').first();
    var $description = $row.find('.download-table__description').first();

    if (!$title.length || !$description.length) {
      return;
    }

    $row.addClass('dataset-accordion-row');
    $title.addClass('dataset-accordion-toggle');
    $title.attr({
      role: 'button',
      tabindex: '0',
      'aria-expanded': 'false'
    });

    if (!$description.attr('id')) {
      $description.attr('id', 'dataset-panel-' + index);
    }

    $title.attr('aria-controls', $description.attr('id'));

    if (!$description.find('.collapse-button').length) {
      $description.append('<button class="collapse-button dataset-collapse-button"><span data-icon=""> Collapse</span></button>');
    }

    $description.hide();
  });

  $('#download-overview .dataset-accordion-toggle').off('click.dataset keydown.dataset').on('click.dataset keydown.dataset', function(event) {
    if (event.type === 'keydown' && event.key !== 'Enter' && event.key !== ' ') {
      return true;
    }

    if (event.type === 'keydown') {
      event.preventDefault();
    }

    var $title = $(this);
    var $panel = $('#' + $title.attr('aria-controls'));
    var isExpanded = $title.hasClass('expanded_content_active');

    $title.toggleClass('expanded_content_active', !isExpanded);
    $title.attr('aria-expanded', String(!isExpanded));
    $panel.toggle('medium');

    return false;
  });

  $('#download-overview .dataset-collapse-button').off('click.dataset').on('click.dataset', function(event) {
    event.preventDefault();

    var $panel = $(this).closest('.download-table__description');
    var $title = $panel.closest('.dataset-accordion-row').find('.dataset-accordion-toggle').first();

    $panel.toggle('medium');
    $title.toggleClass('expanded_content_active', false);
    $title.attr('aria-expanded', 'false');
  });
}

function initDatasetIntro() {
  var $overview = $('#download-overview');
  var $heading = $overview.children('h2').first();

  if (!$overview.length || !$heading.length || $overview.children('.datasets-intro').length) {
    return;
  }

  $('<p class="datasets-intro">This page presents datasets and accompanying source-code resources from NetWIS Lab projects, highlighting representative research platforms, measurement collections, and reusable experimental artifacts.</p>').insertAfter($heading);
}


var pos;
function menuItemPosition() {
  var $menuItems = $(".menu__item");
  var $subMenus = $(".menu__item .sub-menu");

  $menuItems.off("click.mobileMenu");
  $menuItems.css({ top: "", left: "" });
  $subMenus.css({ top: "", left: "", display: "" });

  return false;
};

function wrapperMarginTop() {
  /* Page style: page margin relative to height of fixed header */
  var $header_height = $( ".header").height();
//  $( ".wrapper").css("height", ($(window).height() - $header_height));
  $( ".wrapper").css("padding-top", $header_height+22);


}
  
menuItemPosition();
initDatasetIntro();
initDatasetAccordions();

if ($(window).width() > 768) {
  wrapperMarginTop();
}


$( window ).resize(function() {
  menuItemPosition();
  if ($(window).width() > 768) {
    wrapperMarginTop();
  }

  $('menu__item circle__content').flowtype({
   minimum   : 500,
   maximum   : 1200,
   minFont   : 10,
   maxFont   : 14,
   fontRatio : 30
  });
});

var url      = window.location.href;
console.log(url);

jQuery(document).ready(function () {
  jQuery("#nanoGallery").nanoGallery({
    thumbnailWidth: 'auto',
    thumbnailHeight: 100,
    locationHash: false,
    thumbnailHoverEffect:'borderLighter,imageScaleIn80',
    itemsBaseURL:'img/photos/'
  });
});




//Set the position of logos of external links on left panel
// $(".logos-left >div").offset({ top: $(window).height()-$(window).height()*0.28 });
// $(".logos-left").css("z-index","200");
