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
$(".select_cat > span > a").click(function (event) {
    event.preventDefault();
    var liItem = $(this).parent().parent();
    var id = liItem.attr('id');
    jQuery.ajax({
        url: '/backend/category/detail/' + id,
        
        beforeSend: function () {
            $('#detail-category').addClass('test');
        },
        success: function (data) {
            console.log(data.success);
            if (data.success == 1) {
                $('#cid').val(data.data.cid);
                $('#name').val(data.data.name);
            } else {
                alert(data.message,'',function(){
                    window.location.reload();
                });
            }
        },
        complete: function () {
//            $.unblockUI();
        },
        fail: function () {
//            $.unblockUI();
        }
    });
});