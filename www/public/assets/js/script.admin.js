$(document).on('click', '[name="changePrice"]', function (e) {
    e.preventDefault();
    var parent = $(this).parent();
    var url = parent.data('action');
    var value = parent.find('[name="value"]').val();
    $.ajax({
        url: url,
        type: 'POST',
        dataType: 'json',
        data: {
            value: value
        },
        success: function(response) {
            console.log(response);
        }
    });
})

$(document).on('click', '[name="changeQuantity"]', function (e) {
    e.preventDefault();
    var parent = $(this).parent();
    var url = parent.data('action');
    var value = parent.find('[name="value"]').val();
    $.ajax({
        url: url,
        type: 'POST',
        dataType: 'json',
        data: {
            value: value
        },
        success: function(response) {
            console.log(response);
        }
    });
})

$(document).on('change', '[name="changeStatus"]', function (e) {
    e.preventDefault();
    var parent = $(this).parent();
    var url = parent.data('action');
    var value = $(this).val();
    $.ajax({
        url: url,
        type: 'POST',
        dataType: 'json',
        data: {
            value: value
        },
        success: function(response) {
            console.log(response);
        }
    });
})

$(document).on('change', '[name="changeSupplier"]', function (e) {
    e.preventDefault();
    var parent = $(this).parent();
    var url = parent.data('action');
    var value = $(this).val();
    $.ajax({
        url: url,
        type: 'POST',
        dataType: 'json',
        data: {
            value: value
        },
        success: function(response) {
            console.log(response);
        }
    });
})

$(document).on('click', '[name="split"]', function (e) {
    e.preventDefault();
    var parent = $(this).parent();
    var url = parent.data('action');
    var value = parent.find('[name="value"]').val();
    $.ajax({
        url: url,
        type: 'POST',
        dataType: 'json',
        data: {
            value: value
        },
        success: function(response) {
            console.log(response);
        }
    });
})
var arrayType = {
    'img': [
        'image/png',
        'image/jpg',
        'image/jpeg'
    ],
    'pdf': [
        'application/pdf',
        'application/x-pdf'
    ],
    'xls': [
        "application/vnd.ms-excel",
        "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"
    ]
};
$(document).on("change", 'input[type="file"]', function () {
    var $_this = $(this),
        type = 'xls',
        reader,
        file,
        action;
    file = this.files[0];
    action = $_this.closest('form').data('action');
    if (window.FormData) {
        formdata = new FormData();
    }

    if (window.FileReader) {
        reader = new FileReader();
        reader.readAsDataURL(file);
    }

    if (formdata) {
        formdata.append("file", file);
    }

    if ($.inArray(file.type, arrayType[type])>=0) {
        $.ajax({
            url: action,
            type: "POST",
            data: formdata,
            dataType: 'json',
            processData: false,
            contentType: false,
            success: function (res) {
                console.log(res);
                $('.table_block').html(res.html)
                $('[name="files[jsonTable]"]').val(res.lines)
                $('[name="files[file]"]').val(res.filepath)
                $_this.val('')
                /*var userData = jQuery.parseJSON(res);
                $_this.parent().find('input[type="text"]').val(userData.filePath);*/
            }
        });
    } else {
        alert('Wrong type')
    }
});