(function ($) {
    function init() {
        $('.easy-tree').EasyTree({
            addable: false,
            editable: false,
            deletable: false
        });
    }
    window.onload = init();
})(jQuery)

$(document).ready(function () {
    var idCat = $('#hidden-cat').val();
    if (idCat) {
        $('#'+idCat).addClass('li_selected');
    //    $('html, body').animate({
        //        scrollTop: $('#'+idCat).offset().top
        //    }, 2000);
    }
    
    
});
$(".select_cat > span > a").click(function (event) {
    event.preventDefault();
    var liItem = $(this).parent().parent();
    var id = liItem.attr('id');
    jQuery.ajax({
        url: '/backend/category/detail/' + id,
        beforeSend: function () {
            showLoadmore();
        },
        success: function (data) {
            if (data.success == 1) {
                $('.alert-success').addClass('hide');
                $('.field-name').removeClass('has-error');
                $('.help-block').html('');
                $('#id-cat').val(data.data.id);
                $('#name').val(data.data.name);
                $('#id-parent').val(data.data.id);
                $('#level').val(data.data.level);
                $('.kv-detail-crumbs').html(data.data.breadcrumbs);
                if (data.data.level == 4) {
                    $('#add-sub-cat').attr('disabled', 'disabled');
                } else {
                    $('#add-sub-cat').removeAttr('disabled');
                }
            } else {
                alert(data.message, '', function () {
                    window.location.reload();
                });
            }
        },
        complete: function () {
            hiddenLoadmore();
        },
        fail: function () {
            hiddenLoadmore();
        }
    });
});

$('#add-sub-cat').click(function (event) {
    event.preventDefault();
    //show load more
    showLoadmore();
    $('#add-sub-cat').attr('disabled', 'disabled');
    $('.kv-detail-crumbs').find('span').removeClass('kv-crumb-active');
    var text_breadcrumbs = $('.kv-detail-crumbs').html();
    text_breadcrumbs += ' » <span class="kv-crumb-active">Untitled</span>';
    $('.kv-detail-crumbs').html(text_breadcrumbs);
    $('#id-cat').val('(new)');
    $('#type').val('1');
    $('#name').val('');
    //hidden load more
    hiddenLoadmore();
});

$('#add-cat').click(function (event) {
    event.preventDefault();
    //show load more
    showLoadmore();
    $('#add-sub-cat').attr('disabled', 'disabled');
    $('#delete-cat').attr('disabled', 'disabled');
    var text_breadcrumbs = '<span class="kv-crumb-active">Untitled</span>';
    $('.kv-detail-crumbs').html(text_breadcrumbs);
    $('#id-cat').val('(new)');
    $('#type').val('2');
    $('#name').val('');
    //hidden load more
    hiddenLoadmore();
});

function showLoadmore(){
    $('#detail-category').addClass('show-loadmore');
}
function hiddenLoadmore(){
    setTimeout(function(){
        $('#detail-category').removeClass('show-loadmore');
    }, 500);
    
}