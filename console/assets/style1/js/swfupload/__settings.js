var swfupload_settings = {
//flash_url : "{|$STYLE_URL|}/swfupload/swfupload.swf",
upload_url: "/uploads/upload",
// post_params: {"PHPSESSID" : "{|$session_id|}"},
file_size_limit : "100 MB",
file_types : "*.jpg;*.jpeg;*.gif;*.png",
file_types_description : "All Image Files",
file_upload_limit : 100,
file_queue_limit : 0,
custom_settings : {
    progressTarget : "fsUploadProgress",
    cancelButtonId : "btnCancel"
},
debug: false,

// Button settings
//button_image_url: "{|$STYLE_URL|}/swfupload/images/TestImageNoText_65x29.png",
button_width: "65",
button_height: "29",
button_placeholder_id: "spanButtonPlaceHolder",
button_text: '<span class="theFont">上传</span>',
button_text_style: ".theFont { font-size: 16; }",
button_text_left_padding: 12,
button_text_top_padding: 3,

// The event handler functions are defined in handlers.js
file_queued_handler : fileQueued,
file_queue_error_handler : fileQueueError,
file_dialog_complete_handler : fileDialogComplete,
upload_start_handler : uploadStart,
upload_progress_handler : uploadProgress,
upload_error_handler : uploadError,
upload_success_handler : uploadSuccess,
upload_complete_handler : uploadComplete,
queue_complete_handler : queueComplete    // Queue plugin event
};