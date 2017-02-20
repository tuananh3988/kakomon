$('select.select-root-cat').on('change', function () {
    var id = this.value;
    var sub_level = 2;
    getSubCategoty(id, sub_level);
})

$('select.select-sub1-cat').on('change', function () {
    var id = this.value;
    var sub_level = 3;
    getSubCategoty(id, sub_level);
})
$('select.select-sub2-cat').on('change', function () {
    var id = this.value;
    var sub_level = 4;
    getSubCategoty(id, sub_level);
})

function getSubCategoty(id, sub_level){
    if (id) {
        jQuery.ajax({
            url: '/backend/question/getsubcategory/' + id + '/' + sub_level,
            success: function (data) {
                if (data.success == 1) {
                    var html = '';
                    for (i = sub_level; i < 5; i++) {
                        html = '<option value="">Select sub' + (i-1) + ' category</option>';
                        $('#quiz-category_id_' + i).html(html);
                    }
                    $('#quiz-category_id_' + sub_level).html(data.data);
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
        for (i = sub_level; i < 5; i++) {
            html = '<option value="">Select sub' + (i-1) + ' category</option>';
            $('#quiz-category_id_' + i).html(html);
        }
    }
    
}

function ConfirmDeleteQuestion(id){
    var contents = "";
        contents += '<p class=confirm_text>You want to delete this question？</p><div class=dialogItem>';
        contents += '<input class="confirm_ok" type="button" value="OK" onclick="submitformDelete('+id+');"/>';
        contents += '<input type="button" value="Cancer" class="confirm_cancel" onclick="hideDialog();"></div>';   
    showDialog('Confirm Delete', contents, 'prompt');
}

function submitformDelete(id){
    $('#id-delete').val(id);
    $('form#form').submit();
}

function removeImgAns(id){
    $('#img-ans-'+id).html('');
    $('#answer-answer'+ id + '-remove_img_flg').val('1');
}
function removeImgQuestion(){
    $('#img-question').html('');
    $('#quiz-remove_img_question_flg').val('1');
}

$('#delete-cat').click(function() {
    var id = $('#id-parent').val();
    $('#flag-delete').val('delete');
    jQuery.ajax({
        url: '/backend/category/checkdelete/' + id,
        success: function (data) {
            if (data.success == 1) {
               var contents = "";
                    contents += '<p class=confirm_text>You want to delete this category？</p><div class=dialogItem>';
                    contents += '<input class="confirm_ok" type="button" value="OK" onclick="submitformDeleteCat();"/>';
                    contents += '<input type="button" value="Cancer" class="confirm_cancel" onclick="hideDialog();"></div>';   
                showDialog('Confirm Delete', contents, 'prompt');
            } else {
                alert(data.message, '', function () {
                    window.location.reload();
                });
            }
        },
        complete: function () {}
    });
})


function submitformDeleteCat(){
    $('form#form').submit();
}