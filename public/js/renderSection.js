/**
 * Created by Programador2 on 18/04/2017.
 */

function ajaxRenderSection(url) {
    $.ajax({
        type: 'GET',
        url: url,
        dataType: 'json',
        success: function (data) {
            $('.panel-body').empty().append($(data));
            cargarDatatable();
        },
        error: function (data) {
            var errors = data.responseJSON;
            var error = '';
            if (errors) {
                $.each(errors, function (i) {
//                            console.log(errors[i]);
                    error += errors[i] + '<br>';
                });
                showError(error);
            }
        }
    });
}

$.fn.ajaxPost = function (url, method, sectionToRender, data) {
    var token = $("input[name=_token]").val();
    $.ajax({
        type: method,
        url: url,
        data: data,
        headers: {'X-CSRF-TOKEN': token},
        dataType: 'json',
        success: function (data) {
            showSucces(data.message);
            ajaxRenderSection(sectionToRender);
        },
        error: function (data) {
            var errors = data.responseJSON;
            var error = '';
            if (errors) {
                $.each(errors, function (i) {
                    console.log(errors[i]);
                    error += errors[i] + '<br>';
                });
                showError(error);
            }
        }
    });
};
function showError(errors) {
    $("#msj-error").html(errors);
    $("#message-danger").fadeIn();
}

function showSucces(message) {
    $("#msj-ok").html(message);
    $("#message-success").fadeIn();
}
