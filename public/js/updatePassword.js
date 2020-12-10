$(document).ready(() => {

    let url = "http://45.79.221.17:81/api/change_password"

    authToken = $('#authToken').val();
    $('#authToken').remove();


    password = $('#password');
    password_confirmation = $('#password_confirmation');
    current_password = $('#current_password');

    $(document).on('click', '#updatePasswordButton', (event) => {

        var updateButton = $(event.currentTarget);

        updateButton.attr('disabled', '');
        updateButton.addClass('loading');

        data = {
            "current_password": current_password.val(),
            "password": password.val(),
            "password_confirmation": password_confirmation.val()
        };

        new Promise((resolve, reject) => {
            $.ajax(url, {
                method: 'PUT',
                data: data,
                headers: {
                    "Authorization": "Bearer " + authToken,
                    "accept": "application/json",
                },
                success: (data) => {
                    resolve(data)
                },
                error: (data) => {
                    reject(data)
                },
            });
        }).then((data) => { // resolved
            // console.log(data);
            // let responseJson = data.responseJSON;
            // let responseMessage = responseJson.message || data.statusText
            new SuccessModal(data.message, "Succès", "Continuer", 401).show();
        }, (data) => { // rejected
            // console.log(data);
            let responseJson = data.responseJSON;
            let responseMessage = responseJson.message || data.statusText
            new ErrorModal(responseMessage, "Erreur").show();
        }).then((data) => { // anyway
            updateButton.removeAttr('disabled', '');
            updateButton.removeClass('loading');
        });

    });
})