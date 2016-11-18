//this script get the type of contents from database
function getLocalContentTypeFields(obj) {
    try {
        var form = null;
        var tempFeed = document.getElementById("local_field");
        var installationRoot = document.getElementById("dna_server_root").value;
        var feedbackdiv = tempFeed.id;
        var param = 'content-type=' + obj.value;
        var url_arg = installationRoot+'scoopit-api/get-local-content-type';
        var code = 150;
        var reqType = null;
        //var remoteType = document.getElementsByName("remote_type");
        var func = 'getRemoteContentTypeFields("remote_type")';
        var data = null;
        new function () {
            callprocess(code, form, feedbackdiv, param, url_arg, data, reqType, func);
        }
    } catch (E) {
        //alert(E);
    }
}

//this script get remote scoop it content type.
function getRemoteContentTypeFields(objId) {
    try {
        //alert(objId);
		var installationRoot = document.getElementById("dna_server_root").value;
        var obj = document.getElementById(objId);
        var form = null;
        var tempFeed = document.getElementById("remote_field");
        var localType = document.getElementById("local_type");
        //alert('hello: '+localType[0].value);
        if(obj.value==null || localType.value==null || obj.value=='' || localType.value=='')
        {
            //alert("Please select local cotent type to map, Thank you.");
            return;
        }
        var feedbackdiv = tempFeed.id;
        var param = 'content-type=' + obj.value + '&local-type=' + localType.value;
        var url_arg = installationRoot+'scoopit-api/get-remote-content-type';
        var code = 150;
        var reqType = null;
        var func = null;
        var data = null;
        new function () {
            callprocess(code, form, feedbackdiv, param, url_arg, data, reqType, func);
        }
    } catch (E) {
        //alert(E);
    }
}

//this script get remote scoop it content type.
function saveScoopitAuthor(obj) {
    try {
        //alert(objId);
		var installationRoot = document.getElementById("dna_server_root").value;
        var form = null;
        //alert('hello: '+localType[0].value);
        if(obj.value==null)
        {
            //alert("Please select local cotent type to map, Thank you.");
            return;
        }
        var feedbackdiv = 'authorFeedBack';
        var param = 'scoopItUserSel=' + obj.value ;
        var url_arg = installationRoot+'scoopit-api/save-author-user';
        var code = 150;
        var reqType = true;
        var func = null;
        var data = null;
        new function () {
            callprocess(code, form, feedbackdiv, param, url_arg, data, reqType, func);
        }
    } catch (E) {
        //alert(E);
    }
}

function removeProcessingImage()
{
    removeElement('proceImage');
}

function refreshPage() {

    window.location.reload(true);
    //setTimeout(new function () { location = window.location; }, 4000);
}


function saveContent() {
    try {
        var feedbackdiv = 'cms-form-result';

        var form = null;
        var param = '';
        var url_arg = '/User/AjaxReloadUserAwaitingApproval';
        var code = 101;
        var reqType = null;
        var func = null;
        var data = null;
        //alert(param);
        new function () {
            callprocess(code, form, feedbackdiv, param, url_arg, data, reqType, func);
        }
    } catch (E) {
        //alert(E);
    }
}