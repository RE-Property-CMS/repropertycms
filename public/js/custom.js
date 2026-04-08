//TODO: notifications to be replaced by notify.js - if no other library found
var alert = document.getElementById("alert");
var btn = document.getElementById("btn");

if (btn) {
    btn.addEventListener('click', function () {
        alert.style.display = "none";
    });

    setTimeout(hide, 3000);

    function hide() {
        document.getElementById("alert").style.display = 'none';
    }
}

// add validation on agent profile email edit

function validation() {
    var email = document.getElementById('email');
    if (email.value == '') {
        document.getElementById("small-email").innerHTML = "Email is required";
        document.getElementById("small-email").style.color = "red";
        return false;
    } else if (ValidateEmail() == false) {
        return false;
    }
}

function ValidateEmail() {
    var email = document.getElementById('email');
    var mailformat = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;
    if (email.value.match(mailformat)) {
        document.getElementById("small-email").innerHTML = "Active Email";
        document.getElementById("small-email").style.color = "green";
        return true;
    } else {
        document.getElementById("small-email").innerHTML = "*Incorrect Email";
        document.getElementById("small-email").style.color = "red";
        return false;
    }
}

var floorplansName = 1;

// Add the value filled in signup form
function validaAddress(frm) {

    document.getElementById('cityhelp').innerHTML = "";
    document.getElementById('phonehelp').innerHTML = "";
    document.getElementById('countrieshelp').innerHTML = "";
    document.getElementById('statehelp').innerHTML = "";
    document.getElementById('cityhelp').innerHTML = "";
    document.getElementById('ziphelp').innerHTML = "";

    var failed = false;
    var phone = frm.phone.value;
    if (phone == '') {
        document.getElementById('phonehelp').innerHTML = "* Phone Number filled are required";
        failed = true;
    }
    var countries = frm.countries.value;
    if (countries == 0) {
        document.getElementById('countrieshelp').innerHTML = "* Country filled are required";
        failed = true;
    }
    var state = frm.state.value;
    if (state == '') {
        document.getElementById('statehelp').innerHTML = "* State filled are required";
        failed = true;
    }
    var city = frm.city.value;
    if (city == '') {
        document.getElementById('cityhelp').innerHTML = "* City filled are required";
        failed = true;
    }

    var zip = frm.zip.value;
    if (zip == '') {
        document.getElementById('ziphelp').innerHTML = "* Zip filled are required";
        failed = true;
    }
    var address = frm.address.value;
    if (address == '') {
        document.getElementById('addresshelp').innerHTML = "* City filled are required";
        failed = true;
    }

    if (!failed) {
        return true;
    } else {
        return false;
    }
}

// Append the Amenities data
function append_amenities(Amenities) {
    id = Amenities.id;
    if ($('#addAmenities').find("#" + id).length == 0) {
        const addAmenities = document.getElementById('addAmenities');
        const amenities = Amenities.cloneNode(true).innerHTML;
        $("#" + id).remove();
        let input = document.createElement("input");
        input.type = "text";
        input.value = amenities;
        input.setAttribute('disabled', 'disabled');
        input.setAttribute('value', amenities);
        input.setAttribute("class", "amenitiesbutton");

        let a = document.createElement("a");
        a.setAttribute("href", 'javascript:void(0);');
        a.setAttribute("style", "cursor:pointer;");
        a.classList.add("amenitiesbutton1", id);
        a.setAttribute("data-name", amenities);
        a.append('x');

        let div = document.createElement("div");
        div.append(input, a);
        div.setAttribute("id", id);
        div.setAttribute("class", "amenitie is-filled");

        addAmenities.append(div);
        var amenitites_id = document.getElementById("amenities_array").value;
        if (amenitites_id == '') {
            Array_amenities = [];
        } else {
            Array_amenities = amenitites_id.split(",");
        }
        Array_amenities.push(id);
        document.getElementById("amenities_array").value = Array_amenities;
    }
}

// TODO: Reduce function for adding amenites 

function add_amenities() {
    const amenite = $('#add_amenitie')[0].value;
    const addAmenities = document.getElementById('addAmenities');

    let input = document.createElement("input");
    input.type = "text";
    input.value = amenite;
    input.setAttribute('disabled', 'disabled');
    input.setAttribute('value', amenite);
    input.setAttribute("class", "amenitiesbutton");

    let a = document.createElement("a");
    a.setAttribute("href", 'javascript:void(0);');
    a.setAttribute("style", "cursor:pointer;");
    a.classList.add("amenitiesbutton1");
    a.setAttribute("data-name", amenite);
    a.append('x');

    let div = document.createElement("div");
    div.append(input, a);
    div.setAttribute("class", "amenitie is-filled");

    addAmenities.append(div)
    var amenitites_id = document.getElementById("amenities_array").value;
    console.log(amenitites_id)
    if (amenitites_id == '') {
        Array_amenities = [];
    } else {
        Array_amenities = amenitites_id.split(",");
    }
    Array_amenities.push(amenite);
    document.getElementById("amenities_array").value = Array_amenities;

}

function delete_amenities() {
    document.getElementById('addAmenities').innerHTML = "";
}


$(document).ready(function () {
    $(".confirm-alert").click(function () {
        msg = $(this).data("msg");
        if (typeof msg == "undefined") {
            msg = "Do you really want to do this?";
        }

        if (confirm(msg) == true) {
            return true;
        } else {
            return false;
        }
    });

    $(document).on('click', '.amenitiesbutton1', function (e) {
        e.preventDefault()
        if (e.target.classList[1]) {
            var Amenitite_id = e.currentTarget.classList[1];
            var Amenitite_name = e.currentTarget.dataset['name'];
            var amenitites_id = document.getElementById("amenities_array").value;
            if (amenitites_id == '') {
                Array_amenities = [];
            } else {
                Array_amenities = amenitites_id.split(",");
            }
            Array_amenities.map((data, i) => {
                if (data == Amenitite_id) {
                    Array_amenities.splice(i, 1);
                    $tagName = $('#' + data);

                    let span = document.createElement('span')
                    span.setAttribute("class", "amenities");
                    span.setAttribute("id", Amenitite_id);
                    span.setAttribute("name", Amenitite_name);
                    span.textContent = Amenitite_name;
                    span.setAttribute("onclick", 'append_amenities(this);');
                    $('.amenitieData').append(span)
                    Array_amenities.splice(Amenitite_id, 1);
                    document.getElementById("amenities_array").value = Array_amenities;
                    return;
                }
            });
            $("#" + Amenitite_id).remove();
        } else {
            const amenitites_id = document.getElementById("amenities_array").value;

            if (amenitites_id == '') {
                Array_amenities = [];
            } else {
                Array_amenities = amenitites_id.split(",");
            }

            const index = Array_amenities.indexOf(e.target.dataset.name);

            // only splice array when item is found
            if (index > -1) {
                // 2nd parameter means remove one item only
                Array_amenities.splice(index, 1);
            }
            document.getElementById("amenities_array").value = Array_amenities;

            // code for remove custom amenitie
            e.target.parentElement.remove();
        }
    });

    $("#amenitieToggler").click(function () {
        $("#amenitie_suggation").toggle();
    });

    $('#addFiles').click(function () {
        $('.file').click();
    })

    $(document).on("keypress", ".form-control", function (e) {
        //Clear the text in error div
        $("#" + $(this).attr("name") + "help").html("");
    });

    //image delete alert show
    $(document).on('click', '.image-delete', function () {
        $("#dialog").addClass("open");
        $image_id = $(this).data("image-id");
        //Attach image delete function to yes button
        $("#dialog .dialog-yes-btn").click(function () {
            deleteImage($image_id);
        });
    });

    // Add the video using button

    $('#addvideos').click(function () {
        $('#video').click();
    });

    // drag And drop Floor plans end
    $(document).on("click", "#addhotspot", function () {
        console.log("TEST");
        $('#hotspot-sidebar').show();
    });

    $(document).on("click", "#close-hotspot-sidebar", function () {
        $('#hotspot-sidebar').hide();
    });

    // Here Add code for remove scrollbar on proeprty gallery section

    var a = $('.address_bar').outerHeight();
    var b = $('.gallery_body').outerHeight();
    $(".gallery_body").css("height", parseInt(b) - parseInt(a) - 10 + 'px');

    $(window).on('resize', function () {
        $(".gallery_body").css("height", $(window).outerHeight() - a + 'px');
    });
});

// drag And drop image

var percentage = 0;
var CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').getAttribute("content");


if (document.getElementsByClassName("images_drop").length > 0) {
    // click button then add file
    $('#addFiles').click(function () {
        $('.images_drop').click();
        myDropzone.processQueue();
    });
    Dropzone.autoDiscover = false;
    var myDropzone = new Dropzone(".images_drop", {
        url: 'save-images',
        maxFilesize: 50, // in MB
        acceptedFiles: ".jpeg,.jpg,.png,",
    });

    myDropzone.on("sending", function (file, xhr, formData) {
        $("#progressbar").css("display", "inline-block");
        setperandsize(40);
        formData.append("_token", CSRF_TOKEN);
        setperandsize(60);
    });
    myDropzone.on("success", function (file, response) {
        setperandsize(80);
        if (response.success == 0) { // Error
            $.notify(
                response.error,
                {
                    position: "top center",
                    className: "error"
                }
            );
        } else {
            setperandsize(100);
            $(".size").text(response.size);

            //Client does not want notifications here
            // $.notify(
            //     response.message,
            //     { 
            //       position:"top center",
            //       className: "success"
            //     }
            // ); 

            var propertyUrl = web_url + '/files/property_images_thumb/' + response.thumb_name;
            var str1 = response.thumb_name
            if (str1.startsWith('http')) {
                propertyUrl = response.thumb_name
            }
            console.log(propertyUrl);

            html = "";
            html += '<div class="inline-block h-52 w-52 mx-3 relative gallery-image" id="' + response.image_id + '">'
            html += '<img src="' + propertyUrl + '" id="image' + response.image_id + '" style="height:10.5rem; max-height: 100%; max-width: 100%;"  alt="">';
            html += "<a href='#' title='Rotate Image' onclick=rotateImage(" + response.image_id + ")><i class='fa fa-rotate mr-5 ml-2 mt-3 text-green-500'></i></a>";
            html += '<a href="#" title="Delete Image" class="image-delete float-right" data-image-id="' + response.image_id + '"><i class="fa fa-times mr-5 ml-2 mt-3 text-red-600"></i></a>';
            html += '</div>';
            $(".property_image").append(html);
        }
        setTimeout(() => {
            $(".dz-preview").remove();
            $(".images_drop").removeClass("dz-started");
            $(".progressbar").hide();
            $(".size").html("0 b");
            $(".percentage").html("0");
        }, 5000);
    });
}

function setperandsize(value) {
    setTimeout(() => {
        $(".progressbar").val(value);
        $(".percentage").text(value);
    }, 1000);
}

//delete property  image 

function deleteImage(id) {
    $("#dialog").removeClass("open");
    $.ajax({
        type: "GET",
        url: web_url + "/agent/property-images/delete-images/" + id,
        success: function (response) {
            if (response.success == 0) {
                $.notify(
                    response.error,
                    {
                        position: "top center",
                        className: "error"
                    }
                );
            } else {
                $('#' + id).remove();
                $.notify(
                    response.message,
                    {
                        position: "top center",
                        className: "success"
                    }
                );
            }
        }
    });
}

// drag And drop document

var percentage = 0;
var CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').getAttribute("Content");

let dropzoneDocInit = () => {
    let myDropzone = new Dropzone(".documents_drop", {
        url: web_url + "/agent/property-document/save-docs",
        dictDefaultMessage: "Drop or Click to upload your document here.",
    });

    console.log(myDropzone)

    myDropzone.on("sending", function (file, xhr, formData) {
        $("#progressbar").show(); // More concise
        setperandsize(40);  // Assuming this function is now elsewhere and deduplicated
        formData.append("_token", CSRF_TOKEN); // Or use a better way to manage CSRF tokens
        setperandsize(60);
    });

    myDropzone.on("success", function (file, response) {
        setperandsize(80);
        if (!response.success) {
            $.notify(response.error, {className: 'error', autoHideDelay: 1000, globalPosition: "top center"});
        } else {
            setperandsize(100);
            $(".size").text(response.size);
            $.notify(response.message, {className: 'success', autoHideDelay: 1000, globalPosition: "top center"});
            Livewire.dispatch('refresh_document');
            Livewire.dispatch('reinitDocDropzone');
        }
        setTimeout(() => {
            $(".dz-preview").remove();
            $(".documents_drop").removeClass("dz-started");
            $(".progressbar").hide();
            $(".size").html("0 b");
            $(".percentage").html("0");
        }, 3000);
    });

    return myDropzone;
}

if (document.querySelector(".documents_drop")) {
    window.myDropzone = dropzoneDocInit()
}

window.addEventListener('reinitDocDropzone', event => {
    setTimeout(() => {
        if (window.myDropzone) {
            window.myDropzone.destroy();
        }
        window.myDropzone = dropzoneDocInit()
    }, 3000);
});

// if (document.getElementsByClassName("documents_drop").length > 0) {
//     // click button then add file
//     $('#addDocs').click(function () {
//         $('.documents_drop').click();
//         MyDropzone.processQueue();
//     });
//     Dropzone.autoDiscover = false;
//     var MyDropzone = new Dropzone(".documents_drop", {
//         url: web_url + '/agent/property-document/save-docs',
//         maxFilesize: 50, // in MB
//     });
//
//     MyDropzone.on("sending", function (file, xhr, formData) {
//         $("#progressbar").css("display", "inline-block");
//         setperandsize(40);
//         formData.append("_token", CSRF_TOKEN1);
//         setperandsize(60);
//     });
//
//     MyDropzone.on("success", function (file, response) {
//         setperandsize(80);
//         if (response.success = 0) { // Error
//             alert(response.error);
//         } else {
//             setperandsize(100);
//             $(".size").text(response.size);
//             var documentfilename = $(".documentFileCount").val();
//             documentfilename++;
//             $(".documentFileCount").val(documentfilename);
//
//             var documentUrl = web_url + '/files/property_documents/' + response.property_id + '/' + response.filename;
//             var str1 = response.filename
//             if (str1.startsWith('http')) {
//                 documentUrl = response.filename
//                 documentUrlArr = response.filename.split("property_documents/")
//                 str1 = documentUrlArr[1]
//             }Livewire.dispatch('refresh_document');
//             Livewire.dispatch('reinitDropzone');
//         }
//         setTimeout(() => {
//             $(".dz-preview").remove();
//             $(".documents_drop").removeClass("dz-started");
//             $(".progressbar").hide();
//             $(".percentage").html("0");
//         }, 5000);
//     });
// }

//  Property Document Delete Function Start ========

function deleteDocument(id, property_id) {
    $("#dialog").addClass("open");
    $('.dialog-msg').html("Do you really want to delete the document ?");

    // if user click on dialog's box button NO then remove this dialog box event
    $("#dialog").click(function () {
        $(".dialog-yes-btn").unbind();
    });

    //Attach document delete function to yes button
    $("#dialog .dialog-yes-btn").click(function () {
        $("#dialog").removeClass("open");
        $.ajax({
            type: "GET",
            url: web_url + "/agent/property-document/delete-document/" + property_id,
            success: function (response) {
                $('#documentfilename' + id).remove();
                if (response.success == 0) {
                    $.notify(response.error, {className: 'error', autoHideDelay: 1000, globalPosition: "top center"});
                } else {
                    $.notify(response.message, {
                        className: 'success',
                        autoHideDelay: 1000,
                        globalPosition: "top center"
                    });
                }
            }
        })
    });
}

//  Property Document Delete Function End ==========

// Property Document Name Chnage Function Start ==========
function changeDocumentName(id) {
    var FileName = $("#fileName" + id).text();
    $.ajax({
        type: "GET",
        data: {id: id, name: FileName},
        url: web_url + "/agent/property-document/edit-doc-name",
        success: function (data) {
            // $('#'+id).remove();
            if (data.success == 0) {
                $.notify(data.error, {className: 'error', autoHideDelay: 1000, globalPosition: "top center"});
            } else {
                $.notify(data.message, {className: 'success', autoHideDelay: 1000, globalPosition: "top center"});
            }
        }
    })
}

// document name edit button (onclick edit button focus on name for edit)
function DocumentEditName(id) {
    $("#fileName" + id).focus();
}

// Property Floor Plan Custom js Start ---------------------
// drag And drop Floor plans start

var percentage = 0;
var CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').getAttribute("content");

let dropzoneInit = () => {
    let myDropzone = new Dropzone(".floorplan_drop", {
        url: web_url + "/agent/property-floorplan/save-floorplans",
        dictDefaultMessage: "Drop or Click to upload your floor plan image here.",
    });

    console.log(myDropzone)

    myDropzone.on("sending", function (file, xhr, formData) {
        $("#progressbar").show(); // More concise
        setperandsize(40);  // Assuming this function is now elsewhere and deduplicated
        formData.append("_token", CSRF_TOKEN); // Or use a better way to manage CSRF tokens
        setperandsize(60);
    });

    myDropzone.on("success", function (file, response) {
        setperandsize(80);
        if (!response.success) {
            $.notify(response.error, {className: 'error', autoHideDelay: 1000, globalPosition: "top center"});
        } else {
            setperandsize(100);
            $(".size").text(response.size);
            $.notify(response.message, {className: 'success', autoHideDelay: 1000, globalPosition: "top center"});
            Livewire.dispatch('refresh_hotspot');
            Livewire.dispatch('reinitDropzone');
            location.reload(); // Reloads the current page
        }
        setTimeout(() => {
            $(".dz-preview").remove();
            $(".floorplan_drop").removeClass("dz-started");
            $(".progressbar").hide();
            $(".size").html("0 b");
            $(".percentage").html("0");
        }, 3000);
    });

    return myDropzone;
}

if (document.querySelector(".floorplan_drop")) {
    window.myDropzone = dropzoneInit()
}

window.addEventListener('reinitDropzone', event => {
    setTimeout(() => {
        if (window.myDropzone) {
            window.myDropzone.destroy();
        }
        window.myDropzone = dropzoneInit()
    }, 3000);
});

var drag_start_target;

function onDragStart(event) {
    // event.preventDefault();
    drag_start_target = event.target;
    console.log(drag_start_target)
    event.dataTransfer.setData('text/plain', event.target.id);
}

function onDragOver(event) {
    event.preventDefault();
}

function onDrop(event) {
    const id = event.dataTransfer.getData('text');
    const draggableElement = document.getElementById(id);
    const dropzone = event.target;
    dropzone.appendChild(draggableElement);
    event.dataTransfer.clearData();
}

function dragEnd(event) {
    event.preventDefault();
    console.log(event)
    var x = event.layerX;
    var y = event.layerY;
    var drag_proprtyimage_id = event.target.id;
    var drag_image_id = event.target.classList[0];

    var floorplan_image = document.getElementById('Drop_Box');
    var floorplan_image_height = floorplan_image.offsetHeight;
    var floorplan_image_width = floorplan_image.offsetWidth;

    html = "";
//    html += "<span class='p-5 rounded-full bg-grey z-50 absolute' style='margin-top:"+y+"px; margin-left:"+x+"px;' id='hotspot"+event.target.id+"'>";
    html += '<img src="' + web_url + '/images/icon-camera.png" class="absolute" style="margin-top:' + (y - 25) + 'px; margin-left:' + (x - 25) + 'px;" id="hotspot' + event.target.id + '"/>';
    html += "</span>";
    $('.drop-append').append(html);
    $.ajax({
        type: "GET",
        data: {
            id: drag_image_id,
            property_image_id: drag_proprtyimage_id,
            coordinateX: x,
            coordinateY: y,
            image_height: floorplan_image_height,
            image_width: floorplan_image_width
        },
        url: web_url + "/agent/property-floorplan/save-hotspot-data",
        success: function (response) {
            console.log(response)
            if (response.success == 0) {
                $.notify(response.error, {className: 'error', autoHideDelay: 1000, globalPosition: "top center"});
            } else {

                var propertyUrl = web_url + '/files/property_images/' + response.property_id + '/' + response.file_name;
                var str1 = response.file_name
                if (str1.startsWith('http')) {
                    propertyUrl = response.file_name
                    documentUrlArr = response.file_name.split("property_images/")
                    str1 = documentUrlArr[1]
                }

                html1 = "";
                html1 += '<div class="w-full py-8 px-5  inline-block relative gallery-image" id="HotspotData' + response.id + '">';
                html1 += '<div class="float-left">';
                html1 += '<img src="' + propertyUrl + '" style="max-height:100px;" alt="">';
                html1 += '</div>';
                html1 += '<div class="float-left mx-5">';
                html1 += '<span>' + str1 + ' </span>';
                html1 += '</div>';
                html1 += '<div class="float-right">';
                html1 += '<a href="#" title="Delete hotspot" onclick= "deletehotspot(' + "'" + response.id + "'" + ',' + "'" + response.file_name + "'" + ',' + "'" + response.floorplan_id + "'" + ',' + "'" + response.property_id + "'" + ')">';
                html1 += '<button class="button button-red p-3" data-ripple-light="true"><i class="fa fa-trash mr-2"></i>Delete</button>';
                html += '</a>';
                html1 += '</div>';
                html1 += '</div>';
                $("#hotspot-data").append(html1);
                $("#hotspotImage" + response.property_floorplan_id).addClass("disable-div");
                console.log($('[data-id=' + response.id + ']'))
                $('[data-id=' + response.id + ']').remove();
                $.notify(response.message, {className: 'success', autoHideDelay: 1000, globalPosition: "top center"});
            }
        }
    })

}


//  Property Document Delete Function Start ========

function deletehotspot(id, file_name, floorplan_id, property_id) {
    $("#dialog").addClass("open");
    $('.dialog-msg').html("Do you really want to delete the Hotspot ?");

    // if user click on dialog's box button NO then remove this dialog box event
    $("#dialog").click(function () {
        $(".dialog-yes-btn").unbind();
    });

    //Attach document delete function to yes button
    $("#dialog .dialog-yes-btn").click(function () {
        $("#dialog").removeClass("open");
        $.ajax({
            type: "GET",
            url: "../delete-hotspot",
            data: {id: id},
            success: function (response) {
                $('#hotspot' + id).remove();
                $('#HotspotData' + id).remove();
                if (response.success == 0) {
                    $.notify(response.error, {className: 'error', autoHideDelay: 1000, globalPosition: "top center"});
                } else {
                    location.reload();
                    // source = '';
                    // source += '<div class="relative">';
                    // source += '<img src="' + web_url + '/files/property_images/'+ property_id +'/' + file_name + '" data-id="' + id + '" class="' + floorplan_id + ' w-52 h-48 m-3 inline-block hotspot-image">';
                    // source += '<div ondragstart="onDragStart(event);"  id="' + id + '" draggable="true" class ="' + floorplan_id + ' absolute inset-0 w-52 m-3 h-48 z-30" style="cursor:crosshair;"></div>';
                    // source += '</div>';
                    // $('#Drag_Box').append(source);
                    // $.notify(response.message, { className: 'success', autoHideDelay: 1000, globalPosition: "top center" });        
                }
            }
        });
    });
}

//  Property Floor Plan Delete Function Start ========

function deleteFloorplan(id, property_id) {
    $("#dialog").addClass("open");
    $('.dialog-msg').html("Do you really want to delete the Floor plan ?");

    // if user click on dialog's box button NO then remove this dialog box event
    $("#dialog").click(function () {
        $(".dialog-yes-btn").unbind();
    });

    //Attach document delete function to yes button
    $("#dialog .dialog-yes-btn").click(function () {
        $("#dialog").removeClass("open");
        $.ajax({
            type: "GET",
            url: web_url + "/agent/property-floorplan/delete-floorplan/" + property_id,
            success: function (response) {
                if (response.success == 0) {
                    $.notify(response.error, {className: 'error', autoHideDelay: 1500, globalPosition: "top center"});
                } else {
                    $('#floorplan_block' + id).remove();
                    $.notify(response.message, {
                        className: 'success',
                        autoHideDelay: 1500,
                        globalPosition: "top center"
                    });
                }
            }
        })
    });
}

//  Property Floor Plan Delete Function End ==========

// Property Floor Plan Image Name Chnage Function Start ==========
function changeFloorPlanImageName(id, floorplanId) {
    var FileName = $("#fileName" + id).text();
    $.ajax({
        type: "GET",
        data: {id: id, name: FileName, floorplanId: floorplanId},
        url: web_url + "/agent/property-floorplan/save-floorplans",
        success: function (data) {
            $('#' + id).remove();
            if (data.success == 0) {
                $.notify(data.error, {className: 'error', autoHideDelay: 1000, globalPosition: "top center"});
            } else {
                $.notify(data.message, {className: 'success', autoHideDelay: 1000, globalPosition: "top center"});
            }
        }
    })
}

// Floorplan name edit button (onclick edit button focus on name for edit)
function FloorplanImageEditName(id) {
    $("#fileName" + id).focus();
}

// Property Floor Plan Image Name Chnage Function End ==========
// Property Floor Plan Custom js End

// Hide and show delete button of image

// Rotate the image
function rotateImage(id) {
    $(".image-rotate-wrap").show();
    $('#rotate_img').attr('src', $('#image' + id).attr('src'));
    $('#rotate_id').val(id);
}

// Right Rotation of Image
function rightRotateImg() {
    rotation = parseInt($('#direction').val());
    rotation += 90; // add 90 degrees, you can change this as you want
    if (rotation === 360) {
        rotation = 0;  // 360 means rotate back to 0
    }
    document.querySelector("#rotate_img").style.transform = `rotate(${rotation}deg)`;
    document.querySelector("#direction").value = `${rotation}`;
}

//  Left Rotation of Image
function leftRotateImg() {
    rotation = parseInt(document.querySelector("#direction").value);
    rotation -= 90; // add 90 degrees, you can change this as you want
    if (rotation === -360) {
        rotation = 0;  // 360 means rotate back to 0
    }
    document.querySelector("#rotate_img").style.transform = `rotate(${rotation}deg)`;
    document.querySelector("#direction").value = `${rotation}`;
}

// Close The rotate Image body

function closeRotateImage() {
    $(".image-rotate-wrap").hide();
    $("#direction").val(0);     //reset direction for next rotate
}

// Save The Image Rotate 
function saveImageRotate() {
    $(".rotateImg").attr('disabled', true)
    $(".rotateImg").html("Please Wait..");
    var degrees = document.querySelector("#direction").value;
    var id = document.querySelector("#rotate_id").value;
    const time = new Date();

    $.ajax({
        type: "GET",
        url: web_url + "/agent/property-images/rotate-images/" + id,
        data: {"degrees": degrees},
        success: function (response) {

            var documentUrl = web_url + '/files/property_images/' + response.property_id + '/' + response.file_name + '?q=' + time.getTime();
            var str1 = response.file_name
            if (str1.startsWith('http')) {
                documentUrl = response.file_name
            }

            $(".image-rotate-wrap").hide();
            $.notify("Image Rotate Successfully", {
                className: 'success',
                autoHideDelay: 1000,
                globalPosition: "top center"
            });

            //update the image in page with a new url with timestamp
            $('#image' + id).attr('src', documentUrl);

            $("#rotate_img").removeAttr('style');           //reset style for image in popup

            $("#direction").val(0);     //reset direction for next rotate
            $(".rotateImg").attr('disabled', false)
            $(".rotateImg").html("Save And Close");
            return false;
        }
    });
}

function allowDrop(ev) {
    ev.preventDefault();
}

function drag(ev, id, property_id) {
    $("#property_id").val(property_id);
    ev.dataTransfer.setData("text", ev.target.id);
    ev.dataTransfer.setData("div_id", id);
}

function drop(ev) {
    if ($("#sequence").val() == 0) {
        gallery_id = [];
    }
    ev.preventDefault();
    sequence = parseInt($("#sequence").val());
    sequence = sequence + 1;
    var data = ev.dataTransfer.getData("text");
    this.gallery_id.push(data);
    $("#" + data).removeAttr("ondragstart");
    var div = document.createElement('div');
    // Add Attribute for jquery-ui sortable grid class name (ui-state-default)
    div.setAttribute('class', 'inline ui-state-default');
    var div1 = document.createElement('div');
    div1.setAttribute('class', 'w-52 h-48 ml-3 mt-3 inline-block border-2');
    var span = document.createElement('span');
    span.setAttribute('class', 'mt-3  top-1  relative p-1 pl-2 pr-2 bg-blue')
    span.setAttribute('name', 'sequence');
    span.append(sequence);
    document.getElementById('sequence').value = sequence;
    div1.append(ev.target.appendChild(document.getElementById(data)), span);
    div.append(div1);
    document.getElementById('div1').append(div);
    div_id = ev.dataTransfer.getData("div_id");
    $("#" + div_id).remove();
}

// Add a New Gallery
function addGallery() {
    gallery_id = gallery_count;

    html = "";
    html += '<form action="" id="' + gallery_id + '" method="post">';
    html += '<div class="input-group input-group-outline mt-3 mb-3">';
    html += '<span>Gallery name</span>';
    html += '<input type="text" name="gallery_name" maxlength="100" value = "Gallery ' + gallery_count + '" id="gallery_name' + gallery_count + '" class="form-control" style="border:2px solid lightgrey;" placeholder=""/>';
    html += '</div>';
    html += '<div class="input-group input-group-outline mt-3 mb-3">';
    html += '<span>Short Description : (255 Characters Allowed Max)</span>';
    html += '<textarea id="short_description' + gallery_count + '" name="short_description" maxlength="255" rows="4" class="form-control" placeholder="" style="border:2px solid lightgrey;"></textarea>';
    html += '</div>';
    html += '<div class="border-1 border-grey-300 h-96 overflow-auto relative form-control d-flex grid-2" id="Drop_Box' + gallery_count + '"></div>';
    html += '<input type="hidden" value="' + gallery_count + '" id="sequence" name="sequence" />';
    html += '<input type="hidden" id="gallery_id' + gallery_count + '" value=0>';
    html += '<a href="#" class="button button-green mt-1" onclick="saveImageGallery(' + gallery_count + ');"  data-ripple-light="true">Save Gallery ' + gallery_count + '</a>';
    html += '</form>';
    $("#gallery").append(html);

    //Apply tailwind styules to new added elemnts
    apply_tailwind_styles("#" + gallery_id);

    DropBox = document.getElementById('Drop_Box' + gallery_count),
        new Sortable(DropBox, {
            group: 'shared', // set both lists to same group
            animation: 150
        });

    gallery_count++;
}

// delete image gallery 
function deleteImagegallery(sequence, gallery_id) {
    // for remove form get gallery childer form tag
    var gallery = $('#gallery').children('form').length;

    $("#dialog").addClass("open");
    $('.dialog-msg').html("Do you really want to delete the Gallery ?");

    // if user click on dialog's box button NO then remove this dialog box event
    $("#dialog").click(function () {
        $(".dialog-yes-btn").unbind();
    });

    //Attach gallery delete function to yes button
    $("#dialog .dialog-yes-btn").click(function () {
        $("#dialog").removeClass("open");

        $.ajax({
            type: "GET",
            url: web_url + "/agent/galleries/delete-property-galleries",
            dataType: 'json',
            data: {"sequence": sequence, "gallery_id": gallery_id, "gallery_length": gallery},
            success: function (data) {
                if (data['response'] == 1) {
                    $('#saveimagegallery' + sequence).remove();
                    // when we have on one gallery then remove delete button on deleteting second last gallery
                    if (gallery == 2) {
                        $('.deleteimagegallery').remove();
                    }
                    $.notify(
                        data['messege'],
                        {
                            position: "top center",
                            className: "success"
                        }
                    );
                } else {
                    $.notify(
                        data['messege'],
                        {
                            position: "top center",
                            className: "error"
                        }
                    )
                }
            }
        });
    });
}

// Save Gallery Image
function saveImageGallery(sequence) {
    var Drop_img_id = [];
    if (sequence) {
        if (document.getElementById('Drop_Box' + sequence).innerHTML) {
            id = $("#gallery_id" + sequence).val();
            gallery_name = $("#gallery_name" + sequence).val();
            default_image = $("#default_image" + sequence).val();
            short_description = $('#short_description' + sequence).val();
            Drop_Box = $('#Drop_Box' + sequence).children('.inline-block');
            Drop_Box.each(function () {
                Drop_img_id.push($(this).attr('id'));
            });
        }
    }

    if (Drop_img_id.length == 0) {
        $.notify(
            "Requierd Gallery Image ",
            {
                position: "top center",
                className: "error"
            }
        );
        return false;
    }
    $.ajax({
        type: "GET",
        url: web_url + "/agent/galleries/property-galleries",
        dataType: 'json',
        data: {
            "id": id,
            "gallery_name": gallery_name,
            "short_description": short_description,
            "default_image": default_image,
            "Drop_img_id": Drop_img_id
        },
        success: function (data) {
            if (data['response'] == 1) {

                //Set gallery id in hidden field
                $("#gallery_id" + sequence).val(data["gallery_id"]);

                $.notify(
                    "success",
                    {
                        position: "top center",
                        className: "success"
                    }
                );
                setTimeout(() => {
                    location.reload();
                }, 1000);
            }
        }
    })
}

// $('.default_image').chosen({
//     width:"100%",
//     html_template:'{text}<img style="border:3px solid #ff703d;padding:0px;margin-right:4px"  class="{class_name}" src="{url}" />'
// })
function saveImageSlider() {
    var Drop_img_id = [];
    if (document.getElementById('Drop_Box').innerHTML) {
        id = $("#gallery_id").val();
        Drop_Box = $('#Drop_Box').children('.inline-block');
        Drop_Box.each(function () {
            Drop_img_id.push($(this).attr('id'));
        });
        console.log(Drop_img_id)
    }

    if (Drop_img_id.length == 0) {
        $.notify(
            "Error on Updating Slider",
            {
                position: "top center",
                className: "error"
            }
        );
        return false;
    }
    $.ajax({
        type: "GET",
        url: web_url + "/agent/property-topbar/feature-slider",
        dataType: 'json',
        data: {"id": id, "Drop_img_id": Drop_img_id},
        success: function (data) {
            if (data['success'] == 1) {
                $.notify(
                    "Successfully Updated Slider",
                    {
                        position: "top center",
                        className: "success"
                    }
                );
            }
        }
    })
}

// Save topbar for property as Image, Video, Slider
function saveForProperty(obj) {
    choose_type = $(obj).data('subject');
    $.ajax({
        type: "GET",
        url: web_url + "/agent/property-topbar/selection-for-top",
        dataType: 'json',
        data: {"type": choose_type},
        success: function (data) {
            if (data['success'] == 1) {
                $.notify(
                    data.messege,
                    {
                        position: "top center",
                        className: "success"
                    }
                );
                var image = $('.saveforimage');
                var slider = $('.saveforslider');
                var video = $('.saveforvideo');

                if (data['topbar'] == "Image") {
                    image.attr({'onclick': '', 'data-subject': ''});
                    image.attr('class', 'button button-grey mt-1 float-right saveforimage')

                    slider.attr({'onclick': 'saveForProperty(this)', 'data-subject': 'slider'});
                    slider.attr('class', 'button button-green mt-1 float-right saveforslider')

                    video.attr({'onclick': 'saveForProperty(this)', 'data-subject': 'video'});
                    video.attr('class', 'button button-green mt-1 float-right saveforvideo')

                } else if (data['topbar'] == "Slider") {

                    image.attr({'onclick': 'saveForProperty(this)', 'data-subject': 'image'});
                    image.attr('class', 'button button-green mt-1 float-right saveforimage')

                    slider.attr({'onclick': '', 'data-subject': ''});
                    slider.attr('class', 'button button-grey mt-1 float-right saveforslider')

                    video.attr({'onclick': 'saveForProperty(this)', 'data-subject': 'video'});
                    video.attr('class', 'button button-green mt-1 float-right saveforvideo')

                } else if (data['topbar'] == "Video") {

                    image.attr({'onclick': 'saveForProperty(this)', 'data-subject': 'image'});
                    image.attr('class', 'button button-green mt-1 float-right saveforimage')

                    slider.attr({'onclick': 'saveForProperty(this)', 'data-subject': 'slider'});
                    slider.attr('class', 'button button-green mt-1 float-right saveforslider')

                    video.attr({'onclick': '', 'data-subject': ''});
                    video.attr('class', 'button button-grey mt-1 float-right saveforvideo')

                }
            } else {
                $.notify(
                    data.messege,
                    {
                        position: "top center",
                        className: "error"
                    }
                );
            }
        }
    })
}


// Property Video Custom js Start Here -------------------

// drag And drop video
var percentage = 0;
var CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').getAttribute("content");

if (document.getElementsByClassName("video_drop").length > 0) {
    // click button then add file
    $('#addvideos').click(function () {
        $('.video_drop').click();
        myDropzone.processQueue();
    });
    Dropzone.autoDiscover = false;
    var myDropzone = new Dropzone(".video_drop", {
        url: 'save-video',
        maxFilesize: 50, // 2 mb
        acceptedFiles: ".mp4,.mov,.ogg,",
    });

    myDropzone.on("sending", function (file, xhr, formData) {
        $("#progressbar").css("display", "inline-block");
        setperandsize(40);
        formData.append("_token", CSRF_TOKEN);
        setperandsize(60);
    });
    myDropzone.on("success", function (file, response) {
        setperandsize(80);
        if (response.success == 0) { // Error
            setperandsize(0);
            $.notify(
                response.message,
                {
                    position: "top center",
                    className: "error"
                }
            );

            // remove video preview
            $(".dz-preview").remove();
            $(".video_drop").removeClass("dz-started");
            $(".progressbar").hide();
            $(".size").html("0 b");
            $(".percentage").html("0");
        } else {
            $(".size").text(response.size);
            setperandsize(100);
            location.reload();
        }
    });
}

// Add Property Cover Video
function coverVideo(video_id) {
    $.ajax({
        type: "GET",
        url: "cover-video",
        data: {'video_id': video_id},
        success: function (response) {
            if (response.success == 0) {
                $.notify(
                    response.error,
                    {
                        position: "top center",
                        className: "error"
                    }
                );
            } else {
                $.notify(
                    response.message,
                    {
                        position: "top center",
                        className: "success"
                    }
                );
            }
        }
    })
}

// Add Property Featured Video
function featureVideo(video_id, obj) {
    $.ajax({
        type: "GET",
        url: web_url + "/agent/video/feature-video",
        data: {'video_id': video_id},
        success: function (response) {
            if (response.success == 0) {
                $.notify(
                    response.error,
                    {
                        position: "top center",
                        className: "error"
                    }
                );
            } else {
                $.notify(
                    response.message,
                    {
                        position: "top center",
                        className: "success"
                    }
                );
                // here I display all featuredvideo class element
                // And hide all currentvideo class element
                // And hide current click element
                // And find the siblings of current object and display

                $('.featuredvideo').show();
                $('.currentvideo').hide();
                $(obj).hide();
                $(obj).siblings().show();
            }
        }
    })
}

//  Add property Featured Image
function saveFeatureImage(image_id, obj) {
    $.ajax({
        type: "GET",
        url: web_url + "/agent/property-topbar/feature-image",
        data: {'image_id': image_id},
        success: function (response) {
            if (response.success == 0) {
                $.notify(
                    response.error,
                    {
                        position: "top center",
                        className: "error"
                    }
                );
            } else {
                $.notify(
                    response.message,
                    {
                        position: "top center",
                        className: "success"
                    }
                );
                // here I display all featuredvideo class element
                // And hide all currentvideo class element
                // And hide current click element
                // And find the siblings of current object and display

                $('.featureimage').show();
                $('.currentimage').hide();
                $(obj).hide();
                $(obj).siblings().show();

            }
        }
    })
}

function saveFeatureGalleryImage(gallery_image_id, gallery_id, obj) {
    $.ajax({
        type: "GET",
        url: web_url + "/agent/galleries/save_featured_gallery_images",
        data: {'gallery_image_id': gallery_image_id, 'gallery_id': gallery_id},
        success: function (response) {
            if (response.success == 0) {
                $.notify(
                    response.messege,
                    {
                        position: "top center",
                        className: "error"
                    }
                );
            } else {
                $.notify(
                    response.messege,
                    {
                        position: "top center",
                        className: "success"
                    }
                );
                // here I display all featuredgalleryimage class element
                // And hide all currentgalleryimage class element
                // And hide current click element
                // And find the siblings of current object and display

                $('.featuredgalleryimage').show();
                $('.currentgalleryimage').hide();
                $(obj).hide();
                $(obj).siblings().show();
            }
        }
    })
}

// Delete the Property video

function deleteVideo(id) {
    $("#dialog").addClass("open");
    $('.dialog-msg').html("Do you really want to delete the Video ?");

    // if user click on dialog's box button NO then remove this dialog box event
    $("#dialog").click(function () {
        $(".dialog-yes-btn").unbind();
    });

    //Attach image delete function to yes button
    $("#dialog .dialog-yes-btn").click(function () {
        $("#dialog").removeClass("open");
        $.ajax({
            type: "GET",
            url: "delete-video/" + id,
            success: function (response) {
                $('#' + id).remove();
                if (response.success == 0) {
                    $.notify(
                        response.error,
                        {
                            position: "top center",
                            className: "error"
                        }
                    );
                } else {
                    $.notify(
                        response.message,
                        {
                            position: "top center",
                            className: "success"
                        }
                    );
                    setTimeout(() => {
                        location.reload();
                    }, 1000);
                }
            }
        })
    });
}

// Save the 3d matter port url
function save3D_Url() {
    matterport_url = $("#matterport_url").val();
    if (matterport_url == "") {
        $.notify(
            "The matterport url field is required",
            {
                position: "top center",
                className: "error"
            }
        );
    } else {
        index = matterport_url.indexOf('https://');
        if (index == -1 || index != 0) {
            matterport_url = 'https://' + matterport_url;
        }
        $("#matterport_url").val("");
        $.ajax({
            type: "GET",
            url: "save-matterport-url",
            data: {'matterport_url': matterport_url},
            datatype: 'json',
            success: function (response) {
                if (response.success == 0) {
                    $.notify(
                        response.error,
                        {
                            position: "top center",
                            className: "error"
                        }
                    );
                } else {
                    $.notify(
                        response.message,
                        {
                            position: "top center",
                            className: "success"
                        }
                    );
                    /*html = "";
                    html += '<tr id="'+ response.matterport_id +'">';
                    html += '<td><iframe width="300" height="200" class="overflow-hidden" src="'+ response.matterport_url + '" frameborder="0" allowfullscreen allow="xr-spatial-tracking"></iframe></td>';
                    html += '<td class="p-2 text-2xl">'+ response.matterport_url + '</td>';
                    html += '<td> <a href="#" onclick="delete3D_Url('+ response.matterport_id +');" class="button button-red ml-5 delete">Delete</a></td>';
                    html += '<tr>';*/

                    html = "";
                    html += '<div class="col-md-4" id="' + response.matterport_id + '">';
                    html += '<div class="card">';
                    html += '<div><iframe width="300" height="200" class="overflow-hidden" src="' + response.matterport_url + '" frameborder="0" allowfullscreen allow="xr-spatial-tracking"></iframe></div>';
                    html += '<div class="card-body">';
                    html += '<h6>' + response.matterport_url + '</h6>';
                    html += '<div> <a href="#" onclick="delete3D_Url(' + response.matterport_id + ');" class="button button-red mb-0 delete"><i class="fa fa-trash mr-2"></i>Delete</a></div>';
                    html += '</div>';
                    html += '</div>';
                    html += '</div>';
                    $(".table_3durl").append(html);
                }
            }
        });
    }
}

// 3d matter port url delete alert show

function delete3D_Url(id) {
    $("#dialog").addClass("open");
    $('.dialog-msg').html("Do you really want to delete the 3d matterport url ?");
    //Attach image delete function to yes button
    $("#dialog .dialog-yes-btn").click(function () {
        $("#dialog").removeClass("open");
        $.ajax({
            type: "GET",
            url: "delete-matterport-url/" + id,
            success: function (response) {
                $('#' + id).remove();
                if (response.success == 0) {
                    $.notify(
                        response.error,
                        {
                            position: "top center",
                            className: "error"
                        }
                    );
                } else {
                    $.notify(
                        response.message,
                        {
                            position: "top center",
                            className: "success"
                        }
                    );
                }
            }
        })
    });
}

// Property Video Custom js End Here -------------------

// Update the Address Map

function updateMap() {
    let address_line_1 = $("#address_line_1").val();
    let address_line_2 = $("#address_line_2").val();

    let city = $("#city").val();
    let state = $("#state :selected").val();
    let country = $.trim($('#country').text());
    $('.custom-shadow').attr('src', "https://maps.google.com/maps?width=600&q=" + encodeURIComponent(address_line_1 + " " + address_line_2 + " " + city + " " + state + " " + country) + "&output=embed");
}

// Save the Address Map

function saveMap() {
    let address_line_1 = $("#address_line_1").val();
    let address_line_2 = $("#address_line_2").val();
    let city = $("#city").val();
    let state = $("#state").val();
    let zip = $("#zip").val();
    let country = $("#country").val();
    let latitude = $("#latitude").val();
    let longitude = $("#longitude").val();
    console.log(country)
    if (country != "United States") {
        /* alert("You must need to select the location from US country!"); */
        $.notify(
            "Please select the location from US country only.",
            {
                position: "top center",
                className: "error"
            }
        );
        return false
    }
    $.ajax({
        type: "get",
        url: "update-address-map",
        data: {
            'address_line_1': address_line_1,
            'address_line_2': address_line_2,
            'city': city,
            'state': state,
            'zip': zip,
            'country': country,
            'latitude': latitude,
            'longitude': longitude
        },
        datatype: 'json',
        success: function (response) {
            if (response.success == 0) {
                $.notify(
                    response.error,
                    {
                        position: "top center",
                        className: "error"
                    }
                );
            } else {
                $.notify(
                    response.message,
                    {
                        position: "top center",
                        className: "success"
                    }
                );
            }
        }
    });
}

// Add Code for copy property url in agent default url section in sidebar
function copyPropertyUrl() {
    // Get the text field
    var copyText = document.getElementById("propertyUrl").innerHTML;

    // Copy the text inside the field
    navigator.clipboard.writeText(copyText);

    // Notification the copied text
    $.notify(
        "Copied the clipboard",
        {
            position: "top center",
            className: "success"
        }
    );
}

//  Delete Profile Image

function deleteProfileImage() {
    $("#dialog").addClass("open");
    $('.dialog-msg').html("Do you really want to delete the Profile Image ?");

    // if user click on dialog's box button NO then remove this dialog box event
    $("#dialog").click(function () {
        $(".dialog-yes-btn").unbind();
    });

    //Attach image delete function to yes button
    $("#dialog .dialog-yes-btn").click(function () {
        $("#dialog").removeClass("open");
        $.ajax({
            type: "GET",
            url: "delete-profile-image/",
            success: function (response) {
                if (response.success == 0) {
                    $.notify(response.error, {className: 'error', autoHideDelay: 2000, globalPosition: "top center"});
                } else {
                    $.notify(response.message, {
                        className: 'success',
                        autoHideDelay: 2000,
                        globalPosition: "top center"
                    });
                }
                $("#upload-profile").css("display", "block");
                $('#delete-profile').css('display', 'none');
            }
        })
    });
}

//  Delete Profile Image

function deleteLogoImage() {
    $("#dialog").addClass("open");
    $('.dialog-msg').html("Do you really want to delete the Logo Image ?");

    // if user click on dialog's box button NO then remove this dialog box event
    $("#dialog").click(function () {
        $(".dialog-yes-btn").unbind();
    });

    //Attach image delete function to yes button
    $("#dialog .dialog-yes-btn").click(function () {
        $("#dialog").removeClass("open");
        $.ajax({
            type: "GET",
            url: "delete-logo-image/",
            success: function (response) {
                if (response.success == 0) {
                    $.notify(response.error, {className: 'error', autoHideDelay: 2000, globalPosition: "top center"});
                } else {
                    $.notify(response.message, {
                        className: 'success',
                        autoHideDelay: 2000,
                        globalPosition: "top center"
                    });
                }
                $("#upload-logo").css("display", "block");
                $('#delete-logo').css('display', 'none');
            }
        })
    });
}

// Check Password

function checkPassword() {
    var oldpassword = $('#oldpassword').val();
    var newpassword = $('#newpassword').val();
    var confirm_password = $('#confirm_password').val();
    if (oldpassword == '') {
        $.notify("Old Password is required", {className: 'error', autoHideDelay: 2000, globalPosition: "top center"});
        $('#oldpassword').focus();
        return false;
    } else if (newpassword == '') {
        $.notify("New Password is required", {className: 'error', autoHideDelay: 2000, globalPosition: "top center"});
        $('#newpassword').focus();
        return false;
    } else if (confirm_password == '') {
        $.notify("Confirm Password is required", {
            className: 'error',
            autoHideDelay: 2000,
            globalPosition: "top center"
        });
        $('#confirm_password').focus();
        return false;
    } else if (newpassword !== confirm_password) {
        $.notify("new password and confirm password have same values.", {
            className: 'error',
            autoHideDelay: 2000,
            globalPosition: "top center"
        });
        $('#confirm_password').focus();
        return false;
    } else {
        return true;
    }
}

// Here code forget agent password
function forgotAgentPassword() {
    var email = $('#email').val();
    $.ajax({
        type: "GET",
        url: web_url + "/agent/forgot-agent-password",
        data: {'email': email},
        success: function (response) {
            if (response.success == 0) {
                $.notify(
                    response.error,
                    {
                        position: "top center",
                        className: "error"
                    }
                );
            } else {
                $.notify(
                    response.message,
                    {
                        position: "top center",
                        className: "success"
                    }
                );
                $("#forgot-password").css("display", "none");
                $("#verification-code").removeClass('hidden');
                $("#verification-code").css("display", "block");
                $("#reset_email").val(response.email);
            }
        }
    });
}

// validate here for agent password reset
function resetAgentPasswordValidate() {
    var verified_code = $('#verified_code').val();
    if (verified_code == '') {
        $.notify(
            "Verification code is required",
            {
                position: "top center",
                className: "error"
            }
        );
        return false;
    } else {
        return true;
    }
}

// Validate agent new  password
function resetValidate() {
    var check = 0;
    var newpassword = $('#newpassword').val();
    var confirm_password = $('#confirm_password').val();
    if (newpassword == '') {
        check = 1;
        document.getElementById("newpassword_help").innerHTML = "Old Password is required";
    } else {
        document.getElementById("newpassword_help").innerHTML = "";
    }
    if (confirm_password == '') {
        check = 1;
        document.getElementById("confirm_password_help").innerHTML = "New Password is required";
    } else if (newpassword !== confirm_password) {
        check = 1;
        document.getElementById("confirm_password_help").innerHTML = "New password and confirm password have same values.";
    } else {
        document.getElementById("confirm_password_help").innerHTML = "";
    }
    if (check == 0) {
        return true;
    } else {
        return false;
    }
}

// required both field in agent login form
function AgentLoginForm() {
    $("input").attr("required", true);
}