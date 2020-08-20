; (function ($) {
    $(document).ready(function () {
        $("#qofw_genpw").on('click', function () {
            $.post(swo.ajax_url, {'action': 'swo_gen_password'}, function (data) {
                $("#password").val(data);
            });
        });
        $("#coupon").on('click', function () {
            if ($(this).attr('checked')) {
                $("#discount-label").html(qofw.dc);
                $("#discount").attr("placeholder", qofw.cc);
            } else {
                $("#discount-label").html(qofw.dt);
                $("#discount").attr("placeholder", qofw.dt);
            }
        });

        $("#email").on('blur', function () {

            if($(this).val()==''){
                return;
            }
            $("#first_name").val('');
            $("#last_name").val('');
            let email = $(this).val();
            //alert(qofw.ajax_url);
            $.post(swo.ajax_url, {'action': 'swo_fetch_user', 'email': email}, function (data) {
                if ($("#first_name").val() == '') {
                    $("#first_name").val(data.fn);
                }
                if ($("#last_name").val() == '') {
                    $("#last_name").val(data.ln);
                }
                $("#phone").val(data.pn);
                $("#customer_id").val(data.id);

                if (!data.error) {
                    $("#first_name").attr('readonly', 'readonly');
                    $("#last_name").attr('readonly', 'readonly');
                    $("#password_container").hide();
                } else {
                    $("#password_container").show();
                    $("#first_name").removeAttr('readonly')
                    $("#last_name").removeAttr('readonly');
                }

            }, "json");
        });


        if ($('#qofw-edit-button').length > 0) {
            tb_show(swo.pt, "#TB_inline?inlineId=qofw-modal&width=700");
        }
    });
})(jQuery);
