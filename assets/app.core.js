const Bet = {
    GetBtn      : jQuery('.BetGetBtn'),
    GetAllBtn   : jQuery('.BetGetAllBtn'),
    CreateBtn   : jQuery('.BetCreateBtn'),
    UpdateBtn   : jQuery('.BetUpdateBtn'),
    DeleteBtn   : jQuery('.BetDeleteBtn'),
    GetFrm      : jQuery('.BetGetFrm'),
    GetAllFrm   : jQuery('.BetGetAllFrm'),
    CreateFrm   : jQuery('.BetCreateFrm'),
    UpdateFrm   : jQuery('.BetUpdateFrm'),
    DeleteFrm   : jQuery('.BetDeleteFrm'),
    GetMdl      : jQuery('.BetGetMdl'),
    GetAllMdl   : jQuery('.BetGetAllMdl'),
    CreateMdl   : jQuery('.BetCreateMdl'),
    UpdateMdl   : jQuery('.BetUpdateMdl'),
    DeleteMdl   : jQuery('.BetDeleteMdl'),
    GetMdlBtn   : jQuery('.BetGetMdlBtn'),
    GetAllMdlBtn: jQuery('.BetGetAllMdlBtn'),
    CreateMdlBtn: jQuery('.BetCreateMdlBtn'),
    UpdateMdlBtn: jQuery('.BetUpdateMdlBtn'),
    DeleteMdlBtn: jQuery('.BetDeleteMdlBtn'),
    makeRequest : function (type, url, data, callBackSuccess, callBackError) {
        $.ajax({
                   type   : type,
                   url    : url,
                   data   : data,
                   success: function (response) {
                       callBackSuccess(response);
                   },
                   error  : function (response) {
                      callBackError(response);
                   }
               });
    },
    Get         : function ({id, callBackSuccess = () => {}, callBackError = () => {}}) {
        this.makeRequest('GET', 'api/bet/' + id, null, callBackSuccess, callBackError);
    },
    GetAll      : function ({queryParams, callBackSuccess = () => {}, callBackError = () => {}}) {
        const url = 'api/bet' + '?' + $.param(queryParams);
        this.makeRequest('GET', url, null, callBackSuccess, callBackError);
    },
    Create      : function ({data, callBackSuccess = () => {}, callBackError = () => {}}) {
        this.makeRequest('POST', 'api/bet', data, callBackSuccess, callBackError);
    },
    Update      : function ({id, data, callBackSuccess = () => {}, callBackError = () => {}}) {
        this.makeRequest('PUT', 'api/bet/' + id, data, callBackSuccess, callBackError);
    },
    Delete      : function ({id, callBackSuccess = () => {}, callBackError = () => {}}) {
        this.makeRequest('DELETE', 'api/bet/' + id, null, callBackSuccess, callBackError);
    }
};

const Coupon = {
    GetBtn      : jQuery('.CouponGetBtn'),
    GetAllBtn   : jQuery('.CouponGetAllBtn'),
    CreateBtn   : jQuery('.CouponCreateBtn'),
    UpdateBtn   : jQuery('.CouponUpdateBtn'),
    DeleteBtn   : jQuery('.CouponDeleteBtn'),
    GetFrm      : jQuery('.CouponGetFrm'),
    GetAllFrm   : jQuery('.CouponGetAllFrm'),
    CreateFrm   : jQuery('.CouponCreateFrm'),
    UpdateFrm   : jQuery('.CouponUpdateFrm'),
    DeleteFrm   : jQuery('.CouponDeleteFrm'),
    GetMdl      : jQuery('.CouponGetMdl'),
    GetAllMdl   : jQuery('.CouponGetAllMdl'),
    CreateMdl   : jQuery('.CouponCreateMdl'),
    UpdateMdl   : jQuery('.CouponUpdateMdl'),
    DeleteMdl   : jQuery('.CouponDeleteMdl'),
    GetMdlBtn   : jQuery('.CouponGetMdlBtn'),
    GetAllMdlBtn: jQuery('.CouponGetAllMdlBtn'),
    CreateMdlBtn: jQuery('.CouponCreateMdlBtn'),
    UpdateMdlBtn: jQuery('.CouponUpdateMdlBtn'),
    DeleteMdlBtn: jQuery('.CouponDeleteMdlBtn'),
    makeRequest : function (type, url, data, callBackSuccess, callBackError) {
        $.ajax({
                   type   : type,
                   url    : url,
                   data   : data,
                   success: function (response) {
                       callBackSuccess(response);
                   },
                   error  : function (response) {
                      callBackError(response);
                   }
               });
    },
    Get         : function ({id, callBackSuccess = () => {}, callBackError = () => {}}) {
        this.makeRequest('GET', 'api/coupon/' + id, null, callBackSuccess, callBackError);
    },
    GetAll      : function ({queryParams, callBackSuccess = () => {}, callBackError = () => {}}) {
        const url = 'api/coupon' + '?' + $.param(queryParams);
        this.makeRequest('GET', url, null, callBackSuccess, callBackError);
    },
    Create      : function ({data, callBackSuccess = () => {}, callBackError = () => {}}) {
        this.makeRequest('POST', 'api/coupon', data, callBackSuccess, callBackError);
    },
    Update      : function ({id, data, callBackSuccess = () => {}, callBackError = () => {}}) {
        this.makeRequest('PUT', 'api/coupon/' + id, data, callBackSuccess, callBackError);
    },
    Delete      : function ({id, callBackSuccess = () => {}, callBackError = () => {}}) {
        this.makeRequest('DELETE', 'api/coupon/' + id, null, callBackSuccess, callBackError);
    }
};

const CouponUpdate = {
    GetBtn      : jQuery('.CouponUpdateGetBtn'),
    GetAllBtn   : jQuery('.CouponUpdateGetAllBtn'),
    CreateBtn   : jQuery('.CouponUpdateCreateBtn'),
    UpdateBtn   : jQuery('.CouponUpdateUpdateBtn'),
    DeleteBtn   : jQuery('.CouponUpdateDeleteBtn'),
    GetFrm      : jQuery('.CouponUpdateGetFrm'),
    GetAllFrm   : jQuery('.CouponUpdateGetAllFrm'),
    CreateFrm   : jQuery('.CouponUpdateCreateFrm'),
    UpdateFrm   : jQuery('.CouponUpdateUpdateFrm'),
    DeleteFrm   : jQuery('.CouponUpdateDeleteFrm'),
    GetMdl      : jQuery('.CouponUpdateGetMdl'),
    GetAllMdl   : jQuery('.CouponUpdateGetAllMdl'),
    CreateMdl   : jQuery('.CouponUpdateCreateMdl'),
    UpdateMdl   : jQuery('.CouponUpdateUpdateMdl'),
    DeleteMdl   : jQuery('.CouponUpdateDeleteMdl'),
    GetMdlBtn   : jQuery('.CouponUpdateGetMdlBtn'),
    GetAllMdlBtn: jQuery('.CouponUpdateGetAllMdlBtn'),
    CreateMdlBtn: jQuery('.CouponUpdateCreateMdlBtn'),
    UpdateMdlBtn: jQuery('.CouponUpdateUpdateMdlBtn'),
    DeleteMdlBtn: jQuery('.CouponUpdateDeleteMdlBtn'),
    makeRequest : function (type, url, data, callBackSuccess, callBackError) {
        $.ajax({
                   type   : type,
                   url    : url,
                   data   : data,
                   success: function (response) {
                       callBackSuccess(response);
                   },
                   error  : function (response) {
                      callBackError(response);
                   }
               });
    },
    Get         : function ({id, callBackSuccess = () => {}, callBackError = () => {}}) {
        this.makeRequest('GET', 'api/coupon-update/' + id, null, callBackSuccess, callBackError);
    },
    GetAll      : function ({queryParams, callBackSuccess = () => {}, callBackError = () => {}}) {
        const url = 'api/coupon-update' + '?' + $.param(queryParams);
        this.makeRequest('GET', url, null, callBackSuccess, callBackError);
    },
    Create      : function ({data, callBackSuccess = () => {}, callBackError = () => {}}) {
        this.makeRequest('POST', 'api/coupon-update', data, callBackSuccess, callBackError);
    },
    Update      : function ({id, data, callBackSuccess = () => {}, callBackError = () => {}}) {
        this.makeRequest('PUT', 'api/coupon-update/' + id, data, callBackSuccess, callBackError);
    },
    Delete      : function ({id, callBackSuccess = () => {}, callBackError = () => {}}) {
        this.makeRequest('DELETE', 'api/coupon-update/' + id, null, callBackSuccess, callBackError);
    }
};

const Migration = {
    GetBtn      : jQuery('.MigrationGetBtn'),
    GetAllBtn   : jQuery('.MigrationGetAllBtn'),
    CreateBtn   : jQuery('.MigrationCreateBtn'),
    UpdateBtn   : jQuery('.MigrationUpdateBtn'),
    DeleteBtn   : jQuery('.MigrationDeleteBtn'),
    GetFrm      : jQuery('.MigrationGetFrm'),
    GetAllFrm   : jQuery('.MigrationGetAllFrm'),
    CreateFrm   : jQuery('.MigrationCreateFrm'),
    UpdateFrm   : jQuery('.MigrationUpdateFrm'),
    DeleteFrm   : jQuery('.MigrationDeleteFrm'),
    GetMdl      : jQuery('.MigrationGetMdl'),
    GetAllMdl   : jQuery('.MigrationGetAllMdl'),
    CreateMdl   : jQuery('.MigrationCreateMdl'),
    UpdateMdl   : jQuery('.MigrationUpdateMdl'),
    DeleteMdl   : jQuery('.MigrationDeleteMdl'),
    GetMdlBtn   : jQuery('.MigrationGetMdlBtn'),
    GetAllMdlBtn: jQuery('.MigrationGetAllMdlBtn'),
    CreateMdlBtn: jQuery('.MigrationCreateMdlBtn'),
    UpdateMdlBtn: jQuery('.MigrationUpdateMdlBtn'),
    DeleteMdlBtn: jQuery('.MigrationDeleteMdlBtn'),
    makeRequest : function (type, url, data, callBackSuccess, callBackError) {
        $.ajax({
                   type   : type,
                   url    : url,
                   data   : data,
                   success: function (response) {
                       callBackSuccess(response);
                   },
                   error  : function (response) {
                      callBackError(response);
                   }
               });
    },
    Get         : function ({id, callBackSuccess = () => {}, callBackError = () => {}}) {
        this.makeRequest('GET', 'api/migration/' + id, null, callBackSuccess, callBackError);
    },
    GetAll      : function ({queryParams, callBackSuccess = () => {}, callBackError = () => {}}) {
        const url = 'api/migration' + '?' + $.param(queryParams);
        this.makeRequest('GET', url, null, callBackSuccess, callBackError);
    },
    Create      : function ({data, callBackSuccess = () => {}, callBackError = () => {}}) {
        this.makeRequest('POST', 'api/migration', data, callBackSuccess, callBackError);
    },
    Update      : function ({id, data, callBackSuccess = () => {}, callBackError = () => {}}) {
        this.makeRequest('PUT', 'api/migration/' + id, data, callBackSuccess, callBackError);
    },
    Delete      : function ({id, callBackSuccess = () => {}, callBackError = () => {}}) {
        this.makeRequest('DELETE', 'api/migration/' + id, null, callBackSuccess, callBackError);
    }
};

const User = {
    GetBtn      : jQuery('.UserGetBtn'),
    GetAllBtn   : jQuery('.UserGetAllBtn'),
    CreateBtn   : jQuery('.UserCreateBtn'),
    UpdateBtn   : jQuery('.UserUpdateBtn'),
    DeleteBtn   : jQuery('.UserDeleteBtn'),
    GetFrm      : jQuery('.UserGetFrm'),
    GetAllFrm   : jQuery('.UserGetAllFrm'),
    CreateFrm   : jQuery('.UserCreateFrm'),
    UpdateFrm   : jQuery('.UserUpdateFrm'),
    DeleteFrm   : jQuery('.UserDeleteFrm'),
    GetMdl      : jQuery('.UserGetMdl'),
    GetAllMdl   : jQuery('.UserGetAllMdl'),
    CreateMdl   : jQuery('.UserCreateMdl'),
    UpdateMdl   : jQuery('.UserUpdateMdl'),
    DeleteMdl   : jQuery('.UserDeleteMdl'),
    GetMdlBtn   : jQuery('.UserGetMdlBtn'),
    GetAllMdlBtn: jQuery('.UserGetAllMdlBtn'),
    CreateMdlBtn: jQuery('.UserCreateMdlBtn'),
    UpdateMdlBtn: jQuery('.UserUpdateMdlBtn'),
    DeleteMdlBtn: jQuery('.UserDeleteMdlBtn'),
    makeRequest : function (type, url, data, callBackSuccess, callBackError) {
        $.ajax({
                   type   : type,
                   url    : url,
                   data   : data,
                   success: function (response) {
                       callBackSuccess(response);
                   },
                   error  : function (response) {
                      callBackError(response);
                   }
               });
    },
    Get         : function ({id, callBackSuccess = () => {}, callBackError = () => {}}) {
        this.makeRequest('GET', 'api/user/' + id, null, callBackSuccess, callBackError);
    },
    GetAll      : function ({queryParams, callBackSuccess = () => {}, callBackError = () => {}}) {
        const url = 'api/user' + '?' + $.param(queryParams);
        this.makeRequest('GET', url, null, callBackSuccess, callBackError);
    },
    Create      : function ({data, callBackSuccess = () => {}, callBackError = () => {}}) {
        this.makeRequest('POST', 'api/user', data, callBackSuccess, callBackError);
    },
    Update      : function ({id, data, callBackSuccess = () => {}, callBackError = () => {}}) {
        this.makeRequest('PUT', 'api/user/' + id, data, callBackSuccess, callBackError);
    },
    Delete      : function ({id, callBackSuccess = () => {}, callBackError = () => {}}) {
        this.makeRequest('DELETE', 'api/user/' + id, null, callBackSuccess, callBackError);
    }
};

