function fileDropBlock(block, type) {
    var allowType = {
        'img': ['image/jpeg', 'image/png', 'image/gif']
    };

    block.filedrop({
        url: '/upload-file', // url к которой будет происходить обращение при активации загрузки
    paramname: 'file', // параметр. По сути это атрибут name вашего input поля
    fallbackid: 'upload_button',
        maxfiles: 1, // кол-во файлов
    maxfilesize: 2, // размер файла в mb

    // Реакция на ошибки. Тут может быть что угодно
    error: function (err, file) {
        switch (err) {
            case 'BrowserNotSupported':
                console.log('Old browser');
                break;
            case 'FileTooLarge':
                console.log('File Too Large');
                break;
            case 'TooManyFiles':
                console.log('Only 1 file can be downloader');
                break;
            case 'FileTypeNotAllowed':
                console.log('Wrong file type');
                break;
            default:
                console.log('Some error');
        }

    },
    allowedfiletypes: allowType[type], // разрешенные типы файлов для загрузки
    dragOver: function () {
        block.addClass('active-drag-block');
    },
    dragLeave: function () {
        block._removeClass('active-drag-block');
    },


    uploadFinished: function (i, file, response) {
        block.find('input[type="text"]').val(response.filePath); // в инпут поместим путь к файлу
    }
})
}