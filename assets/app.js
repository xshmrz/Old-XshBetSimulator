var XSH           = {}, $window = jQuery(window), $document = jQuery(document), $body = jQuery('body'), $modal = null, $form = null, $mobile = false;
XSH.FORMSET       = function (form, data) {
    $form = form;
    XSH.FROMCLEAR($form);
    $form.find('input,select,textarea').each(function () {
        var name = jQuery(this).attr('name');
        if (jQuery(this).is('input')) {
            jQuery(this).val(data[name]);
        }
        else if (jQuery(this).is('select')) {
            jQuery(this).find('option').prop('selected', false);
            jQuery(this).find('option[value="' + data[name] + '"]').prop('selected', true);
        }
        else if (jQuery(this).is('textarea')) {
            jQuery(this).text(data[name]);
        }
    });
};
XSH.FORMGET       = function (form) {
    $form = form;
    return $form.serialize();
};
XSH.FROMCLEAR     = function (form) {
    $form = form;
    return $form.trigger('reset');
};
XSH.MODALSHOW     = function (modal) {
    jQuery('.modal').modal('hide');
    $modal = modal;
    $modal.modal('show');
};
XSH.ROUTEREDIRECT = function (route, timeout) {
    if (timeout) {
        setTimeout(function () {
            window.location.href = route;
        }, timeout);
    }
    else {
        window.location.href = route;
    }
};
XSH.ROUTEREFRESH  = function (timeout) {
    if (timeout) {
        setTimeout(function () {
            location.reload();
        }, timeout);
    }
    else {
        location.reload();
    }
};
XSH.INITUSERAGENT = function () {
    return 'ontouchstart' in document.documentElement;
};
XSH.INITBASE = function () {
    $form = jQuery('form:not(.text-editor)');
    $form.on('keyup keypress', function (e) {
        var keyCode = e.keyCode || e.which;
        if (keyCode === 13) {
            e.preventDefault();
            return false;
        }
    });
    // # ->
    $document.on('hidden.bs.modal', '.modal', function () {
        XSH.FROMCLEAR(jQuery('.modal').find('form'));
    });
    // # ->
    if (jQuery().dataTable) {
        jQuery.extend(jQuery.fn.dataTable.ext.classes, {
            sWrapper     : 'dataTables_wrapper dt-bootstrap5',
            sFilterInput : 'form-control',
            sLengthSelect: 'form-select'
        });
        jQuery.extend(!0, jQuery.fn.dataTable.defaults, {
            language: {
                lengthMenu       : '_MENU_',
                search           : '_INPUT_',
                searchPlaceholder: 'Search..',
                info             : 'Page <strong>_PAGE_</strong> of <strong>_PAGES_</strong>',
                paginate         : {
                    first   : '<i class="fa fa-angle-double-left"></i>',
                    previous: '<i class="fa fa-angle-left"></i>',
                    next    : '<i class="fa fa-angle-right"></i>',
                    last    : '<i class="fa fa-angle-double-right"></i>'
                }
            }
        });
        var jsDataTable = jQuery('.jsDataTable').DataTable({
                                                               pageLength: 10,
                                                               lengthMenu: false,
                                                               searching : true,
                                                               autoWidth : false,
                                                               order     : [],
                                                               dom       : '<"table-responsive"t>' +
                                                                           '<"bg-body-light rounded rounded-top-0 py-2 mt-0"p>'
                                                           });
        jsDataTable.on('order', function () {
            jQuery('.number-format').number(true, 2);
        });
        // Search
        let jsDataTableSearch = jQuery('.jsDataTableSearch');
        jsDataTableSearch.keyup(function () {
            jsDataTable = jQuery(this).closest('.tab-pane,.block').find('.jsDataTable').DataTable();
            jsDataTable.search(jQuery(this).val()).draw();
        });
    }
    // # ->
    var $ajaxElement;
    var $ajaxElementHtml;
    jQuery(document).on('ajaxStart', function (event) {
        $ajaxElement = jQuery(event.target.activeElement);
        if ($ajaxElement.is(':button')) {
            $ajaxElementHtml = $ajaxElement.html();
            $ajaxElement.html('<i class="fa fa-spinner fa-spin"></i>');
        }
        else {
        }
    });
    jQuery(document).on('ajaxStop', function (event) {
        if ($ajaxElement.is(':button')) {
            $ajaxElement.html($ajaxElementHtml);
        }
        else {
        }
    });
};
// # ->
XSH.INITBASE();
// # ->
function UIBLOCKENABLA() {
    $.blockUI({
                  css    : {backgroundColor: 'transparent', border: 'unset'},
                  message: '<i class="fa fa-circle-notch fa-spin fa-2x text-white"></i>'
              });
}
function UIBLOCKDISABLE() {
    $.unblockUI();
}
function toastMessage(message, callBack) {
    var toast = Toastify({
                             text        : '<div class="d-flex justify-content-start align-items-center rounded">' +
                                           '    <div class=""><i class="fa fa-check-circle font-20px pe-3"></i></div>' +
                                           '    <div class="">' +
                                           '        <div class="fw-normal">' + jQuery('meta[name="name"]').attr('content') + '</div>' +
                                           '        <div class="fw-normal">' + message + '</div>' +
                                           '    </div>' +
                                           '</div>',
                             duration    : 3000,
                             close       : false,
                             gravity     : 'top',
                             position    : 'right',
                             stopOnFocus : true,
                             style       : {
                                 'background': 'var(--bs-info)',
                                 'min-width' : '300px',
                                 'box-shadow': 'unset'
                             },
                             escapeMarkup: false,
                             onClick     : function () {
                                 toast.hideToast();
                             },
                             callback    : function () {
                                 if (typeof callBack == 'function') {
                                     callBack();
                                 }
                             }
                         }).showToast();
}
// ->
const Authorize = {
    LoginBtn          : jQuery('.LoginBtn'),
    LoginFrm          : jQuery('.LoginFrm'),
    LoginMdl          : jQuery('.LoginMdl'),
    LoginMdlBtn       : jQuery('.LoginMdlBtn'),
    RegisterBtn       : jQuery('.RegisterBtn'),
    RegisterFrm       : jQuery('.RegisterFrm'),
    RegisterMdl       : jQuery('.RegisterMdl'),
    RegisterMdlBtn    : jQuery('.RegisterMdlBtn'),
    LostPasswordBtn   : jQuery('.LostPasswordBtn'),
    LostPasswordFrm   : jQuery('.LostPasswordFrm'),
    LostPasswordMdl   : jQuery('.LostPasswordMdl'),
    LostPasswordMdlBtn: jQuery('.LostPasswordMdlBtn'),
    LogoutBtn         : jQuery('.LogoutBtn'),
    LogoutFrm         : jQuery('.LogoutFrm'),
    LogoutMdl         : jQuery('.LogoutMdl'),
    LogoutMdlBtn      : jQuery('.LogoutMdlBtn'),
    makeRequest       : function (type, url, data, callBackSuccess, callBackError) {
        $.ajax({
                   type   : type,
                   url    : url,
                   data   : data,
                   success: function (response) {
                       callBackSuccess(response);
                   },
                   error  : function (response) {
                       if (typeof callBackError === 'function') {
                           callBackError();
                       }
                       else {
                           toastMessage(response.responseJSON.message);
                       }
                   }
               });
    },
    Login             : function (data, callBackSuccess, callBackError) {
        this.makeRequest('POST', 'api/login', data, callBackSuccess, callBackError);
    },
    Register          : function (data, callBackSuccess, callBackError) {
        this.makeRequest('POST', 'api/register', data, callBackSuccess, callBackError);
    },
    LostPassword      : function (data, callBackSuccess, callBackError) {
        this.makeRequest('POST', 'api/lost-password', data, callBackSuccess, callBackError);
    },
    Logout            : function (callBackSuccess, callBackError) {
        this.makeRequest('POST', 'api/logout', null, callBackSuccess, callBackError);
    }
};

