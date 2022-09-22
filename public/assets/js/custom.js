var _asyncTime = 2000;

function reportAndRefresh(target,data,action,timeOut){
  	var _timeOut = timeOut || _asyncTime;
  	var data = data.trim(),
		data = JSON.parse(data);
	if(action){
    if(action == 'redirect'){
      var path = data.message;
      location.assign(path);
    }else{
      showNotification(data.status,data.message,_timeOut);
      timeOutReload(_timeOut);
    }
		
	}else{
		showNotification(data.status,data.message,_timeOut);
		// timeOutReload(_timeOut);
	}
}

function timeOutReload(delay){
	var timeDelay = delay || 3000;
	setTimeout(function(){
		location.reload();
	},timeDelay);
}

// this is to alternate between toastr and manual(self) notification
function showNotification(status,data,asyncTime,position){
	if(typeof toastr !== 'undefined'){
		showToastNotification(status,data,asyncTime,position);
    var btnSubmit = $('input[type=submit]');
    btnSubmit.removeClass('disabled');
    btnSubmit.prop('disabled', false);
    btnSubmit.html('Submit');
	}else{
		showNotifications(status,data);
	}
}

// this is the toast functionality method
function showToastNotification(status,data,asyncTime,position){
    var i = -1;
    var toastCount = 0;
    var $toastlast;

    // this is the default setting for the notification
    var _showDuration = 1000,
        _hideDuration = 1000,
        _timeOut = asyncTime || 10000,
        _extendedTimeOut = 1000,
        _showEasing = 'swing' || 'linear',
        _hideEasing=  'swing' || 'linear',
        _showMethod = 'show'  || 'fadeIn',
        _hideMethod = 'hide'  || 'fadeOut',
        _positionClass = position ? 'toast-top-right' : 'toast-bottom-left',
        _shortCutFunction =  status  ? 'success' : 'error',
        _message = data || '',
        _title =  status ? 'Success' : 'Error';

    var getMessage = function () {
    	const _checkStatusMsg = status ? 'Operation Successful' : 'error in performing the operation';
        return _checkStatusMsg;
    };

    var getMessageWithClearButton = function (msg) {
        msg = msg ? msg : 'Clear itself?';
        msg += '<br /><br /><button type="button" class="btn clear">Yes</button>';
        return msg;
    };

    var shortCutFunction = _shortCutFunction;
    var msg = _message;
    var title = _title || '';
    var $showDuration = _showDuration;
    var $hideDuration = _hideDuration;
    var $timeOut = _timeOut;
    var $extendedTimeOut = _extendedTimeOut;
    var $showEasing = _showEasing;
    var $hideEasing = _hideEasing;
    var $showMethod = _showMethod;
    var $hideMethod = _hideMethod;
    var toastIndex = toastCount++;

    toastr.options = {
        closeButton: true,
        progressBar: true,
        positionClass: _positionClass,
        onclick: null,
        ui: 'is-dark',
    };

    if (_showDuration) {
        toastr.options.showDuration = _showDuration;
    }

    if (_hideDuration) {
        toastr.options.hideDuration = _hideDuration;
    }

    if (_timeOut) {
        toastr.options.timeOut = _timeOut;
    }

    if (_extendedTimeOut) {
        toastr.options.extendedTimeOut = _extendedTimeOut;
    }

    if (_showEasing) {
        toastr.options.showEasing = _showEasing;
    }

    if (_hideEasing) {
        toastr.options.hideEasing = _hideEasing;
    }

    if (_showMethod) {
        toastr.options.showMethod = _showMethod;
    }

    if (_hideMethod) {
        toastr.options.hideMethod = _hideMethod;
    }

    if (!msg) {
        msg = getMessage();
    }

    toastr.clear();
    var $toast = toastr[shortCutFunction](msg, title); // Wire up an event handler to a button in the toast, if it exists
    $toastlast = $toast;

    if (typeof $toast === 'undefined') {
        return;
    }

    if ($toast.find('#okBtn').length) {
        $toast.delegate('#okBtn', 'click', function () {
            alert('you clicked me. i was toast #' + toastIndex + '. goodbye!');
            $toast.remove();
        });
    }
    if ($toast.find('#surpriseBtn').length) {
        $toast.delegate('#surpriseBtn', 'click', function () {
            alert('Surprise! you clicked me. i was toast #' + toastIndex + '. You could perform an action here.');
        });
    }
    if ($toast.find('.clear').length) {
        $toast.delegate('.clear', 'click', function () {
            toastr.clear($toast, {
                force: true
            });
        });
    }

}
//end toast notification


/* BoxWidget()
 * ======
 * Adds box widget functions to boxes.
 *
 * @Usage: $('.my-box').boxWidget(options)
 *         This plugin auto activates on any element using the `.box` class
 *         Pass any option as data-option="value"
 */
+function ($) {
  'use strict';

  var DataKey = 'lte.boxwidget';

  var Default = {
    animationSpeed : 500,
    collapseTrigger: '[data-widget="collapse"]',
    removeTrigger  : '[data-widget="remove"]',
    collapseIcon   : 'mdi-minus',
    expandIcon     : 'mdi-plus',
    removeIcon     : 'mdi-times'
  };

  var Selector = {
    data     : '.box',
    collapsed: '.collapsed-box',
    header   : '.box-header',
    body     : '.box-body',
    footer   : '.box-footer',
    tools    : '.box-tools'
  };

  var ClassName = {
    collapsed: 'collapsed-box'
  };

  var Event = {
    collapsed: 'collapsed.boxwidget',
    expanded : 'expanded.boxwidget',
    removed  : 'removed.boxwidget'
  };

  // BoxWidget Class Definition
  // =====================
  var BoxWidget = function (element, options) {
    this.element = element;
    this.options = options;

    this._setUpListeners();
  };

  BoxWidget.prototype.toggle = function () {
    var isOpen = !$(this.element).is(Selector.collapsed);

    if (isOpen) {
      this.collapse();
    } else {
      this.expand();
    }
  };

  BoxWidget.prototype.expand = function () {
    var expandedEvent = $.Event(Event.expanded);
    var collapseIcon  = this.options.collapseIcon;
    var expandIcon    = this.options.expandIcon;

    $(this.element).removeClass(ClassName.collapsed);

    $(this.element)
      .children(Selector.header + ', ' + Selector.body + ', ' + Selector.footer)
      .children(Selector.tools)
      .find('.' + expandIcon)
      .removeClass(expandIcon)
      .addClass(collapseIcon);

    $(this.element).children(Selector.body + ', ' + Selector.footer)
      .slideDown(this.options.animationSpeed, function () {
        $(this.element).trigger(expandedEvent);
      }.bind(this));
  };

  BoxWidget.prototype.collapse = function () {
    var collapsedEvent = $.Event(Event.collapsed);
    var collapseIcon   = this.options.collapseIcon;
    var expandIcon     = this.options.expandIcon;

    $(this.element)
      .children(Selector.header + ', ' + Selector.body + ', ' + Selector.footer)
      .children(Selector.tools)
      .find('.' + collapseIcon)
      .removeClass(collapseIcon)
      .addClass(expandIcon);

    $(this.element).children(Selector.body + ', ' + Selector.footer)
      .slideUp(this.options.animationSpeed, function () {
        $(this.element).addClass(ClassName.collapsed);
        $(this.element).trigger(collapsedEvent);
      }.bind(this));
  };

  BoxWidget.prototype.remove = function () {
    var removedEvent = $.Event(Event.removed);

    $(this.element).slideUp(this.options.animationSpeed, function () {
      $(this.element).trigger(removedEvent);
      $(this.element).remove();
    }.bind(this));
  };

  // Private

  BoxWidget.prototype._setUpListeners = function () {
    var that = this;

    $(this.element).on('click', this.options.collapseTrigger, function (event) {
      if (event) event.preventDefault();
      that.toggle($(this));
      return false;
    });

    $(this.element).on('click', this.options.removeTrigger, function (event) {
      if (event) event.preventDefault();
      that.remove($(this));
      return false;
    });
  };

  // Plugin Definition
  // =================
  function Plugin(option) {
    return this.each(function () {
      var $this = $(this);
      var data  = $this.data(DataKey);

      if (!data) {
        var options = $.extend({}, Default, $this.data(), typeof option == 'object' && option);
        $this.data(DataKey, (data = new BoxWidget($this, options)));
      }

      if (typeof option == 'string') {
        if (typeof data[option] == 'undefined') {
          throw new Error('No method named ' + option);
        }
        data[option]();
      }
    });
  }

  var old = $.fn.boxWidget;

  $.fn.boxWidget             = Plugin;
  $.fn.boxWidget.Constructor = BoxWidget;

  // No Conflict Mode
  // ================
  $.fn.boxWidget.noConflict = function () {
    $.fn.boxWidget = old;
    return this;
  };

  // BoxWidget Data API
  // ==================
  $(window).on('load', function () {
    $(Selector.data).each(function () {
      Plugin.call($(this));
    });
  });
}(jQuery);

// BoxWidget()

$(document).ready(function(){
  // addAsterisk();
  // this is aspect shows when you are trying to perform an action that requires permission
  $('span[data-critical=1] a').click(function(event){
    event.preventDefault();
    var link = $(this).attr('href');
    var action = $(this).text();
    if (confirm("are you sure you want to "+action+" item?")) {
      sendAjax(null,link,'','get');
    }
  });

  bindDropDown('autoload');

  $('#notification').click(function(event) {
    $(this).hide('slow');
  });

  if (typeof addMoreEvent ==='function') {
    addMoreEvent();
  }

});

// adding the notification status plain
function showStatus(target,data){
  var data = JSON.parse(data);
    showNotification(data.status,data.message,4000);
    return true;
}

//function for adding asterisks to all required element
function addAsterisk(){
	var required =$('input[required],select[required],textarea[required]');
	required.each(function(ind,ele){
		var label = $(this).siblings('label');
		label.after("<i class='"+"input-required'>*</i>");
	});
}

//function to test if an item is an array
function isArray(array) {
    return Object.prototype.toString.call(array) == '[object Array]';
}

function isObject(array) {
    return Object.prototype.toString.call(array) == '[object Object]';
}

function isArrayEmpty(arr){
	return !arr || arr.length === 0;
}

function isObjectEmpty(obj){
  if (!obj) {
    return true;
  }
  for (const key in obj) {
    if (Object.hasOwnProperty.call(obj, key) && obj[key]) {
      return false;
    }
  }
  return true;
}

function showNotifications(status,data){//a boolean value for success or failure
	//work on this code to show toast
	var notification =$("#notification");
	if (status) {
		notification.removeClass('alert alert-danger');
		notification.addClass('alert alert-success');	
	}
	else{
		notification.removeClass('alert alert-success');
		notification.addClass('alert alert-danger');
	}
	notification.html(data);
	animateTop(notification);
	//notification.show('slow');
}

function animateTop(element){
	element.show();
	element.animate({
    bottom: '2%',
		opacity: 1},
		"slow", function() {
		fadeTimer(element);
	});
}

function fadeTimer(element){
	setTimeout(function() {reverseAnimateTop(element)}, 7000);
	
}

function reverseAnimateTop(element){
	element.animate({
    bottom: -50,
		opacity: 0
	},
		"slow",function() {
		element.hide();
	});
}

function clearNotification() {
    var notification = $("#notification");
    notification.text("");
}

//function for ajax form submission success
function ajaxFormSubmissionSuccess(target,data) {
	try{
		data = JSON.parse(data);
		if (typeof(ajaxFormSuccess) ==='function') {
			ajaxFormSuccess(target.attr("name"),data);return;
		}
		else{
			showNotification(data.status,data.message);
			if (data.status && target!==null ) {
				//if the insert is an insert command just clear the form else leave the form u
				if (target.attr('action').indexOf('add')!==-1) {
					clearForm(target);
				}
				 
			}
			
		}
	}
	catch(err){
		showNotification(false,data);
	}
}

function ajaxFormSubmissionFailure(target,xhr,data,exception){
	data = data==null?"an error occur while processing the request":data.toString() + ": an error occur while processing the request";
	if (typeof ajaxFormFail =='function') {
		ajaxFormFail(form.attr("name"),data,Exception);
	}
	else{
		showNotification(false,data);
		// clearForm(form);
	}
}
// this function helps send ajax request to the server.
// the first parameter is the target. not always needed can therefore be null,
// then the link , the data(already encode) ,
// the function to call on success and the function to call on failure.
function sendAjax(target,url,data,type,success, failure){
    $.ajax({
        url: url,
        type: type,
        cache:false,
        processData:typeof data==='string'?true:false,
        data: data,
        contentType:typeof data==='string'?'application/x-www-form-urlencoded':false,
        success: function(data){
        	var $parse = JSON.parse(data);
        	if((typeof $parse.flagAction !== 'undefined')){
        		reportAndRefresh(target,data,$parse.flagAction);
        		return true;
        	}
          
        	if (typeof(success) === 'undefined') {
        		ajaxFormSubmissionSuccess(target,data);return;
        	}

        	var len=success.length;
        	if (len ==1) {
        		success(data);
        	}else{
            success(target,data);
        	}
        },
        error:function(xhr,data,exception){
        	if (failure==undefined) {
        		ajaxFormSubmissionFailure(target,xhr,data,exception);return;
        	}
        	var param = failure.length;
        	if (param ==1) {
        		failure(exception);
        	}
        	else if (param=2) {
        		failure(exception,data);
        	}
        	else if (param==3) {
        		failure(exception,data,target)
        	}
        	else{
          	 	failure(target,xhr,data,exception);
       		}
        }
    }
    );
}

//function to submit ajax call
//let the data be added using formdata when it is supported by the browser
function submitAjaxForm(form){
	//the submitted form is passed
	clearNotification();
	var message = "";
	if (typeof (message =validateFormData(form)!="string")) {
		var data = loadFormData(form);
		var url = form.attr('action');
		sendAjax(form,url,data,'post',ajaxFormSubmissionSuccess,ajaxFormSubmissionFailure);
	}
	else{
		showNotification(false,message);
	}
}

/**
 * @param  {form}  the form whose data is to be processed
 * @return {[mixed]} form data object or a serialised string format.
 */
function loadFormData(form){
	var submit = form.find("input[type='submit']");
	var subName = submit.attr('name');
	var subValue = submit.val();
	if (window.FormData === undefined ) {
		var data = form.serialize();
		data+= "&"+encodeURIComponent(subName)+"="+encodeURIComponent(subValue);
		return data;
	}
	var data = new FormData(form[0]);
	data.append(subName,subValue);
	return data;
}

// this is function is to preview an image
function filePreview(form){
  if(form && form[0]){
    var preview;
    var formId = form.attr('id'),
       preview = document.querySelector("input[type='file'] ~ img"),
       file    = document.querySelector('input[type=file]').files[0];
       if(preview == null){
        var tempPreview = document.querySelector("input[type='file']").previousElementSibling;
         if(tempPreview.tagName.toLowerCase() == 'img' || tempPreview == null){
          preview = tempPreview;
         }else{
            $("input[type='file']").after("<img src='' alt='image' width ='80%' height='50%' style='margin-top:10px;' />");
            preview = document.querySelector("input[type='file'] ~ img");
         }
       }

    if(preview !== null){
      var reader = new FileReader();
      reader.onload = function (e){
        preview.src = reader.result;
      }
      // reader.addEventListener("load", function () {
      //   preview.src = reader.result;
      // }, false);

      if (file) {
        reader.readAsDataURL(file);
      }
    }
  }
}

//function to clear the content of a form
function clearForm(form){
	formItems = form.find("input, select, textarea");
	formItems.each(function() {
		var  attribute =$(this).attr("type");
		if (!(attribute=="hidden" || attribute=="submit" || attribute=="reset")) {
			$(this).val("");
		}

	});
}

//function to validate the form submitted
function validateFormData(form){
	form.find('input[required], select[required], textarea[required]').each(function() {
		if ($(this).val().trim()=="") {
 			var name = $(this).attr('name');
 			return name+" is required";
		}
	});;
	return true;//means the form is validated.
}

//set of functions for working with cookie
function setCookie(name, value){
	document.cookie  = name+'='+value;
}

function readCookie(name){
	var cookie = document.cookie;
	var values = cookie.split(';');
	for (var i = values.length - 1; i >= 0; i--) {
		if (values[i]=='') {
			continue;
		}
		var temp = values[i].split('=');
		if (temp.length !=2) {
			return;
		}
		if(temp[0]==name){
			return temp[1];
		}
	}
	return null;
}

/**
 * This function load data form url and pass a function to be called to afer the work is done
 * @param  {[type]}   url      [description]
 * @param  {Function} callback [description]
 * @return {[type]}            [description]
 */
function loadSelectFromUrl(url,select){
	$.get(url, function(data) {
		/*optional stuff to do after success */
		var obj = '';
		if (data.trim()!='') {
			var obj = JSON.parse(data);
		} 
		
		loadSelect(select,obj);
	});
}

/**
 * This method help load data to any select element specified
 * @param  html select element [description]
 * @param  array[object] data   the object must have an id and value as the field
 * @return none.
 */
function loadSelect(select,data){
	var options = buildOption(data);
	select.html(options);
}

function buildOption(data){
	var result = "<option value=''>..choose..</option>";
	if (data==null) {
		return result;
	}
	for (var i = 0; i < data.length; i++) {
		var current =data[i];
		result+="<option value='"+current.id+"'>"+current.value+"</option>";
	}
	return result;
}

//function for building ajax link based on a relative address
function buildLink(link){
	return $("#base_link").val()+link;
}

//function to show comfirm dialog for delete operation
function processDelete(event,target){
	
}

//function to convert a tabele into a csv format
function convertTableToCsv(table){
	var content = '';
	//check if the table heade paramete is present.
	//just get all the row item on the table and process it into csv
	var rows = table[0].rows;
	for (var i = 0; i < rows.length; i++) {
		content+=extractRow(rows[i]);
	}

	return content;
}

function extractRow(element){
	//load all the data
	var result='';
	var columns=element.cells;
	for(var i=0;i < columns.length; i++) {
		var separator= ',';
		if (i==0) {
			separator='';
		}
		if (columns[i].innerHTML.indexOf('ul')!==-1) {continue;}
		result+=separator+columns[i].textContent;
	}
	result+="\n";
	return result;
}

// this function is what we use to get the autoload from the entity files
function bindDropDown(className){
	$('div').on('change',"."+className, function(event) {
		event.stopImmediatePropagation();
		var path = $(this).attr('data-load');
		var child =  $(this).attr('data-child');
		var depend = $(this).attr('data-depend');
		var childChange = $(this).attr('data-child-change');
		//check if this has a form parent
		var selector = '#'+child;
		var par = $(this).parents('form');
		var currentChild = par?par.find(selector):$(selector);
		var val = $(this).val();
		if (val=="" || val=="..choose..") {
			currentChild.html("<option value='"+"'>..choose..</option>");
			return;
		}
		var data = '';
		var dp = '';
		if (depend) {
			var temp = depend.split(',');
			for (var i = 0; i < temp.length; i++) {
				let val1 = $("#"+temp[i]).val();
				temp[i] = val1;
			}
			dp = temp.join('/');
		}
		var target = currentChild;
		var loadFunction = '';
		loadFunction = (childChange == 'true') ? childInputChangeAttr : childLoad;
		url = $('#baseurl').val()+'/'+'ajaxData/'+path+'/'+val+'/'+dp;
		sendAjax(target,url,data,'get',loadFunction);
	});
}

function childInputChangeAttr(target,data){
	if(target[0].tagName.toLowerCase() == 'input'){
		if(data.trim() == ""){target.html('');return;}
		var fromServer = JSON.parse(data),
			_attribute = fromServer.attribute,
			_value = fromServer.value;
			if(_value == 'true'){
				// this means that the attribute default value should be changed
				if(target[0].attributes.getNamedItem(_attribute) !== "null"){
					target[0].attributes.removeNamedItem(_attribute);
				}
			}else{
				var setAttr = document.createAttribute(_attribute);
				setAttr.value = _value;
				target[0].attributes.setNamedItem(setAttr);
			}
	}
}

function childLoad(target,data){
	if (target[0].tagName.toLowerCase()=='select') {
		if (data.trim()=="") {target.html('');return;}
		var fromServer = JSON.parse(data);
		loadSelect(target,fromServer);
		return;
	}
	// if not select just
  var fromServer = JSON.parse(data);
  if (fromServer.value == null) {
    target.html('');return;
  }
	target.html(fromServer.value);
}

function toggleCheckBox(element){
	element[0].checked=!element[0].checked;
}

function successFunction(target,data){
	var message = JSON.parse(data.trim());
	// message.message = message.status?'Operation Succesful':'Operation failed';
	if (!message.status) {
		toggleCheckBox(target);
	}
	showNotification(message.status,message.message);
}

function failedFunction(exception,data,target){
	target[0].checked =true;
	showNotification(false,'Operation failed');
	toggleCheckBox(target);
}

function saveJsFile(table,anchor){
	var csv = convertTableToCsv(table);
	var outputFile = window.prompt("What do you want to name your output file (Note: This won't have any effect on Safari)") || 'export';
    outputFile = outputFile.replace('.csv','') + '.csv'
    var csvLink = 'data:application/csv;charset=UTF-8,' + encodeURIComponent(csv);
	if (window.navigator.msSaveOrOpenBlob) {
        var blob = new Blob([decodeURIComponent(encodeURI(csv))], {
            type: "text/csv;charset=utf-8;"
        });
        navigator.msSaveBlob(blob, outputFile);
    } else {
        anchor
            .attr({
                'download': outputFile
                ,'href': csvLink
        });
    }
}

//function to add check box to the begining of every an table rows
function addCheckBox(table){
	var rows = table.find('tbody tr');
	for (var i = 0; i < rows.length; i++) {
		var current = rows[i];
		var temp = "<td><input type='checkbox' class='selection' /></td>";
		var html =  current.innerHTML+temp;
		rows[i].innerHTML = html;
	}
}

//function to get the index of a string after a particular position
function getPosition(str,start,needle){
	var index = str.substr(start).indexOf(needle);
	if (index==-1) {
		return index;
	}
	return index+start;
}

function replaceOrAdd(str,variable,value){
	value = encodeURIComponent(value);
	var path = str;
	var ind = path.indexOf(variable+'=');
	if (ind==-1) {
		path += path.indexOf('?')==-1?'?'+variable+'='+value:'&'+variable+'='+value;
		return path;
	}
	else{
		var next = getPosition(path,ind,'&');
		path =next==-1?replaceSubstr(path,ind,next,variable+'='+value):
		path= replaceSubstr(path,ind,next,variable+'='+value);
		return path;
		}
}

function replaceSubstr(str,start,end,replace){
	var temp = end==-1?str.substr(start):str.substr(start,end-start);
	return str.replace(temp,replace);
}

//function to get base url
function getBase(){
	return $('#baseurl').val();
}

function sendAjaxWithObj(target,url,data,type,success, failure){
  $.ajax({
    url: url,
    type: type,
    cache:false,
    data: data,
    success: function(data){

      if (typeof(success)==='undefined') {
        ajaxFormSubmissionSuccess(target,data);return;
      }

      var len=success.length;
      if (len ==1) {
        success(data);
      }else{
        success(target,data);
      }
    },
    error:function(xhr,data,exception){
      if (failure==undefined) {
        ajaxFormSubmissionFailure(target,xhr,data,exception);return;
      }
      var param = failure.length;
      if (param ==1) {
        failure(exception);
      }
      else if (param=2) {
        failure(exception,data);
      }
      else if (param==3) {
        failure(exception,data,target)
      }
      else{
          failure(target,xhr,data,exception);
      }
    }
  });
}

