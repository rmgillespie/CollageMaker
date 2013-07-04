$.fn.alternateRowColors = function() {
  $('tbody tr:odd', this).removeClass('even').addClass('odd');
  $('tbody tr:even', this).removeClass('odd').addClass('even');
  return this;
};

$(document).ready(function() {

  var alternateRowColors = function($table) {
$('tbody tr:odd', $table).removeClass('even').addClass('odd');
$('tbody tr:even', $table).removeClass('odd').addClass('even');

  };

  $('table.sortable').each(function() {
var $table = $(this);
$table.alternateRowColors($table);
$table.find('th').each(function(column) {
  var findSortKey;

  if ($(this).is('.sort-alpha')) {
    findSortKey = function($cell) {
      return $cell.find('.sort-key').text().toUpperCase() + ' ' + $cell.text().toUpperCase();
    };
  }
  else if ($(this).is('.sort-numeric')) {
    findSortKey = function($cell) {
      var key = parseFloat($cell.text().replace(/[^\d\.]+/gi, ''));
      //alert(key);
      return isNaN(key) ? 0 : key;
    };
  }
  else if ($(this).is('.sort-date')) {
    findSortKey = function($cell) {
      return Date.parse($cell);
    };
  }

  if (findSortKey) {
    $(this).addClass('clickable').hover(function() {
      $(this).addClass('ui-state-hover');
    }, function() {
      $(this).removeClass('ui-state-hover');
    }).click(function() {
      var newDirection = 1;
      if ($(this).is('.sorted-asc')) {
        newDirection = -1;
      }

      rows = $table.find('tbody > tr').get();

      $.each(rows, function(index, row) {
        row.sortKey =                    findSortKey($(row).children('td').eq(column));
      });
      rows.sort(function(a, b) {
        if (a.sortKey < b.sortKey) return -newDirection;
        if (a.sortKey > b.sortKey) return newDirection;
        return 0;
      });
      $.each(rows, function(index, row) {
        $table.children('tbody').append(row);
        row.sortKey = null;
      });

      $table.find('th').removeClass('sorted-asc').removeClass('sorted-desc');
      var $sortHead = $table.find('th').filter(':nth-child(' + (column + 1) + ')');
      if (newDirection == 1) {
        $sortHead.removeClass('sorted-desc').addClass('ui-state-active').addClass('sorted-asc');
        $sortHead.siblings().removeClass('sorted-desc')
        						.removeClass('sorted-asc')
        						.removeClass('ui-state-active');
        $table.find('th').find('span').removeClass('ui-icon-arrowthick-1-s')
        						.removeClass('ui-icon-arrowthick-1-n')
        						.addClass('ui-icon-arrowthick-2-n-s');
        $sortHead.find('span').removeClass('ui-icon-arrowthick-2-n-s')
        						.removeClass('ui-icon-arrowthick-1-n')
        						.addClass('ui-icon-arrowthick-1-s');
      } else {
        $sortHead.removeClass('sorted-asc').addClass('ui-state-active').addClass('sorted-desc');
        $sortHead.siblings().removeClass('sorted-asc')
        						.removeClass('sorted-desc')
        						.removeClass('ui-state-active');
        $table.find('th').find('span').removeClass('ui-icon-arrowthick-1-n')
        						.removeClass('ui-icon-arrowthick-1-s')
        						.addClass('ui-icon-arrowthick-2-n-s');
        $sortHead.find('span').removeClass('ui-icon-arrowthick-2-n-s')
        						.removeClass('ui-icon-arrowthick-1-s')
        						.addClass('ui-icon-arrowthick-1-n');
      }
      $table.find('td').removeClass('sorted')
        .filter(':nth-child(' + (column + 1) + ')').addClass('sorted');
      $table.alternateRowColors($table);
      $table.trigger('repaginate');
    });
  }
});

  });

});
		$('tr').hover(function() {
    $('tr').toggleClass('highlight');
});
$('tr').hover(function() {
    $('tr').toggleClass('highlight');
});