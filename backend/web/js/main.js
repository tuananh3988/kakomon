$('select.select-root-cat').on('change', function () {
    var id = this.value;
    var sub_level = 1;
    getSubCategoty(id, sub_level);
})

$('select.select-sub1-cat').on('change', function () {
    var id = this.value;
    var sub_level = 2;
    getSubCategoty(id, sub_level);
})
$('select.select-sub2-cat').on('change', function () {
    var id = this.value;
    var sub_level = 3;
    getSubCategoty(id, sub_level);
})

function getSubCategoty(id, sub_level){
    if (id) {
        jQuery.ajax({
            url: '/backend/question/getsubcategory/' + id + '/' + sub_level,
            success: function (data) {
                if (data.success == 1) {
                    var html = '';
                    for (i = sub_level; i < 4; i++) {
                        html = '<option value="">Select sub' + i + 'category</option>';
                        $('#question-id_sub' + i).html(html);
                    }
                    $('#question-id_sub' + sub_level).html(data.data);
                } else {
                    alert(data.message, '', function () {
                        window.location.reload();
                    });
                }
            },
            complete: function () {

            }
        });
    } else {
        var html = '';
        for (i = sub_level; i < 4; i++) {
            html = '<option value="">Select sub' + i + 'category</option>';
            $('#question-id_sub' + i).html(html);
        }
    }
    
}